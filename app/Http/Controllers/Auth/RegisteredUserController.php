<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use GuzzleHttp\Client;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'invite_code' => ['required', 'string', function ($attribute, $value, $fail) {
                if ($value !== env('ADMIN_INVITE_CODE')) {
                    $fail('The provided invite code is invalid.');
                }
            }],
        ]);

        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $adminRole ? $adminRole->id : null,
            'status' => 'pending',
        ]);

        // Generate the verification URL
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addDays(3),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // Send to Super Admin via Resend API
        try {
            $client = new Client();
            $client->post('https://api.resend.com/emails', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('RESEND_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'from' => 'PIL System <onboarding@resend.dev>',
                    'to' => env('SUPER_ADMIN_EMAIL'),
                    'subject' => 'New Admin Registration: ' . $user->name,
                    'html' => "
                        <div style='font-family: sans-serif; padding: 20px;'>
                            <h2>New Admin Access Request</h2>
                            <p><strong>Name:</strong> {$user->name}</p>
                            <p><strong>Email:</strong> {$user->email}</p>
                            <p>A new admin has registered using your invite code. Please click the button below to verify their identity and activate their account.</p>
                            <a href='{$verificationUrl}' style='display: inline-block; padding: 12px 24px; background: #FF6B00; color: white; border-radius: 8px; text-decoration: none; font-weight: bold;'>Confirm & Activate Account</a>
                            <p style='font-size: 12px; color: #666; margin-top: 20px;'>If you did not authorize this registration, please ignore this email.</p>
                        </div>
                    ",
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Resend API Error: ' . $e->getMessage());
        }

        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
