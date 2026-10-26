<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Ustawienia Beer Wall') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Zarządzanie godzinami zamknięcia sprzedaży dla każdego dnia tygodnia.
                </p>
            </div>
            <a href="{{ route('beerwall.admin.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                Powrót do listy
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="max-w-3xl mx-auto">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6">
                        <form action="{{ route('beerwall.admin.settings.update') }}" method="POST">
                            @csrf

                            <div class="space-y-6">
                                @php
                                    $days = [
                                        'monday' => 'Poniedziałek',
                                        'tuesday' => 'Wtorek',
                                        'wednesday' => 'Środa',
                                        'thursday' => 'Czwartek',
                                        'friday' => 'Piątek',
                                        'saturday' => 'Sobota',
                                        'sunday' => 'Niedziela',
                                    ];
                                @endphp

                                @foreach($days as $key => $label)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                        <label for="{{ $key }}_close_time" class="text-sm font-bold text-gray-700">
                                            {{ $label }}
                                        </label>
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs text-gray-500 uppercase font-semibold">Godzina zamknięcia:</span>
                                            <input type="time"
                                                   id="{{ $key }}_close_time"
                                                   name="{{ $key }}_close_time"
                                                   value="{{ $settings ? $settings->{$key . '_close_time'} : '' }}"
                                                   class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-8 flex justify-end gap-3">
                                <a href="{{ route('beerwall.admin.index') }}"
                                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition ease-in-out duration-150">
                                    Anuluj
                                </a>
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 transition ease-in-out duration-150">
                                    Zapisz ustawienia
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
