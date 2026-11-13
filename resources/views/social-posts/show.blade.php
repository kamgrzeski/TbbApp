<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Post') }}: {{ $post->topicLabel() }}
            </h2>
            <a href="{{ route('social-posts.index') }}" class="text-gray-600 dark:text-gray-400 underline">← Historia postów</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 rounded-md bg-green-50 text-green-800 dark:bg-green-900/30 dark:text-green-300">{{ session('success') }}</div>
            @endif
            @if (session('warning'))
                <div class="mb-4 p-4 rounded-md bg-amber-50 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">{{ session('warning') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 rounded-md bg-red-50 text-red-800 dark:bg-red-900/30 dark:text-red-300">{{ session('error') }}</div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6"
                 x-data="{ text: @js($post->generated_content), copied: false, copyError: false,
                     async copy() {
                         try {
                             if (navigator.clipboard && window.isSecureContext) {
                                 await navigator.clipboard.writeText(this.text);
                             } else {
                                 this.$refs.editor.select();
                                 document.execCommand('copy');
                             }
                             this.copied = true; this.copyError = false;
                             setTimeout(() => this.copied = false, 2000);
                         } catch (e) {
                             this.copyError = true;
                         }
                     }
                 }">

                {{-- Podgląd w stylu posta FB/IG --}}
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="flex items-center gap-3 p-4 border-b border-gray-100 dark:border-gray-700">
                        <div class="w-10 h-10 rounded-full bg-amber-500 flex items-center justify-center text-white font-bold">
                            {{ mb_substr(config('brewery.name'), 0, 1) }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-gray-100">{{ config('brewery.name') }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ \App\Models\SocialPost::PLATFORMS[$post->platform] ?? $post->platform }} · podgląd
                            </div>
                        </div>
                    </div>
                    <div class="p-4 text-gray-900 dark:text-gray-100 whitespace-pre-line break-words min-h-[12rem] text-[15px] leading-relaxed"
                         x-text="text"></div>
                    <div class="px-4 py-2 text-xs text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700">
                        <span x-text="text.length"></span> znaków
                        @if ($post->platform !== \App\Models\SocialPost::PLATFORM_FB)
                            · limit podpisu na Instagramie: 2200 znaków
                        @endif
                    </div>
                </div>

                {{-- Edycja, akcje i metadane --}}
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <form method="POST" action="{{ route('social-posts.update', $post) }}" class="space-y-3">
                            @csrf
                            @method('PUT')
                            <div class="flex items-center justify-between">
                                <x-input-label for="generated_content" value="Treść posta (możesz edytować)" />
                                <span class="text-xs text-gray-500" x-show="copied" x-cloak>✓ Skopiowano</span>
                                <span class="text-xs text-red-600" x-show="copyError" x-cloak>Nie udało się skopiować</span>
                            </div>
                            <textarea id="generated_content" name="generated_content" rows="16" required maxlength="5000"
                                      x-ref="editor" x-model="text"
                                      class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm font-mono text-sm">{{ old('generated_content', $post->generated_content) }}</textarea>
                            <x-input-error :messages="$errors->get('generated_content')" class="mt-2" />

                            <div class="flex flex-wrap gap-3 justify-between pt-2">
                                <div class="flex flex-wrap gap-3">
                                    <x-primary-button type="submit">Zapisz zmiany</x-primary-button>
                                    <button type="button" @click="copy()"
                                            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:outline-none transition">
                                        <span x-show="!copied">Kopiuj do schowka</span>
                                        <span x-show="copied" x-cloak>Skopiowano ✓</span>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <form method="POST" action="{{ route('social-posts.regenerate', $post) }}"
                                  onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').innerText = 'Generowanie… (to może chwilę potrwać)';">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-60 transition">
                                    Generuj ponownie
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6 space-y-3 text-sm text-gray-700 dark:text-gray-300">
                        <div><span class="font-semibold">Piwo:</span> {{ $post->recipe?->name ?? '—' }}{{ $post->recipe?->style ? ' ('.$post->recipe->style.')' : '' }}</div>
                        @if ($post->topic_type === \App\Models\SocialPost::TOPIC_CUSTOM && $post->custom_topic)
                            <div><span class="font-semibold">Temat:</span> {{ $post->custom_topic }}</div>
                        @endif
                        @if ($post->custom_guidelines)
                            <div><span class="font-semibold">Wytyczne:</span> {{ $post->custom_guidelines }}</div>
                        @endif
                        <div><span class="font-semibold">Adres:</span> {{ $post->address ?: config('brewery.address') }}</div>
                        <div><span class="font-semibold">Termin:</span> {{ $post->event_at ? $post->event_at->format('d.m.Y H:i') : '—' }}</div>
                        <div><span class="font-semibold">Wygenerowano:</span> {{ $post->created_at->format('d.m.Y H:i') }}</div>
                        <div>
                            <span class="font-semibold">Zużycie:</span>
                            @if ($post->cost_usd > 0)
                                {{ number_format($post->input_tokens, 0, ',', ' ') }} tokenów wejściowych,
                                {{ number_format($post->output_tokens, 0, ',', ' ') }} wyjściowych,
                                koszt ${{ number_format($post->cost_usd, 4, '.', ' ') }}
                            @else
                                brak danych (post wygenerowany przed włączeniem licznika)
                            @endif
                        </div>

                        <details class="pt-2">
                            <summary class="cursor-pointer text-gray-500 dark:text-gray-400">Pokaż użyty prompt</summary>
                            <pre class="mt-2 p-3 bg-gray-50 dark:bg-gray-900 rounded text-xs whitespace-pre-wrap">{{ $post->prompt_used }}</pre>
                        </details>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <form method="POST" action="{{ route('social-posts.status', $post) }}" class="flex flex-wrap items-end gap-3">
                            @csrf
                            @method('PATCH')
                            <div>
                                <x-input-label for="status" value="Status" />
                                <select id="status" name="status"
                                        class="mt-1 block border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                    @foreach ($statuses as $key => $label)
                                        <option value="{{ $key }}" @selected($post->status === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <x-secondary-button type="submit">Zapisz status</x-secondary-button>
                        </form>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Publikacja odbywa się ręcznie w aplikacji Facebook / Instagram. Status służy do prowadzenia historii.</p>

                        <form method="POST" action="{{ route('social-posts.destroy', $post) }}" class="mt-4"
                              onsubmit="return confirm('Usunąć ten post z historii?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 dark:text-red-400 underline">Usuń z historii</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
