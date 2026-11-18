<?php

namespace App\Queries;

use App\Models\KegStock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class KegStockQueries
{
    
    public static function forRecipe(int $recipeId): Builder
    {
        return KegStock::query()->where('recipe_id', $recipeId);
    }

    
    public static function topByFullKegs(int $limit = 10): Collection
    {
        return KegStock::query()
            ->with(['recipe', 'movements'])
            ->orderBy('full_kegs', 'desc')
            ->take($limit)
            ->get();
    }

    
    public static function coolerStocks(): Collection
    {
        return KegStock::query()
            ->with('recipe')
            ->where('is_archived', 0)
            ->orderByDesc('full_kegs')
            ->get();
    }

    
    public static function totalFullKegs()
    {
        return KegStock::query()->sum('full_kegs');
    }

    
    public static function anyFullKegsForRecipes(array $recipeIds): bool
    {
        return KegStock::query()
            ->whereIn('recipe_id', $recipeIds)
            ->where('full_kegs', '>', 0)
            ->exists();
    }
}
