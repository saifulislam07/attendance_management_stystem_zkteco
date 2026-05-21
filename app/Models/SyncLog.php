<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    protected $fillable = [
        'device_id',
        'last_sync_time',
        'total_records',
        'processed_records',
        'duplicate_records',
        'failed_records',
        'errors',
    ];

    protected $casts = [
        'last_sync_time' => 'datetime',
    ];
}
