<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hop extends Model
{
    protected $fillable = ['recipe_id', 'batch_number', 'name', 'amount', 'alpha_acids', 'time', 'is_active'];
}
