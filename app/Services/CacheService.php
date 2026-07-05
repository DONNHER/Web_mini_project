<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Invalidate all catalog-related caches.
     */
    public function invalidateCatalog(): void
    {
        try {
            Cache::tags(['loan_products', 'catalog'])->flush();
        } catch (\BadMethodCallException $e) {
            Cache::forget('loan_products_active_p_start');
        }
    }

    /**
     * Invalidate cache for a specific category.
     */
    public function invalidateCategory($categoryId): void
    {
        try {
            Cache::tags(["category_{$categoryId}"])->flush();
        } catch (\BadMethodCallException $e) {
            Cache::forget("loan_products_cat_{$categoryId}_p_start");
        }
    }
}
