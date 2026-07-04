<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIUsageLog extends Model
{
    protected $table = 'ai_usage_logs';

    protected $fillable = [
        'provider',
        'feature',
        'tokens_used',
        'cost_estimate',
    ];
}
