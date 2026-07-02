<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoFactorSecret extends Model
{
    use HasFactory;

    protected $table = 'two_factor_secrets';

    protected $fillable = [
        'user_id',
        'secret',
        'recovery_codes',
        'enabled'
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'recovery_codes' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}