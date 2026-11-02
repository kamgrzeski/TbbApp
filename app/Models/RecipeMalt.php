<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeMalt extends Model
{
    protected $table = 'recipe_malts'; 
    protected $fillable = ['recipe_id', 'malt_stock_id', 'name', 'kg', 'batch_number'];

    protected $casts = [
        'kg' => 'float',
    ];

    public function maltStock(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MaltStock::class, 'malt_stock_id');
    }
}
