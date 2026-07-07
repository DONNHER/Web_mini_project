<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = auth()->user();

        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'region' => $request->input('region'),
            'province' => $request->input('province'),
            'city' => $request->input('city'),
            'barangay' => $request->input('barangay'),
            'street_address' => $request->input('street_address'),
        ];

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        if ($user->email !== $request->input('email')) {
            $user->email_verified_at = null;
        }

        $user->update($data);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Export user data
     */
    public function export(Request $request)
    {
        $user = $request->user()->load(['loans.loanProduct', 'loans.repayments']);

        return response()->json([
            'user' => $user->only(['name', 'email', 'tier', 'status']),
            'loans' => $user->loans->map(function ($loan) {
                return [
                    'id' => $loan->id,
                    'product' => $loan->loanProduct?->name,
                    'principal' => $loan->principal_amount,
                    'interest' => $loan->interest_rate,
                    'status' => $loan->status,
                    'repayments' => $loan->repayments->map->only(['amount_paid', 'payment_date', 'status']),
                ];
            }),
            'exported_at' => now(),
        ])->header('Content-Disposition', 'attachment; filename="personal_data_export.json"');
    }

    /**
     * Log out from other browser sessions.
     */
    public function logoutOtherBrowserSessions(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        Auth::guard('web')->logoutOtherDevices($request->password);

        return Redirect::route('profile.edit')->with('status', 'sessions-cleared');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
