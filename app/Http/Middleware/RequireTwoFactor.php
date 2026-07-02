<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireTwoFactor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->hasTwoFactorEnabled() && !session('2fa_verified')) {
            return redirect()->route('two-factor.challenge');
        }

        return $next($request);
    }
}