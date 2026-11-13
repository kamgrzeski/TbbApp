<?php

namespace Database\Seeders;

use App\Models\SocialPost;
use Illuminate\Database\Seeder;

/**
 * Wgrywa ręcznie napisane posty browaru jako OPUBLIKOWANE przykłady stylu dla generatora.
 * Uruchomienie: php artisan db:seed --class=SocialPostSeeder
 * Idempotentne: ponowne uruchomienie nie duplikuje postów (dopasowanie po treści).
 *
 * Zasada: w przykładach są wyłącznie treści, które browar potwierdził. Nie dodawaj postów z niepotwierdzonymi faktami.
 */
class SocialPostSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->posts() as $post) {
            SocialPost::updateOrCreate(
                ['generated_content' => $post['content']],
                [
                    'topic_type' => $post['topic_type'],
                    'custom_topic' => $post['custom_topic'] ?? null,
                    'prompt_used' => '[seed] ręcznie napisany post',
                    'platform' => SocialPost::PLATFORM_ALL,
                    'status' => SocialPost::STATUS_PUBLISHED,
                    'address' => 'ul. Staszica 3A, Lublin',
                ],
            );
        }
    }

    /**
     * @return array<int, array{topic_type: string, custom_topic?: string, content: string}>
     */
    private function posts(): array
    {
        return [
            [
                'topic_type' => SocialPost::TOPIC_NEW_BEER,
                'content' => <<<'TXT'
Nowe piwo powstaje u nas, w browarze. Stout w nowej wersji jest już w warzelni, a my pilnujemy każdej warki od pierwszego zacierania.

Pełne ciało i ciemna barwa sprawiają, że to piwo na wieczór przy stole, a nie na jedno szybkie. Jeśli lubisz ciemne style, ten będzie dla Ciebie.
TXT,
            ],
        ];
    }
}
