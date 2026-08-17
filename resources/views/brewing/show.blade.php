<x-app-layout>
	
	<style>
        @media print {
            /* Ukrywamy wszystko co nie jest słodami, chmielami i parametrami */
            .no-print,
            nav,
            button,
            form,
            .status-section,
            .monitoring-section,
            .journal-section {
                display: none !important;
            }

            /* Reset marginesów dla strony A4 */
            @page {
                size: A4;
                margin: 0.5cm;
            }

            body {
                background: white !important;
                color: black !important;
            }

            /* Wymuszamy, żeby wszystko było ciasno na jednej stronie */
            .py-12 { padding-top: 0 !important; padding-bottom: 0 !important; }
            .max-w-7xl { max-width: 100% !important; }
            .space-y-8 > * + * { margin-top: 0.5rem !important; }

            /* Zmniejszenie czcionek tylko w druku */
            h2 { font-size: 1.5rem !important; }
            h3 { font-size: 1rem !important; }
            table { font-size: 9pt !important; }
            .p-4, .p-6 { padding: 0.25rem !important; }

            /* Upewnienie się, że tła (kolory sekcji) się drukują */
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
	</style>
	
	<x-slot name="header">
		<div class="flex no-print justify-between items-center">
			<div>
				<nav class="text-sm font-medium text-gray-500 mb-1 no-print">
					<a href="{{ route('brewing.index') }}" class="hover:text-blue-600 transition">Receptury</a> / Detale
				</nav>
				<h2 class="font-bold text-2xl text-gray-800 leading-tight">
					{{ $recipe->name }}
				</h2>
			</div>
			<div class="flex space-x-3 no-print">
				<a href="{{ route('brewing.clone', $recipe) }}"
				   class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 shadow-md transition">
					Klonuj Recepturę
				</a>
				
				<a href="{{ route('brewing.edit', $recipe) }}"
				   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 shadow-md transition">
					Edytuj Recepturę
				</a>
				
				<a href="{{ route('brewing.print', $recipe) }}" target="_blank"
				   class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 shadow-md transition">
					Drukuj (Widok A4)
				</a>
				
				<a href="{{ route('brewing.index') }}"
				   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
					Powrót
				</a>
			</div>
		</div>
	</x-slot>
	
	@php
		// Obliczenia na serwerze dla widoku podglądu
		$totalKg = $recipe->malts->sum('kg');
        $w1TotalKg = $recipe->malts->where('batch_number', 1)->sum('kg');
        $w2TotalKg = $recipe->malts->where('batch_number', 2)->sum('kg');

		$totalExtractKg = $recipe->malts->where('is_active', true)->where('batch_number', 1)->sum(fn($m) => $m->kg * ($m->extract / 100));
		$blg = $recipe->volume > 0 ? ($totalExtractKg / ($recipe->volume / 2) * 100 * (($recipe->efficiency) / 100)) : 0;
		$brix = $blg * 1.04 - 0.04;

        $formatDuration = function($start, $end = null) {
            if (!$start) return null;
            $end = $end ?: now();
            $diff = $start->diff($end);
            
            $parts = [];
            if ($diff->days > 0) $parts[] = $diff->days . 'd';
            if ($diff->h > 0) $parts[] = $diff->h . 'h';
            if ($diff->i > 0 || empty($parts)) $parts[] = $diff->i . 'm';
            
            return implode(' ', $parts);
        };
	@endphp
	
	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
			
			<!-- HARMONOGRAM I STATUS Warki -->
			<div class="bg-white overflow-hidden shadow-md sm:rounded-xl border border-gray-100 mb-8 no-print">
				<div class="bg-gray-800 px-6 py-3 flex justify-between items-center text-white">
					<h3 class="text-sm font-bold uppercase tracking-widest flex items-center">
						<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
						Harmonogram i Status Warki
					</h3>
					@if($recipe->yeast_pitched_at && $recipe->finished_at)
						<span class="text-green-400 text-xs font-bold uppercase italic">Całkowity czas: {{ $formatDuration($recipe->yeast_pitched_at, $recipe->finished_at) }}</span>
					@endif
				</div>
				<div class="p-6">
					<div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 text-center">
						
						<div class="p-4 border rounded-xl {{ $recipe->yeast_pitched_at ? 'bg-amber-50 border-amber-200' : 'bg-gray-50 border-dashed' }}">
							<p class="text-[12px] font-bold text-gray-400 uppercase mb-2">Zadanie drożdży</p>
							@if($recipe->yeast_pitched_at)
								<p class="text-amber-700 font-black text-lg leading-none">{{ $recipe->yeast_pitched_at->format('d.m.Y H:i') }}</p>
							@else
								<form action="{{ route('brewing.status', $recipe) }}" method="POST">
									@csrf
									<input type="hidden" name="type" value="yeast">
									<button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-[12px] font-bold py-2 px-3 rounded-lg transition uppercase">Zadaj drożdże</button>
								</form>
							@endif
						</div>
						
						<div class="p-4 border rounded-xl {{ $recipe->primary_fermentation_start ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-dashed' }}">
							<p class="text-[12px] font-bold text-gray-400 uppercase mb-2">Fermentacja burzliwa</p>
							@if($recipe->primary_fermentation_start)
								<p class="text-blue-700 font-black text-lg leading-none">{{ $recipe->primary_fermentation_start->format('d.m.Y H:i') }}</p>
								<div class="mt-2 inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-[12px] font-bold">
									@if($recipe->secondary_fermentation_start)
										Trwała: {{ $formatDuration($recipe->primary_fermentation_start, $recipe->secondary_fermentation_start) }}
									@else
										Trwa: {{ $formatDuration($recipe->primary_fermentation_start) }}
									@endif
								</div>
							@else
								<form action="{{ route('brewing.status', $recipe) }}" method="POST">
									@csrf
									<input type="hidden" name="type" value="primary">
									<button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-[12px] font-bold py-2 px-3 rounded-lg transition {{ !$recipe->yeast_pitched_at ? 'opacity-40' : '' }}" {{ !$recipe->yeast_pitched_at ? 'disabled' : '' }}>START BURZLIWA</button>
								</form>
							@endif
						</div>
						
						<div class="p-4 border rounded-xl {{ $recipe->secondary_fermentation_start ? 'bg-purple-50 border-purple-200' : 'bg-gray-50 border-dashed' }}">
							<p class="text-[12px] font-bold text-gray-400 uppercase mb-2">Fermentacja cicha</p>
							@if($recipe->secondary_fermentation_start)
								<p class="text-purple-700 font-black text-lg leading-none">{{ $recipe->secondary_fermentation_start->format('d.m.Y H:i') }}</p>
								<div class="mt-2 inline-block px-2 py-1 bg-purple-100 text-purple-800 rounded text-[12px] font-bold">
									@if($recipe->finished_at)
										Trwała: {{ $formatDuration($recipe->secondary_fermentation_start, $recipe->finished_at) }}
									@else
										Trwa: {{ $formatDuration($recipe->secondary_fermentation_start) }}
									@endif
								</div>
							@else
								<form action="{{ route('brewing.status', $recipe) }}" method="POST">
									@csrf
									<input type="hidden" name="type" value="secondary">
									<button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white text-[12px] font-bold py-2 px-3 rounded-lg transition {{ !$recipe->primary_fermentation_start ? 'opacity-40' : '' }}" {{ !$recipe->primary_fermentation_start ? 'disabled' : '' }}>START CICHA</button>
								</form>
							@endif
						</div>
						
						<div class="p-4 border rounded-xl {{ $recipe->finished_at ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-dashed' }}">
							<p class="text-[12px] font-bold text-gray-400 uppercase mb-2">Koniec</p>
							@if($recipe->finished_at)
								<p class="text-green-700 font-black text-lg leading-none">{{ $recipe->finished_at->format('d.m.Y H:i') }}</p>
								<p class="text-green-600 text-[12px] font-bold mt-1 uppercase italic">Gotowe</p>
							@else
								<form action="{{ route('brewing.status', $recipe) }}" method="POST">
									@csrf
									<input type="hidden" name="type" value="finish">
									<button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-[12px] font-bold py-2 px-3 rounded-lg transition {{ !$recipe->secondary_fermentation_start ? 'opacity-40' : '' }}" {{ !$recipe->secondary_fermentation_start ? 'disabled' : '' }}>ZAKOŃCZ</button>
								</form>
							@endif
						</div>
					</div>
				</div>
			</div>

			<!-- KLUCZOWE PARAMETRY -->
			<div class="bg-white overflow-hidden shadow-md sm:rounded-xl border border-gray-100">
				<div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
					<h3 class="text-lg font-bold text-gray-800 flex items-center">
						<svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
						Kluczowe parametry warki - {{ $recipe->name }} - Numer tanka: {{ $recipe->tank_number }}
					</h3>
				</div>
				
				<div class="p-4 md:p-6 no-print">
					<div class="grid grid-cols-3 gap-2 md:gap-6">
						<div class="relative group p-3 md:p-5 bg-gradient-to-br from-blue-50 to-white border border-blue-100 rounded-xl md:rounded-2xl transition-all shadow-sm">
							<p class="text-[12px] md:text-xs font-black text-blue-600 uppercase tracking-tighter md:tracking-wider mb-1">Objętość</p>
							<div class="flex items-baseline">
								<span class="text-xl md:text-3xl font-black text-gray-900">{{ $recipe->volume }}</span>
								<span class="ml-1 text-sm md:text-xl font-bold text-gray-500">L</span>
							</div>
						</div>
						<div class="relative group p-3 md:p-5 bg-gradient-to-br from-emerald-50 to-white border border-emerald-100 rounded-xl md:rounded-2xl transition-all shadow-sm">
							<p class="text-[12px] md:text-xs font-black text-emerald-600 uppercase tracking-tighter md:tracking-wider mb-1">Wydajność</p>
							<div class="flex items-baseline">
								<span class="text-xl md:text-3xl font-black text-gray-900">{{ $recipe->efficiency }}</span>
								<span class="ml-1 text-sm md:text-xl font-bold text-gray-500">%</span>
							</div>
						</div>
						<div class="relative group p-3 md:p-5 bg-gradient-to-br from-amber-50 to-white border border-amber-100 rounded-xl md:rounded-2xl transition-all shadow-sm">
							<p class="text-[12px] md:text-xs font-black text-amber-600 uppercase tracking-tighter md:tracking-wider mb-1">Gęstość</p>
							<div class="flex flex-col">
                                <span class="text-lg md:text-2xl font-black text-gray-900 leading-none">
                                    {{ number_format($blg, 1) }}<small class="text-[12px] md:text-sm font-bold text-gray-400 ml-0.5">°Blg</small>
                                </span>
								<span class="text-[9px] md:text-xs font-bold text-amber-500 mt-1">
                                    {{ number_format($brix, 1) }}°Brix
                                </span>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<!-- ZASYP SŁODÓW - WARKA 1 -->
			<div class="bg-white shadow-md sm:rounded-xl overflow-hidden border border-gray-100">
				<div class="bg-blue-50 border-b border-blue-100 px-6 py-4">
					<h3 class="font-bold text-blue-800 uppercase tracking-widest text-sm">Zasyp Warka 1 (500L)</h3>
				</div>
				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200">
						<thead class="bg-gray-50/50 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">
						<tr>
							<th class="px-6 py-3 text-left">Nazwa słodu</th>
							<th class="px-6 py-3">Masa (kg)</th>
							<th class="px-6 py-3 text-orange-600 italic">Worki (25kg)</th>
							<th class="px-6 py-3">Ekstrakt</th>
							<th class="px-6 py-3">Udział %</th>
						</tr>
						</thead>
						<tbody class="bg-white divide-y divide-gray-200 text-center">
						@foreach($recipe->malts->where('batch_number', 1) as $malt)
							<tr>
								<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-left">{{ $malt->name }}</td>
								<td class="px-6 py-4 text-sm font-bold">{{ number_format($malt->kg, 2) }} kg</td>
								<td class="px-6 py-4 text-sm font-bold text-orange-600">{{ ceil($malt->kg / 25) }} szt.</td>
								<td class="px-6 py-4 text-sm text-gray-500">{{ $malt->extract }} %</td>
								<td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                        {{ $w1TotalKg > 0 ? number_format(($malt->kg / $w1TotalKg) * 100, 1) : 0 }} %
                                    </span>
								</td>
							</tr>
						@endforeach
						</tbody>
						<tfoot class="bg-gray-50">
						<tr>
							<td class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Suma Warka 1:</td>
							<td class="px-6 py-3 text-center font-black text-gray-900">{{ number_format($w1TotalKg, 2) }} kg</td>
							<td colspan="3" class="px-6 py-3 text-sm text-blue-700 font-bold italic">Łącznie worków: {{ ceil($w1TotalKg / 25) }} szt.</td>
						</tr>
						</tfoot>
					</table>
				</div>
			</div>
			
			<!-- ZASYP SŁODÓW - WARKA 2 -->
			<div class="bg-white shadow-md sm:rounded-xl overflow-hidden border border-gray-100">
				<div class="bg-blue-50 border-b border-blue-100 px-6 py-4">
					<h3 class="font-bold text-blue-800 uppercase tracking-widest text-sm">Zasyp Warka 2 (500L)</h3>
				</div>
				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200">
						<thead class="bg-gray-50/50 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">
						<tr>
							<th class="px-6 py-3 text-left">Nazwa słodu</th>
							<th class="px-6 py-3">Masa (kg)</th>
							<th class="px-6 py-3 text-orange-600 italic">Worki (25kg)</th>
							<th class="px-6 py-3">Ekstrakt</th>
							<th class="px-6 py-3">Udział %</th>
						</tr>
						</thead>
						<tbody class="bg-white divide-y divide-gray-200 text-center">
						@foreach($recipe->malts->where('batch_number', 2) as $malt)
							<tr>
								<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-left">{{ $malt->name }}</td>
								<td class="px-6 py-4 text-sm font-bold">{{ number_format($malt->kg, 2) }} kg</td>
								<td class="px-6 py-4 text-sm font-bold text-orange-600">{{ ceil($malt->kg / 25) }} szt.</td>
								<td class="px-6 py-4 text-sm text-gray-500">{{ $malt->extract }} %</td>
								<td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                        {{ $w2TotalKg > 0 ? number_format(($malt->kg / $w2TotalKg) * 100, 1) : 0 }} %
                                    </span>
								</td>
							</tr>
						@endforeach
						</tbody>
						<tfoot class="bg-gray-50">
						<tr>
							<td class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Suma Warka 1:</td>
							<td class="px-6 py-3 text-center font-black text-gray-900">{{ number_format($w2TotalKg, 2) }} kg</td>
							<td colspan="3" class="px-6 py-3 text-sm text-blue-700 font-bold italic">Łącznie worków: {{ ceil($w2TotalKg / 25) }} szt.</td>
						</tr>
						</tfoot>
					</table>
				</div>
			</div>
			
			<!-- SEKCJA: CHMIELENIE (PODZIAŁ NA WARKI) -->
			@foreach($recipe->hops->groupBy('batch_number') as $batch => $hops)
				<div class="bg-white shadow-md sm:rounded-xl overflow-hidden border border-gray-100 mb-6">
					<div class="bg-amber-600 border-b border-amber-700 px-6 py-3 flex justify-between items-center text-white">
						<h3 class="font-bold uppercase tracking-widest text-xs">Chmielenie - Warka {{ $batch }}</h3>
						<span class="text-[10px] bg-amber-700 px-2 py-1 rounded">{{ $hops->sum('amount') }} g</span>
					</div>
					<div class="overflow-x-auto">
						<table class="min-w-full divide-y divide-gray-200 text-center text-sm">
							<thead class="bg-amber-50 text-[10px] font-bold text-amber-700 uppercase">
							<tr>
								<th class="px-6 py-2 text-left">Chmiel</th>
								<th class="px-6 py-2">Ilość</th>
								<th class="px-6 py-2">Alfa-kwasy</th>
								<th class="px-6 py-2">Czas</th>
							</tr>
							</thead>
							<tbody class="bg-white divide-y divide-gray-200">
							@foreach($hops as $hop)
								<tr class="{{ $hop->is_active ? '' : 'opacity-50 grayscale' }}">
									<td class="px-6 py-3 text-left">{{ $hop->name }}</td>
									<td class="px-6 py-3 font-bold">{{ number_format($hop->amount, 1) }} g</td>
									<td class="px-6 py-3">{{ $hop->alpha_acids }} %</td>
									<td class="px-6 py-3 text-center">
										<span class="px-2 py-1 rounded bg-amber-100 text-amber-800 font-bold">{{ $hop->time }} min</span>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			@endforeach
			
			<!-- MONITORING ODFERMENTOWANIA -->
			<div class="bg-white shadow-md sm:rounded-xl overflow-hidden border border-gray-100 mb-8 no-print">
				<div class="bg-blue-900 px-6 py-4 flex items-center justify-between">
					<h3 class="text-white font-bold flex items-center">
						<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
						Poziom odfermentowania BLG
					</h3>
					@php
						$latestReading = $recipe->gravityReadings->first();
						$attenuation = ($blg > 0 && $latestReading) ? (($blg - $latestReading->value) / $blg) * 100 : 0;
                        
                        $og = $blg;
						$fg = $latestReading ? $latestReading->value : $og;

                        $abv = ($og > 0) ? ($og - $fg) / 1.938 : 0;
					
					@endphp
					<span class="text-white text-xs uppercase tracking-widest font-bold">Poziom odfermentowania - {{ number_format($attenuation, 1) }}%</span>
					<span class="text-white text-xs uppercase tracking-widest font-bold">Alkohol -  {{ number_format($abv, 2) }}%</span>
				</div>
				
				<div class="p-6">
					<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
						<!-- Formularz dodawania pomiaru -->
						<div class="md:col-span-1 bg-gray-50 p-4 rounded-xl border border-gray-200">
							<form action="{{ route('gravity.store', $recipe) }}" method="POST">
								@csrf
								<label class="block text-xs font-bold text-gray-500 uppercase mb-2">Nowy pomiar BLG</label>
								<div class="flex space-x-2">
									<input type="number" name="value" step="0.01" required
										   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
										   placeholder="np. 3.50">
									<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-bold text-sm">
										+
									</button>
								</div>
								<p class="text-[10px] text-gray-400 mt-2 italic">Zapisz aktualną gęstość brzeczki w tanku.</p>
							</form>
						</div>
						
						<!-- Lista pomiarów -->
						<div class="md:col-span-3">
							<div class="flex flex-wrap gap-4">
								@forelse($recipe->gravityReadings as $reading)
									<div class="bg-white border border-blue-100 rounded-xl p-3 shadow-sm flex items-center space-x-4">
										<div class="bg-blue-50 rounded-lg p-2">
											<span class="text-blue-800 font-black text-lg">{{ number_format($reading->value, 2) }}</span>
											<span class="text-blue-400 text-xs font-bold">°Blg</span>
										</div>
										<div>
											<p class="text-[10px] text-gray-400 uppercase font-bold">{{ $reading->created_at->format('d.m.Y') }}</p>
											<p class="text-xs font-medium text-gray-600">{{ $reading->created_at->format('H:i') }}</p>
										</div>
									</div>
								@empty
									<div class="w-full text-center py-4 text-gray-400 italic border-2 border-dashed border-gray-50 rounded-xl">
										Brak pomiarów odfermentowania.
									</div>
								@endforelse
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<!-- DZIENNIK WARZENIA -->
			<div class="bg-white shadow-md sm:rounded-xl overflow-hidden border border-gray-100 no-print">
				<div class="bg-gray-800 px-6 py-4 flex items-center justify-between">
					<h3 class="text-white font-bold flex items-center uppercase tracking-widest text-xs">Dziennik warzenia & Notatki</h3>
					<span class="text-gray-400 text-[10px] font-bold">{{ $recipe->comments->count() }} WPISÓW</span>
				</div>
				
				<div class="p-6">
					<form action="{{ route('comments.store', $recipe) }}" method="POST" class="mb-8 no-print">
						@csrf
						<textarea name="body" rows="3" required class="w-full border-gray-200 rounded-xl p-4 text-sm focus:border-blue-500 focus:ring-1" placeholder="Dodaj notatkę z procesu..."></textarea>
						<div class="mt-3 flex justify-end">
							<button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold text-xs hover:bg-blue-700 transition shadow-lg">DODAJ WPIS</button>
						</div>
					</form>
					
					<div class="space-y-6">
						@forelse($recipe->comments as $comment)
							<div class="flex items-start space-x-4">
								<div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center font-bold text-gray-500 text-sm shadow-inner">{{ strtoupper(substr($comment->user->name, 0, 1)) }}</div>
								<div class="flex-1 bg-gray-50 border border-gray-100 rounded-2xl p-4 relative shadow-sm">
									<div class="flex items-center justify-between mb-2">
										<h4 class="text-sm font-bold text-gray-900">{{ $comment->user->name }}</h4>
										@php
											$hours = (int) $comment->created_at->diffInHours(now());
											$days  = (int) $comment->created_at->diffInDays(now());
										@endphp
										
										<time class="text-[10px] font-bold text-gray-400">
											{{ $comment->created_at->format('d.m.Y H:i') }}
											(
											{{ $hours }}
											{{ $hours == 1 ? 'godzina' : ($hours >= 2 && $hours <= 4 ? 'godziny' : 'godzin') }} /
											{{ $days }}
											{{ $days == 1 ? 'dzień' : 'dni' }} temu
											)
										</time>
									</div>
									<p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">{{ $comment->body }}</p>
								</div>
							</div>
						@empty
							<div class="text-center py-8 text-gray-400 italic">Brak wpisów.</div>
						@endforelse
					</div>
				</div>
			</div>
		</div>
	</div>
</x-app-layout>