<?php

namespace App\Queries;

use App\Models\Recipe;
use App\Queries\Contracts\RecipeQueriesInterface;
use Illuminate\Database\Eloquent\Collection;

class RecipeQueries implements RecipeQueriesInterface
{
    public function findById(int|string $id): ?Recipe
    {
        return Recipe::find($id);
    }

    public function findByIdOrFail(int|string $id): Recipe
    {
        return Recipe::findOrFail($id);
    }

    public function findAllWithBatchesNewestFirst(): Collection
    {
        return Recipe::with('batches')
            ->orderByDesc('recipes.id')
            ->get();
    }

    public function findActiveFinishedNewestFirst(): Collection
    {
        return Recipe::query()
            ->notArchived()
            ->whereNotNull('finished_at')
            ->orderByDesc('finished_at')
            ->get();
    }

    public function existsMashed(int $recipeId): bool
    {
        return Recipe::whereKey($recipeId)->whereNotNull('mashed_at')->exists();
    }

    public function existsYeastPitched(int $recipeId): bool
    {
        return Recipe::whereKey($recipeId)->whereNotNull('yeast_pitched_at')->exists();
    }

    public function findMaxNumberForUser(int $userId): int
    {
        return (int) (Recipe::where('user_id', $userId)->max('number') ?? 0);
    }
}
