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
					// Wersje robocze: warki bez wykonanego zasypu (oraz bez zadania drożdży)
					// Warki z datą zadania drożdży trafiają do sekcji miesięcznej
					// Data warki: zadanie drożdży, a gdy go jeszcze nie ma – wykonany zasyp
					$recipes->each(fn($recipe) => $recipe->planned_at = $recipe->yeast_pitched_at ?? $recipe->mashed_at);
					[$draftRecipes, $pitchedRecipes] = $recipes->partition(fn($recipe) => is_null($recipe->planned_at));
					
					// Zgrupowanie warek według formatu ROK-MIESIĄC (np. "2024-10")
					// Sortowanie po numerze warki (najniższy numer warki receptury), potem zgrupowanie po miesiącu
					$groupedPitched = $pitchedRecipes
						->sortByDesc(fn($recipe) => $recipe->batches->max('batch_number') ?? 0)
						->groupBy(fn($recipe) => $recipe->planned_at->format('Y-m'));
					
					// Ustalenie zakresu miesięcy: od najstarszej warki (lub początku bieżącego roku) do bieżącego miesiąca
					$startDate = $pitchedRecipes->min('planned_at')?->copy()->startOfMonth() ?? now()->startOfYear();
					$endDate = now()->startOfMonth();
					
					// Jeśli są warki zaplanowane w przyszłości, uwzględnij najdalszy miesiąc
					if ($pitchedRecipes->isNotEmpty() && $pitchedRecipes->max('planned_at')->startOfMonth()->gt($endDate)) {
						$endDate = $pitchedRecipes->max('planned_at')->copy()->startOfMonth();
					}
					
					// Generujemy listę miesięcy malejąco (od najnowszego do najstarszego)
					$monthsRange = collect();
					$cursor = $endDate->copy();
					while ($cursor->gte($startDate)) {
						$monthsRange->push($cursor->format('Y-m'));
						$cursor->subMonth();
					}
				@endphp
				
				<div class="space-y-8">
					
					{{-- SEKCJA: WERSJE ROBOCZE --}}
					@if($draftRecipes->isNotEmpty())
						<div class="bg-amber-50/40 overflow-hidden shadow-sm sm:rounded-xl border border-amber-200">
							
							{{-- Górny pasek wersji roboczych --}}
							<div class="bg-gradient-to-r from-amber-50 to-amber-100/70 px-4 sm:px-6 py-4 border-b border-amber-200 flex flex-wrap sm:flex-nowrap gap-2 sm:gap-0 justify-between items-center">
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
							</div>
							
							{{-- Karty wersji roboczych (mobile) --}}
								<div class="md:hidden divide-y divide-amber-100 bg-white">
									@foreach($draftRecipes as $recipe)
										<div
												onclick="window.location='{{ route('brewing.show', $recipe) }}'"
												class="p-4 cursor-pointer active:bg-amber-50/60"
										>
											<div class="flex justify-between items-start gap-3">
												<div class="min-w-0">
													<div class="font-bold text-gray-900 break-words">
														{{ $recipe->name }}@if($recipe->number !== null) #{{ $recipe->number }}@endif
													</div>
													<div class="mt-1.5 flex flex-wrap gap-1.5">
														@forelse($recipe->batches as $batch)
															<span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 border border-gray-200 text-xs font-bold font-mono">
																#{{ $batch->batch_number }}
															</span>
														@empty
															<span class="text-xs text-gray-400 italic">Brak numerów warek</span>
														@endforelse
													</div>
												</div>
												<div class="shrink-0 flex items-center gap-2"><span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
													Szkic
												</span>
               @include('brewing.partials.recipe-actions', ['recipe' => $recipe, 'locked' => false])</div>
											</div>

											<div class="mt-3 flex flex-wrap items-center gap-2">
												<span class="text-sm font-semibold text-gray-700">
													{{ $recipe->volume ?? '—' }}@if($recipe->volume) L @endif
												</span>
												@if($recipe->akcpa_blg)
													<span class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200 text-sm font-bold">
														BLG {{ $recipe->akcpa_blg }}°
													</span>
												@endif
												@if($recipe->akcpa_value)
													<span class="inline-flex items-center px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200 text-sm font-bold">
														AKC-PA {{ $recipe->akcpa_value }}
													</span>
												@endif
											</div>

										</div>
									@endforeach
								</div>

								{{-- Tabela wersji roboczych (desktop) --}}
							<div class="overflow-x-auto">
								<table class="hidden md:table min-w-full divide-y divide-amber-200/60">
									<thead class="bg-amber-50/75">
									<tr>
										<th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
											Numery warek
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
												class="group cursor-pointer transition-colors duration-150 hover:bg-amber-50/50 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}"
										>
											{{-- Numer --}}
											<td class="px-6 py-4 align-top">
												@forelse($recipe->batches as $batch)
													<div class="inline-block mb-1.5 last:mb-0">
														<span class="inline-flex items-center px-2.5 py-1 rounded-md
																	 bg-gray-100 text-gray-700
																	 border border-gray-200
																	 text-xs font-bold font-mono
																	 group-hover:bg-amber-100 group-hover:text-amber-800
																	 group-hover:border-amber-200 transition-colors">
															#{{ $batch->batch_number }}
														</span>
													</div>
												@empty
													<span class="text-xs text-gray-400 italic">Brak</span>
												@endforelse
											</td>
											
											{{-- Nazwa --}}
											<td class="px-6 py-4">
												<div class="font-bold text-gray-900 group-hover:text-amber-800 transition-colors">
													{{ $recipe->name }}@if($recipe->number !== null) #{{ $recipe->number }}@endif
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
																 bg-indigo-50 text-indigo-700
																 border border-indigo-200
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
                                                    @include('brewing.partials.recipe-actions', ['recipe' => $recipe, 'locked' => false])
											</td>
										</tr>
									@endforeach
									</tbody>
								</table>
							</div>
						</div>
					@endif
					
					{{-- SEKCJA: WARKI ZREALIZOWANE POGRUPOWANE WG MIESIĘCY --}}
					@foreach($monthsRange as $month)
						@php
							$monthRecipes = $groupedPitched->get($month, collect());
						@endphp
						
						{{-- KARTA DLA POJEDYNCZEGO MIESIĄCA --}}
						<div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
							
							{{-- Górny pasek miesiąca z podsumowaniem --}}
							<div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 sm:px-6 py-4 border-b border-gray-200 flex flex-wrap sm:flex-nowrap gap-2 sm:gap-0 justify-between items-center">
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
								
								<div class="text-sm font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-3 py-1 rounded-lg shadow-sm">
									Suma AKC-PA:
									<span class="text-base ml-1">{{ number_format($monthRecipes->sum('akcpa_value'), 2, ',', ' ') }}</span>
								</div>
							</div>
							
							{{-- Karty warek danego miesiąca (mobile) --}}
								<div class="md:hidden divide-y divide-gray-100 bg-white">
									@forelse($monthRecipes as $recipe)
										<div
												onclick="window.location='{{ route('brewing.show', $recipe) }}'"
												class="p-4 cursor-pointer active:bg-blue-50/60"
										>
											<div class="flex justify-between items-start gap-3">
												<div class="min-w-0">
													<div class="font-bold text-gray-900 break-words">
														{{ $recipe->name }}@if($recipe->number !== null) #{{ $recipe->number }}@endif
													</div>
													<div class="mt-1.5 flex flex-wrap gap-1.5">
														@forelse($recipe->batches as $batch)
															<span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 border border-gray-200 text-xs font-bold font-mono">
																#{{ $batch->batch_number }}
															</span>
														@empty
															<span class="text-xs text-gray-400 italic">Brak numerów warek</span>
														@endforelse
													</div>
												</div>
												<div class="shrink-0 flex items-center gap-2"><div class="text-sm font-medium text-gray-700">
													{{ ($recipe->mashed_at ?? $recipe->yeast_pitched_at)->format('d.m.Y') }}
												</div>
               @include('brewing.partials.recipe-actions', ['recipe' => $recipe, 'locked' => $recipe->planned_at->isPast()])</div>
											</div>

											<div class="mt-3 flex flex-wrap items-center gap-2">
												<span class="text-sm font-semibold text-gray-700">
													{{ $recipe->volume }} <span class="text-xs text-gray-400">L</span>
												</span>
												<span class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200 text-sm font-bold">
													BLG {{ $recipe->akcpa_blg }}°
												</span>
												<span class="inline-flex items-center px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200 text-sm font-bold">
													AKC-PA {{ $recipe->akcpa_value }}
												</span>
											</div>

										</div>
									@empty
										<div class="px-4 py-8 text-center text-sm text-gray-400 italic bg-gray-50/30">
											Brak warek w tym miesiącu
										</div>
									@endforelse
								</div>

								{{-- Tabela warek danego miesiąca (desktop) --}}
							<div class="overflow-x-auto">
								<table class="hidden md:table min-w-full divide-y divide-gray-200">
									<thead class="bg-gray-50/75">
									<tr>
										<th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
											Numery warek
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
									@forelse($monthRecipes as $recipe)
										<tr
												onclick="window.location='{{ route('brewing.show', $recipe) }}'"
												class="group cursor-pointer transition-colors duration-150 hover:bg-blue-50/60 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}"
										>
											{{-- Numer --}}
											<td class="px-6 py-4 whitespace-nowrap">
												@forelse($recipe->batches as $batch)
													<span class="inline-flex items-center px-2.5 py-1 rounded-md
															 bg-gray-100 text-gray-700
															 border border-gray-200
															 text-xs font-bold font-mono
															 group-hover:bg-blue-100 group-hover:text-blue-700
															 group-hover:border-blue-200 transition-colors">
														#{{ $batch->batch_number }}
													</span>
												@empty
													<span class="text-xs text-gray-400 italic">Brak</span>
												@endforelse
											</td>
											
											{{-- Nazwa --}}
											<td class="px-6 py-4">
												<div class="font-bold text-gray-900 group-hover:text-blue-700 transition-colors">
													{{ $recipe->name }}@if($recipe->number !== null) #{{ $recipe->number }}@endif
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
															 bg-indigo-50 text-indigo-700
															 border border-indigo-200
															 text-sm font-bold">
													{{ $recipe->akcpa_value }}
												</span>
											</td>
											
											{{-- Data --}}
											<td class="px-6 py-4 whitespace-nowrap">
												<div class="text-sm font-medium text-gray-700">
													{{ ($recipe->mashed_at ?? $recipe->yeast_pitched_at)->format('d.m.Y') }}
												</div>
											</td>
											
											{{-- Akcje --}}
											<td class="px-6 py-4 whitespace-nowrap text-right">
                                                    @include('brewing.partials.recipe-actions', ['recipe' => $recipe, 'locked' => $recipe->planned_at->isPast()])
											</td>
										</tr>
									@empty
										<tr>
											<td colspan="7" class="px-6 py-8 text-center text-sm text-gray-400 italic bg-gray-50/30">
												Brak warek w tym miesiącu
											</td>
										</tr>
									@endforelse
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