<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeBatches extends Model
{
    protected $fillable = [
        'recipe_id',
        'batch_number',
        'start_time',
        'end_time'
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}