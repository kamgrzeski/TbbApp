<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\SocialPost;
use App\Services\SocialMediaGenerationException;
use App\Services\SocialMediaGeneratorService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SocialPostController extends Controller
{
    public function __construct(private SocialMediaGeneratorService $generator)
    {
    }

    public function index(): View
    {
        $posts = SocialPost::with('recipe:id,name')
            ->latest()
            ->paginate(20);

        $totals = [
            'posts' => SocialPost::count(),
            'input_tokens' => (int) SocialPost::sum('input_tokens'),
            'output_tokens' => (int) SocialPost::sum('output_tokens'),
            'cost_usd' => (float) SocialPost::sum('cost_usd'),
        ];

        return view('social-posts.index', compact('posts', 'totals'));
    }

    public function create(): View
    {
        return view('social-posts.create', [
            'recipes' => $this->recipeOptions(),
            'topics' => SocialPost::TOPICS,
            'platforms' => SocialPost::PLATFORMS,
            'defaultAddress' => config('brewery.address'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'topic_type' => ['required', Rule::in(array_keys(SocialPost::TOPICS))],
            'recipe_id' => ['nullable', 'integer', 'exists:recipes,id'],
            'custom_topic' => ['nullable', 'string', 'max:255', 'required_if:topic_type,' . SocialPost::TOPIC_CUSTOM],
            'custom_guidelines' => ['nullable', 'string', 'max:1000'],
            'platform' => ['required', Rule::in(array_keys(SocialPost::PLATFORMS))],
            'event_at' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $recipe = !empty($data['recipe_id']) ? Recipe::find($data['recipe_id']) : null;
        $eventAt = !empty($data['event_at']) ? CarbonImmutable::parse($data['event_at']) : null;

        try {
            $result = $this->generator->generate(
                topicType: $data['topic_type'],
                recipe: $recipe,
                customTopic: $data['custom_topic'] ?? null,
                guidelines: $data['custom_guidelines'] ?? null,
                platform: $data['platform'],
                eventAt: $eventAt,
                address: $data['address'] ?? null,
            );
        } catch (SocialMediaGenerationException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $post = SocialPost::create([
            'recipe_id' => $recipe?->id,
            'topic_type' => $data['topic_type'],
            'custom_topic' => $data['custom_topic'] ?? null,
            'custom_guidelines' => $data['custom_guidelines'] ?? null,
            'prompt_used' => $result['prompt'],
            'generated_content' => $result['content'],
            'platform' => $data['platform'],
            'status' => SocialPost::STATUS_DRAFT,
            'event_at' => $eventAt,
            'address' => ($data['address'] ?? null) ?: null,
            'input_tokens' => $result['usage']['input_tokens'],
            'output_tokens' => $result['usage']['output_tokens'],
            'cost_usd' => $result['usage']['cost_usd'],
        ]);

        return $this->redirectWithWarnings(
            redirect()->route('social-posts.show', $post)->with('success', 'Post wygenerowany.'),
            $result['warnings']
        );
    }

    public function show(SocialPost $socialPost): View
    {
        $socialPost->load('recipe:id,name,style');

        return view('social-posts.show', [
            'post' => $socialPost,
            'statuses' => SocialPost::STATUSES,
        ]);
    }

    public function update(Request $request, SocialPost $socialPost): RedirectResponse
    {
        $data = $request->validate([
            'generated_content' => ['required', 'string', 'max:5000'],
        ]);

        $socialPost->update(['generated_content' => $data['generated_content']]);

        return redirect()->route('social-posts.show', $socialPost)->with('success', 'Zmiany zapisane.');
    }

    /**
     * Generuje nową wersję posta z tymi samymi danymi wejściowymi. Każda wersja trafia do historii.
     */
    public function regenerate(SocialPost $socialPost): RedirectResponse
    {
        $recipe = $socialPost->recipe_id ? Recipe::find($socialPost->recipe_id) : null;

        try {
            $result = $this->generator->generate(
                topicType: $socialPost->topic_type,
                recipe: $recipe,
                customTopic: $socialPost->custom_topic,
                guidelines: $socialPost->custom_guidelines,
                platform: $socialPost->platform,
                eventAt: $socialPost->event_at ? CarbonImmutable::instance($socialPost->event_at) : null,
                address: $socialPost->address,
            );
        } catch (SocialMediaGenerationException $e) {
            return back()->with('error', $e->getMessage());
        }

        $new = SocialPost::create([
            'recipe_id' => $socialPost->recipe_id,
            'topic_type' => $socialPost->topic_type,
            'custom_topic' => $socialPost->custom_topic,
            'custom_guidelines' => $socialPost->custom_guidelines,
            'prompt_used' => $result['prompt'],
            'generated_content' => $result['content'],
            'platform' => $socialPost->platform,
            'status' => SocialPost::STATUS_DRAFT,
            'event_at' => $socialPost->event_at,
            'address' => $socialPost->address,
            'input_tokens' => $result['usage']['input_tokens'],
            'output_tokens' => $result['usage']['output_tokens'],
            'cost_usd' => $result['usage']['cost_usd'],
        ]);

        return $this->redirectWithWarnings(
            redirect()->route('social-posts.show', $new)->with('success', 'Wygenerowano nową wersję posta.'),
            $result['warnings']
        );
    }

    public function updateStatus(Request $request, SocialPost $socialPost): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(SocialPost::STATUSES))],
        ]);

        $socialPost->update(['status' => $data['status']]);

        return redirect()->route('social-posts.show', $socialPost)->with('success', 'Status zaktualizowany.');
    }

    public function destroy(SocialPost $socialPost): RedirectResponse
    {
        $socialPost->delete();

        return redirect()->route('social-posts.index')->with('success', 'Post usunięty z historii.');
    }

    /**
     * @param array<int, string> $warnings
     */
    private function redirectWithWarnings(RedirectResponse $response, array $warnings): RedirectResponse
    {
        if ($warnings === []) {
            return $response;
        }

        return $response->with(
            'warning',
            'Model użył zakazanych sformułowań (' . implode(', ', $warnings) . '), mimo poprawki. Sprawdź i popraw tekst przed publikacją.'
        );
    }

    private function recipeOptions()
    {
        return Recipe::notArchived()
            ->orderBy('name')
            ->get(['id', 'name', 'style']);
    }
}
