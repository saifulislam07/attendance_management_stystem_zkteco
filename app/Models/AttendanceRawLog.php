<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRawLog extends Model
{
    protected $fillable = [
        'device_user_id',
        'punch_time',
        'device_id',
        'user_id',
        'status',
        'error',
    ];

    protected $casts = [
        'punch_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
