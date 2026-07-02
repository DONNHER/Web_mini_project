<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ImportExportLog extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'type',
        'file_name',
        'status',
        'total_rows',
        'processed_rows',
        'errors',
        'user_id',
    ];

    protected $casts = [
        'errors' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
