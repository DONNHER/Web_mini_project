<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// --- 4.2.2 Maintenance Scheduling ---

$logPath = storage_path('logs/scheduler.log');

// Database only backup - daily
Schedule::command('backup:run --only-db')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->onSuccess(fn () => Log::info('Task Success: backup:run --only-db'))
    ->onFailure(fn () => Log::error('Task Failure: backup:run --only-db'))
    ->appendOutputTo($logPath);

// Full database and files backup - weekly
Schedule::command('backup:run')
    ->weeklyOn(0, '03:00')
    ->withoutOverlapping()
    ->onSuccess(fn () => Log::info('Task Success: backup:run (full)'))
    ->onFailure(fn () => Log::error('Task Failure: backup:run (full)'))
    ->appendOutputTo($logPath);

// Remove old backups per retention policy
Schedule::command('backup:clean')
    ->dailyAt('04:00')
    ->withoutOverlapping()
    ->onSuccess(fn () => Log::info('Task Success: backup:clean'))
    ->onFailure(fn () => Log::error('Task Failure: backup:clean'))
    ->appendOutputTo($logPath);

// Monitor backup health
Schedule::command('backup:monitor')
    ->dailyAt('09:00')
    ->withoutOverlapping()
    ->onSuccess(fn () => Log::info('Task Success: backup:monitor'))
    ->onFailure(fn () => Log::error('Task Failure: backup:monitor'))
    ->appendOutputTo($logPath);

// Cancel pending orders > 24 hours old
Schedule::command('order:cleanup-pending')
    ->hourly()
    ->withoutOverlapping()
    ->onSuccess(fn () => Log::info('Task Success: order:cleanup-pending'))
    ->onFailure(fn () => Log::error('Task Failure: order:cleanup-pending'))
    ->appendOutputTo($logPath);

// Clear expired sessions
Schedule::command('session:cleanup')
    ->daily()
    ->withoutOverlapping()
    ->onSuccess(fn () => Log::info('Task Success: session:cleanup'))
    ->onFailure(fn () => Log::error('Task Failure: session:cleanup'))
    ->appendOutputTo($logPath);

// Archive and compress old logs
Schedule::command('log:rotate')
    ->weekly()
    ->withoutOverlapping()
    ->onSuccess(fn () => Log::info('Task Success: log:rotate'))
    ->onFailure(fn () => Log::error('Task Failure: log:rotate'))
    ->appendOutputTo($logPath);

// Generate daily sales report
Schedule::command('report:generate-daily')
    ->dailyAt('06:00')
    ->withoutOverlapping()
    ->onSuccess(fn () => Log::info('Task Success: report:generate-daily'))
    ->onFailure(fn () => Log::error('Task Failure: report:generate-daily'))
    ->appendOutputTo($logPath);

// Delete old notification records > 90 days
Schedule::command('notification:prune')
    ->weekly()
    ->withoutOverlapping()
    ->onSuccess(fn () => Log::info('Task Success: notification:prune'))
    ->onFailure(fn () => Log::error('Task Failure: notification:prune'))
    ->appendOutputTo($logPath);

// Archive audit logs > 1 year old
Schedule::command('audit:archive')
    ->monthly()
    ->withoutOverlapping()
    ->onSuccess(fn () => Log::info('Task Success: audit:archive'))
    ->onFailure(fn () => Log::error('Task Failure: audit:archive'))
    ->appendOutputTo($logPath);

// Materialized Views for Reporting
Schedule::command('app:refresh-materialized-views')
    ->hourly()
    ->withoutOverlapping()
    ->onSuccess(fn () => Log::info('Task Success: app:refresh-materialized-views'))
    ->onFailure(fn () => Log::error('Task Failure: app:refresh-materialized-views'))
    ->appendOutputTo($logPath);
