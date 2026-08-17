<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Beerwall extends Model
{
    use SoftDeletes;

    protected $table = 'beerwall';

    protected $fillable = [
        'beer_name',
        'beer_style',
        'beer_description',
        'beer_blg',
        'beer_alc',
        'beer_price_small',
        'beer_price_medium',
        'beer_price_large',
        'is_ended',
        'is_coming_soon',
        'is_premiere',
        'position',
    ];
}
