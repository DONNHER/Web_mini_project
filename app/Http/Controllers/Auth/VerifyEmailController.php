<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\User;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request): View|RedirectResponse
    {
        // Find user by ID from route
        $user = User::findOrFail($request->route('id'));

        // Check if signature is valid for this user
        if (! hash_equals(sha1($user->getEmailForVerification()), (string) $request->route('hash'))) {
            return redirect()->route('login')->with('error', 'Invalid verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            return view('auth.verified-success', ['name' => $user->name]);
        }

        if ($user->markEmailAsVerified()) {
            $user->update(['status' => 'active']); // Activate the account
            event(new Verified($user));
        }

        return view('auth.verified-success', ['name' => $user->name]);
    }
}
