<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiRateLimit extends Model
{
    protected $fillable = [
        'ip_address',
        'user_id',
        'endpoint',
        'hits',
        'throttled_at',
        'retry_after',
    ];

    protected $casts = [
        'throttled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
