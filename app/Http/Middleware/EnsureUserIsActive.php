<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->status !== 'active') {
            // Allow them to see the verification page if they are pending
            if (Auth::user()->status === 'pending' && !Auth::user()->hasVerifiedEmail()) {
                if ($request->routeIs('verification.*') || $request->routeIs('logout')) {
                    return $next($request);
                }
                return redirect()->route('verification.notice');
            }

            $status = Auth::user()->status;
            Auth::logout();

            $message = "Your account is {$status}. Please contact support.";
            return redirect()->route('login')->with('error', $message);
        }

        return $next($request);
    }
}
