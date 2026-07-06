<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Display the shareholder login view.
     */
    public function createShareholder(): View
    {
        return view('auth.shareholder-login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Check if user has 2FA enabled
        if ($request->user()->hasTwoFactorEnabled()) {
            // If they use Email OTP (simplified: always generate one if 2FA enabled)
            $otp = rand(100000, 999999);
            $request->user()->update([
                'otp_code' => $otp,
                'otp_expires_at' => now()->addMinutes(10)
            ]);

            $request->user()->notify(new \App\Notifications\SendOtpNotification($otp));

            session(['2fa_required' => true]);
            return redirect()->route('two-factor.challenge');
        }

        if ($request->user()->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('home', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
