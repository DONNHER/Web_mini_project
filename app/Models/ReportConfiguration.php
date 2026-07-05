<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportConfiguration extends Model
{
    protected $fillable = [
        'name',
        'report_type',
        'filters',
        'format',
        'is_favorite',
        'is_scheduled',
        'schedule_frequency',
        'recipient_email',
        'user_id',
    ];

    protected $casts = [
        'filters' => 'json',
        'is_favorite' => 'boolean',
        'is_scheduled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
