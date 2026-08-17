<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			@if(isset($isClone))
				Klonowanie receptury: {{ $recipe->name }}
			@elseif(isset($recipe))
				Edycja receptury: {{ $recipe->name }}
			@else
				Nowa receptura
			@endif
		</h2>
	</x-slot>
	
	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
				<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
				
				<form action="{{ isset($isClone) && $isClone ? route('brewing.store') : (isset($recipe) ? route('brewing.update', $recipe) : route('brewing.store')) }}" method="POST">
					@csrf
					@if(isset($recipe) && (!isset($isClone) || !$isClone))
						@method('PATCH')
					@endif
					
					<!-- Nagłówek Receptury -->
					<div class="row mb-4 g-3">
						<div class="col-md-5">
							<label class="form-label fw-bold">Nazwa receptury</label>
							<input type="text" name="recipe_name" class="form-control border-gray-300 shadow-sm" value="{{ isset($isClone) ? $recipe->name . ' (Kopia)' : ($recipe->name ?? '') }}" required>
						</div>
						<div class="col-md-2">
							<label class="form-label fw-bold">Numer tanka</label>
							<select name="tank_number" class="form-select border-gray-300 shadow-sm">
								<option value="2" {{ (isset($recipe) && $recipe->tank_number == '2') ? 'selected' : '' }}>2</option>
								<option value="3" {{ (isset($recipe) && $recipe->tank_number == '3') ? 'selected' : '' }}>3</option>
							</select>
						</div>
						<div class="col-md-3">
							<label class="form-label fw-bold">Liczba warek (każda 500L)</label>
							<select id="liczba-warek" class="form-select border-primary fw-bold shadow-sm"
									onchange="liczWszystko()"
									{{ (isset($recipe) && (!isset($isClone) || !$isClone)) ? 'disabled' : '' }}>
								<option value="1" {{ (isset($recipe) && $recipe->volume <= 500) ? 'selected' : '' }}>1 warka (500L)</option>
								<option value="2" {{ (!isset($recipe) || (isset($recipe) && $recipe->volume > 500)) ? 'selected' : 'selected' }}>2 warki (1000L)</option>
							</select>
							<input type="hidden" name="batch_count" id="hidden-batch-count" value="{{ (isset($recipe) && $recipe->volume <= 500) ? 1 : 2 }}">
						</div>
						<div class="col-md-2">
							<label class="form-label fw-bold">Objętość (L)</label>
							<input type="number" name="volume" id="objetosc" class="form-control bg-light" value="{{ $recipe->volume ?? 1000 }}" readonly>
						</div>
					</div>
					
					<div class="row mb-4 g-3">
						<div class="col-md-12">
							<label class="form-label fw-bold">Wydajność warzelni (%)</label>
							<input type="number" name="efficiency" id="wydajnosc" class="form-control border-gray-300 shadow-sm" value="{{ $recipe->efficiency ?? 75 }}" min="0" max="100">
						</div>
					</div>
					
					<!-- SEKCJA WARKA 1 -->
					<div id="section-batch-1">
						<div class="p-3 mb-4 border rounded bg-light border-primary shadow-sm">
							<h4 class="fw-bold text-primary mb-3">Warka 1 (500L)</h4>
							<div class="table-responsive">
								<table class="table table-bordered table-hover align-middle">
									<thead class="table-primary text-center">
									<tr>
										<th class="text-start">Nazwa słodu</th>
										<th style="width: 180px;">Kg</th>
										<th style="width: 120px;">Ekstrakt (%)</th>
										<th style="width: 80px;">Aktywny</th>
										<th style="width: 50px;"></th>
									</tr>
									</thead>
									<tbody id="slody-lista-1"></tbody>
								</table>
							</div>
							<button type="button" onclick="dodajSlod(1)" class="btn btn-primary btn-sm">+ Dodaj słód do Warki 1</button>
						</div>
						
						<div class="p-3 mb-4 border rounded bg-light">
							<h4 class="fw-bold text-primary mb-3">Chmielenie - Warka 1 (500L)</h4>
							<div class="table-responsive">
								<table class="table table-bordered table-hover align-middle text-center">
									<thead class="table-dark">
									<tr>
										<th class="text-start">Nazwa chmielu</th>
										<th style="width: 120px;">Gramy (g)</th>
										<th style="width: 120px;">Alfa (%)</th>
										<th style="width: 120px;">Czas (min)</th>
										<th style="width: 80px;">Aktywny</th>
										<th style="width: 50px;"></th>
									</tr>
									</thead>
									<tbody id="chmiele-lista-1"></tbody>
								</table>
							</div>
							<button type="button" onclick="dodajChmiel(1)" class="btn btn-primary btn-sm">+ Dodaj chmiel do Warki 1</button>
						</div>
					</div>
					
					<!-- SEKCJA WARKA 2 -->
					<div id="section-batch-2">
						<div class="p-3 mb-4 border rounded bg-light border-success shadow-sm">
							<h4 class="fw-bold text-success mb-3">Warka 2 (500L)</h4>
							<div class="table-responsive">
								<table class="table table-bordered table-hover align-middle">
									<thead class="table-success text-center">
									<tr>
										<th class="text-start">Nazwa słodu</th>
										<th style="width: 180px;">Kg</th>
										<th style="width: 120px;">Ekstrakt (%)</th>
										<th style="width: 80px;">Aktywny</th>
										<th style="width: 50px;"></th>
									</tr>
									</thead>
									<tbody id="slody-lista-2"></tbody>
								</table>
							</div>
							<button type="button" onclick="dodajSlod(2)" class="btn btn-success btn-sm">+ Dodaj słód do Warki 2</button>
						</div>
						
						<div class="p-3 mb-4 border rounded bg-light">
							<h4 class="fw-bold text-success mb-3">Chmielenie - Warka 2 (500L)</h4>
							<div class="table-responsive">
								<table class="table table-bordered table-hover align-middle text-center">
									<thead class="table-dark">
									<tr>
										<th class="text-start">Nazwa chmielu</th>
										<th style="width: 120px;">Gramy (g)</th>
										<th style="width: 120px;">Alfa (%)</th>
										<th style="width: 120px;">Czas (min)</th>
										<th style="width: 80px;">Aktywny</th>
										<th style="width: 50px;"></th>
									</tr>
									</thead>
									<tbody id="chmiele-lista-2"></tbody>
								</table>
							</div>
							<button type="button" onclick="dodajChmiel(2)" class="btn btn-success btn-sm">+ Dodaj chmiel do Warki 2</button>
						</div>
					</div>
					
					<!-- PODSUMOWANIE -->
					<div class="row mt-4">
						<div class="col-12 mb-3">
							<div class="p-3 rounded border bg-dark text-white shadow-sm">
								<div class="row g-3 text-center text-md-start align-items-center">
									<div class="col-md-4 border-end border-secondary" id="summary-batch-1">
										<h6 class="text-primary fw-bold small mb-1">WARKA 1 (500L)</h6>
										<p class="mb-0 small">
											<span id="w1-kg" class="fw-bold">0</span> kg | <span id="w1-blg" class="text-primary fw-bold">0</span> °Blg | <span id="w1-worki">0</span> worków
										</p>
									</div>
									<div class="col-md-4 border-end border-secondary" id="summary-batch-2">
										<h6 class="text-success fw-bold small mb-1">WARKA 2 (500L)</h6>
										<p class="mb-0 small">
											<span id="w2-kg" class="fw-bold">0</span> kg | <span id="w2-blg" class="text-success fw-bold">0</span> °Blg | <span id="w2-worki">0</span> worków
										</p>
									</div>
									<div class="col-md-4">
										<h6 class="text-warning fw-bold small mb-1">TANK SUMA (<span id="js-total-vol-display">1000</span>L)</h6>
										<p class="mb-0">
											<span class="fw-bold"><span id="total-kg">0</span> kg</span> / <span class="text-warning fw-bold"><span id="total-blg">0</span> °Blg</span>
										</p>
									</div>
								</div>
							</div>
						</div>
						
						<div class="col-12 d-flex flex-column-reverse flex-md-row justify-content-md-end gap-2">
							<a href="{{ route('brewing.index') }}" class="btn btn-outline-secondary px-4 py-2 text-sm">Anuluj</a>
							<button type="submit" class="btn btn-primary bg-blue-700 px-4 py-2 fw-bold text-sm shadow-sm">
								@if(isset($isClone)) Utwórz kopię @elseif(isset($recipe)) Zaktualizuj @else Zapisz @endif recepturę
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<script>
        let maltIndex = 0;
        let hopIndex = 0;

        function liczWszystko() {
            const numBatches = parseInt(document.getElementById('liczba-warek').value) || 1;
            const vBatch = 500;
            const vTotal = numBatches * vBatch;
            const wydajnosc = (parseFloat(document.getElementById('wydajnosc').value) || 75) / 100;

            document.getElementById('objetosc').value = vTotal;
            document.getElementById('js-total-vol-display').textContent = vTotal;
            document.getElementById('hidden-batch-count').value = numBatches;

            // UKRYWANIE I WYŁĄCZANIE PÓL WARKI 2
            const section2 = document.getElementById('section-batch-2');
            const summary2 = document.getElementById('summary-batch-2');

            if (numBatches === 1) {
                section2.style.display = 'none';
                summary2.style.display = 'none';
                // Wyłączamy wszystkie inputy w sekcji 2, żeby nie były wysyłane
                section2.querySelectorAll('input, select, button').forEach(el => el.disabled = true);
            } else {
                section2.style.display = 'block';
                summary2.style.display = 'block';
                // Włączamy z powrotem
                section2.querySelectorAll('input, select, button').forEach(el => el.disabled = false);
            }

            let kg1 = 0, ext1 = 0, kg2 = 0, ext2 = 0;

            document.querySelectorAll('.slod-row').forEach(row => {
                const kgInput = row.querySelector('.slod-kg');
                if (kgInput.disabled) return; // Pomijamy wyłączone wiersze

                const kg = parseFloat(kgInput.value) || 0;
                const extract = (parseFloat(row.querySelector('.slod-extract').value) || 80) / 100;
                const active = row.querySelector('.slod-active').checked;
                const batch = row.querySelector('.batch-number').value;

                if (batch == "1") {
                    kg1 += kg;
                    if (active) ext1 += kg * extract;
                } else if (batch == "2") {
                    kg2 += kg;
                    if (active) ext2 += kg * extract;
                }
            });

            const blg1 = (ext1 / vBatch * 100 * wydajnosc);
            const blg2 = (ext2 / vBatch * 100 * wydajnosc);
            const totalBlg = ((ext1 + (numBatches === 2 ? ext2 : 0)) / vTotal * 100 * wydajnosc);

            document.getElementById('w1-kg').textContent = kg1.toFixed(2);
            document.getElementById('w1-blg').textContent = blg1.toFixed(2);
            document.getElementById('w1-worki').textContent = Math.ceil(kg1 / 25);
            document.getElementById('w2-kg').textContent = kg2.toFixed(2);
            document.getElementById('w2-blg').textContent = blg2.toFixed(2);
            document.getElementById('w2-worki').textContent = Math.ceil(kg2 / 25);
            document.getElementById('total-kg').textContent = (kg1 + (numBatches === 2 ? kg2 : 0)).toFixed(2);
            document.getElementById('total-blg').textContent = totalBlg.toFixed(2);
        }

        function dodajSlod(batch, name = '', kg = '', extract = 80, active = true) {
            const tbody = document.getElementById(`slody-lista-${batch}`);
            const tr = document.createElement('tr');
            tr.className = 'slod-row';
            tr.innerHTML = `
                <input type="hidden" name="malts[${maltIndex}][batch_number]" class="batch-number" value="${batch}">
                <td><input type="text" name="malts[${maltIndex}][name]" class="form-control" value="${name}"></td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.01" name="malts[${maltIndex}][kg]" class="form-control slod-kg" value="${kg}" oninput="liczWszystko()">
                        <span class="input-group-text malt-percentage font-monospace" style="min-width: 60px; font-size: 0.75rem;">0%</span>
                    </div>
                </td>
                <td><input type="number" name="malts[${maltIndex}][extract]" class="form-control slod-extract" value="${extract}" oninput="liczWszystko()"></td>
                <td class="text-center"><input type="checkbox" name="malts[${maltIndex}][active]" class="form-check-input slod-active" ${active ? 'checked' : ''} onchange="liczWszystko()"></td>
                <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('tr').remove(); liczWszystko();">X</button></td>
            `;
            tbody.appendChild(tr);
            maltIndex++;

            // Jeśli dodajemy do sekcji która jest obecnie ukryta, od razu wyłączamy inputy
            const numBatches = parseInt(document.getElementById('liczba-warek').value) || 1;
            if (batch === 2 && numBatches === 1) {
                tr.querySelectorAll('input, select, button').forEach(el => el.disabled = true);
            }
        }

        function dodajChmiel(batch, name = '', amount = '', alpha = '', time = 60, active = true) {
            const tbody = document.getElementById(`chmiele-lista-${batch}`);
            const tr = document.createElement('tr');
            tr.className = 'hop-row';
            tr.innerHTML = `
                <input type="hidden" name="hops[${hopIndex}][batch_number]" value="${batch}">
                <td class="text-start"><input type="text" name="hops[${hopIndex}][name]" class="form-control" value="${name}"></td>
                <td><input type="number" step="0.1" name="hops[${hopIndex}][amount]" class="form-control" value="${amount}"></td>
                <td><input type="number" step="0.1" name="hops[${hopIndex}][alpha_acids]" class="form-control" value="${alpha}"></td>
                <td><input type="number" name="hops[${hopIndex}][time]" class="form-control" value="${time}"></td>
                <td class="text-center"><input type="checkbox" name="hops[${hopIndex}][active]" class="form-check-input" ${active ? 'checked' : ''}></td>
                <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('tr').remove()">X</button></td>
            `;
            tbody.appendChild(tr);
            hopIndex++;

            const numBatches = parseInt(document.getElementById('liczba-warek').value) || 1;
            if (batch === 2 && numBatches === 1) {
                tr.querySelectorAll('input, select, button').forEach(el => el.disabled = true);
            }
        }

        document.getElementById('wydajnosc').addEventListener('input', liczWszystko);
		
		@if(isset($recipe))
		@foreach($recipe->malts as $m)
        dodajSlod({{ $m->batch_number }}, "{{ $m->name }}", "{{ $m->kg }}", {{ $m->extract }}, {{ $m->is_active ? 'true' : 'false' }});
		@endforeach
		@foreach($recipe->hops as $h)
        dodajChmiel({{ $h->batch_number }}, "{{ $h->name }}", "{{ $h->amount }}", "{{ $h->alpha_acids }}", {{ $h->time }}, {{ $h->is_active ? 'true' : 'false' }});
		@endforeach
		@else
        dodajSlod(1, 'Słód Pilzneński', 100);
        dodajSlod(2, 'Słód Pilzneński', 100);
        dodajChmiel(1, '', '', '', 60, true);
        dodajChmiel(2, '', '', '', 60, true);
		@endif

            window.onload = function() {
            liczWszystko();
        };
	</script>
</x-app-layout>