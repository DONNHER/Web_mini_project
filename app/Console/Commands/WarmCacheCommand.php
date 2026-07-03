<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Jobs\WarmCategoryCache;
use Illuminate\Console\Command;

class WarmCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:warm-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm up category cache by dispatching background jobs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching cache warmup jobs for all categories...');

        Category::all()->each(function ($category) {
            WarmCategoryCache::dispatch($category->id);
            $this->line("Dispatched warmup for category: {$category->name}");
        });

        $this->info('All warmup jobs have been dispatched to the queue.');
    }
}
