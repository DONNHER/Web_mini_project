<?php

namespace App\Observers;

use App\Models\Book;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

class BookObserver
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Book "saved" event.
     */
    public function saved(Book $book): void
    {
        $this->invalidateCache($book);
    }

    /**
     * Handle the Book "deleted" event.
     */
    public function deleted(Book $book): void
    {
        $this->invalidateCache($book);
    }

    /**
     * Common cache invalidation logic
     */
    protected function invalidateCache(Book $book): void
    {
        // 1. Invalidate Catalog (using service as requested)
        $this->cacheService->invalidateCatalog();

        // 2. Invalidate individual ISBN cache
        Cache::forget("book_isbn_{$book->isbn}");

        // 3. Invalidate category cache
        $this->cacheService->invalidateCategory($book->category_id);

        // 4. Invalidate Bestseller lists
        Cache::forget('bestseller_books_5');
        Cache::forget('bestseller_books_8');
    }
}
