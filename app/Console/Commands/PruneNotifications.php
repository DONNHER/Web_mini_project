<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PruneNotifications extends Command
{
    protected $signature = 'notification:prune';
    protected $description = 'Delete old notification records > 90 days';

    public function handle()
    {
        $this->info('Pruning old notifications...');

        $count = DB::table('notifications')
            ->where('created_at', '<', Carbon::now()->subDays(90))
            ->delete();

        $this->info("Deleted {$count} old notifications.");

        return Command::SUCCESS;
    }
}
