<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// --- 7. AUTOMATED BACKUP SYSTEM ---

// 1. Weekly Database Backup - Sundays at 2:00 AM (Requirement 7.1)
Schedule::command('backup:run --only-db')
    ->weeklyOn(0, '02:00')
    ->onSuccess(fn () => Log::info('Automated DB Backup Success'))
    ->onFailure(fn () => Log::error('Automated DB Backup Failed'));

// 2. Weekly File Uploads Backup - Sundays (Requirement 7.1)
Schedule::command('backup:run --only-files')
    ->weeklyOn(0, '04:00')
    ->onSuccess(fn () => Log::info('File Uploads Backup Success'))
    ->onFailure(fn () => Log::error('File Uploads Backup Failed'));

// 3. Monthly Full System Backup (Requirement 7.1)
Schedule::command('backup:run')
    ->monthly()
    ->onSuccess(fn () => Log::info('Monthly Full System Backup Success'))
    ->onFailure(fn () => Log::error('Monthly Full System Backup Failed'));

// --- 4.2.2 Maintenance Scheduling ---

$logPath = storage_path('logs/scheduler.log');

// System Health Check (Requirement 6.2)
Schedule::command('system:health-check')
    ->hourly()
    ->onFailure(fn () => Log::error('Task Failure: system:health-check'));

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

// Automated Monthly System Report Email (Requirement 8.2)
Schedule::call(function () {
    $configs = \App\Models\ReportConfiguration::where('is_scheduled', true)->where('schedule_frequency', 'monthly')->get();
    foreach ($configs as $config) {
        \App\Jobs\SendScheduledReport::dispatch($config->id);
    }
})->monthly();

// Delete old notification records > 90 days
Schedule::command('notification:prune')
    ->weekly()
    ->withoutOverlapping()
    ->onSuccess(fn () => Log::info('Task Success: notification:prune'))
    ->onFailure(fn () => Log::error('Task Failure: notification:prune'))
    ->appendOutputTo($logPath);

// Auto-archive audit logs > 90 days old (Requirement 3.3)
Schedule::command('logs:archive --days=90')
    ->daily()
    ->withoutOverlapping()
    ->onSuccess(fn () => Log::info('Task Success: logs:archive'))
    ->onFailure(fn () => Log::error('Task Failure: logs:archive'))
    ->appendOutputTo($logPath);

// Materialized Views for Reporting
Schedule::command('app:refresh-materialized-views')
    ->hourly()
    ->withoutOverlapping()
    ->onSuccess(fn () => Log::info('Task Success: app:refresh-materialized-views'))
    ->onFailure(fn () => Log::error('Task Failure: app:refresh-materialized-views'))
    ->appendOutputTo($logPath);

// AI Security Batch Audit - Every 30 minutes
Schedule::command('app:batch-security-audit')
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->onSuccess(fn () => Log::info('Task Success: app:batch-security-audit'))
    ->onFailure(fn () => Log::error('Task Failure: app:batch-security-audit'))
    ->appendOutputTo($logPath);
