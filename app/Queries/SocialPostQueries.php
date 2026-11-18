<?php

namespace App\Queries;

use App\Models\SocialPost;
use Illuminate\Pagination\LengthAwarePaginator;

class SocialPostQueries
{
    
    public static function paginatedWithRecipe(int $perPage = 20): LengthAwarePaginator
    {
        return SocialPost::with('recipe:id,name')
            ->latest()
            ->paginate($perPage);
    }

    
    public static function totals(): array
    {
        return [
            'posts' => SocialPost::count(),
            'input_tokens' => (int) SocialPost::sum('input_tokens'),
            'output_tokens' => (int) SocialPost::sum('output_tokens'),
            'cost_usd' => (float) SocialPost::sum('cost_usd'),
        ];
    }

    
    public static function publishedContentSamples(int $limit): \Illuminate\Support\Collection
    {
        return SocialPost::query()
            ->where('status', SocialPost::STATUS_PUBLISHED)
            ->whereNotNull('generated_content')
            ->latest('id')
            ->limit($limit)
            ->pluck('generated_content');
    }
}
