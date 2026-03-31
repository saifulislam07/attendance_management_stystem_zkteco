<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'port',
        'location',
        'status',
        'last_online_at',
    ];

    protected $casts = [
        'status' => 'boolean',
        'last_online_at' => 'datetime',
    ];
}
