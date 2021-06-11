<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;

    protected $casts = [
        'created_at' => 'datetime'   // date | datetime | timestamp
    ];

    protected $hidden = ['created_at', 'updated_at'];
    public $timestamps = false;
}
