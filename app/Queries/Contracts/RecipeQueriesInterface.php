<?php

namespace App\Queries\Contracts;

use App\Models\Recipe;
use Illuminate\Support\Collection;

interface RecipeQueriesInterface
{
    public function findById(int|string $id): ?Recipe;

    public function findByIdOrFail(int|string $id): Recipe;

    public function findAllWithBatchesNewestFirst(): Collection;

    public function findActiveFinishedNewestFirst(): Collection;

    public function existsMashed(int $recipeId): bool;

    public function existsYeastPitched(int $recipeId): bool;

    public function findMaxNumberForUser(int $userId): int;
}
