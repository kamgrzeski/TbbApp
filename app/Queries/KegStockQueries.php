<?php

namespace App\Queries;

use App\Models\KegStock;
use App\Queries\Contracts\KegStockQueriesInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class KegStockQueries implements KegStockQueriesInterface
{
    public function queryForRecipe(int $recipeId): Builder
    {
        return KegStock::query()->where('recipe_id', $recipeId);
    }

    public function findTopByFullKegs(int $limit = 10): Collection
    {
        return KegStock::query()
            ->with(['recipe', 'movements'])
            ->orderBy('full_kegs', 'desc')
            ->take($limit)
            ->get();
    }

    public function findNotArchivedOrderedByFullKegs(): Collection
    {
        return KegStock::query()
            ->with('recipe')
            ->where('is_archived', 0)
            ->orderByDesc('full_kegs')
            ->get();
    }

    public function sumFullKegs()
    {
        return KegStock::query()->sum('full_kegs');
    }

    public function existsFullKegsForRecipes(array $recipeIds): bool
    {
        return KegStock::query()
            ->whereIn('recipe_id', $recipeIds)
            ->where('full_kegs', '>', 0)
            ->exists();
    }
}
