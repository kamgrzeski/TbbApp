<div>
    <style>
        /* Bootstrap (ładowany na tym widoku) podkreśla wszystkie linki – nawigacja ma zostać bez podkreśleń */
        nav a { text-decoration: none; }
        /* Bootstrap dodaje h2 margines dolny – nagłówek strony ma wyglądać jak w pozostałych widokach */
        header h2 { margin: 0; }

        @media (max-width: 767.98px) {
            .stack-mobile thead { display: none; }
            .stack-mobile,
            .stack-mobile tbody,
            .stack-mobile tr,
            .stack-mobile td { display: block; width: 100%; }
            .stack-mobile tr {
                border: 1px solid #dee2e6;
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
                color: #6c757d;
                text-transform: uppercase;
                margin-bottom: .2rem;
            }
            .stack-mobile td.td-action { text-align: right !important; }
        }
    </style>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <!-- Bootstrap CSS dla tabel i siatki -->
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

                @if($locked)
                    <div class="alert alert-warning fw-bold">Receptura ma wykonany zasyp – nie można jej edytować.</div>
                @endif

                <form wire:submit="save">
                    <fieldset @disabled($locked)>
                    <div class="row mb-4 g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Nazwa receptury</label>
                            <input type="text" wire:model="recipe_name" class="form-control border-gray-300 shadow-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Styl</label>
                            <input type="text" wire:model="style" maxlength="100" placeholder="np. Hefeweizen" class="form-control border-gray-300 shadow-sm">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label fw-bold">Tank</label>
                            <select wire:model="tank_number" class="form-select border-gray-300">
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Liczba warek</label>
                            <select wire:model.live="batch_count" class="form-select border-primary fw-bold shadow-sm" {{ $recipeId && !$isClone ? 'disabled' : '' }}>
                                <option value="1">1 warka (500L)</option>
                                <option value="2">2 warki (1000L)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Objętość (L)</label>
                            <input type="text" class="form-control bg-light" value="{{ $this->stats['volume'] }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Wydajność (%)</label>
                            <input type="number" wire:model.live="efficiency" class="form-control border-gray-300 shadow-sm">
                        </div>
                    </div>

                    <div class="row mb-4 g-3">

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Temperatura zadania drożdży</label>
                            <input
                                    type="number"
                                    step="0.1"
                                    wire:model.live="yeast_pitch_temperature"
                                    class="form-control border-gray-300 shadow-sm"
                            >
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Temperatura fermetnacji</label>
                            <input
                                    type="number"
                                    step="0.1"
                                    wire:model.live="fermentation_temperature"
                                    class="form-control border-gray-300 shadow-sm"
                            >
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">AKCPA Blg</label>
                            <input
                                    type="number"
                                    step="0.1"
                                    wire:model.live="akcpa_blg"
                                    class="form-control border-gray-300 shadow-sm"
                            >
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">GoPos Items Ids</label>
                            <input
                                    type="text"
                                    wire:model.blur="gopos_item_ids"
                                    placeholder="np. 1, 2, 3"
                                    class="form-control border-gray-300 shadow-sm"
                            >
                        </div>
                    </div>

                    @foreach([1, 2] as $batchIdx)
                        @if($batch_count >= $batchIdx)
                            <div class="mb-5 p-4 border rounded bg-light shadow-sm {{ $batchIdx == 1 ? 'border-primary' : 'border-success' }}" wire:key="batch-{{ $batchIdx }}">
                                <h4 class="fw-bold {{ $batchIdx == 1 ? 'text-primary' : 'text-success' }} mb-3">Zasyp Warka {{ $batchIdx }} (500L)</h4>

                                <div class="table-responsive mb-3">
                                    <table class="stack-mobile table table-bordered align-middle bg-white">
                                        <thead class="{{ $batchIdx == 1 ? 'table-primary' : 'table-success' }} text-center">
                                        <tr>
                                            <th class="text-start">Nazwa słodu</th>
                                            <th style="width: 180px;">Kg</th>
                                            <th style="width: 140px;">(%)</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            // Aktywne enzymatycznie na górze; nieaktywne na dole, posortowane po wadze malejąco
                                            $batchMalts = collect($malts)->filter(fn($m) => $m['batch_number'] == $batchIdx);
                                            $isActiveEnz = fn($m) => !empty($m['malt_stock_id']) && !empty($m['is_active']);
                                            // union() zachowuje oryginalne klucze (używane w wire:model i removeMalt)
                                            $rows = $batchMalts->filter($isActiveEnz)
                                                ->union($batchMalts->reject($isActiveEnz)->sortByDesc(fn($m) => (float)$m['kg']));
                                        @endphp
                                        @foreach($rows as $idx => $malt)
                                            @php $isInactiveEnzymatic = !empty($malt['malt_stock_id']) && empty($malt['is_active']); @endphp
                                                <tr wire:key="malt-row-{{ $idx }}" class="{{ $isInactiveEnzymatic ? 'table-warning' : '' }}">
                                                    <td data-label="Nazwa słodu">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="flex-grow-1 position-relative">
                                                                <select wire:model.live="malts.{{ $idx }}.malt_stock_id"
                                                                        wire:change="updateMaltExtract({{ $idx }}, $event.target.value)"
                                                                        class="form-select shadow-sm">
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
                                                                <div class="text-nowrap">
                                                                    <span class="badge bg-light text-dark border fw-normal" style="font-size: 0.7rem;">
                                                                        📦 {{ number_format($selectedMalt->kg, 2) }} kg
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td data-label="Kg">
                                                        @error("malts.{$idx}.kg")
                                                            <div class="text-danger small mb-1">{{ $message }}</div>
                                                        @enderror
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" wire:model.live="malts.{{ $idx }}.kg" class="form-control">
                                                            <span class="input-group-text small">
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
                                                    <td class="text-center" data-label="Udział aktywnych (%)">
                                                        @if(array_key_exists($idx, $this->enzymaticShares))
                                                            <input type="text" class="form-control text-center bg-light" value="{{ $this->enzymaticShares[$idx] }}%" readonly>
                                                        @else
                                                            @if(!empty($malt['malt_stock_id']))
                                                                <span class="badge bg-secondary" title="Nieaktywny enzymatycznie">nieakt. enz.</span>
                                                            @else
                                                                <span class="text-muted small">—</span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="td-action"><button type="button" wire:click="removeMalt({{ $idx }})" class="btn btn-outline-danger btn-sm">X</button></td>
                                                </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                        <tr>
                                            <td class="text-end fw-bold text-uppercase small">Suma Warka {{ $batchIdx }}:</td>
                                            <td class="text-center fw-bold">{{ number_format($this->stats['w'.$batchIdx]['kg'] ?? 0, 2) }} kg</td>
                                            <td colspan="3" class="text-primary fw-bold fst-italic">Łącznie worków: {{ ceil(($this->stats['w'.$batchIdx]['kg'] ?? 0) / 25) }} szt.</td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <button type="button" wire:click="addMalt({{ $batchIdx }})" class="btn btn-sm btn-{{ $batchIdx == 1 ? 'primary' : 'success' }}">+ Dodaj słód</button>

                                <h5 class="fw-bold mt-4 mb-3">Chmielenie - Warka {{ $batchIdx }} <span class="badge bg-warning text-dark ms-2">{{ collect($hops)->where('batch_number', $batchIdx)->sum('amount') }} g</span></h5>
                                <div class="table-responsive">
                                    <table class="stack-mobile table table-bordered align-middle bg-white text-center">
                                        <thead class="table-dark">
                                        <tr>
                                            <th class="text-start">Chmiel</th>
                                            <th style="width: 120px;">Ilość (g)</th>
                                            <th style="width: 120px;">Alfa-kwasy</th>
                                            <th style="width: 120px;">Czas</th>
                                            <th style="width: 80px;">Akt.</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($hops as $idx => $hop)
                                            @if($hop['batch_number'] == $batchIdx)
                                                <tr wire:key="hop-row-{{ $idx }}" class="{{ empty($hop['is_active']) ? 'opacity-50 grayscale' : '' }}">
                                                    <td data-label="Chmiel"><input type="text" wire:model="hops.{{ $idx }}.name" class="form-control text-start"></td>
                                                    <td data-label="Gramy"><input type="number" step="0.1" wire:model="hops.{{ $idx }}.amount" class="form-control"></td>
                                                    <td data-label="Alfa %"><input type="number" step="0.1" wire:model="hops.{{ $idx }}.alpha_acids" class="form-control"></td>
                                                    <td data-label="Czas"><input type="number" wire:model="hops.{{ $idx }}.time" class="form-control"></td>
                                                    <td data-label="Akt."><input type="checkbox" wire:model="hops.{{ $idx }}.is_active" class="form-check-input"></td>
                                                    <td class="td-action"><button type="button" wire:click="removeHop({{ $idx }})" class="btn btn-outline-danger btn-sm">X</button></td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" wire:click="addHop({{ $batchIdx }})" class="btn btn-sm btn-secondary">+ Dodaj chmiel</button>
                            </div>
                        @endif
                    @endforeach

                    <!-- PODSUMOWANIE (Przyklejone na dole lub w ramce) -->
                    <div class="p-4 rounded bg-dark text-white shadow mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-4 border-end border-secondary">
                                <span class="text-primary fw-bold">WARKA 1:</span><br>
                                <strong>{{ number_format($this->stats['w1']['kg'], 2) }} kg</strong> |
                                <span class="text-primary fw-bold">{{ number_format($this->stats['w1']['blg'], 2) }} °Blg</span> |
                                {{ $this->stats['w1']['bags'] }} worków
                            </div>
                            @if($batch_count == 2)
                                <div class="col-md-4 border-end border-secondary">
                                    <span class="text-success fw-bold">WARKA 2:</span><br>
                                    <strong>{{ number_format($this->stats['w2']['kg'], 2) }} kg</strong> |
                                    <span class="text-success fw-bold">{{ number_format($this->stats['w2']['blg'], 2) }} °Blg</span> |
                                    {{ $this->stats['w2']['bags'] }} worków
                                </div>
                            @endif
                            <div class="{{ $batch_count == 2 ? 'col-md-4' : 'col-md-8' }} text-center text-md-end">
                                <span class="text-warning fw-bold">SUMA TANK:</span><br>
                                <span class="h4 mb-0 fw-bold">{{ number_format($this->stats['total_kg'], 2) }} kg</span> /
                                <span class="h4 mb-0 text-warning fw-bold">{{ number_format($this->stats['total_blg'], 2) }} °Blg</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3">
                        <a href="{{ route('brewing.index') }}" class="btn btn-outline-secondary px-4 py-2">Anuluj</a>
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
                            {{ $isClone ? 'Utwórz kopię' : ($recipeId ? 'Zaktualizuj' : 'Zapisz') }}
                        </button>
                    </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
