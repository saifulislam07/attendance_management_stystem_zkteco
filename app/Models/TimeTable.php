<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeTable extends Model
{
    protected $fillable = [
        'role',
        'class_id',
        'day',
        'in_time',
        'late_time',
        'out_time',
        'grace_time',
        'half_day_time',
        'overtime_start',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}
