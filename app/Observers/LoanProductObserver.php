<?php

namespace App\Observers;

use App\Models\LoanProduct;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

class LoanProductObserver
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the LoanProduct "saved" event.
     */
    public function saved(LoanProduct $product): void
    {
        $this->invalidateCache($product);
    }

    /**
     * Handle the LoanProduct "deleted" event.
     */
    public function deleted(LoanProduct $product): void
    {
        $this->invalidateCache($product);
    }

    /**
     * Common cache invalidation logic
     */
    protected function invalidateCache(LoanProduct $product): void
    {
        // 1. Invalidate Catalog
        $this->cacheService->invalidateCatalog();

        // 2. Invalidate category cache
        $this->cacheService->invalidateCategory($product->category_id);

        // 3. Invalidate specific product cache if any
        Cache::forget("loan_product_{$product->id}");
    }
}
