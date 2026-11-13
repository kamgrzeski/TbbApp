<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\SocialPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SocialPostTest extends TestCase
{
    use RefreshDatabase;

    private const GENERATED = "LUBLIN! WARZYMY HEFEWEIZENA 2.0! 🌾🍌🔥\n\nPytaliście, kiedy znowu pszenica.\n\n📍 ul. Staszica 3A, Lublin\n\n#Lublin #piwo";

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.anthropic.key' => 'test-key',
            'services.anthropic.model' => 'claude-sonnet-5-5',
            'services.anthropic.base_url' => 'https://api.anthropic.com',
        ]);
    }

    private function fakeApiOk(): void
    {
        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response([
                'content' => [['type' => 'text', 'text' => self::GENERATED]],
            ], 200),
        ]);
    }

    private function makeUser(): User
    {
        // Brak fakera w projekcie – tworzymy użytkownika bez factory
        return User::forceCreate([
            'name' => 'Tester',
            'email' => uniqid('tester', true) . '@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    private function makeRecipe(): Recipe
    {
        $recipe = Recipe::create([
            'user_id' => $this->makeUser()->id,
            'number' => 1,
            'name' => 'Hefeweizen 2.0',
            'style' => 'Hefeweizen',
            'tank_number' => 2,
            'batch_count' => 2,
            'volume' => 1000,
            'efficiency' => 75,
            'blg' => 12.5,
        ]);

        $recipe->hops()->create([
            'name' => 'Mosaic',
            'amount' => 50,
            'alpha_acids' => 11.5,
            'time' => 15,
            'is_active' => true,
        ]);

        return $recipe;
    }

    public function test_guest_cannot_access_social_posts(): void
    {
        $this->get(route('social-posts.index'))->assertRedirect(route('login'));
    }

    public function test_creator_page_is_displayed(): void
    {
        $this->actingAs($this->makeUser())
            ->get(route('social-posts.create'))
            ->assertOk()
            ->assertSee('Typ tematu');
    }

    public function test_index_and_show_pages_render_stored_post(): void
    {
        $user = $this->makeUser();
        $post = SocialPost::create([
            'topic_type' => SocialPost::TOPIC_CUSTOM,
            'custom_topic' => 'Test tematu',
            'prompt_used' => 'p',
            'generated_content' => 'TREŚĆ-TESTOWA',
            'platform' => SocialPost::PLATFORM_FB,
            'status' => SocialPost::STATUS_DRAFT,
        ]);

        $this->actingAs($user)->get(route('social-posts.index'))->assertOk()->assertSee('Test tematu');
        $this->actingAs($user)->get(route('social-posts.show', $post))->assertOk()->assertSee('TREŚĆ-TESTOWA');
    }

    public function test_recipe_form_contains_style_field(): void
    {
        $this->actingAs($this->makeUser());

        \Livewire\Livewire::test(\App\Livewire\RecipeForm::class)
            ->assertSee('Styl');
    }

    public function test_generates_and_stores_post_for_recipe(): void
    {
        $this->fakeApiOk();
        $recipe = $this->makeRecipe();

        $response = $this->actingAs($this->makeUser())->post(route('social-posts.store'), [
            'topic_type' => SocialPost::TOPIC_NEW_BEER,
            'recipe_id' => $recipe->id,
            'custom_guidelines' => 'podkreśl mega aromat mango',
            'platform' => SocialPost::PLATFORM_IG,
            'address' => 'ul. Staszica 3A, Lublin',
        ]);

        $post = SocialPost::firstOrFail();
        $response->assertRedirect(route('social-posts.show', $post));

        $this->assertSame(self::GENERATED, $post->generated_content);
        $this->assertSame($recipe->id, $post->recipe_id);
        $this->assertSame(SocialPost::STATUS_DRAFT, $post->status);
        $this->assertSame(SocialPost::PLATFORM_IG, $post->platform);

        // Prompt do historii zawiera dane receptury i wytyczne
        $this->assertStringContainsString('Hefeweizen', $post->prompt_used);
        $this->assertStringContainsString('Mosaic', $post->prompt_used);
        $this->assertStringContainsString('podkreśl mega aromat mango', $post->prompt_used);

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://api.anthropic.com/v1/messages'
                && $request->hasHeader('x-api-key', 'test-key')
                && $request->hasHeader('anthropic-version', '2023-06-01')
                && $request['model'] === 'claude-sonnet-5-5';
        });
    }

    public function test_missing_api_key_shows_flash_error_and_stores_nothing(): void
    {
        config(['services.anthropic.key' => null]);
        Http::fake();

        $response = $this->actingAs($this->makeUser())->post(route('social-posts.store'), [
            'topic_type' => SocialPost::TOPIC_WEEKEND,
            'platform' => SocialPost::PLATFORM_ALL,
        ]);

        $response->assertSessionHas('error');
        $this->assertSame(0, SocialPost::count());
        Http::assertNothingSent();
    }

    public function test_api_rate_limit_shows_readable_error(): void
    {
        Http::fake(['api.anthropic.com/*' => Http::response(['error' => ['message' => 'rate']], 429)]);

        $response = $this->actingAs($this->makeUser())->post(route('social-posts.store'), [
            'topic_type' => SocialPost::TOPIC_EDUCATION,
            'platform' => SocialPost::PLATFORM_FB,
        ]);

        $response->assertSessionHas('error', 'Przekroczono limit zapytań do API Anthropic. Odczekaj chwilę i spróbuj ponownie.');
        $this->assertSame(0, SocialPost::count());
    }

    public function test_timeout_shows_readable_error(): void
    {
        Http::fake(fn () => throw new ConnectionException('cURL error 28: Operation timed out after 60000 milliseconds'));

        $response = $this->actingAs($this->makeUser())->post(route('social-posts.store'), [
            'topic_type' => SocialPost::TOPIC_BEHIND_SCENES,
            'platform' => SocialPost::PLATFORM_ALL,
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('timeout', session('error'));
        $this->assertSame(0, SocialPost::count());
    }

    public function test_custom_topic_requires_text(): void
    {
        $response = $this->actingAs($this->makeUser())->post(route('social-posts.store'), [
            'topic_type' => SocialPost::TOPIC_CUSTOM,
            'platform' => SocialPost::PLATFORM_ALL,
        ]);

        $response->assertSessionHasErrors('custom_topic');
    }

    public function test_recipe_is_optional_for_any_topic(): void
    {
        $this->fakeApiOk();

        $this->actingAs($this->makeUser())->post(route('social-posts.store'), [
            'topic_type' => SocialPost::TOPIC_NEW_BEER,
            'platform' => SocialPost::PLATFORM_ALL,
            'custom_guidelines' => 'dzisiaj 11 listopada, browar działa normalnie',
        ])->assertRedirect();

        $post = SocialPost::firstOrFail();
        $this->assertNull($post->recipe_id);
        $this->assertStringContainsString('nie podano konkretnego piwa', $post->prompt_used);
    }

    public function test_edited_content_is_saved(): void
    {
        $post = SocialPost::create([
            'topic_type' => SocialPost::TOPIC_WEEKEND,
            'prompt_used' => 'p',
            'generated_content' => 'stara treść',
            'platform' => SocialPost::PLATFORM_ALL,
            'status' => SocialPost::STATUS_DRAFT,
        ]);

        $this->actingAs($this->makeUser())
            ->put(route('social-posts.update', $post), ['generated_content' => 'nowa treść'])
            ->assertRedirect(route('social-posts.show', $post));

        $this->assertSame('nowa treść', $post->fresh()->generated_content);
    }

    public function test_regenerate_creates_new_history_entry(): void
    {
        $this->fakeApiOk();
        $recipe = $this->makeRecipe();

        $original = SocialPost::create([
            'recipe_id' => $recipe->id,
            'topic_type' => SocialPost::TOPIC_NEW_BEER,
            'prompt_used' => 'p',
            'generated_content' => 'stara wersja',
            'platform' => SocialPost::PLATFORM_ALL,
            'status' => SocialPost::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($this->makeUser())
            ->post(route('social-posts.regenerate', $original));

        $this->assertSame(2, SocialPost::count());
        $new = SocialPost::where('id', '!=', $original->id)->firstOrFail();
        $response->assertRedirect(route('social-posts.show', $new));
        $this->assertSame(self::GENERATED, $new->generated_content);
        $this->assertSame('stara wersja', $original->fresh()->generated_content);
    }

    public function test_status_can_be_marked_published_and_post_deleted(): void
    {
        $user = $this->makeUser();
        $post = SocialPost::create([
            'topic_type' => SocialPost::TOPIC_EDUCATION,
            'prompt_used' => 'p',
            'generated_content' => 'treść',
            'platform' => SocialPost::PLATFORM_FB,
            'status' => SocialPost::STATUS_DRAFT,
        ]);

        $this->actingAs($user)
            ->patch(route('social-posts.status', $post), ['status' => SocialPost::STATUS_PUBLISHED])
            ->assertRedirect();
        $this->assertSame(SocialPost::STATUS_PUBLISHED, $post->fresh()->status);

        $this->actingAs($user)
            ->delete(route('social-posts.destroy', $post))
            ->assertRedirect(route('social-posts.index'));
        $this->assertSame(0, SocialPost::count());
    }

    public function test_forbidden_terms_are_detected_and_clean_text_passes(): void
    {
        $generator = app(\App\Services\SocialMediaGeneratorService::class);

        $this->assertNotEmpty($generator->violations('Zapraszamy serdecznie na piwo.'));
        $this->assertNotEmpty($generator->violations('Przekonaj się, jak smakuje.'));
        // Tanki i pasteryzacja to niepotwierdzone fakty o browarze – nie wolno ich pisać.
        $this->assertNotEmpty($generator->violations('Piwo leżakuje w tankach, a w sobotę trafia do kufli.'));
        $this->assertNotEmpty($generator->violations('Piwo niepasteryzowane, prosto z tanku.'));
        $this->assertSame([], $generator->violations('Piwo leżakuje, a w sobotę trafia do kufli.'));
        // Stopka z godziną jest dozwolona.
        $this->assertSame([], $generator->violations("📍 ul. Staszica 3A\n🕓 Zapraszamy od 16:00"));
    }

    public function test_published_posts_with_forbidden_terms_are_not_used_as_style_examples(): void
    {
        config(['brewery.style_examples_count' => 5]);
        $this->fakeApiOk();

        SocialPost::create([
            'topic_type' => SocialPost::TOPIC_EDUCATION,
            'prompt_used' => 'p',
            'generated_content' => 'ZŁY PRZYKŁAD: jesteśmy mega, klawo!',
            'platform' => SocialPost::PLATFORM_FB,
            'status' => SocialPost::STATUS_PUBLISHED,
        ]);
        SocialPost::create([
            'topic_type' => SocialPost::TOPIC_EDUCATION,
            'prompt_used' => 'p',
            'generated_content' => 'DOBRY PRZYKŁAD: piwo leżakuje w tankach.',
            'platform' => SocialPost::PLATFORM_FB,
            'status' => SocialPost::STATUS_PUBLISHED,
        ]);

        app(\App\Services\SocialMediaGeneratorService::class)->generate(SocialPost::TOPIC_EDUCATION);

        Http::assertSent(function (Request $request) {
            $system = (string) $request['system'];

            return str_contains($system, 'DOBRY PRZYKŁAD') && !str_contains($system, 'ZŁY PRZYKŁAD');
        });
    }

    public function test_final_pass_repairs_forbidden_terms_introduced_by_review(): void
    {
        Http::fake([
            'api.anthropic.com/v1/messages' => Http::sequence()
                // 1. Generowanie – czyste.
                ->push(['content' => [['type' => 'text', 'text' => 'Pierwsza wersja posta.']]], 200)
                // 2. Ocena i redakcja – wprowadza zakazany zwrot.
                ->push(['content' => [['type' => 'text', 'text' => 'Przekonaj się, jak smakuje.']]], 200)
                // 3. Poprawka końcowa – czysty tekst.
                ->push(['content' => [['type' => 'text', 'text' => 'Wersja końcowa czysta.']]], 200),
        ]);

        $result = app(\App\Services\SocialMediaGeneratorService::class)->generate(SocialPost::TOPIC_EDUCATION);

        $this->assertSame('Wersja końcowa czysta.', $result['content']);
        $this->assertSame([], $result['warnings']);
        Http::assertSentCount(3);
    }
}
