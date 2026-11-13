<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialPost extends Model
{
    public const TOPIC_NEW_BEER = 'new_beer';
    public const TOPIC_BEHIND_SCENES = 'behind_scenes';
    public const TOPIC_WEEKEND = 'weekend';
    public const TOPIC_EDUCATION = 'education';
    public const TOPIC_CUSTOM = 'custom';

    public const TOPICS = [
        self::TOPIC_NEW_BEER => 'Premiera piwa',
        self::TOPIC_BEHIND_SCENES => 'Z życia browaru / Kulisy warzenia',
        self::TOPIC_WEEKEND => 'Weekend / Zaproszenie do browaru',
        self::TOPIC_EDUCATION => 'Edukacja piwna / O stylach',
        self::TOPIC_CUSTOM => 'Własny temat (wpisz od zera)',
    ];

    public const PLATFORM_FB = 'fb';
    public const PLATFORM_IG = 'ig';
    public const PLATFORM_ALL = 'all';

    public const PLATFORMS = [
        self::PLATFORM_FB => 'Facebook',
        self::PLATFORM_IG => 'Instagram',
        self::PLATFORM_ALL => 'Facebook + Instagram',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    public const STATUSES = [
        self::STATUS_DRAFT => 'Szkic',
        self::STATUS_PUBLISHED => 'Opublikowany',
    ];

    protected $fillable = [
        'recipe_id', 'topic_type', 'custom_topic', 'custom_guidelines', 'prompt_used',
        'generated_content', 'platform', 'status', 'event_at', 'address',
        'input_tokens', 'output_tokens', 'cost_usd',
    ];

    protected $casts = [
        'event_at' => 'datetime',
        'input_tokens' => 'integer',
        'output_tokens' => 'integer',
        'cost_usd' => 'float',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function topicLabel(): string
    {
        return self::TOPICS[$this->topic_type] ?? $this->topic_type;
    }
}
