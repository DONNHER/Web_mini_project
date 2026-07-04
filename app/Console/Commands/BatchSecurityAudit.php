<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Jobs\ProcessAITask;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BatchSecurityAudit extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:batch-security-audit {--limit=50 : Number of orders to process}';

    /**
     * The console command description.
     */
    protected $description = 'Dispatch background AI security audits for recent orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');

        $this->info("Scanning for orders requiring background AI audit...");

        // Find orders that don't have an AI security log yet
        $orders = Order::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('ai_security_logs')
                  ->whereColumn('ai_security_logs.resource_id', 'orders.id')
                  ->where('ai_security_logs.resource_type', 'Order');
        })
        ->orderBy('created_at', 'desc')
        ->limit($limit)
        ->get();

        if ($orders->isEmpty()) {
            $this->info("No pending audits found.");
            return 0;
        }

        $this->info("Dispatching " . $orders->count() . " AI tasks to the queue...");

        foreach ($orders as $order) {
            ProcessAITask::dispatch($order->id)->onQueue('ai-tasks');
            $this->line("Dispatched Order #{$order->id}");
        }

        $this->info("All tasks dispatched successfully.");
        return 0;
    }
}
