<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Facades\Cache;

class Book extends Model implements Auditable
{
    use HasFactory;
    use AuditableTrait;

    protected $fillable = [
        'category_id',
        'title',
        'author',
        'isbn',
        'price',
        'stock_quantity',
        'description',
        'cover_image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
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
            return static::withCount('orderItems')
                ->orderBy('order_items_count', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('bestseller_books_5');
            Cache::forget('bestseller_books_8');
        });

        static::deleted(function () {
            Cache::forget('bestseller_books_5');
            Cache::forget('bestseller_books_8');
        });
    }
}
