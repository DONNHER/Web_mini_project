<?php

namespace App\Console\Commands;

use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanupOverdueLoans extends Command
{
    protected $signature = 'loan:cleanup-overdue';
    protected $description = 'Mark loans as overdue if they passed the due date';

    public function handle()
    {
        $this->info('Cleaning up overdue loans...');

        $count = Loan::where('status', 'borrowed')
            ->where('due_date', '<', Carbon::now())
            ->update(['status' => 'overdue']);

        $this->info("Marked {$count} loans as overdue.");

        return Command::SUCCESS;
    }
}
