<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    protected $except = [
        'password',
        'password_confirmation',
        'current_password',
    ];

    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();

        array_walk_recursive($input, function (&$value, $key) {
            if (!in_array($key, $this->except) && is_string($value)) {
                // Trim whitespace
                $value = trim($value);
                
                // Remove control characters
                $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
                
                // Convert special HTML entities
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
            }
        });

        $request->merge($input);

        return $next($request);
    }
}