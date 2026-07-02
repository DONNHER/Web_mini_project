<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LogRotate extends Command
{
    protected $signature = 'log:rotate';
    protected $description = 'Archive and compress old logs';

    public function handle()
    {
        $this->info('Rotating logs...');

        $logPath = storage_path('logs/laravel.log');

        if (File::exists($logPath) && File::size($logPath) > 0) {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $archivePath = storage_path("logs/laravel-{$timestamp}.log.gz");

            $content = File::get($logPath);
            $compressedContent = gzencode($content, 9);

            File::put($archivePath, $compressedContent);
            File::put($logPath, ''); // Clear current log

            $this->info("Log rotated and compressed to: laravel-{$timestamp}.log.gz");
        } else {
            $this->warn('Log file does not exist or is empty.');
        }

        return Command::SUCCESS;
    }
}
