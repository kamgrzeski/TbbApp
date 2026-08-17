<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Malt extends Model
{
    protected $fillable = ['recipe_id', 'name', 'kg', 'extract', 'is_active', 'batch_number'];

}
