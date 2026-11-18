<?php

namespace App\Queries;

use App\Models\SocialPost;
use App\Queries\Contracts\SocialPostQueriesInterface;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SocialPostQueries implements SocialPostQueriesInterface
{
    public function paginateWithRecipeNewestFirst(int $perPage = 20): LengthAwarePaginator
    {
        return SocialPost::with('recipe:id,name')
            ->latest()
            ->paginate($perPage);
    }

    public function findUsageTotals(): array
    {
        return [
            'posts' => SocialPost::count(),
            'input_tokens' => (int) SocialPost::sum('input_tokens'),
            'output_tokens' => (int) SocialPost::sum('output_tokens'),
            'cost_usd' => (float) SocialPost::sum('cost_usd'),
        ];
    }

    public function findRecentPublishedContent(int $limit): Collection
    {
        return SocialPost::query()
            ->where('status', SocialPost::STATUS_PUBLISHED)
            ->whereNotNull('generated_content')
            ->latest('id')
            ->limit($limit)
            ->pluck('generated_content');
    }
}
