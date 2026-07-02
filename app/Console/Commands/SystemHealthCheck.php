<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SystemHealthCheck extends Command
{
    protected $signature = 'app:health-check';
    protected $description = 'Check system health and send alerts if issues detected';

    public function handle()
    {
        $issues = [];
        
        // Check database connection
        try {
            DB::connection()->getPdo();
            $this->info('✓ Database connection successful');
        } catch (\Exception $e) {
            $issues[] = 'Database connection failed: ' . $e->getMessage();
        }
        
        // Check cache (Redis optional - not required for basic health)
        try {
            Cache::store('redis')->set('health_check', 'ok', 10);
            if (Cache::store('redis')->get('health_check') !== 'ok') {
                $issues[] = 'Redis cache is not working properly';
            }
        } catch (\Exception $e) {
            // Redis not available - note but don't treat as failure
            $this->line('! Redis not available (optional for rate limiting)');
        }
        
        // Check disk space
        $freeSpace = disk_free_space(storage_path());
        if ($freeSpace < 1024 * 1024 * 500) { // Less than 500MB
            $issues[] = 'Low disk space: ' . round($freeSpace / (1024*1024), 2) . ' MB remaining';
        }
        
        // Check queue size
        $queueSize = DB::table('jobs')->count();
        if ($queueSize > 1000) {
            $issues[] = "Queue has {$queueSize} pending jobs, may need attention";
        }
        
        // Check failed jobs
        $failedJobs = DB::table('failed_jobs')->count();
        if ($failedJobs > 10) {
            $issues[] = "{$failedJobs} failed jobs in queue";
        }
        
        // Only consider database connection critical
        $criticalIssues = array_filter($issues, function($issue) {
            return str_contains($issue, 'Database connection');
        });
        
        if (!empty($criticalIssues)) {
            $this->error('Critical health issues found:');
            foreach ($criticalIssues as $issue) {
                $this->line('  ✗ ' . $issue);
            }
            return Command::FAILURE;
        }
        
        if (!empty($issues)) {
            $this->warn('Non-critical issues found (Redis, disk, queues):');
            foreach ($issues as $issue) {
                $this->line('  ⚠ ' . $issue);
            }
        }
        
        $this->info('All critical systems operational.');
        return Command::SUCCESS;
    }
}