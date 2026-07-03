<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;

class IndexBooksBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:index-books-batch {--chunk=500 : The number of records to import per chunk}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index books in batches for better observability and performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chunkSize = $this->option('chunk');
        $total = Book::where('is_active', true)->count();

        $this->info("Starting to index {$total} active books in chunks of {$chunkSize}...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        Book::where('is_active', true)->chunk($chunkSize, function ($books) use ($bar) {
            $books->searchable();
            $bar->advance($books->count());
        });

        $bar->finish();
        $this->newLine();
        $this->info('Indexing completed successfully!');
    }
}
