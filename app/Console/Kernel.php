<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Database backup - daily at 2 AM
        $schedule->command('backup:run --only-db')->dailyAt('02:00')
            ->onSuccess(function () {
                Log::info('Database backup completed successfully');
            })
            ->onFailure(function () {
                Log::error('Database backup failed');
            })
            ->emailOutputTo(env('BACKUP_NOTIFICATION_EMAIL'));

        // Full backup (files + db) - weekly on Sunday at 3 AM
        $schedule->command('backup:run')->weeklyOn(0, '3:00');

        // Clean old backups - daily at 4 AM
        $schedule->command('backup:clean')->dailyAt('04:00');

        // Monitor backup health - daily at 9 AM
        $schedule->command('backup:monitor')->dailyAt('09:00')
            ->emailOutputOnFailure(env('ADMIN_EMAIL'));

        // Data cleanup tasks (Objective 7)
        $schedule->command('app:cleanup-old-sessions')->dailyAt('01:00');
        $schedule->command('app:cleanup-temp-files')->dailyAt('01:30');
        $schedule->command('app:generate-sales-report')->monthlyOn(1, '05:00');

        // Warm up cache daily at 3:30 AM
        $schedule->command('app:warm-cache')->dailyAt('03:30');

        // Run batch security audits hourly
        $schedule->command('app:batch-security-audit')->hourly();

        // Queue worker (if using database queue)
        $schedule->command('queue:work --stop-when-empty')->everyMinute();

        $schedule->command('app:health-check')->hourly();
    }

    protected $commands = [
        Commands\CleanupOldSessions::class,
        Commands\CleanupTempFiles::class,
        Commands\GenerateSalesReport::class,
        Commands\WarmCacheCommand::class,
        Commands\BatchSecurityAudit::class,
        Commands\SimulateFraud::class,
    ];

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
