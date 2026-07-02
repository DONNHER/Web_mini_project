<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Listeners\AuditSubscriber;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Audit Subscriber for authentication events
        Event::subscribe(AuditSubscriber::class);

        /**
         * 4.4 API Rate Limiting - Tiered Strategy
         */
        RateLimiter::for('api', function (Request $request) {
            $user = $request->user();

            // 1. Determine Identity (User ID for auth, IP for guest)
            $key = $user ? $user->id : $request->ip();

            // 2. Define Limits based on Tier/Role
            $limits = [];

            if ($user) {
                if ($user->isAdmin()) {
                    // admin: 1000 requests/minute
                    $limits = [
                        Limit::perMinute(1000)->by($key),
                        Limit::perSecond(50)->by($key), // Burst protection
                    ];
                } elseif ($user->isPremium()) {
                    // premium: 300 requests/minute
                    $limits = [
                        Limit::perMinute(300)->by($key),
                        Limit::perSecond(10)->by($key), // Burst protection
                    ];
                } else {
                    // standard: 60 requests/minute
                    $limits = [
                        Limit::perMinute(60)->by($key),
                        Limit::perSecond(2)->by($key), // Burst protection
                    ];
                }
            } else {
                // public: 30 requests/minute for guests
                $limits = [
                    Limit::perMinute(30)->by($key),
                    Limit::perSecond(1)->by($key), // Burst protection
                ];
            }

            // Apply custom JSON 429 response to all limits
            return array_map(function (Limit $limit) {
                return $limit->response(function (Request $request, array $headers) {
                    return response()->json([
                        'error' => 'Rate limit exceeded',
                        'message' => 'Too many requests. Please slow down.',
                        'details' => [
                            'limit' => $headers['X-RateLimit-Limit'] ?? 'Unknown',
                            'remaining' => $headers['X-RateLimit-Remaining'] ?? 0,
                            'retry_after' => $headers['Retry-After'] ?? null,
                        ]
                    ], 429, $headers);
                });
            }, $limits);
        });

        /**
         * 4.4.1 Authentication Rate Limiting
         */
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'error' => 'Too many attempts',
                        'message' => 'Authentication rate limit exceeded. Please try again later.',
                        'retry_after' => $headers['Retry-After'] ?? null
                    ], 429, $headers);
                });
        });
    }
}
