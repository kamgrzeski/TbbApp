<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Magazyn słodów') }}
            </h2>
            <a href="{{ route('malts.admin.create') }}">
                <x-primary-button class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Dodaj słód') }}
                </x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Wszystkie słody</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Aktywne enzymatycznie</div>
                    <div class="mt-1 text-2xl font-semibold text-green-600 dark:text-green-400">{{ $stats['active'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Nieaktywne enzymatycznie</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-600 dark:text-gray-300">{{ $stats['inactive'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Brak na stanie</div>
                    <div class="mt-1 text-2xl font-semibold text-red-600 dark:text-red-400">{{ $stats['empty'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Łącznie kg</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stats['total_kg'], 2) }}</div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-0 text-gray-900 dark:text-gray-100">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nazwa słodu</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Enzymatycznie</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ilość w magazynie</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ekstrakt</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Faktura</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Zarządzanie</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($malts as $maltStock)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $maltStock->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($maltStock->is_enzymatic_active)
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">Aktywny enzymatycznie</span>
                                        @else
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Nieaktywny enzymatycznie</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $maltStock->kg <= 0 ? 'bg-red-600 text-white animate-pulse' : ($maltStock->kg < 50 ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300') }}">
                                            {{ $maltStock->kg <= 0 ? number_format($maltStock->kg, 2) . ' kg' : number_format($maltStock->kg, 2) . ' kg' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-sm">{{ number_format($maltStock->extract, 2) }}</span>
                                            <span class="ml-1 text-xs text-gray-400">%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $maltStock->invoice_number ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        {{-- Menu akcji --}}
                                        <div x-data="{ open: false, top: 0, left: 0 }" @click.outside="open = false" @scroll.window="open = false" class="inline-flex justify-end">
                                            <button type="button" aria-label="Akcje"
                                                    @click="const r = $el.getBoundingClientRect(); top = r.bottom + 6; left = r.right - 192; open = !open"
                                                    class="p-2 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition dark:hover:bg-gray-700 dark:hover:text-gray-200">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="12" cy="19" r="1.8"/></svg>
                                            </button>

                                            <div x-show="open" x-transition x-cloak :style="`top: ${top}px; left: ${left}px`"
                                                 class="fixed z-50 w-48 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg overflow-hidden divide-y divide-gray-100 dark:divide-gray-700 text-left">
                                                <a href="{{ route('malts.admin.edit', $maltStock->id) }}"
                                                   class="block px-4 py-3 text-sm font-medium text-indigo-700 dark:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30">
                                                    Edytuj
                                                </a>

                                                <form action="{{ route('malts.admin.destroy', $maltStock->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            onclick="return confirm('Czy na pewno chcesz usunąć ten słód?')"
                                                            class="w-full text-left px-4 py-3 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30">
                                                        Usuń
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
