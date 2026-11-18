<div>
    <style>
        @media (max-width: 767.98px) {
            .stack-mobile thead { display: none; }
            .stack-mobile,
            .stack-mobile tbody,
            .stack-mobile tr,
            .stack-mobile td { display: block; width: 100%; }
            .stack-mobile tr {
                border: 1px solid #e5e7eb;
                border-radius: .375rem;
                margin-bottom: .75rem;
                padding: .5rem .75rem;
            }
            .stack-mobile td {
                border: 0 !important;
                padding: .35rem 0 !important;
                text-align: left !important;
            }
            .stack-mobile td[data-label]::before {
                content: attr(data-label);
                display: block;
                font-size: .72rem;
                font-weight: 600;
                color: #6b7280;
                text-transform: uppercase;
                margin-bottom: .2rem;
            }
            .stack-mobile td.td-action { text-align: right !important; }
        }
    </style>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">

                @if($locked)
                    <div class="mb-4 p-3 rounded-md border border-yellow-300 bg-yellow-100 text-yellow-800 font-bold">Receptura ma wykonany zasyp – nie można jej edytować.</div>
                @endif

                <form wire:submit="save">
                    <fieldset @disabled($locked)>
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-4">
                        <div class="md:col-span-3">
                            <label class="block mb-1 text-sm font-bold text-gray-700">Nazwa receptury</label>
                            <input type="text" wire:model="recipe_name" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-1 text-sm font-bold text-gray-700">Styl</label>
                            <input type="text" wire:model="style" maxlength="100" placeholder="np. Hefeweizen" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block mb-1 text-sm font-bold text-gray-700">Tank</label>
                            <select wire:model="tank_number" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm">
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block mb-1 text-sm font-bold text-gray-700">Liczba warek</label>
                            <select wire:model.live="batch_count" class="block w-full rounded-md border border-blue-500 bg-white px-3 py-2 text-sm font-bold shadow-sm" {{ $recipeId && !$isClone ? 'disabled' : '' }}>
                                <option value="1">1 warka (500L)</option>
                                <option value="2">2 warki (1000L)</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-1 text-sm font-bold text-gray-700">Objętość (L)</label>
                            <input type="text" class="block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm" value="{{ $this->stats['volume'] }}" readonly>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-1 text-sm font-bold text-gray-700">Wydajność (%)</label>
                            <input type="number" wire:model.live="efficiency" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-4">
                        <div class="md:col-span-4">
                            <label class="block mb-1 text-sm font-bold text-gray-700">Temperatura zadania drożdży</label>
                            <input
                                    type="number"
                                    step="0.1"
                                    wire:model.live="yeast_pitch_temperature"
                                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm"
                            >
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-1 text-sm font-bold text-gray-700">Temperatura fermetnacji</label>
                            <input
                                    type="number"
                                    step="0.1"
                                    wire:model.live="fermentation_temperature"
                                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm"
                            >
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-1 text-sm font-bold text-gray-700">AKCPA Blg</label>
                            <input
                                    type="number"
                                    step="0.1"
                                    wire:model.live="akcpa_blg"
                                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm"
                            >
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-1 text-sm font-bold text-gray-700">GoPos Items Ids</label>
                            <input
                                    type="text"
                                    wire:model.blur="gopos_item_ids"
                                    placeholder="np. 1, 2, 3"
                                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm"
                            >
                        </div>
                    </div>

                    @foreach([1, 2] as $batchIdx)
                        @if($batch_count >= $batchIdx)
                            <div class="mb-5 p-4 border-2 rounded-md bg-gray-50 shadow-sm {{ $batchIdx == 1 ? 'border-blue-500' : 'border-green-500' }}" wire:key="batch-{{ $batchIdx }}">
                                <h4 class="text-lg font-bold mb-3 {{ $batchIdx == 1 ? 'text-blue-600' : 'text-green-600' }}">Zasyp Warka {{ $batchIdx }} (500L)</h4>

                                <div class="overflow-x-auto mb-3">
                                    <table class="stack-mobile min-w-full text-sm bg-white border border-gray-300 align-middle">
                                        <thead class="{{ $batchIdx == 1 ? 'bg-blue-100' : 'bg-green-100' }} text-center">
                                        <tr>
                                            <th class="border border-gray-300 px-3 py-2 text-left">Nazwa słodu</th>
                                            <th class="border border-gray-300 px-3 py-2" style="width: 180px;">Kg</th>
                                            <th class="border border-gray-300 px-3 py-2" style="width: 140px;">(%)</th>
                                            <th class="border border-gray-300 px-3 py-2" style="width: 50px;"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $batchMalts = collect($malts)->filter(fn($m) => $m['batch_number'] == $batchIdx);
                                            $isActiveEnz = fn($m) => !empty($m['malt_stock_id']) && !empty($m['is_active']);
                                            $rows = $batchMalts->filter($isActiveEnz)
                                                ->union($batchMalts->reject($isActiveEnz)->sortByDesc(fn($m) => (float)$m['kg']));
                                        @endphp
                                        @foreach($rows as $idx => $malt)
                                            @php $isInactiveEnzymatic = !empty($malt['malt_stock_id']) && empty($malt['is_active']); @endphp
                                                <tr wire:key="malt-row-{{ $idx }}" class="{{ $isInactiveEnzymatic ? 'bg-yellow-100' : '' }}">
                                                    <td data-label="Nazwa słodu" class="border border-gray-300 px-3 py-2">
                                                        <div class="flex items-center gap-2">
                                                            <div class="grow relative">
                                                                <select wire:model.live="malts.{{ $idx }}.malt_stock_id"
                                                                        wire:change="updateMaltExtract({{ $idx }}, $event.target.value)"
                                                                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm">
                                                                    <option value="">Wybierz słód...</option>
                                                                    @foreach($this->availableMalts as $availableMalt)
                                                                        <option value="{{ $availableMalt->id }}"
                                                                            {{ $malt['malt_stock_id'] == $availableMalt->id ? 'selected' : '' }}>
                                                                            {{ $availableMalt->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            @php
                                                                $selectedMalt = $this->availableMalts->firstWhere('id', $malt['malt_stock_id']);
                                                            @endphp
                                                            @if($selectedMalt)
                                                                <div class="whitespace-nowrap">
                                                                    <span class="inline-block px-2 py-0.5 rounded border border-gray-300 bg-gray-100 text-gray-900 text-xs font-normal">
                                                                        📦 {{ number_format($selectedMalt->kg, 2) }} kg
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td data-label="Kg" class="border border-gray-300 px-3 py-2">
                                                        @error("malts.{$idx}.kg")
                                                            <div class="text-red-600 text-sm mb-1">{{ $message }}</div>
                                                        @enderror
                                                        <div class="flex">
                                                            <input type="number" step="0.01" wire:model.live="malts.{{ $idx }}.kg" class="block w-full rounded-l-md border border-gray-300 bg-white px-3 py-2 text-sm">
                                                            <span class="inline-flex items-center rounded-r-md border border-l-0 border-gray-300 bg-gray-100 px-3 text-sm">
                                                                {{ (float)($this->stats['w'.$batchIdx]['kg'] ?? 0) > 0
    ? round(
        ((float)($malt['kg'] ?? 0) / (float)$this->stats['w'.$batchIdx]['kg']) * 100,
        1
    )
    : 0
}}%
                                                                </span>
                                                        </div>
                                                    </td>
                                                    <td class="border border-gray-300 px-3 py-2 text-center" data-label="Udział aktywnych (%)">
                                                        @if(array_key_exists($idx, $this->enzymaticShares))
                                                            <input type="text" class="block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-center" value="{{ $this->enzymaticShares[$idx] }}%" readonly>
                                                        @else
                                                            @if(!empty($malt['malt_stock_id']))
                                                                <span class="inline-block px-2 py-0.5 rounded bg-gray-500 text-white text-xs" title="Nieaktywny enzymatycznie">nieakt. enz.</span>
                                                            @else
                                                                <span class="text-gray-500 text-sm">—</span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="td-action border border-gray-300 px-3 py-2"><button type="button" wire:click="removeMalt({{ $idx }})" class="px-2 py-1 rounded-md border border-red-500 text-red-600 text-xs hover:bg-red-50">X</button></td>
                                                </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-50">
                                        <tr>
                                            <td class="border border-gray-300 px-3 py-2 text-right font-bold uppercase text-sm">Suma Warka {{ $batchIdx }}:</td>
                                            <td class="border border-gray-300 px-3 py-2 text-center font-bold">{{ number_format($this->stats['w'.$batchIdx]['kg'] ?? 0, 2) }} kg</td>
                                            <td colspan="3" class="border border-gray-300 px-3 py-2 text-blue-600 font-bold italic">Łącznie worków: {{ ceil(($this->stats['w'.$batchIdx]['kg'] ?? 0) / 25) }} szt.</td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <button type="button" wire:click="addMalt({{ $batchIdx }})" class="px-3 py-1.5 rounded-md text-sm font-semibold text-white {{ $batchIdx == 1 ? 'bg-blue-600 hover:bg-blue-700' : 'bg-green-600 hover:bg-green-700' }}">+ Dodaj słód</button>

                                <h5 class="font-bold mt-4 mb-3">Chmielenie - Warka {{ $batchIdx }} <span class="inline-block px-2 py-0.5 rounded bg-yellow-400 text-gray-900 text-xs ml-2">{{ collect($hops)->where('batch_number', $batchIdx)->sum('amount') }} g</span></h5>
                                <div class="overflow-x-auto">
                                    <table class="stack-mobile min-w-full text-sm bg-white border border-gray-300 align-middle text-center">
                                        <thead class="bg-gray-800 text-white">
                                        <tr>
                                            <th class="border border-gray-300 px-3 py-2 text-left">Chmiel</th>
                                            <th class="border border-gray-300 px-3 py-2" style="width: 120px;">Ilość (g)</th>
                                            <th class="border border-gray-300 px-3 py-2" style="width: 120px;">Alfa-kwasy</th>
                                            <th class="border border-gray-300 px-3 py-2" style="width: 120px;">Czas</th>
                                            <th class="border border-gray-300 px-3 py-2" style="width: 80px;">Akt.</th>
                                            <th class="border border-gray-300 px-3 py-2" style="width: 50px;"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($hops as $idx => $hop)
                                            @if($hop['batch_number'] == $batchIdx)
                                                <tr wire:key="hop-row-{{ $idx }}" class="{{ empty($hop['is_active']) ? 'opacity-50 grayscale' : '' }}">
                                                    <td data-label="Chmiel" class="border border-gray-300 px-3 py-2"><input type="text" wire:model="hops.{{ $idx }}.name" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-left"></td>
                                                    <td data-label="Gramy" class="border border-gray-300 px-3 py-2"><input type="number" step="0.1" wire:model="hops.{{ $idx }}.amount" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm"></td>
                                                    <td data-label="Alfa %" class="border border-gray-300 px-3 py-2"><input type="number" step="0.1" wire:model="hops.{{ $idx }}.alpha_acids" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm"></td>
                                                    <td data-label="Czas" class="border border-gray-300 px-3 py-2"><input type="number" wire:model="hops.{{ $idx }}.time" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm"></td>
                                                    <td data-label="Akt." class="border border-gray-300 px-3 py-2"><input type="checkbox" wire:model="hops.{{ $idx }}.is_active" class="h-4 w-4 rounded border-gray-300"></td>
                                                    <td class="td-action border border-gray-300 px-3 py-2"><button type="button" wire:click="removeHop({{ $idx }})" class="px-2 py-1 rounded-md border border-red-500 text-red-600 text-xs hover:bg-red-50">X</button></td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" wire:click="addHop({{ $batchIdx }})" class="mt-3 px-3 py-1.5 rounded-md text-sm font-semibold bg-gray-600 text-white hover:bg-gray-700">+ Dodaj chmiel</button>
                            </div>
                        @endif
                    @endforeach


                    <div class="p-4 rounded-md bg-gray-900 text-white shadow mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                            <div class="md:col-span-4 md:border-r md:border-gray-600">
                                <span class="text-blue-400 font-bold">WARKA 1:</span><br>
                                <strong>{{ number_format($this->stats['w1']['kg'], 2) }} kg</strong> |
                                <span class="text-blue-400 font-bold">{{ number_format($this->stats['w1']['blg'], 2) }} °Blg</span> |
                                {{ $this->stats['w1']['bags'] }} worków
                            </div>
                            @if($batch_count == 2)
                                <div class="md:col-span-4 md:border-r md:border-gray-600">
                                    <span class="text-green-400 font-bold">WARKA 2:</span><br>
                                    <strong>{{ number_format($this->stats['w2']['kg'], 2) }} kg</strong> |
                                    <span class="text-green-400 font-bold">{{ number_format($this->stats['w2']['blg'], 2) }} °Blg</span> |
                                    {{ $this->stats['w2']['bags'] }} worków
                                </div>
                            @endif
                            <div class="{{ $batch_count == 2 ? 'md:col-span-4' : 'md:col-span-8' }} text-center md:text-right">
                                <span class="text-yellow-400 font-bold">SUMA TANK:</span><br>
                                <span class="text-xl font-bold">{{ number_format($this->stats['total_kg'], 2) }} kg</span> /
                                <span class="text-xl font-bold text-yellow-400">{{ number_format($this->stats['total_blg'], 2) }} °Blg</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('recipe.index') }}" class="px-4 py-2 rounded-md border border-gray-400 text-gray-700 text-sm font-semibold hover:bg-gray-50">Anuluj</a>
                        <button type="submit" class="px-5 py-2 rounded-md bg-blue-600 text-white text-sm font-bold hover:bg-blue-700">
                            {{ $isClone ? 'Utwórz kopię' : ($recipeId ? 'Zaktualizuj' : 'Zapisz') }}
                        </button>
                    </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
