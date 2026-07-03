<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class BenchmarkBookQueries extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'benchmark:books {--iterations=100 : Number of iterations for each test}';

    /**
     * The console command description.
     */
    protected $description = 'Benchmark book queries and validate against performance targets';

    /**
     * Performance targets in milliseconds
     */
    protected array $targets = [
        'isbn_search' => 50,
        'catalog_listing' => 100,
        'category_filter' => 150,
        'full_text_search' => 300,
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $iterations = (int) $this->option('iterations');
        $this->info("Starting benchmarks with {$iterations} iterations...");

        // Ensure we have data to test with
        $book = Book::where('is_active', true)->first();
        if (!$book) {
            $this->error('No active books found in database. Please seed the database first.');
            return 1;
        }

        $results = [];

        $results['isbn_search'] = $this->benchmark('ISBN Search (< 50ms)', function () use ($book) {
            Book::findByIsbn($book->isbn);
        }, $iterations);

        $results['catalog_listing'] = $this->benchmark('Catalog Listing (< 100ms)', function () {
            // Using the repository logic for consistency with real usage
            (new \App\Repositories\BookRepository())->getActiveCatalog(20);
        }, $iterations);

        $results['category_filter'] = $this->benchmark('Category Filter (< 150ms)', function () use ($book) {
            Book::getByCategoryCached($book->category_id, 20);
        }, $iterations);

        $results['full_text_search'] = $this->benchmark('Full-Text Search (< 300ms)', function () use ($book) {
            // Using Boolean Mode bypasses the 50% rule and is usually more performant for specific term lookups
            // We use a specific numeric suffix from the title to ensure we hit a unique record
            $word = '*' . substr($book->title, -4) . '*';
            Book::whereRaw("MATCH(title, description) AGAINST(? IN BOOLEAN MODE)", [$word])->limit(20)->get();
        }, 50); // Requirement specifies 50 iterations for search

        return $this->displayAndValidate($results);
    }

    /**
     * Run a benchmark for a specific query
     */
    protected function benchmark(string $name, callable $callback, int $iterations): array
    {
        $this->comment("Benchmarking {$name}...");

        // Warmup pass
        for ($i = 0; $i < 5; $i++) {
            $callback();
        }

        $times = [];
        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            $callback();
            $times[] = (microtime(true) - $start) * 1000; // Convert to ms
        }

        return [
            'avg' => array_sum($times) / count($times),
            'min' => min($times),
            'max' => max($times),
            'total' => array_sum($times),
        ];
    }

    /**
     * Display results and validate against targets
     */
    protected function displayAndValidate(array $results): int
    {
        $rows = [];
        $failed = false;

        foreach ($results as $key => $stats) {
            $target = $this->targets[$key];
            $status = $stats['avg'] <= $target ? '<info>PASS</info>' : '<error>FAIL</error>';

            if ($stats['avg'] > $target) {
                $failed = true;
            }

            $rows[] = [
                ucwords(str_replace('_', ' ', $key)),
                $target . 'ms',
                number_format($stats['avg'], 2) . 'ms',
                number_format($stats['min'], 2) . 'ms',
                number_format($stats['max'], 2) . 'ms',
                $status
            ];
        }

        $this->table(
            ['Query Type', 'Target', 'Avg', 'Min', 'Max', 'Status'],
            $rows
        );

        if ($failed) {
            $this->error('Performance targets NOT met!');
            return 1;
        }

        $this->info('All performance targets met successfully.');
        return 0;
    }
}
