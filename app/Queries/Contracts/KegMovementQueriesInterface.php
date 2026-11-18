<?php

namespace App\Queries\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface KegMovementQueriesInterface
{
    public function queryCurrentIssues(): Builder;

    public function queryCurrentIssuesOfRecipeOnOtherTap(int $recipeId, int $tapNumber): Builder;

    public function queryCurrentIssuesOnTap(int $tapNumber): Builder;

    public function queryCurrent(): Builder;

    public function findCurrentIssuesWithRecipeAndStock(): Collection;

    public function findCurrentTapConnections(): Collection;

    public function paginateWithRecipe(int $perPage = 30): LengthAwarePaginator;
}
