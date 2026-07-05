<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use App\Listeners\AuditSubscriber;
use App\Listeners\LogErrorToAudit;
use Illuminate\Log\Events\MessageLogged;
use App\Models\LoanProduct;
use App\Observers\LoanProductObserver;
use Illuminate\Support\Facades\URL;

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
        // Force HTTPS in production (Requirement 11.5)
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Register Observers
        LoanProduct::observe(LoanProductObserver::class);

        // Register Audit Subscriber for authentication events
        Event::subscribe(AuditSubscriber::class);

        // Register Error Logger
        Event::listen(MessageLogged::class, LogErrorToAudit::class);

        /**
         * 2.7 Password Policy - Advanced Configuration
         * Enforces min 8 chars, uppercase, lowercase, numbers, and special symbols
         */
        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();
        });

        /**
         * Query Performance Logging
         * Logs queries exceeding 100ms to the query_performance_logs table
         */
        DB::listen(function ($query) {
            if ($query->time >= 100) {
                try {
                    DB::table('query_performance_logs')->insert([
                        'sql' => $query->sql,
                        'bindings' => json_encode($query->bindings),
                        'time_ms' => $query->time,
                        'connection' => $query->connectionName,
                        'url' => request()->fullUrl(),
                        'method' => request()->method(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    // Fail silently to avoid interrupting the main application flow
                }
            }
        });

        /**
         * 4.4 API Rate Limiting - Tiered Strategy (Intelligent Redis-backed)
         */
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(100)->by($request->ip());
        });

        RateLimiter::for('api', function (Request $request) {
            $user = $request->user();

            // 1. Determine Tier
            $tier = 'public';
            if ($user) {
                if ($user->isAdmin()) {
                    $tier = 'admin';
                } elseif ($user->isPremium()) {
                    $tier = 'premium';
                } else {
                    $tier = 'standard';
                }
            }

            // 2. Define Limits per Tier
            $limits = [
                'public' => 30,
                'standard' => 60,
                'premium' => 300,
                'admin' => 1000,
            ];

            // 3. Apply Limit based on Tier
            return Limit::perMinute($limits[$tier] ?? 30)
                ->by($user?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
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
