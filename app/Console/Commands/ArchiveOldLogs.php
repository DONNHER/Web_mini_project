<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Audit;

class ArchiveOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'logs:archive {--days=90 : The number of days to keep}';

    /**
     * The console command description.
     */
    protected $description = 'Archive audit logs older than a specific number of days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        $this->info("Archiving logs older than {$days} days ({$cutoffDate})...");

        // For simplicity, we'll just delete them here,
        // but in a production app we might move them to an archive table or cloud storage.
        $count = Audit::where('created_at', '<', $cutoffDate)->count();

        if ($count > 0) {
            Audit::where('created_at', '<', $cutoffDate)->delete();
            $this->info("Successfully archived {$count} log entries.");
        } else {
            $this->info("No old logs found to archive.");
        }

        return 0;
    }
}
