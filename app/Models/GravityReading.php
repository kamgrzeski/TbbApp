<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GravityReading extends Model
{
    protected $fillable = ['recipe_id', 'value'];
}
