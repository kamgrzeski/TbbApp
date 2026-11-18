<?php

namespace App\Queries;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\Collection;

class RecipeQueries
{
    public static function find(int|string $id): ?Recipe
    {
        return Recipe::find($id);
    }

    public static function findOrFail(int|string $id): Recipe
    {
        return Recipe::findOrFail($id);
    }


    public static function listing(): Collection
    {
        return Recipe::with('batches')
            ->orderByDesc('recipes.id')
            ->get();
    }


    public static function brewedAndActive(): Collection
    {
        return Recipe::query()
            ->notArchived()
            ->whereNotNull('finished_at')
            ->orderByDesc('finished_at')
            ->get();
    }


    public static function isMashed(int $recipeId): bool
    {
        return Recipe::whereKey($recipeId)->whereNotNull('mashed_at')->exists();
    }


    public static function isYeastPitched(int $recipeId): bool
    {
        return Recipe::whereKey($recipeId)->whereNotNull('yeast_pitched_at')->exists();
    }

    public static function maxNumberForUser(int $userId): int
    {
        return (int) (Recipe::where('user_id', $userId)->max('number') ?? 0);
    }
}
