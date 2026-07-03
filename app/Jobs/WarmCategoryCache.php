<?php

namespace App\Jobs;

use App\Models\Book;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WarmCategoryCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $categoryId;

    /**
     * Create a new job instance.
     */
    public function __construct($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $books = Book::select(['id', 'title', 'author', 'price', 'stock_quantity'])
            ->where('category_id', $this->categoryId)
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->limit(1000)
            ->get();

        $cacheKey = "category:{$this->categoryId}:popular";

        try {
            Cache::tags(["category:{$this->categoryId}"])
                ->put($cacheKey, $books, 7200);
        } catch (\BadMethodCallException $e) {
            // Fallback for cache drivers that don't support tags (like 'file' or 'database')
            Cache::put($cacheKey, $books, 7200);
        }
    }
}
