<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nowy post social media') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 p-4 rounded-md bg-red-50 text-red-800 dark:bg-red-900/30 dark:text-red-300">{{ session('error') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100"
                     x-data="{ topic: '{{ old('topic_type', \App\Models\SocialPost::TOPIC_NEW_BEER) }}', submitting: false }">
                    <form method="POST" action="{{ route('social-posts.store') }}" @submit="submitting = true" class="space-y-5">
                        @csrf

                        <div>
                            <x-input-label for="topic_type" value="Typ tematu" />
                            <select id="topic_type" name="topic_type" x-model="topic"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                @foreach ($topics as $key => $label)
                                    <option value="{{ $key }}" @selected(old('topic_type', \App\Models\SocialPost::TOPIC_NEW_BEER) === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('topic_type')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="recipe_id" value="Receptura / piwo (opcjonalnie)" />
                            <select id="recipe_id" name="recipe_id"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                <option value="">— wybierz piwo —</option>
                                @foreach ($recipes as $recipe)
                                    <option value="{{ $recipe->id }}" @selected((string) old('recipe_id') === (string) $recipe->id)>
                                        {{ $recipe->name }}{{ $recipe->style ? ' (' . $recipe->style . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('recipe_id')" class="mt-2" />
                        </div>

                        <div x-show="topic === '{{ \App\Models\SocialPost::TOPIC_CUSTOM }}'" x-cloak>
                            <x-input-label for="custom_topic" value="Temat" />
                            <x-text-input id="custom_topic" name="custom_topic" type="text" class="mt-1 block w-full"
                                          value="{{ old('custom_topic') }}" maxlength="255"
                                          placeholder="np. Nowa karta sezonowa na wiosnę" />
                            <x-input-error :messages="$errors->get('custom_topic')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="custom_guidelines" value="Dodatkowe wytyczne (opcjonalnie)" />
                            <textarea id="custom_guidelines" name="custom_guidelines" rows="3" maxlength="1000"
                                      class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
                                      placeholder="np. Stout 2.0 to ulepszona wersja naszego stouta. Warzymy go na miejscu. Dostępny od soboty 15 listopada.">{{ old('custom_guidelines') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Im konkretniej, tym lepszy post. Podaj, co dokładnie jest nowe, co z tego wynika dla gościa i od kiedy można to zamówić.
                                Model nie dopisze faktów spoza tego pola i danych piwa, więc ogólne zdania dadzą ogólny post.
                            </p>
                            <x-input-error :messages="$errors->get('custom_guidelines')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="platform" value="Platforma" />
                            <select id="platform" name="platform"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                @foreach ($platforms as $key => $label)
                                    <option value="{{ $key }}" @selected(old('platform', 'all') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('platform')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <x-input-label for="event_at" value="Dzień i godzina startu (opcjonalnie)" />
                                <x-text-input id="event_at" name="event_at" type="datetime-local" class="mt-1 block w-full"
                                              value="{{ old('event_at') }}" />
                                <x-input-error :messages="$errors->get('event_at')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="address" value="Adres" />
                                <x-text-input id="address" name="address" type="text" class="mt-1 block w-full"
                                              value="{{ old('address', $defaultAddress) }}" maxlength="255" />
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-2">
                            <a href="{{ route('social-posts.index') }}" class="text-gray-600 dark:text-gray-400 underline mr-4">Anuluj</a>
                            <x-primary-button type="submit" x-bind:disabled="submitting" x-bind:class="{ 'opacity-60 cursor-wait': submitting }">
                                <span x-show="!submitting">{{ __('Generuj post') }}</span>
                                <span x-show="submitting" x-cloak>Generowanie… (to może chwilę potrwać)</span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
