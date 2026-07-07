<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Facades\Cache;
use Laravel\Scout\Searchable;
use App\Traits\Shardable;
use App\Traits\OptimisticLocking;

class LoanProduct extends Model implements Auditable
{
    use HasFactory;
    use Searchable;
    use AuditableTrait;
    use Shardable;
    use SoftDeletes;
    use OptimisticLocking;

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }

    /**
     * Determine if the model should be searchable.
     */
    public function shouldBeSearchable(): bool
    {
        return (bool) $this->is_active;
    }

    protected $fillable = [
        'name',
        'description',
        'interest_rate',
        'duration_months',
        'min_amount',
        'max_amount',
        'is_active',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class, 'loan_product_id');
    }

    /**
     * Optimized Accessor: Get active loan products with caching.
     */
    public static function getActiveProducts()
    {
        return Cache::remember('active_loan_products', 3600, function () {
            return static::where('is_active', true)->get();
        });
    }
}
