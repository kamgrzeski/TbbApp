<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Magazyn słodów') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-end">
                <a href="{{ route('malts.admin.create') }}">
                    <x-primary-button>
                        {{ __('Dodaj słód') }}
                    </x-primary-button>
                </a>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nazwa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ilość (kg)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ekstrakt</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Numer faktury</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Akcje</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($malts as $maltStock)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $maltStock->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $maltStock->kg }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $maltStock->extract }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $maltStock->invoice_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <form action="{{ route('malts.admin.subtract-bag', $maltStock->id) }}" method="POST" class="inline">
                                                @csrf
                                                <x-primary-button
                                                    class="!bg-orange-100 !text-orange-700 dark:!bg-orange-900/30 dark:!text-orange-300 hover:!bg-orange-200 dark:hover:!bg-orange-900/50 !border-none"
                                                    onclick="return confirm('Czy na pewno chcesz odjąć jeden worek (25kg)?')"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                    </svg>
                                                    -25kg
                                                </x-primary-button>
                                            </form>
                                            <form action="{{ route('malts.admin.edit', $maltStock->id) }}" method="GET" class="inline">
                                                <x-primary-button
                                                    class="!bg-indigo-100 !text-indigo-700 dark:!bg-indigo-900/30 dark:!text-indigo-300 hover:!bg-indigo-200 dark:hover:!bg-indigo-900/50 !border-none"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edytuj
                                                </x-primary-button>
                                            </form>
                                            <form action="{{ route('malts.admin.destroy', $maltStock->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <x-primary-button
                                                    class="!bg-red-100 !text-red-700 dark:!bg-red-900/30 dark:!text-red-300 hover:!bg-red-200 dark:hover:!bg-red-900/50 !border-none"
                                                    onclick="return confirm('Czy na pewno chcesz usunąć ten słód?')"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Usuń
                                                </x-primary-button>
                                            </form>
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
