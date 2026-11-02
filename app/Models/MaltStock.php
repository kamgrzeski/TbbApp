<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaltStock extends Model
{
    protected $fillable = ['name', 'kg', 'extract', 'invoice_number', 'is_active'];

    protected $casts = [
        'extract' => 'float',
        'kg' => 'float',
    ];
}
