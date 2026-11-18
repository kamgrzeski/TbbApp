<?php

namespace App\Queries\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface SocialPostQueriesInterface
{
    public function paginateWithRecipeNewestFirst(int $perPage = 20): LengthAwarePaginator;

    public function findUsageTotals(): array;

    public function findRecentPublishedContent(int $limit): Collection;
}
