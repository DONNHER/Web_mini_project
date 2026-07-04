<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AISecurityLog extends Model
{
    protected $table = 'ai_security_logs';
    protected $guarded = [];
    protected $casts = [
        'input_context' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getResource()
    {
        if ($this->resource_type && $this->resource_id) {
            $modelClass = "App\\Models\\" . $this->resource_type;
            if (class_exists($modelClass)) {
                return $modelClass::find($this->resource_id);
            }
        }
        return null;
    }
}
