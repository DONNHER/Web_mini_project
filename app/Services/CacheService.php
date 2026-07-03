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
            Cache::tags(['books', 'catalog'])->flush();
        } catch (\BadMethodCallException $e) {
            Cache::forget('catalog_active_p_start');
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
            Cache::forget("books_cat_{$categoryId}_p_start");
            Cache::forget("catalog_cat_{$categoryId}_p_start");
        }
    }
}
