<x-app-layout>
	<x-slot name="header">
		<div class="flex justify-between items-center">
			<div>
				<h2 class="font-semibold text-xl text-gray-800 leading-tight">
					{{ __('Warki') }}
				</h2>
				
				<p class="text-sm text-gray-500 mt-1">
					Wszystkie warki w browarze
				</p>
			</div>
			
			<div class="flex gap-2">
				<a href="{{ route('brewing.create') }}"
				   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
				>
					+ Dodaj
				</a>
			</div>
		</div>
	</x-slot>
	
	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			
			@if(session('success'))
				<div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm">
					{{ session('success') }}
				</div>
			@endif
			
			@if($recipes->isEmpty())
				<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-12 text-center">
					<p class="text-gray-500 text-lg">Nie masz jeszcze żadnych zapisanych receptur.</p>
					<a href="{{ route('brewing.create') }}" class="text-blue-600 hover:underline mt-2 inline-block">Stwórz swoją pierwszą recepturę teraz!</a>
				</div>
			@else
				@php
					// Rozdzielenie na wersje robocze (bez daty zadania drożdży) oraz warki z datą
					[$draftRecipes, $pitchedRecipes] = $recipes->partition(fn($recipe) => is_null($recipe->yeast_pitched_at));
				@endphp
				
				<div class="space-y-8">
					
					{{-- SEKCJA: WERSJE ROBOCZE --}}
					@if($draftRecipes->isNotEmpty())
						<div class="bg-amber-50/40 overflow-hidden shadow-sm sm:rounded-xl border border-amber-200">
							
							{{-- Górny pasek wersji roboczych --}}
							<div class="bg-gradient-to-r from-amber-50 to-amber-100/70 px-6 py-4 border-b border-amber-200 flex justify-between items-center">
								<div class="flex items-center gap-2">
									<svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
									</svg>
									<span class="text-base font-extrabold text-amber-900 uppercase tracking-wide">
										Wersje robocze
									</span>
									<span class="text-xs font-semibold bg-amber-200 text-amber-800 px-2 py-0.5 rounded-full ml-1">
										{{ $draftRecipes->count() }} {{ $draftRecipes->count() === 1 ? 'warka' : 'warek' }}
									</span>
								</div>
								<span class="text-xs font-medium text-amber-700 bg-amber-100/80 px-2.5 py-1 rounded-md border border-amber-200">
									Brak daty zadania drożdży
								</span>
							</div>
							
							{{-- Tabela wersji roboczych --}}
							<div class="overflow-x-auto">
								<table class="min-w-full divide-y divide-amber-200/60">
									<thead class="bg-amber-50/75">
									<tr>
										<th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
											Numer warki
										</th>
										<th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
											Nazwa
										</th>
										<th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
											Objętość
										</th>
										<th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
											BLG
										</th>
										<th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
											AKC-PA
										</th>
										<th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
											Status
										</th>
										<th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
											Akcje
										</th>
									</tr>
									</thead>
									
									<tbody class="divide-y divide-gray-100 bg-white">
									@foreach($draftRecipes as $recipe)
										<tr
												onclick="window.location='{{ route('brewing.show', $recipe) }}'"
												class="group cursor-pointer transition-colors duration-150 hover:bg-amber-50/50"
										>
											{{-- Numer --}}
											<td class="px-6 py-4 whitespace-nowrap">
												@forelse($recipe->batches as $batch)
													<span class="inline-flex items-center px-2.5 py-1 rounded-md
															 bg-gray-100 text-gray-700
															 border border-gray-200
															 text-xs font-bold font-mono
															 group-hover:bg-amber-100 group-hover:text-amber-800
															 group-hover:border-amber-200 transition-colors">
														#{{ $batch->batch_number }}
													</span>
												@empty
													<span class="text-xs text-gray-400 italic">Brak</span>
												@endforelse
											</td>
											
											{{-- Nazwa --}}
											<td class="px-6 py-4">
												<div class="font-bold text-gray-900 group-hover:text-amber-800 transition-colors">
													{{ $recipe->name }}
												</div>
											</td>
											
											{{-- Objętość --}}
											<td class="px-6 py-4 whitespace-nowrap">
												<span class="text-sm font-semibold text-gray-700">
													{{ $recipe->volume ?? '—' }}
												</span>
												@if($recipe->volume)
													<span class="text-xs text-gray-400 ml-0.5">L</span>
												@endif
											</td>
											
											{{-- BLG --}}
											<td class="px-6 py-4 whitespace-nowrap">
												@if($recipe->akcpa_blg)
													<span class="inline-flex items-center px-2.5 py-1 rounded-md
																 bg-amber-50 text-amber-700
																 border border-amber-200
																 text-sm font-bold">
														{{ $recipe->akcpa_blg }}°
													</span>
												@else
													<span class="text-xs text-gray-400">—</span>
												@endif
											</td>
											
											{{-- AKCPA --}}
											<td class="px-6 py-4 whitespace-nowrap">
												@if($recipe->akcpa_value)
													<span class="inline-flex items-center px-2.5 py-1 rounded-md
																 bg-red-50 text-red-700
																 border border-red-200
																 text-sm font-bold">
														{{ $recipe->akcpa_value }}
													</span>
												@else
													<span class="text-xs text-gray-400">—</span>
												@endif
											</td>
											
											{{-- Status --}}
											<td class="px-6 py-4 whitespace-nowrap">
												<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
													Szkic
												</span>
											</td>
											
											{{-- Akcje --}}
											<td class="px-6 py-4 whitespace-nowrap text-right">
												<form
														action="{{ route('brewing.destroy', $recipe) }}"
														method="POST"
														onclick="event.stopPropagation()"
														onsubmit="return confirm('Czy na pewno chcesz usunąć recepturę: {{ $recipe->name }}?');"
														class="inline-block"
												>
													@csrf
													@method('DELETE')
													
													<button
															type="submit"
															class="inline-flex items-center gap-1.5 px-3 py-1.5
															   rounded-lg
															   bg-red-50 text-red-700
															   border border-red-200
															   text-sm font-semibold
															   hover:bg-red-600 hover:text-white hover:border-red-600
															   transition-all duration-150"
													>
														<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4v3m-7 0h10"/>
														</svg>
														Usuń
													</button>
												</form>
											</td>
										</tr>
									@endforeach
									</tbody>
								</table>
							</div>
						</div>
					@endif
					
					{{-- SEKCJA: WARKI ZREALIZOWANE POGRUPOWANE WG MIESIĘCY --}}
					@foreach($pitchedRecipes->groupBy(fn($recipe) => $recipe->yeast_pitched_at->format('Y-m')) as $month => $monthRecipes)
						
						{{-- KARTA DLA POJEDYNCZEGO MIESIĄCA --}}
						<div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
							
							{{-- Górny pasek miesiąca z podsumowaniem --}}
							<div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
								<div class="flex items-center gap-2">
									<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
									</svg>
									<span class="text-base font-extrabold text-gray-800 uppercase tracking-wide">
										{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}
									</span>
									<span class="text-xs font-semibold bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full ml-1">
										{{ $monthRecipes->count() }} {{ $monthRecipes->count() === 1 ? 'warka' : 'warek' }}
									</span>
								</div>
								
								<div class="text-sm font-bold text-red-700 bg-red-50 border border-red-200 px-3 py-1 rounded-lg shadow-sm">
									Suma AKC-PA:
									<span class="text-base ml-1">{{ number_format($monthRecipes->sum('akcpa_value'), 2, ',', ' ') }}</span>
								</div>
							</div>
							
							{{-- Tabela warek danego miesiąca --}}
							<div class="overflow-x-auto">
								<table class="min-w-full divide-y divide-gray-200">
									<thead class="bg-gray-50/75">
									<tr>
										<th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
											Numer warki
										</th>
										<th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
											Nazwa
										</th>
										<th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
											Objętość
										</th>
										<th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
											BLG
										</th>
										<th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
											AKC-PA
										</th>
										<th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
											Data
										</th>
										<th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
											Akcje
										</th>
									</tr>
									</thead>
									
									<tbody class="divide-y divide-gray-100 bg-white">
									@foreach($monthRecipes as $recipe)
										<tr
												onclick="window.location='{{ route('brewing.show', $recipe) }}'"
												class="group cursor-pointer transition-colors duration-150 hover:bg-blue-50/60"
										>
											{{-- Numer --}}
											<td class="px-6 py-4 whitespace-nowrap">
												@foreach($recipe->batches as $batch)
													<span class="inline-flex items-center px-2.5 py-1 rounded-md
															 bg-gray-100 text-gray-700
															 border border-gray-200
															 text-xs font-bold font-mono
															 group-hover:bg-blue-100 group-hover:text-blue-700
															 group-hover:border-blue-200 transition-colors">
														#{{ $batch->batch_number }}
													</span>
												@endforeach
											</td>
											
											{{-- Nazwa --}}
											<td class="px-6 py-4">
												<div class="font-bold text-gray-900 group-hover:text-blue-700 transition-colors">
													{{ $recipe->name }}
												</div>
											</td>
											
											{{-- Objętość --}}
											<td class="px-6 py-4 whitespace-nowrap">
												<span class="text-sm font-semibold text-gray-700">
													{{ $recipe->volume }}
												</span>
												<span class="text-xs text-gray-400 ml-0.5">L</span>
											</td>
											
											{{-- BLG --}}
											<td class="px-6 py-4 whitespace-nowrap">
												<span class="inline-flex items-center px-2.5 py-1 rounded-md
															 bg-amber-50 text-amber-700
															 border border-amber-200
															 text-sm font-bold">
													{{ $recipe->akcpa_blg }}°
												</span>
											</td>
											
											{{-- AKCPA --}}
											<td class="px-6 py-4 whitespace-nowrap">
												<span class="inline-flex items-center px-2.5 py-1 rounded-md
															 bg-red-50 text-red-700
															 border border-red-200
															 text-sm font-bold">
													{{ $recipe->akcpa_value }}
												</span>
											</td>
											
											{{-- Data --}}
											<td class="px-6 py-4 whitespace-nowrap">
												<div class="text-sm font-medium text-gray-700">
													{{ $recipe->yeast_pitched_at->format('d.m.Y') }}
												</div>
											</td>
											
											{{-- Akcje --}}
											<td class="px-6 py-4 whitespace-nowrap text-right">
												<form
														action="{{ route('brewing.destroy', $recipe) }}"
														method="POST"
														onclick="event.stopPropagation()"
														onsubmit="return confirm('Czy na pewno chcesz usunąć recepturę: {{ $recipe->name }}?');"
														class="inline-block"
												>
													@csrf
													@method('DELETE')
													
													<button
															type="submit"
															class="inline-flex items-center gap-1.5 px-3 py-1.5
															   rounded-lg
															   bg-red-50 text-red-700
															   border border-red-200
															   text-sm font-semibold
															   hover:bg-red-600 hover:text-white hover:border-red-600
															   transition-all duration-150"
													>
														<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4v3m-7 0h10"/>
														</svg>
														Usuń
													</button>
												</form>
											</td>
										</tr>
									@endforeach
									</tbody>
								</table>
							</div>
						</div>
					@endforeach
				</div>
			@endif
		
		</div>
	</div>
</x-app-layout>