<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Jobs\ProcessAITask;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BatchSecurityAudit extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:batch-security-audit {--limit=50 : Number of loans to process}';

    /**
     * The console command description.
     */
    protected $description = 'Dispatch background AI security audits for recent loans';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');

        $this->info("Scanning for loans requiring background AI audit...");

        // Find loans that don't have an AI security log yet
        $loans = Loan::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('ai_security_logs')
                  ->whereColumn('ai_security_logs.resource_id', 'loans.id')
                  ->where('ai_security_logs.resource_type', 'Loan');
        })
        ->orderBy('created_at', 'desc')
        ->limit($limit)
        ->get();

        if ($loans->isEmpty()) {
            $this->info("No pending audits found.");
            return 0;
        }

        $this->info("Dispatching " . $loans->count() . " AI tasks to the queue...");

        foreach ($loans as $loan) {
            ProcessAITask::dispatch($loan->id)->onQueue('ai-tasks');
            $this->line("Dispatched Loan #{$loan->id}");
        }

        $this->info("All tasks dispatched successfully.");
        return 0;
    }
}
