<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'number', 'name', 'tank_number', 'volume', 'efficiency', 'user_id', 'yeast_pitched_at',
        'primary_fermentation_start', 'secondary_fermentation_start', 'finished_at'
    ];

    protected $casts = [
        'yeast_pitched_at' => 'datetime',
        'primary_fermentation_start' => 'datetime',
        'secondary_fermentation_start' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function malts()
    {
        return $this->hasMany(Malt::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function gravityReadings() {
        return $this->hasMany(GravityReading::class)->latest();
    }

    public function hops() {
        return $this->hasMany(Hop::class);
    }
}
