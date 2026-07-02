<?php

namespace App\Http\Controllers;

use App\Models\TwoFactorSecret;
use Illuminate\Http\Request;
use PragmaRX\Google2FALaravel\Google2FA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct(Google2FA $google2fa)
    {
        $this->google2fa = $google2fa;
    }

    /**
     * Show 2FA settings page
     */
    public function index()
    {
        $user = Auth::user();
        $twoFactor = $user->twoFactorSecret;
        
        return view('profile.two-factor', compact('user', 'twoFactor'));
    }

    /**
     * Enable 2FA
     */
    public function enable(Request $request)
    {
        $user = Auth::user();
        
        // Generate secret key
        $secret = $this->google2fa->generateSecretKey();
        
        // Generate QR code
        $qrCode = $this->google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $secret
        );
        
        // Generate recovery codes
        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = Str::random(10);
        }
        
        // Save to database
        $twoFactor = TwoFactorSecret::updateOrCreate(
            ['user_id' => $user->id],
            [
                'secret' => $secret,
                'recovery_codes' => json_encode($recoveryCodes),
                'enabled' => false
            ]
        );
        
        return view('profile.two-factor-enable', compact('qrCode', 'recoveryCodes', 'secret'));
    }

    /**
     * Verify and confirm 2FA setup
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $user = Auth::user();
        $twoFactor = $user->twoFactorSecret;

        if (!$twoFactor) {
            return back()->with('error', '2FA not set up properly.');
        }

        $valid = $this->google2fa->verifyKey($twoFactor->secret, $request->code);

        if ($valid) {
            $twoFactor->update(['enabled' => true]);
            
            // Send notification
            $user->notify(new \App\Notifications\TwoFactorEnabled());
            
            return redirect()->route('profile.two-factor')
                ->with('success', 'Two-factor authentication enabled successfully!');
        }

        return back()->with('error', 'Invalid verification code.');
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password'
        ]);

        $user = Auth::user();
        $twoFactor = $user->twoFactorSecret;

        if ($twoFactor) {
            $twoFactor->delete();
            
            // Send notification
            $user->notify(new \App\Notifications\TwoFactorDisabled());
        }

        return redirect()->route('profile.two-factor')
            ->with('success', 'Two-factor authentication disabled.');
    }

    /**
     * Show 2FA challenge page
     */
    public function challenge()
    {
        return view('auth.two-factor-challenge');
    }

    /**
     * Verify 2FA code during login
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $user = Auth::user();
        $twoFactor = $user->twoFactorSecret;

        if (!$twoFactor || !$twoFactor->enabled) {
            return redirect()->intended('/');
        }

        // Check if code is a recovery code
        $recoveryCodes = json_decode($twoFactor->recovery_codes, true);
        if (in_array($request->code, $recoveryCodes)) {
            // Remove used recovery code
            $recoveryCodes = array_diff($recoveryCodes, [$request->code]);
            $twoFactor->update(['recovery_codes' => json_encode(array_values($recoveryCodes))]);
            
            session(['2fa_verified' => true]);
            return redirect()->intended('/');
        }

        // Verify TOTP code
        $valid = $this->google2fa->verifyKey($twoFactor->secret, $request->code);

        if ($valid) {
            session(['2fa_verified' => true]);
            return redirect()->intended('/');
        }

        return back()->with('error', 'Invalid verification code.');
    }
}