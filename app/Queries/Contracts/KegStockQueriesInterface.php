<?php

namespace App\Queries\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface KegStockQueriesInterface
{
    public function queryForRecipe(int $recipeId): Builder;

    public function findTopByFullKegs(int $limit = 10): Collection;

    public function findNotArchivedOrderedByFullKegs(): Collection;

    public function sumFullKegs();

    public function existsFullKegsForRecipes(array $recipeIds): bool;
}
