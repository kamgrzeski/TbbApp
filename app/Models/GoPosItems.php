<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoPosItems extends Model
{
    protected $table = 'gopos_items';

    protected $fillable = ['gopos_id', 'gopos_name', 'gopos_price', 'gopos_catgory_id', 'gopos_status'];
}
