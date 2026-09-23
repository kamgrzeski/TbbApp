<x-app-layout>
	
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ isset($beerwall) ? __('Edytuj piwo na kranie') : __('Dodaj nowe piwo') }}
		</h2>
	</x-slot>
	
	
	<div class="py-12">
		
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			
			<div class="bg-white shadow-sm sm:rounded-lg p-6">
				
				
				{{-- ===================================================== --}}
				{{-- FORMULARZ --}}
				{{-- ===================================================== --}}
				
				<form
						action="{{ isset($beerwall)
                        ? route('beerwall.admin.update', $beerwall)
                        : route('beerwall.admin.store') }}"
						method="POST"
				>
					
					@csrf
					
					@if(isset($beerwall))
						@method('PUT')
					@endif
					
					
					{{-- ================================================= --}}
					{{-- INFORMACJE PODSTAWOWE --}}
					{{-- ================================================= --}}
					
					<div class="mb-8">
						
						<div class="flex items-center gap-3 mb-5 pb-3 border-b border-gray-100">
							
							<div class="flex-shrink-0 w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
								
								<svg
										class="w-5 h-5 text-blue-600"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
								>
									<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M13 16h-1v-4h-1m1-4h.01M12 3a9 9 0 110 18 9 9 0 010-18z"
									/>
								</svg>
							
							</div>
							
							<div>
								
								<h3 class="font-bold text-gray-800">
									Informacje o piwie
								</h3>
								
								<p class="text-xs text-gray-500 mt-0.5">
									Podstawowe informacje wyświetlane na Beer Wall.
								</p>
							
							</div>
						
						</div>
						
						
						<div class="grid grid-cols-1 md:grid-cols-3 gap-5">
							
							
							{{-- Nazwa --}}
							<div class="md:col-span-2">
								
								<label
										for="beer_name"
										class="block text-sm font-bold text-gray-700 mb-1.5"
								>
									Nazwa piwa
									<span class="text-red-500">*</span>
								</label>
								
								<input
										id="beer_name"
										type="text"
										name="beer_name"
										value="{{ old('beer_name', $beerwall->beer_name ?? '') }}"
										placeholder="np. American IPA"
										class="w-full border-gray-300 rounded-md shadow-sm
                                           focus:border-blue-500 focus:ring-blue-500"
										required
								>
								
								@error('beer_name')
								<p class="mt-1 text-xs text-red-600">
									{{ $message }}
								</p>
								@enderror
							
							</div>
							
							
							{{-- Pozycja --}}
							<div>
								
								<label
										for="position"
										class="block text-sm font-bold text-gray-700 mb-1.5"
								>
									Pozycja / kran
								</label>
								
								<input
										id="position"
										type="text"
										name="position"
										value="{{ old('position', $beerwall->position ?? '') }}"
										placeholder="np. 3"
										class="w-full border-gray-300 rounded-md shadow-sm
                                           focus:border-blue-500 focus:ring-blue-500"
								>
								
								<p class="mt-1 text-xs text-gray-400">
									Numer lub oznaczenie kranu.
								</p>
							
							</div>
							
							
							{{-- Styl --}}
							<div class="md:col-span-3">
								
								<label
										for="beer_style"
										class="block text-sm font-bold text-gray-700 mb-1.5"
								>
									Styl piwa
								</label>
								
								<input
										id="beer_style"
										type="text"
										name="beer_style"
										value="{{ old('beer_style', $beerwall->beer_style ?? '') }}"
										placeholder="np. IPA, Pils, Porter, Sour..."
										class="w-full border-gray-300 rounded-md shadow-sm
                                           focus:border-blue-500 focus:ring-blue-500"
								>
							
							</div>
						
						</div>
					
					</div>
					
					
					{{-- ================================================= --}}
					{{-- PARAMETRY PIWA --}}
					{{-- ================================================= --}}
					
					<div class="mb-8">
						
						<div class="flex items-center gap-3 mb-5 pb-3 border-b border-gray-100">
							
							<div class="flex-shrink-0 w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center">
								
								<svg
										class="w-5 h-5 text-amber-600"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
								>
									<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M12 3v18m9-9H3"
									/>
								</svg>
							
							</div>
							
							<div>
								
								<h3 class="font-bold text-gray-800">
									Parametry piwa
								</h3>
								
								<p class="text-xs text-gray-500 mt-0.5">
									Podstawowe parametry techniczne.
								</p>
							
							</div>
						
						</div>
						
						
						<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
							
							
							{{-- BLG --}}
							<div>
								
								<label
										for="beer_blg"
										class="block text-sm font-bold text-gray-700 mb-1.5"
								>
									Ekstrakt (Blg)
								</label>
								
								<div class="relative">
									
									<input
											id="beer_blg"
											type="text"
											name="beer_blg"
											value="{{ old('beer_blg', $beerwall->beer_blg ?? '') }}"
											placeholder="np. 14"
											class="w-full border-gray-300 rounded-md shadow-sm
                                               focus:border-blue-500 focus:ring-blue-500
                                               pr-14"
									>
								</div>
							
							</div>
							
							
							{{-- ALKOHOL --}}
							<div>
								
								<label
										for="beer_alc"
										class="block text-sm font-bold text-gray-700 mb-1.5"
								>
									Alkohol (%)
								</label>
								
								<div class="relative">
									
									<input
											id="beer_alc"
											type="text"
											name="beer_alc"
											value="{{ old('beer_alc', $beerwall->beer_alc ?? '') }}"
											placeholder="np. 5.6"
											class="w-full border-gray-300 rounded-md shadow-sm
                                               focus:border-blue-500 focus:ring-blue-500
                                               pr-10"
									>
								
								</div>
							
							</div>
						
						</div>
					
					</div>
					
					
					{{-- ================================================= --}}
					{{-- CENY --}}
					{{-- ================================================= --}}
					
					<div class="mb-8">
						
						{{-- Nagłówek --}}
						<div class="flex items-center gap-3 mb-5 pb-3 border-b border-gray-100">
							
							<div class="flex-shrink-0 w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
								
								<svg
										class="w-5 h-5 text-green-600"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
								>
									<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M12 8c-1.657 0-3 .895-3 2s1.343 3 3 3 3 .895 3 2-1.343 2-3 2m0-10v2m0 8v2m9-6a9 9 0 11-18 0 9 9 0 0118 0z"
									/>
								</svg>
							
							</div>
							
							<div>
								<h3 class="font-bold text-gray-800">
									Ceny
								</h3>
								
								<p class="text-xs text-gray-400 mt-0.5">
									Ceny sprzedaży piwa.
								</p>
							</div>
						
						</div>
						
						
						{{-- Cennik --}}
						<div class="rounded-xl border border-gray-200 overflow-hidden bg-white">
							
							{{-- Nagłówek --}}
							<div class="flex items-center justify-between px-5 py-2.5 bg-gray-50 border-b border-gray-200">

        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">
            Pojemność
        </span>
								
								<span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">
            Cena (zł)
        </span>
							
							</div>
							
							
							{{-- 0.25 L --}}
							<div class="flex items-center justify-between gap-6 px-5 py-4 border-b border-gray-100">
								
								<div class="flex items-center gap-3">
									
									<div class="w-10 h-10 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center">

                <span class="text-sm font-bold text-gray-600">
                    0.25
                </span>
									
									</div>
									
									<div>
										<div class="text-sm font-semibold text-gray-800">
											0.25 L
										</div>
										
										<div class="text-[11px] text-gray-400">
											mała porcja
										</div>
									</div>
								
								</div>
								
								
								<div class="relative w-36">
									
									<input
											id="beer_price_small"
											type="number"
											step="0.01"
											min="0"
											name="beer_price_small"
											value="{{ old('beer_price_small', $beerwall->beer_price_small ?? '') }}"
											placeholder="0.00"
											class="w-full h-10
                       border-gray-200
                       bg-gray-50
                       rounded-lg
                       pr-9
                       text-right
                       font-semibold
                       text-gray-800
                       focus:bg-white
                       focus:border-green-500
                       focus:ring-green-500"
											required
									>
									
								
								</div>
							
							</div>
							
							
							{{-- 0.5 L --}}
							<div class="flex items-center justify-between gap-6 px-5 py-4 border-b border-gray-100">
								
								<div class="flex items-center gap-3">
									
									<div class="w-10 h-10 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center">

                <span class="text-sm font-bold text-blue-600">
                    0.5
                </span>
									
									</div>
									
									<div>
										<div class="text-sm font-semibold text-gray-800">
											0.5 L
										</div>
										
										<div class="text-[11px] text-gray-400">
											standardowa porcja
										</div>
									</div>
								
								</div>
								
								
								<div class="relative w-36">
									
									<input
											id="beer_price_medium"
											type="number"
											step="0.01"
											min="0"
											name="beer_price_medium"
											value="{{ old('beer_price_medium', $beerwall->beer_price_medium ?? '') }}"
											placeholder="0.00"
											class="w-full h-10
                       border-gray-200
                       bg-gray-50
                       rounded-lg
                       pr-9
                       text-right
                       font-semibold
                       text-gray-800
                       focus:bg-white
                       focus:border-green-500
                       focus:ring-green-500"
											required
									>
								
								</div>
							
							</div>
							
							
							{{-- 1.0 L --}}
							<div class="flex items-center justify-between gap-6 px-5 py-4">
								
								<div class="flex items-center gap-3">
									
									<div class="w-10 h-10 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center">

                <span class="text-sm font-bold text-gray-600">
                    1.0
                </span>
									
									</div>
									
									<div>
										<div class="text-sm font-semibold text-gray-800">
											1.0 L
										</div>
										
										<div class="text-[11px] text-gray-400">
											duża porcja
										</div>
									</div>
								
								</div>
								
								
								<div class="relative w-36">
									
									<input
											id="beer_price_large"
											type="number"
											step="0.01"
											min="0"
											name="beer_price_large"
											value="{{ old('beer_price_large', $beerwall->beer_price_large ?? '') }}"
											placeholder="0.00"
											class="w-full h-10
                       border-gray-200
                       bg-gray-50
                       rounded-lg
                       pr-9
                       text-right
                       font-semibold
                       text-gray-800
                       focus:bg-white
                       focus:border-green-500
                       focus:ring-green-500"
											required
									>
									
								
								</div>
							
							</div>
						
						</div>
					</div>
					
					
					{{-- ================================================= --}}
					{{-- OPIS --}}
					{{-- ================================================= --}}
					
					<div class="mb-8">
						
						<div class="flex items-center gap-3 mb-5 pb-3 border-b border-gray-100">
							
							<div class="flex-shrink-0 w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center">
								
								<svg
										class="w-5 h-5 text-purple-600"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
								>
									<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M4 6h16M4 12h16M4 18h10"
									/>
								</svg>
							
							</div>
							
							<div>
								
								<h3 class="font-bold text-gray-800">
									Opis piwa
								</h3>
								
								<p class="text-xs text-gray-500 mt-0.5">
									Krótki opis widoczny dla klientów.
								</p>
							
							</div>
						
						</div>
						
						
						<textarea
								name="beer_description"
								rows="4"
								maxlength="1000"
								placeholder="Np. Jasne, mocno chmielone piwo o cytrusowym aromacie..."
								class="w-full border-gray-300 rounded-md shadow-sm
                                   focus:border-blue-500 focus:ring-blue-500
                                   resize-y"
						>{{ old('beer_description', $beerwall->beer_description ?? '') }}</textarea>
					
					</div>
					
					
					{{-- ================================================= --}}
					{{-- STATUS --}}
					{{-- ================================================= --}}
					
					@php
						
						$currentStatus = old('beer_status');

						if ($currentStatus === null && isset($beerwall)) {

							if ($beerwall->is_premiere) {
								$currentStatus = 'premiere';
							} elseif ($beerwall->is_coming_soon) {
								$currentStatus = 'coming_soon';
							} elseif ($beerwall->is_ended) {
								$currentStatus = 'ended';
							} else {
								$currentStatus = 'normal';
							}

						}

						if ($currentStatus === null) {
							$currentStatus = 'normal';
						}
					
					@endphp
					
					
					<div class="mb-8">
						
						<div class="flex items-center gap-3 mb-5 pb-3 border-b border-gray-100">
							
							<div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center">
								
								<svg
										class="w-5 h-5 text-gray-600"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
								>
									<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
									/>
								</svg>
							
							</div>
							
							<div>
								
								<h3 class="font-bold text-gray-800">
									Status piwa
								</h3>
								
								<p class="text-xs text-gray-500 mt-0.5">
									Wybierz jeden status dla piwa.
								</p>
							
							</div>
						
						</div>
						
						
						<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
							
							
							{{-- NORMALNE --}}
							<label class="relative flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer
                                          transition hover:bg-gray-50
                                          {{ $currentStatus === 'normal'
                                                ? 'border-blue-500 bg-blue-50'
                                                : 'border-gray-200 bg-white' }}">
								
								<input
										type="radio"
										name="beer_status"
										value="normal"
										class="w-5 h-5 border-gray-300 text-blue-600 focus:ring-blue-500"
										{{ $currentStatus === 'normal' ? 'checked' : '' }}
								>
								
								<div>
									
									<div class="font-bold text-sm text-gray-800">
										NORMALNE
									</div>
									
									<div class="text-xs text-gray-500 mt-0.5">
										Standardowa oferta
									</div>
								
								</div>
							
							</label>
							
							
							{{-- PREMIERA --}}
							<label class="relative flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer
                                          transition hover:bg-red-50
                                          {{ $currentStatus === 'premiere'
                                                ? 'border-red-500 bg-red-50'
                                                : 'border-gray-200 bg-white' }}">
								
								<input
										type="radio"
										name="beer_status"
										value="premiere"
										class="w-5 h-5 border-gray-300 text-red-600 focus:ring-red-500"
										{{ $currentStatus === 'premiere' ? 'checked' : '' }}
								>
								
								<div>
									
									<div class="font-bold text-sm text-red-700">
										PREMIERA
									</div>
									
									<div class="text-xs text-gray-500 mt-0.5">
										Nowe piwo
									</div>
								
								</div>
							
							</label>
							
							
							{{-- WKRÓTCE --}}
							<label class="relative flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer
                                          transition hover:bg-amber-50
                                          {{ $currentStatus === 'coming_soon'
                                                ? 'border-amber-500 bg-amber-50'
                                                : 'border-gray-200 bg-white' }}">
								
								<input
										type="radio"
										name="beer_status"
										value="coming_soon"
										class="w-5 h-5 border-gray-300 text-amber-500 focus:ring-amber-500"
										{{ $currentStatus === 'coming_soon' ? 'checked' : '' }}
								>
								
								<div>
									
									<div class="font-bold text-sm text-amber-600">
										WKRÓTCE
									</div>
									
									<div class="text-xs text-gray-500 mt-0.5">
										Jeszcze niedostępne
									</div>
								
								</div>
							
							</label>
							
							
							{{-- WYPRZEDANE --}}
							<label class="relative flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer
                                          transition hover:bg-gray-50
                                          {{ $currentStatus === 'ended'
                                                ? 'border-gray-500 bg-gray-100'
                                                : 'border-gray-200 bg-white' }}">
								
								<input
										type="radio"
										name="beer_status"
										value="ended"
										class="w-5 h-5 border-gray-300 text-gray-700 focus:ring-gray-500"
										{{ $currentStatus === 'ended' ? 'checked' : '' }}
								>
								
								<div>
									
									<div class="font-bold text-sm text-gray-700">
										WYPRZEDANE
									</div>
									
									<div class="text-xs text-gray-500 mt-0.5">
										Piwo niedostępne
									</div>
								
								</div>
							
							</label>
						
						</div>
					
					</div>
					
					
					{{-- ================================================= --}}
					{{-- PRZYCISKI --}}
					{{-- ================================================= --}}
					
					<div class="pt-6 border-t border-gray-100 flex items-center justify-between gap-4">
						
						<a
								href="{{ route('beerwall.admin.index') }}"
								class="text-gray-500 hover:text-gray-700 hover:underline text-sm font-medium"
						>
							Anuluj
						</a>
						
						
						<button
								type="submit"
								class="inline-flex items-center gap-2
                                   px-6 py-3
                                   bg-blue-600
                                   border border-transparent
                                   rounded-md
                                   font-bold text-sm text-white
                                   uppercase tracking-widest
                                   hover:bg-blue-700
                                   focus:outline-none
                                   focus:ring
                                   ring-blue-300
                                   transition duration-150
                                   shadow-md"
						>
							
							@if(isset($beerwall))
								
								<svg
										class="w-4 h-4"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
								>
									<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M5 13l4 4L19 7"
									/>
								</svg>
								
								Zaktualizuj piwo
							
							@else
								
								<svg
										class="w-4 h-4"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
								>
									<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M12 4v16m8-8H4"
									/>
								</svg>
								
								+ Dodaj
							
							@endif
						
						</button>
					
					</div>
				
				</form>
			
			</div>
		
		</div>
	
	</div>

</x-app-layout>
```
