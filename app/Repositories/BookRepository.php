<?php

namespace App\Repositories;

use App\Models\Book;
use Illuminate\Support\Facades\Cache;

class BookRepository
{
    /**
     * Get optimized active books catalog.
     *
     * Strategy:
     * - Column Selection: Reduced I/O bandwidth.
     * - Relation Limiting: Eager-load only necessary columns.
     * - Cursor Pagination: O(1) performance for large datasets.
     * - Cache Tagging: Redis tag-based caching for targeted invalidation.
     */
    public function getActiveCatalog($perPage = 100)
    {
        $cursor = request('cursor', 'start');
        $cacheKey = "catalog_active_p_{$cursor}";

        $callback = function () use ($perPage) {
            return Book::select([
                'id',
                'isbn',
                'title',
                'author',
                'price',
                'stock_quantity',
                'published_at',
                'category_id',
                'created_at',
                'updated_at'
            ])
            ->with(['category:id,name'])
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc') // Secondary sort for stable pagination
            ->cursorPaginate($perPage);
        };

        try {
            return Cache::tags(['books', 'catalog'])->remember($cacheKey, 3600, $callback);
        } catch (\BadMethodCallException $e) {
            return Cache::remember($cacheKey, 3600, $callback);
        }
    }

    /**
     * Search books by ISBN (Optimized for < 50ms)
     */
    public function findByIsbn($isbn)
    {
        $callback = function () use ($isbn) {
            return Book::select([
                'id', 'isbn', 'title', 'author', 'price',
                'category_id', 'published_at', 'created_at', 'updated_at'
            ])
            ->with(['category:id,name'])
            ->where('isbn', $isbn)
            ->first();
        };

        try {
            return Cache::tags(['books', 'isbn'])->remember("book_isbn_{$isbn}", 3600, $callback);
        } catch (\BadMethodCallException $e) {
            return Cache::remember("book_isbn_{$isbn}", 3600, $callback);
        }
    }

    /**
     * Filter by Category (Optimized for < 150ms)
     */
    public function getByCategory($categoryId, $perPage = 100)
    {
        $cursor = request('cursor', 'start');
        $cacheKey = "catalog_cat_{$categoryId}_p_{$cursor}";

        $callback = function () use ($categoryId, $perPage) {
            return Book::select([
                'id', 'isbn', 'title', 'author', 'price',
                'stock_quantity', 'published_at', 'category_id',
                'created_at', 'updated_at'
            ])
            ->with(['category:id,name'])
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc')
            ->cursorPaginate($perPage);
        };

        try {
            return Cache::tags(['books', "category_{$categoryId}"])->remember($cacheKey, 3600, $callback);
        } catch (\BadMethodCallException $e) {
            return Cache::remember($cacheKey, 3600, $callback);
        }
    }

    /**
     * Search books by Title or Author (Optimized)
     */
    public function search($term, $perPage = 100)
    {
        return Book::select([
            'id', 'isbn', 'title', 'author', 'price',
            'stock_quantity', 'published_at', 'category_id',
            'created_at', 'updated_at'
        ])
        ->with(['category:id,name'])
        ->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('author', 'like', "%{$term}%");
        })
        ->where('is_active', true)
        ->orderBy('published_at', 'desc')
        ->orderBy('id', 'desc')
        ->cursorPaginate($perPage);
    }
}
