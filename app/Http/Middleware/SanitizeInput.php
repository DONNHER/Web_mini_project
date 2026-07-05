<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    /**
     * Fields that should not be sanitized (e.g., passwords).
     */
    protected $except = [
        'password',
        'password_confirmation',
        'current_password',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();

        array_walk_recursive($input, function (&$value, $key) {
            if (!in_array($key, $this->except) && is_string($value)) {
                // 1. Strip HTML tags (Requirement: Strip HTML tags where not allowed)
                $value = strip_tags($value);

                // 2. Trim whitespace
                $value = trim($value);

                // 3. Remove control characters
                $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);

                // 4. Convert special HTML entities for extra safety
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
            }
        });

        $request->merge($input);

        return $next($request);
    }
}
