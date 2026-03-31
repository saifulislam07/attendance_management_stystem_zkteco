<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
    ];

    public function sections()
    {
        return $this->hasMany(Section::class, 'class_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'class_id');
    }
}
