<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupMonitoring extends Model
{
    protected $table = 'backup_monitoring';

    protected $fillable = [
        'backup_name',
        'status',
        'file_size',
        'destination',
        'verified_at',
        'error_message',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];
}
