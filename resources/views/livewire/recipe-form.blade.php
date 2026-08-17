<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $isClone ? 'Klonowanie: '.$recipe_name : ($recipeId ? 'Edycja: '.$recipe_name : 'Nowa receptura') }}
        </h2>
    </x-slot>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <!-- Bootstrap CSS dla tabel i siatki -->
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                
                <form wire:submit="save">
                    <div class="row mb-4 g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Nazwa receptury</label>
                            <input type="text" wire:model="recipe_name" class="form-control border-gray-300 shadow-sm">
                        </div>
                        <div class="col-md-2">
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
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Wydajność (%)</label>
                        <input type="number" wire:model.live="efficiency" class="form-control border-gray-300 shadow-sm" style="width: 120px">
                    </div>
                    
                    @foreach([1, 2] as $batchIdx)
                        @if($batch_count >= $batchIdx)
                            <div class="mb-5 p-4 border rounded bg-light shadow-sm {{ $batchIdx == 1 ? 'border-primary' : 'border-success' }}" wire:key="batch-{{ $batchIdx }}">
                                <h4 class="fw-bold {{ $batchIdx == 1 ? 'text-primary' : 'text-success' }} mb-3">WARKA {{ $batchIdx }}</h4>
                                
                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered align-middle bg-white">
                                        <thead class="{{ $batchIdx == 1 ? 'table-primary' : 'table-success' }} text-center">
                                        <tr>
                                            <th class="text-start">Nazwa słodu</th>
                                            <th style="width: 180px;">Kg</th>
                                            <th style="width: 120px;">Ekstrakt %</th>
                                            <th style="width: 80px;">Akt.</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($malts as $idx => $malt)
                                            @if($malt['batch_number'] == $batchIdx)
                                                <tr wire:key="malt-row-{{ $idx }}">
                                                    <td><input type="text" wire:model="malts.{{ $idx }}.name" class="form-control"></td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" wire:model.live="malts.{{ $idx }}.kg" class="form-control">
                                                            <span class="input-group-text small">
                                                                    {{ $this->stats['w'.$batchIdx]['kg'] > 0 ? round(($malt['kg'] / $this->stats['w'.$batchIdx]['kg']) * 100, 1) : 0 }}%
                                                                </span>
                                                        </div>
                                                    </td>
                                                    <td><input type="number" wire:model.live="malts.{{ $idx }}.extract" class="form-control"></td>
                                                    <td class="text-center"><input type="checkbox" wire:model.live="malts.{{ $idx }}.is_active" class="form-check-input"></td>
                                                    <td><button type="button" wire:click="removeMalt({{ $idx }})" class="btn btn-outline-danger btn-sm">X</button></td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" wire:click="addMalt({{ $batchIdx }})" class="btn btn-sm btn-{{ $batchIdx == 1 ? 'primary' : 'success' }}">+ Dodaj słód</button>
                                
                                <h5 class="fw-bold mt-4 mb-3">Chmielenie</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle bg-white text-center">
                                        <thead class="table-dark">
                                        <tr>
                                            <th class="text-start">Chmiel</th>
                                            <th style="width: 120px;">Gramy</th>
                                            <th style="width: 120px;">Alfa %</th>
                                            <th style="width: 120px;">Czas</th>
                                            <th style="width: 80px;">Akt.</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($hops as $idx => $hop)
                                            @if($hop['batch_number'] == $batchIdx)
                                                <tr wire:key="hop-row-{{ $idx }}">
                                                    <td><input type="text" wire:model="hops.{{ $idx }}.name" class="form-control text-start"></td>
                                                    <td><input type="number" step="0.1" wire:model="hops.{{ $idx }}.amount" class="form-control"></td>
                                                    <td><input type="number" step="0.1" wire:model="hops.{{ $idx }}.alpha_acids" class="form-control"></td>
                                                    <td><input type="number" wire:model="hops.{{ $idx }}.time" class="form-control"></td>
                                                    <td><input type="checkbox" wire:model="hops.{{ $idx }}.is_active" class="form-check-input"></td>
                                                    <td><button type="button" wire:click="removeHop({{ $idx }})" class="btn btn-outline-danger btn-sm">X</button></td>
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
                </form>
            </div>
        </div>
    </div>
</div>