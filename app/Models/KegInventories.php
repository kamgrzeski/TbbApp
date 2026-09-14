<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegInventories extends Model
{
    protected $fillable = [
        'total_kegs',
        'empty_kegs',
    ];

    protected $casts = [
        'total_kegs' => 'integer',
        'empty_kegs' => 'integer',
    ];
}