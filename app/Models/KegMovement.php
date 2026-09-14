<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KegMovement extends Model
{
    protected $fillable = [
        'recipe_id',
        'keg_stock_id',
        'type',
        'quantity',
        'note',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class)->orderByDesc('id');
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(KegStock::class, 'keg_stock_id')->orderByDesc('id');
    }
}