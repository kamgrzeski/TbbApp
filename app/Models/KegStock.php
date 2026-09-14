<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KegStock extends Model
{
    protected $fillable = [
        'recipe_id',
        'full_kegs',
        'empty_kegs',
        'is_archived'
    ];

    protected $casts = [
        'full_kegs' => 'integer',
        'empty_kegs' => 'integer',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(KegMovement::class);
    }
}