<?php

namespace App\Queries;

use App\Models\KegMovement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class KegMovementQueries
{

    public static function currentIssues(): Builder
    {
        return KegMovement::query()
            ->where('type', 'issue')
            ->where('is_current', true);
    }


    public static function currentIssuesForRecipeOnOtherTap(int $recipeId, int $tapNumber): Builder
    {
        return self::currentIssues()
            ->where('recipe_id', $recipeId)
            ->where('tap_number', '!=', $tapNumber);
    }


    public static function currentIssuesOnTap(int $tapNumber): Builder
    {
        return self::currentIssues()->where('tap_number', $tapNumber);
    }


    public static function isCurrent(): Builder
    {
        return KegMovement::query()->where('is_current', 1);
    }


    public static function currentIssuesWithRecipeAndStock(): Collection
    {
        return KegMovement::with(['recipe', 'stock'])
            ->where('is_current', 1)
            ->where('type', 'issue')
            ->orderByDesc('id')
            ->get();
    }


    public static function currentTapConnections(): Collection
    {
        return self::currentIssues()
            ->with('recipe')
            ->orderByDesc('id')
            ->get();
    }


    public static function paginatedWithRecipe(int $perPage = 30): LengthAwarePaginator
    {
        return KegMovement::query()
            ->with('recipe')
            ->latest()
            ->paginate($perPage);
    }
}
