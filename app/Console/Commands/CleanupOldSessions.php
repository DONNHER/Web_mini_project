<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanupOldSessions extends Command
{
    protected $signature = 'session:cleanup';
    protected $description = 'Remove expired session records from database';

    public function handle()
    {
        $this->info('Cleaning up old sessions...');

        $count = DB::table('sessions')
            ->where('last_activity', '<', Carbon::now()->subDays(config('session.lifetime'))->timestamp)
            ->delete();

        $this->info("Deleted {$count} old session records.");

        return Command::SUCCESS;
    }
}
