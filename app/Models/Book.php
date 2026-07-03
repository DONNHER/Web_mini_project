<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Facades\Cache;
use Laravel\Scout\Searchable;
use App\Traits\Shardable;

class Book extends Model implements Auditable
{
    use HasFactory;
    use Searchable;
    use AuditableTrait;
    use Shardable;

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'description' => $this->description,
            'category' => $this->category?->name,
            'format' => $this->format,
        ];
    }

    /**
     * Determine if the model should be searchable.
     */
    public function shouldBeSearchable(): bool
    {
        return $this->is_active;
    }


    /**
     * Requirement 3.2.2 - Benchmarks: Search by ISBN (< 50ms)
     * Implementation: Unique Index + Redis Cache with Tags
     */
    public static function findByIsbn($isbn)
    {
        $key = "book_isbn_{$isbn}";

        $callback = function () use ($isbn) {
            return static::where('isbn', $isbn)->first();
        };

        try {
            return Cache::tags(['books', 'isbn'])->remember($key, 3600, $callback);
        } catch (\BadMethodCallException $e) {
            return Cache::remember($key, 3600, $callback);
        }
    }

    /**
     * Requirement 3.2.2 - Benchmarks: Category Filter (< 150ms)
     * Implementation: Composite Index + Redis Cache Tags
     */
    public static function getByCategoryCached($categoryId, $limit = 100)
    {
        $page = request('cursor', 'start');
        $key = "books_cat_{$categoryId}_p_{$page}";

        $callback = function () use ($categoryId, $limit) {
            return static::where('category_id', $categoryId)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->cursorPaginate($limit);
        };

        try {
            return Cache::tags(['books', "category_{$categoryId}"])->remember($key, 300, $callback);
        } catch (\BadMethodCallException $e) {
            return Cache::remember($key, 300, $callback);
        }
    }

    /**
     * Advanced Scalability: Range Partitioning Query Scope
     * Simulates selecting from partitioned table by publication_year
     */
    public function scopePublishedIn($query, $year)
    {
        return $query->where('publication_year', $year);
    }



    protected $fillable = [
        'category_id',
        'author_id',
        'publisher_id',
        'title',
        'author',
        'isbn',
        'price',
        'stock_quantity',
        'description',
        'cover_image',
        'published_at',
        'is_active',
        'publication_year',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Optimized Accessor: Uses the loaded collection to prevent N+1 queries.
     */
    public function getAverageRatingAttribute()
    {
        if ($this->relationLoaded('reviews')) {
            return $this->reviews->avg('rating') ?? 0;
        }

        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get best selling books with caching.
     */
    public static function getBestsellers($limit = 5)
    {
        return Cache::remember('bestseller_books_' . $limit, 3600, function () use ($limit) {
            return static::on('mysql')->withCount('orderItems')
                ->orderBy('order_items_count', 'desc')
                ->limit($limit)
                ->get();
        });
    }
}
