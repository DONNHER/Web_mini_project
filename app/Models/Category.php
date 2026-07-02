<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Cache;

class Category extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['name', 'description'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    /**
     * Get categories from cache or database.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCached()
    {
        return Cache::remember('all_categories', 3600, function () {
            return static::all();
        });
    }

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('all_categories');
        });

        static::deleted(function () {
            Cache::forget('all_categories');
        });
    }
}
