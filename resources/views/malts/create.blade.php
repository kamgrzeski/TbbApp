<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dodaj nowy słód') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('malts.admin.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <x-input-label for="name" value="Nazwa słodu" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required autofocus />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="kg" value="Ilość (kg)" />
                            <x-text-input id="kg" name="kg" type="number" step="0.01" class="mt-1 block w-full" required />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="extract" value="Ekstrakt" />
                            <x-text-input id="extract" name="extract" type="number" step="0.01" class="mt-1 block w-full" />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="invoice_number" value="Numer faktury" />
                            <x-text-input id="invoice_number" name="invoice_number" type="text" class="mt-1 block w-full" />
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('malts.admin.index') }}" class="text-gray-600 dark:text-gray-400 underline mr-4">Anuluj</a>
                            <x-primary-button>
                                {{ __('Zapisz') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
