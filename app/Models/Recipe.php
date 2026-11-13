<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    protected $fillable = [
        'number', 'name', 'style', 'gopos_item_ids', 'tank_number', 'volume', 'batch_count', 'efficiency', 'blg', 'akcpa_value', 'akcpa_blg', 'user_id', 'mashed_at', 'yeast_pitched_at',
        'yeast_pitch_temperature', 'fermentation_temperature', 'primary_fermentation_start', 'secondary_fermentation_start', 'finished_at', 'is_archived', 'created_at'
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'mashed_at' => 'datetime',
        'yeast_pitched_at' => 'datetime',
        'primary_fermentation_start' => 'datetime',
        'secondary_fermentation_start' => 'datetime',
        'finished_at' => 'datetime',
        'gopos_item_ids' => 'array'
    ];

    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', false);
    }

    public function malts(): HasMany
    {
        return $this->hasMany(RecipeMalt::class);
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
