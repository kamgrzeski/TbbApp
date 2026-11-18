<?php

namespace App\Queries;

use App\Models\KegMovement;
use App\Queries\Contracts\KegMovementQueriesInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class KegMovementQueries implements KegMovementQueriesInterface
{
    public function queryCurrentIssues(): Builder
    {
        return KegMovement::query()
            ->where('type', 'issue')
            ->where('is_current', true);
    }

    public function queryCurrentIssuesOfRecipeOnOtherTap(int $recipeId, int $tapNumber): Builder
    {
        return $this->queryCurrentIssues()
            ->where('recipe_id', $recipeId)
            ->where('tap_number', '!=', $tapNumber);
    }

    public function queryCurrentIssuesOnTap(int $tapNumber): Builder
    {
        return $this->queryCurrentIssues()->where('tap_number', $tapNumber);
    }

    public function queryCurrent(): Builder
    {
        return KegMovement::query()->where('is_current', 1);
    }

    public function findCurrentIssuesWithRecipeAndStock(): Collection
    {
        return KegMovement::with(['recipe', 'stock'])
            ->where('is_current', 1)
            ->where('type', 'issue')
            ->orderByDesc('id')
            ->get();
    }

    public function findCurrentTapConnections(): Collection
    {
        return $this->queryCurrentIssues()
            ->with('recipe')
            ->orderByDesc('id')
            ->get();
    }

    public function paginateWithRecipe(int $perPage = 30): LengthAwarePaginator
    {
        return KegMovement::query()
            ->with('recipe')
            ->latest()
            ->paginate($perPage);
    }
}
