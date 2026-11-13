<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Posty social media') }}
            </h2>
            <a href="{{ route('social-posts.create') }}">
                <x-primary-button>
                    {{ __('Nowy post') }}
                </x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 rounded-md bg-green-50 text-green-800 dark:bg-green-900/30 dark:text-green-300">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 rounded-md bg-red-50 text-red-800 dark:bg-red-900/30 dark:text-red-300">{{ session('error') }}</div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Wygenerowane posty</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totals['posts'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tokeny wejściowe</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totals['input_tokens'], 0, ',', ' ') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tokeny wyjściowe</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totals['output_tokens'], 0, ',', ' ') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Łączny koszt</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">${{ number_format($totals['cost_usd'], 4, '.', ' ') }}</div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                <th class="px-4 py-3">Data</th>
                                <th class="px-4 py-3">Temat</th>
                                <th class="px-4 py-3">Piwo</th>
                                <th class="px-4 py-3">Platforma</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Tokeny / koszt</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-900 dark:text-gray-100">
                            @forelse ($posts as $post)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">{{ $post->created_at->format('d.m.Y H:i') }}</td>
                                    <td class="px-4 py-3">
                                        {{ $post->topicLabel() }}
                                        @if ($post->topic_type === \App\Models\SocialPost::TOPIC_CUSTOM && $post->custom_topic)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($post->custom_topic, 60) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $post->recipe?->name ?? '—' }}</td>
                                    <td class="px-4 py-3">{{ \App\Models\SocialPost::PLATFORMS[$post->platform] ?? $post->platform }}</td>
                                    <td class="px-4 py-3">
                                        @if ($post->status === \App\Models\SocialPost::STATUS_PUBLISHED)
                                            <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">Opublikowany</span>
                                        @else
                                            <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Szkic</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        @if ($post->cost_usd > 0)
                                            <div>{{ number_format($post->input_tokens + $post->output_tokens, 0, ',', ' ') }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">${{ number_format($post->cost_usd, 4, '.', ' ') }}</div>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('social-posts.show', $post) }}" class="text-indigo-600 dark:text-indigo-400 underline">Otwórz</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                        Brak wygenerowanych postów. <a href="{{ route('social-posts.create') }}" class="underline">Wygeneruj pierwszy</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
