<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    protected $fillable = [
        'number', 'name', 'tank_number', 'volume', 'batch_count', 'efficiency', 'blg', 'user_id', 'yeast_pitched_at',
        'yeast_pitch_temperature', 'fermentation_temperature', 'primary_fermentation_start', 'secondary_fermentation_start', 'finished_at'
    ];

    protected $casts = [
        'yeast_pitched_at' => 'datetime',
        'primary_fermentation_start' => 'datetime',
        'secondary_fermentation_start' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function malts(): HasMany
    {
        return $this->hasMany(Malt::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function gravityReadings()
    {
        return $this->hasMany(GravityReading::class)->latest();
    }

    public function hops() : HasMany
    {
        return $this->hasMany(Hop::class);
    }

    public function kegStock(): HasOne
    {
        return $this->hasOne(KegStock::class);
    }

    public function kegMovements(): HasMany
    {
        return $this->hasMany(KegMovement::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(RecipeBatches::class);
    }
}
