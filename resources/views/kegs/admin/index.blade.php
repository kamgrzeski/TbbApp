<x-app-layout>
	
	<x-slot name="header">
		<div class="flex justify-between items-center">
			<div>
				<h2 class="font-semibold text-xl text-gray-800 leading-tight">
					{{ __('Zarządzanie kegami w chłodni') }}
				</h2>
				
				<p class="text-sm text-gray-500 mt-1">
					Stan wszystkich kegów w chłodni
				</p>
			</div>
			
		</div>
	</x-slot>
	
	
	<div class="py-12">
		
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			
			@if($errors->any())
				<div style="margin-bottom: 16px; padding: 12px 16px; border-left: 4px solid #dc2626; border-radius: 6px; background: #fef2f2; color: #991b1b; font-size: 0.875rem; font-weight: 600;">
					@foreach($errors->all() as $error)
						<div>{{ $error }}</div>
					@endforeach
				</div>
			@endif

			@if(session('success'))
				<div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm">
					{{ session('success') }}
				</div>
			@endif
			
			
			<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
				
				<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
					<div class="text-sm font-medium text-gray-500 uppercase">
						Wszystkie kegi
					</div>
					
					<div class="mt-2 text-3xl font-bold text-gray-800">
						{{ $summary['total'] }}
					</div>
					
					<div class="text-xs text-gray-400 mt-1">
						fizycznie w obiegu
					</div>
				</div>
				
				
				<div class="bg-white rounded-lg shadow-sm border border-green-100 p-6">
					<div class="text-sm font-medium text-gray-500 uppercase">
						Pełnych kegów w chłodni
					</div>
					
					<div class="mt-2 text-3xl font-bold text-green-600">
						{{ $summary['full'] }}
					</div>
					
					<div class="text-xs text-gray-400 mt-1">
						{{ $summary['full_liters'] }} L piwa
					</div>
				</div>
				
				
				{{-- Puste --}}
				<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
					<div class="text-sm font-medium text-gray-500 uppercase">
						Puste kegi
					</div>
					
					<div class="mt-2 text-3xl font-bold text-gray-600">
						{{ $summary['available_empty'] }}
					</div>
					
					<div class="text-xs text-gray-400 mt-1">
						dostępne do napełnienia
					</div>
				</div>
				
				
				<div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6">
					<div class="text-sm font-medium text-gray-500 uppercase">
					     Dostępne krany
					</div>
					
					<div class="mt-2 text-3xl font-bold text-blue-600">
						4
					</div>
					
					<div class="text-xs text-gray-400 mt-1">
						Dostępne krany do podczepienia kegów
					</div>
				</div>
			
			</div>
				
				<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100  mb-6">
					
					<div class="p-6 text-gray-900">
						
							<div class="flex flex-wrap justify-between items-start gap-4 mb-6">

								<div>
									<h3 class="text-lg font-bold text-gray-800">
										Krany
									</h3>

									<p class="text-sm text-gray-500 mt-1">
										Wybierz kran i podłącz do niego keg. W chłodni: {{ $summary['full'] }} pełnych kegów (50 L każdy), razem {{ number_format($summary['full_liters'], 0, ',', ' ') }} L.
									</p>
								</div>

								{{-- Dodaj keg do puli (modal) --}}
								<div x-data="{ open: false, qty: 1 }" class="inline-block">
									<button
											type="button"
											@click="open = true"
											@disabled($summary['available_empty'] <= 0)
											class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-green-600 text-white border border-green-600 text-sm font-bold shadow-sm hover:bg-green-700 hover:border-green-700 active:scale-[0.98] transition-all duration-150 disabled:bg-gray-100 disabled:text-gray-400 disabled:border-gray-200 disabled:shadow-none disabled:cursor-not-allowed"
											title="{{ $summary['available_empty'] > 0 ? 'Dodaj kegi do puli' : 'Brak wolnych kegów - 4 krany są zajęte' }}"
									>
										<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
										</svg>
										Dodaj kega
									</button>

									<template x-teleport="body">
										<div x-show="open" x-transition.opacity
											 style="position: fixed; top: 0; right: 0; bottom: 0; left: 0; z-index: 9999; align-items: center; justify-content: center; padding: 16px; background: rgba(0, 0, 0, 0.5);"
											 class="flex"
										>
											<div @click="open = false" style="position: absolute; top: 0; right: 0; bottom: 0; left: 0;"></div>

											<div @click.stop
												 style="position: relative; width: 100%; max-width: 42rem; box-sizing: border-box; padding: 24px; border-radius: 12px; background: #ffffff; box-shadow: 0 20px 25px -5px rgba(0,0,0,.2), 0 8px 10px -6px rgba(0,0,0,.2); color: #1f2937;"
											>
												<h3 style="margin: 0 0 12px; font-size: 1.125rem; font-weight: 700; color: #1f2937;">Dodaj kegi do puli</h3>

												<div style="margin-bottom: 16px; padding: 12px; border-radius: 8px; background: #f9fafb; border: 1px solid #e5e7eb; font-size: 0.875rem; line-height: 1.6; color: #374151;">
													<div><span style="color: #6b7280;">Dostępne do napełnienia (bez 4 kranów):</span> <strong>{{ $summary['available_empty'] }}</strong></div>
												</div>
													<div style="margin-bottom: 16px;">
														<div style="font-size: 0.75rem; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px;">
															Stan w chłodni &middot; pełne: {{ $summary['full'] }} &middot; puste: {{ $summary['empty'] }}
														</div>
														<div style="max-height: 240px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px;">
															<table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; color: #1f2937;">
																<thead style="position: sticky; top: 0; background: #f9fafb;">
																	<tr style="color: #6b7280; text-align: left;">
																		<th style="padding: 8px 10px; font-weight: 600; border-bottom: 1px solid #e5e7eb;">Receptura</th>
																		<th style="padding: 8px 10px; font-weight: 600; text-align: right; border-bottom: 1px solid #e5e7eb;">Pełne</th>
																		<th style="padding: 8px 10px; font-weight: 600; text-align: right; border-bottom: 1px solid #e5e7eb;">Litry</th>
																		<th style="padding: 8px 10px; font-weight: 600; border-bottom: 1px solid #e5e7eb;">Kran</th>
																	</tr>
																</thead>
																<tbody>
																	@forelse($coolerStocks as $coolerStock)
																		@php
																			$coolerTaps = $stocksMovements->where('recipe_id', $coolerStock->recipe_id)->pluck('tap_number')->sort()->implode(', ');
																			$coolerEmpty = $coolerStock->full_kegs <= 0;
																		@endphp
																		<tr style="border-bottom: 1px solid #f3f4f6;">
																			<td style="padding: 8px 10px;">{{ $coolerStock->recipe->name }} #{{ $coolerStock->recipe->number }}</td>
																			<td style="padding: 8px 10px; text-align: right; font-weight: 700; color: {{ $coolerEmpty ? '#dc2626' : '#111827' }};">{{ $coolerStock->full_kegs }}</td>
																			<td style="padding: 8px 10px; text-align: right; color: #374151;">{{ number_format($coolerStock->full_kegs * \App\Services\KegStockService::KEG_CAPACITY_LITERS, 0, ',', ' ') }} L</td>
																			<td style="padding: 8px 10px; color: #374151;">{{ $coolerTaps !== '' ? $coolerTaps : '—' }}</td>
																		</tr>
																	@empty
																		<tr>
																			<td colspan="4" style="padding: 12px 10px; text-align: center; color: #6b7280;">Brak kegów w chłodni.</td>
																		</tr>
																	@endforelse
																</tbody>
															</table>
														</div>
													</div>

												<form method="POST" action="{{ route('kegs.admin.pool.store') }}">
													@csrf

													<label style="display: block; margin-bottom: 4px; font-size: 0.875rem; font-weight: 600; color: #374151;">Receptura</label>
													<select name="recipe_id" required
															style="display: block; width: 100%; box-sizing: border-box; margin-bottom: 12px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.875rem; background: #ffffff;">
														@foreach($recipes as $recipe)
															<option value="{{ $recipe->id }}">
																Warka {{ $recipe->number }} — {{ $recipe->name }}
																| Zbiornik {{ $recipe->tank_number }}
																| {{ number_format($recipe->volume, 0, ',', ' ') }} L
																| {{ $recipe->blg }}°Blg
															</option>
														@endforeach
													</select>

													<label style="display: block; margin-bottom: 4px; font-size: 0.875rem; font-weight: 600; color: #374151;">Ilość kegów (50 L każdy)</label>
													<input
															type="number"
															name="quantity"
															x-model.number="qty"
															min="1"
															max="{{ max(1, min(20, $summary['available_empty'])) }}"
															style="display: block; width: 100%; box-sizing: border-box; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.875rem;"
													>

													<div style="margin-top: 12px; font-size: 0.875rem; line-height: 1.6; color: #374151;">
														<div>Dodasz: <strong x-text="Number(qty) || 0"></strong> kega = <strong x-text="(Number(qty) || 0) * 50"></strong> L</div>
													</div>

													<div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
														<button type="button" @click="open = false"
																style="padding: 8px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #ffffff; color: #374151; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
															Anuluj
														</button>
														<button type="submit"
																style="padding: 8px 16px; border: 0; border-radius: 8px; background: #16a34a; color: #ffffff; font-size: 0.875rem; font-weight: 700; cursor: pointer;">
															Dodaj <span x-text="Number(qty) || 0"></span>
														</button>
													</div>
												</form>
											</div>
										</div>
									</template>
								</div>
							</div>

							@php
								$connectedTaps = $stocksMovements->keyBy('tap_number');
								// Piwo już podłączone do jakiegoś kranu nie może być wydane na kolejny kran.
								$connectedRecipeIds = $stocksMovements->pluck('recipe_id');
								$issuable = $stocks
									->where('is_archived', 0)
									->where('full_kegs', '>', 0)
									->reject(fn ($stock) => $connectedRecipeIds->contains($stock->recipe_id));
							@endphp

							{{-- Krany --}}
							<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
								@for ($tap = 0; $tap < \App\Services\KegStockService::TAPS_COUNT; $tap++)
									@php $connected = $connectedTaps->get($tap + 1); @endphp

									<div class="rounded-xl p-5 flex flex-col relative {{ $connected ? 'bg-white border-2 border-blue-200 shadow-sm' : 'bg-gray-50 border-2 border-dashed border-gray-300' }}">

											@if($connected)
												<div class="absolute top-3 right-3" x-data="{ open: false }" @click.outside="open = false">
													<button type="button" @click="open = !open" aria-label="Akcje kranu {{ $tap + 1 }}"
													        class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
														<svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="12" cy="19" r="1.8"/></svg>
													</button>

													<div x-show="open" x-transition x-cloak
													     class="absolute right-0 mt-1 w-48 z-20 rounded-xl bg-white border border-gray-200 shadow-lg overflow-hidden divide-y divide-gray-100">
														<form method="POST" action="{{ route('kegs.admin.remove.store') }}"
														      onsubmit="return confirm('Odjąć 1 keg z tego piwa w chłodni?')">
															@csrf
															<input type="hidden" name="recipe_id" value="{{ $connected->recipe_id }}">
															<input type="hidden" name="quantity" value="1">
															<button type="submit"
															        @disabled(($connected->stock?->full_kegs ?? 0) <= 0)
															        class="w-full text-left px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50 disabled:text-gray-400 disabled:hover:bg-white disabled:cursor-not-allowed"
															        title="{{ ($connected->stock?->full_kegs ?? 0) > 0 ? 'Odejmij 1 keg' : 'Brak pełnych kegów' }}">
																Odejmij 1 keg
															</button>
														</form>
													</div>
												</div>
											@endif
										<div class="flex justify-between items-center pr-10">
											<span class="text-xs font-extrabold text-gray-500 uppercase tracking-wider">
												Kran {{ $tap + 1 }}
											</span>

											@if($connected)
												<span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold">
													Podłączony
												</span>
											@else
												<span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 border border-gray-200 text-xs font-semibold">
													Wolny
												</span>
											@endif
										</div>

										@if($connected)
											<div class="mt-3 font-bold text-gray-900 text-base">
												{{ $connected->recipe->name }} #{{ $connected->recipe->number }}
											</div>

											@php
												$connectedFullKegs = $connected->stock?->full_kegs ?? 0;
												$connectedLiters = $connectedFullKegs * \App\Services\KegStockService::KEG_CAPACITY_LITERS;
												// 0 L → czerwony, poniżej 200 L → pomarańczowy, reszta → niebieski
												$litersTone = match (true) {
													$connectedLiters <= 0 => 'bg-red-600',
													$connectedLiters < 200 => 'bg-orange-500',
													default => 'bg-blue-600',
												};
											@endphp

											<div class="mt-4 rounded-lg {{ $litersTone }} px-4 py-3 text-white">
												<div class="flex items-baseline justify-between gap-2">
													<span class="text-2xl sm:text-3xl font-bold tabular-nums leading-none">
														{{ $connectedLiters }} L
													</span>
													<span class="text-xs font-semibold text-blue-100 whitespace-nowrap">
														{{ $connected->stock?->full_kegs ?? 0 }} kegów w chłodni
													</span>
												</div>
											</div>

											@else
												<div class="mt-3 text-sm text-gray-500">
													Brak podłączonego kega.
												</div>
											@endif

											@php
												$tapLabel = $tap + 1;
												$connectedEmpty = $connected && ($connected->stock?->full_kegs ?? 0) <= 0;
												// Wolny kran albo piwo z kranu skończyło się w chłodni: można wybrać inne piwo.
												$canSelect = ! $connected || $connectedEmpty;
												$confirmMsg = match (true) {
													$connectedEmpty => "To ostatnia sztuka kega. Odłączyć ją z kranu {$tapLabel} i podłączyć wybrane piwo?",
													$connected => "Kran {$tapLabel} ma już podłączony keg. Podłączyć kolejny keg tego piwa?",
													default => "Czy na pewno chcesz podłączyć keg pod kran {$tapLabel}?",
												};
											@endphp

											<form
													method="POST"
													action="{{ route('kegs.admin.issue.store') }}"
													onsubmit="return confirm(this.dataset.confirm)"
													data-confirm="{{ $confirmMsg }}"
													class="mt-auto pt-4 space-y-2"
											>
												@csrf
												<input type="hidden" name="quantity" value="1">
												<input type="hidden" name="tap_number" value="{{ $tap + 1 }}">
												@if($connected && ! $connectedEmpty)<input type="hidden" name="recipe_id" value="{{ $connected->recipe_id }}">@endif

												@if($connectedEmpty)
													<div class="rounded-lg bg-amber-50 border border-amber-200 px-3 py-2 text-xs font-semibold text-amber-800">
														To ostatnia sztuka kega tego piwa. Po zmianie warki zostanie odłączona z kranu.
													</div>
												@endif

												@if($canSelect && $issuable->isNotEmpty())
													<select name="recipe_id" required
															class="block w-full rounded-lg border-gray-300 text-sm">
														@foreach($issuable as $stock)
															<option value="{{ $stock->recipe_id }}">
																{{ $stock->recipe->name }} #{{ $stock->recipe->number }} ({{ $stock->full_kegs }} szt.)
															</option>
														@endforeach
													</select>
												@endif

												<button
														type="submit"
														@disabled($canSelect && $issuable->isEmpty())
														class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600 text-white border border-emerald-600 text-sm font-bold shadow-sm hover:bg-emerald-700 hover:border-emerald-700 active:scale-[0.98] transition-all duration-150 disabled:bg-gray-100 disabled:text-gray-400 disabled:border-gray-200 disabled:shadow-none disabled:cursor-not-allowed"
														title="{{ $connectedEmpty ? 'Odłącz puste piwo i podłącz wybrane' : ($connected ? 'Podłącz kolejny keg tego piwa' : ($issuable->isNotEmpty() ? 'Podłącz keg do kranu ' . ($tap + 1) : 'Brak pełnych kegów w chłodni')) }}"
												>
													{{ $connectedEmpty ? 'Zmień warkę' : ($connected ? 'Podłącz kolejny keg' : 'Podłącz kega') }}
												</button>

												@if($canSelect && $issuable->isEmpty())
													<p class="text-xs text-gray-400">
														Brak pełnych kegów w chłodni.
													</p>
												@endif
											</form>

									</div>
								@endfor
							</div>
					
					</div>
				
				</div>
				
				<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 mb-6">
					<div class="p-6 text-gray-900">
						<div class="flex justify-between items-center mb-6">
							<div>
								<h3 class="text-lg font-bold text-gray-800">
									Ostatnie podłączenia pod krany
								</h3>
								<p class="text-sm text-gray-500 mt-1">
									Lista ostatnich podłączeń piwa do kranu.
								</p>
							</div>
						</div>
						
						<div class="overflow-x-auto">
							<table class="min-w-full divide-y divide-gray-200 text-sm">
								<thead class="bg-gray-50 text-gray-500 uppercase text-[11px]">
								<tr>
									<th class="px-4 py-2.5 text-left font-bold tracking-wider">
						Kran
					</th>
					<th class="px-4 py-2.5 text-left font-bold tracking-wider">
										Piwo
									</th>
									<th class="px-4 py-2.5 text-left font-bold tracking-wider w-64">
										Pojemność
									</th>
									<th class="px-4 py-2.5 text-right font-bold tracking-wider">
										Podłączono
									</th>
									<th class="px-4 py-2.5 text-right font-bold tracking-wider">
										Akcja
									</th>
								</tr>
								</thead>
								
								<tbody class="bg-white divide-y divide-gray-100">
								@forelse($recentConnections as $movement)
									@php
										$maxCapacity = \App\Services\KegStockService::KEG_MAX_ML;
										// Obliczanie procentu z zabezpieczeniem od 0% do 100%
										$percentage = min(100, max(0, round(($movement->capacity / $maxCapacity) * 100)));
									@endphp
									
									<tr class="hover:bg-blue-50/50 transition-colors duration-100">
										{{-- Kran --}}
										<td class="px-4 py-3 whitespace-nowrap">
											@if($movement->tap_number)
												<span class="font-semibold text-gray-800">Kran {{ $movement->tap_number }}</span>
											@else
												<span class="text-gray-400">—</span>
											@endif
						</td>

						{{-- Piwo --}}
										<td class="px-4 py-3">
											<div class="font-semibold text-gray-800">
												<a href="{{ route('brewing.show', $movement->recipe) }}" class="hover:text-blue-600">
													{{ $movement->recipe->name }} #{{ $movement->recipe->number }}
												</a>
											</div>
										</td>
										
										{{-- Pojemność (Progress Bar) --}}
										<td class="px-4 py-3">
											<div class="w-full">
												<div class="flex justify-between items-center mb-1 text-xs">
                                        <span class="font-semibold text-gray-700">
                                            {{ number_format($movement->capacity, 0, ',', ' ') }} / {{ number_format(\App\Services\KegStockService::KEG_MAX_ML, 0, ',', ' ') }} ml
                                        </span>
													<span class="font-medium {{ $percentage > 20 ? 'text-blue-600' : 'text-amber-600' }}">
                                            {{ $percentage }}%
                                        </span>
												</div>
												<div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
													<div
															class="h-2 rounded-full transition-all duration-300 {{ $percentage > 20 ? 'bg-blue-600' : 'bg-amber-500' }}"
															style="width: {{ $percentage }}%;"
													></div>
												</div>
											</div>
										</td>
										
										{{-- Data --}}
										<td class="px-4 py-3 text-right whitespace-nowrap">
											<div class="text-sm text-gray-600">
												{{ $movement->created_at->format('d.m.Y H:i') }}
											</div>
											<div class="text-[11px] text-gray-400">
												{{ $movement->created_at->diffForHumans() }}
											</div>
										</td>

										{{-- Akcja --}}
										<td class="px-4 py-3 text-right whitespace-nowrap">
											<div x-data="{ open: false, ml: {{ (int) $movement->capacity }}, take: 1, sub(litres) { this.ml = Math.max(0, this.ml - Math.round((Number(litres) || 0) * 1000)); } }" class="inline-block">
	<button
			type="button"
			@click="open = true"
			class="px-3 py-1.5 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700 transition-colors"
	>
		Zmień ml
	</button>

	<template x-teleport="body">
		<div x-show="open" x-transition.opacity
			 style="position: fixed; top: 0; right: 0; bottom: 0; left: 0; z-index: 9999; align-items: center; justify-content: center; padding: 16px; background: rgba(0, 0, 0, 0.5);"
			 class="flex"
		>
			<div @click="open = false" style="position: absolute; top: 0; right: 0; bottom: 0; left: 0;"></div>

			<div @click.stop
				 style="position: relative; width: 100%; max-width: 26rem; box-sizing: border-box; padding: 24px; border-radius: 12px; background: #ffffff; box-shadow: 0 20px 25px -5px rgba(0,0,0,.2), 0 8px 10px -6px rgba(0,0,0,.2); color: #1f2937;"
			>
				<h3 style="margin: 0 0 12px; font-size: 1.125rem; font-weight: 700; color: #1f2937;">Zmień ilość piwa w kegu</h3>

				<div style="margin-bottom: 16px; padding: 12px; border-radius: 8px; background: #f9fafb; border: 1px solid #e5e7eb; font-size: 0.875rem; line-height: 1.6; color: #374151;">
					<div><span style="color: #6b7280;">Piwo:</span> <strong>{{ $movement->recipe->name }} #{{ $movement->recipe->number }}</strong></div>
					<div><span style="color: #6b7280;">Podłączono:</span> {{ $movement->created_at->format('d.m.Y H:i') }}</div>
					<div><span style="color: #6b7280;">Obecnie:</span> <strong>{{ number_format($movement->capacity, 0, ',', ' ') }} ml</strong></div>
				</div>

				<div style="margin-bottom: 16px; padding: 12px; border-radius: 8px; background: #eff6ff; border: 1px solid #bfdbfe;">
					<div style="margin-bottom: 8px; font-size: 0.8rem; font-weight: 700; color: #1e40af;">Odejmij pobraną ilość</div>
					<div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px;">
						<template x-for="l in [0.25, 0.5, 1, 2, 3]" :key="l">
							<button type="button" @click="sub(l)"
									style="padding: 6px 10px; border: 1px solid #93c5fd; border-radius: 6px; background: #ffffff; color: #1e40af; font-size: 0.8rem; font-weight: 700; cursor: pointer;">
								−<span x-text="l"></span> l
							</button>
						</template>
					</div>
					<div style="display: flex; gap: 8px; align-items: center;">
						<input type="number" x-model.number="take" min="0" step="0.05"
							   style="flex: 1; min-width: 0; box-sizing: border-box; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.85rem;">
						<span style="font-size: 0.85rem; color: #374151;">l</span>
						<button type="button" @click="sub(take)"
								style="padding: 6px 12px; border: 0; border-radius: 6px; background: #1d4ed8; color: #ffffff; font-size: 0.8rem; font-weight: 700; cursor: pointer;">
							Odejmij
						</button>
					</div>
				</div>

				<form method="POST" action="{{ route('kegs.admin.movements.update', $movement) }}">
					@csrf
					@method('PATCH')

					<label style="display: block; margin-bottom: 4px; font-size: 0.875rem; font-weight: 600; color: #374151;">Ilość w kegu (ml), maks. {{ number_format(\App\Services\KegStockService::KEG_MAX_ML, 0, ',', ' ') }}</label>
					<input
							type="number"
							name="capacity"
							x-model.number="ml"
							min="0"
							max="{{ \App\Services\KegStockService::KEG_MAX_ML }}"
							style="display: block; width: 100%; box-sizing: border-box; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.875rem;"
					>

					<div style="margin-top: 12px; font-size: 0.875rem; line-height: 1.6; color: #374151;">
						<div>Po zapisie: <strong x-text="(Number(ml) || 0).toLocaleString('pl-PL')"></strong> ml
							(<strong x-text="Math.min(100, Math.max(0, Math.round(((Number(ml) || 0) / {{ \App\Services\KegStockService::KEG_MAX_ML }}) * 100)))"></strong>%)</div>
					</div>

					<div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
						<button type="button" @click="open = false"
								style="padding: 8px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: #ffffff; color: #374151; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
							Anuluj
						</button>
						<button type="submit"
								style="padding: 8px 16px; border: 0; border-radius: 8px; background: #2563eb; color: #ffffff; font-size: 0.875rem; font-weight: 700; cursor: pointer;">
							Zapisz
						</button>
					</div>
				</form>
			</div>
		</div>
	</template>
</div>


										</td>
									</tr>
								@empty
									<tr>
										<td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">
											Brak historii podłączeń.
										</td>
									</tr>
								@endforelse
								</tbody>
							</table>
						</div>
					</div>
				</div>
			
		</div>
	
	</div>

</x-app-layout>
