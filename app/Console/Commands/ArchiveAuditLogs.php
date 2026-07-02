<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Models\Audit;
use Carbon\Carbon;

class ArchiveAuditLogs extends Command
{
    protected $signature = 'audit:archive';
    protected $description = 'Archive audit logs older than 1 year';

    public function handle()
    {
        $this->info('Archiving old audit logs...');

        $cutoff = Carbon::now()->subYear();

        $oldLogs = Audit::where('created_at', '<', $cutoff)->get();

        if ($oldLogs->isEmpty()) {
            $this->info('No logs to archive.');
            return Command::SUCCESS;
        }

        $filename = 'archives/audits_' . now()->format('Y-m-d_H-i-s') . '.json';

        Storage::disk('local')->put($filename, $oldLogs->toJson(JSON_PRETTY_PRINT));

        $count = Audit::where('created_at', '<', $cutoff)->delete();

        $this->info("Archived and deleted {$count} audit logs to {$filename}");

        return Command::SUCCESS;
    }
}
