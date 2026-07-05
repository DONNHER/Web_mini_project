<?php

namespace App\Repositories;

use App\Models\LoanProduct;
use Illuminate\Support\Facades\Cache;

class LoanProductRepository
{
    /**
     * Get optimized active loan products catalog.
     */
    public function getActiveCatalog($perPage = 100)
    {
        $cursor = request('cursor', 'start');
        $cacheKey = "loan_products_active_p_{$cursor}";

        $callback = function () use ($perPage) {
            return LoanProduct::select([
                'id',
                'name',
                'description',
                'interest_rate',
                'duration_months',
                'min_amount',
                'max_amount',
                'category_id',
                'created_at',
                'updated_at'
            ])
            ->with(['category:id,name'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->cursorPaginate($perPage);
        };

        try {
            return Cache::tags(['loan_products', 'catalog'])->remember($cacheKey, 3600, $callback);
        } catch (\BadMethodCallException $e) {
            return Cache::remember($cacheKey, 3600, $callback);
        }
    }

    /**
     * Filter by Category
     */
    public function getByCategory($categoryId, $perPage = 100)
    {
        $cursor = request('cursor', 'start');
        $cacheKey = "loan_products_cat_{$categoryId}_p_{$cursor}";

        $callback = function () use ($categoryId, $perPage) {
            return LoanProduct::select([
                'id', 'name', 'description', 'interest_rate',
                'duration_months', 'min_amount', 'max_amount', 'category_id',
                'created_at', 'updated_at'
            ])
            ->with(['category:id,name'])
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->cursorPaginate($perPage);
        };

        try {
            return Cache::tags(['loan_products', "category_{$categoryId}"])->remember($cacheKey, 3600, $callback);
        } catch (\BadMethodCallException $e) {
            return Cache::remember($cacheKey, 3600, $callback);
        }
    }

    /**
     * Search loan products by name or description
     */
    public function search($term, $perPage = 100)
    {
        return LoanProduct::select([
            'id', 'name', 'description', 'interest_rate',
            'duration_months', 'min_amount', 'max_amount', 'category_id',
            'created_at', 'updated_at'
        ])
        ->with(['category:id,name'])
        ->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        })
        ->where('is_active', true)
        ->orderBy('created_at', 'desc')
        ->orderBy('id', 'desc')
        ->cursorPaginate($perPage);
    }
}
