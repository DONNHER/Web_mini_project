<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\RequireTwoFactor;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware aliases (these can be used in routes)
        $middleware->alias([
            'verified' => EnsureEmailIsVerified::class,
            'admin' => AdminMiddleware::class,
            '2fa' => RequireTwoFactor::class,
            'sanitize' => \App\Http\Middleware\SanitizeInput::class,
            'transform.api' => \App\Http\Middleware\TransformApiResponse::class,
        ]);

        // Apply sanitization to all web routes
        $middleware->appendToGroup('web', \App\Http\Middleware\SanitizeInput::class);
        
        // Apply transformation to API routes
        $middleware->appendToGroup('api', \App\Http\Middleware\TransformApiResponse::class);
        
        
        // Add middleware to web group
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // Add middleware to api group
        $middleware->group('api', [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();