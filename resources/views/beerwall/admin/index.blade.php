<x-app-layout>
	<x-slot name="header">
		<div class="flex justify-between items-center">
			<div>
				<h2 class="font-semibold text-xl text-gray-800 leading-tight">
					{{ __('Zarządzanie Beer Wall') }}
				</h2>
				<p class="text-sm text-gray-500 mt-1">
					Wszystkie dostępne piwa na beer wallu oraz zapisane wersje robocze
				</p>
			</div>
			
			<div class="flex gap-2">
				<a href="{{ route('beerwall.admin.create') }}"
				   class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
					</svg>
					Dodaj piwo
				</a>
			</div>
		</div>
	</x-slot>
	
	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
			
			@if(session('success'))
				<div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded shadow-sm flex items-center justify-between">
					<span>{{ session('success') }}</span>
				</div>
			@endif
			
			{{-- Ustawienia globalne --}}
			<div class="flex justify-end">
				<a href="{{ route('beerwall.admin.settings.edit') }}"
				   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 active:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition ease-in-out duration-150">
					<svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
						<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
					</svg>
					Ustawienia globalne
				</a>
			</div>
			
			{{-- TABELA 1: KARTA PIW (AKTYWNE) --}}
			<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
				<div class="p-6 text-gray-900">
					<div class="flex items-center justify-between mb-6">
						<div class="flex items-center gap-2.5">
							<span class="w-2.5 h-6 bg-blue-600 rounded-full"></span>
							<h3 class="text-lg font-bold text-gray-900">Karta piw</h3>
							<span class="ml-2 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
								{{ $beers->where('is_draft', false)->count() }}
							</span>
						</div>
					</div>
					
					@if($beers->where('is_draft', false)->isEmpty())
						<div class="text-center py-10 bg-gray-50 rounded-lg border border-dashed border-gray-300">
							<p class="text-gray-500 font-medium">Karta piw jest obecnie pusta.</p>
							<a href="{{ route('beerwall.admin.create') }}" class="mt-2 inline-block text-sm text-blue-600 font-semibold hover:underline">
								Dodaj pierwsze piwo do karty &rarr;
							</a>
						</div>
					@else
						<div class="overflow-x-auto">
							<table class="min-w-full divide-y divide-gray-200">
								<thead class="bg-gray-50 text-gray-500 uppercase text-xs">
								<tr>
									<th class="px-6 py-3 text-left font-bold tracking-wider">Piwo / Styl</th>
									<th class="px-6 py-3 text-left font-bold tracking-wider text-center">Ceny (S/M/L)</th>
									<th class="px-6 py-3 text-left font-bold tracking-wider text-center">Status / pozycja</th>
									<th class="px-6 py-3 text-right font-bold tracking-wider w-40">Akcje</th>
								</tr>
								</thead>
								<tbody class="bg-white divide-y divide-gray-200">
								@foreach($beers->where('is_draft', false) as $beer)
									<tr class="hover:bg-slate-50/80 transition duration-150 {{ $beer->is_ended ? 'opacity-60 bg-gray-50/50' : '' }}">
										{{-- Kolumna 1: Nazwa i parametry --}}
										<td class="px-6 py-4">
											<div class="flex flex-col gap-1.5">
												<div class="text-base font-bold text-gray-900 leading-tight">
													{{ $beer->beer_name }}
												</div>
												
												<div>
													<span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-xs font-semibold uppercase tracking-wide">
														{{ $beer->beer_style }}
													</span>
												</div>
												
												<div class="flex items-center gap-2 mt-0.5 text-xs text-gray-500">
													<span class="inline-flex items-center gap-1">
														<span class="font-semibold text-gray-700">{{ $beer->beer_blg ?? '-' }}°</span> BLG
													</span>
													<span class="text-gray-300">•</span>
													<span class="inline-flex items-center gap-1">
														<span class="font-semibold text-gray-700">{{ $beer->beer_alc ?? '-' }}%</span> alk.
													</span>
												</div>
											</div>
										</td>
										
										{{-- Kolumna 2: Ceny --}}
										<td class="px-6 py-4 whitespace-nowrap text-center">
											<div class="flex items-center justify-center gap-2">
												<div class="flex flex-col items-center min-w-[54px] px-2 py-1.5 rounded-lg bg-slate-50 border border-slate-200">
													<span class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Małe</span>
													<span class="text-sm font-bold text-slate-900">{{ $beer->beer_price_small }} zł</span>
												</div>
												<div class="flex flex-col items-center min-w-[54px] px-2 py-1.5 rounded-lg bg-blue-50 border border-blue-200">
													<span class="text-[10px] font-bold uppercase tracking-wide text-blue-600">Średnie</span>
													<span class="text-sm font-bold text-blue-900">{{ $beer->beer_price_medium }} zł</span>
												</div>
												<div class="flex flex-col items-center min-w-[54px] px-2 py-1.5 rounded-lg bg-amber-50 border border-amber-200">
													<span class="text-[10px] font-bold uppercase tracking-wide text-amber-600">Duże</span>
													<span class="text-sm font-bold text-amber-900">{{ $beer->beer_price_large }} zł</span>
												</div>
											</div>
										</td>
										
										{{-- Kolumna 3: Status / Pozycja --}}
										<td class="px-6 py-4 whitespace-nowrap text-center">
											<div class="flex flex-col items-center gap-1.5">
												@if($beer->is_premiere)
													<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase bg-red-100 text-red-800 border border-red-200">
														🔥 Premiera
													</span>
												@endif
												
												@if($beer->is_coming_soon)
													<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase bg-amber-100 text-amber-800 border border-amber-200">
														⏳ Wkrótce
													</span>
												@endif
												
												@if($beer->is_ended)
													<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase bg-gray-200 text-gray-800 border border-gray-300">
														✕ Wyprzedane
													</span>
												@endif
												
												<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-slate-800 text-white shadow-sm">
													Pozycja&nbsp;#{{ $beer->position }}
												</span>
											</div>
										</td>
										
										{{-- Kolumna 4: Akcje --}}
										<td class="px-6 py-4 whitespace-nowrap text-right">
											<div class="flex justify-end items-center gap-1.5">
												{{-- Kopiuj --}}
												<form action="{{ route('beerwall.admin.duplicate', $beer) }}" method="POST" class="inline">
													@csrf
													<button type="submit"
															title="Duplikuj piwo"
															class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 text-xs font-semibold hover:bg-indigo-600 hover:text-white transition-all duration-150">
														<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
														</svg>
														Kopiuj
													</button>
												</form>
												
												{{-- Edytuj --}}
												<a href="{{ route('beerwall.admin.edit', $beer) }}"
												   title="Edytuj piwo"
												   class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 text-xs font-semibold hover:bg-blue-600 hover:text-white transition-all duration-150">
													<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/>
														<path stroke-linecap="round" stroke-linejoin="round" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
													</svg>
													Edytuj
												</a>
												
												{{-- Usuń --}}
												<form action="{{ route('beerwall.admin.destroy', $beer) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć {{ $beer->beer_name }} z karty?');" class="inline">
													@csrf
													@method('DELETE')
													<button type="submit"
															title="Usuń piwo"
															class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 text-xs font-semibold hover:bg-red-600 hover:text-white transition-all duration-150">
														<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h10"/>
														</svg>
														Usuń
													</button>
												</form>
											</div>
										</td>
									</tr>
								@endforeach
								</tbody>
							</table>
						</div>
					@endif
				</div>
			</div>
			
			{{-- TABELA 2: SZKICE PIW --}}
			<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
				<div class="p-6 text-gray-900">
					<div class="flex items-center justify-between mb-6">
						<div class="flex items-center gap-2.5">
							<span class="w-2.5 h-6 bg-slate-400 rounded-full"></span>
							<h3 class="text-lg font-bold text-gray-900">Szkice piw</h3>
							<span class="ml-2 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-600 border border-gray-200">
								{{ $beers->where('is_draft', true)->count() }}
							</span>
						</div>
					</div>
					
					@if($beers->where('is_draft', true)->isEmpty())
						<div class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-300">
							<p class="text-gray-500 font-medium">Brak zapisanych szkiców.</p>
						</div>
					@else
						<div class="overflow-x-auto">
							<table class="min-w-full divide-y divide-gray-200">
								<thead class="bg-gray-50 text-gray-500 uppercase text-xs">
								<tr>
									<th class="px-6 py-3 text-left font-bold tracking-wider">Piwo / Styl</th>
									<th class="px-6 py-3 text-left font-bold tracking-wider text-center">Ceny (S/M/L)</th>
									<th class="px-6 py-3 text-left font-bold tracking-wider text-center">Status / pozycja</th>
									<th class="px-6 py-3 text-right font-bold tracking-wider w-40">Akcje</th>
								</tr>
								</thead>
								<tbody class="bg-white divide-y divide-gray-200">
								@foreach($beers->where('is_draft', true) as $beer)
									<tr class="hover:bg-slate-50/80 transition duration-150">
										{{-- Kolumna 1: Nazwa i parametry --}}
										<td class="px-6 py-4">
											<div class="flex flex-col gap-1.5">
												<div class="text-base font-bold text-gray-900 leading-tight">
													{{ $beer->beer_name }}
												</div>
												
												<div>
													<span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-xs font-semibold uppercase tracking-wide">
														{{ $beer->beer_style }}
													</span>
												</div>
												
												<div class="flex items-center gap-2 mt-0.5 text-xs text-gray-500">
													<span class="inline-flex items-center gap-1">
														<span class="font-semibold text-gray-700">{{ $beer->beer_blg ?? '-' }}°</span> BLG
													</span>
													<span class="text-gray-300">•</span>
													<span class="inline-flex items-center gap-1">
														<span class="font-semibold text-gray-700">{{ $beer->beer_alc ?? '-' }}%</span> alk.
													</span>
												</div>
											</div>
										</td>
										
										{{-- Kolumna 2: Ceny --}}
										<td class="px-6 py-4 whitespace-nowrap text-center">
											<div class="flex items-center justify-center gap-2">
												<div class="flex flex-col items-center min-w-[54px] px-2 py-1.5 rounded-lg bg-slate-50 border border-slate-200">
													<span class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Małe</span>
													<span class="text-sm font-bold text-slate-900">{{ $beer->beer_price_small ?? 0 }} zł</span>
												</div>
												<div class="flex flex-col items-center min-w-[54px] px-2 py-1.5 rounded-lg bg-blue-50 border border-blue-200">
													<span class="text-[10px] font-bold uppercase tracking-wide text-blue-600">Średnie</span>
													<span class="text-sm font-bold text-blue-900">{{ $beer->beer_price_medium ?? 0 }} zł</span>
												</div>
												<div class="flex flex-col items-center min-w-[54px] px-2 py-1.5 rounded-lg bg-amber-50 border border-amber-200">
													<span class="text-[10px] font-bold uppercase tracking-wide text-amber-600">Duże</span>
													<span class="text-sm font-bold text-amber-900">{{ $beer->beer_price_large ?? 0 }} zł</span>
												</div>
											</div>
										</td>
										
										{{-- Kolumna 3: Status / Pozycja --}}
										<td class="px-6 py-4 whitespace-nowrap text-center">
											<div class="flex flex-col items-center gap-1.5">
												<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-300">
													📝 Wersja robocza
												</span>
												<span class="text-xs text-gray-400 italic">
													Brak na kranie
												</span>
											</div>
										</td>
										
										{{-- Kolumna 4: Akcje --}}
										<td class="px-6 py-4 whitespace-nowrap text-right">
											<div class="flex justify-end items-center gap-1.5">
												{{-- Kopiuj --}}
												<form action="{{ route('beerwall.admin.duplicate', $beer) }}" method="POST" class="inline">
													@csrf
													<button type="submit"
															title="Duplikuj szkic"
															class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 text-xs font-semibold hover:bg-indigo-600 hover:text-white transition-all duration-150">
														<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
														</svg>
														Kopiuj
													</button>
												</form>
												
												{{-- Edytuj / Wdróż --}}
												<a href="{{ route('beerwall.admin.edit', $beer) }}"
												   title="Edytuj szkic"
												   class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 text-xs font-semibold hover:bg-blue-600 hover:text-white transition-all duration-150">
													<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/>
														<path stroke-linecap="round" stroke-linejoin="round" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
													</svg>
													Edytuj
												</a>
												
												{{-- Usuń --}}
												<form action="{{ route('beerwall.admin.destroy', $beer) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć szkic {{ $beer->beer_name }}?');" class="inline">
													@csrf
													@method('DELETE')
													<button type="submit"
															title="Usuń szkic"
															class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 text-xs font-semibold hover:bg-red-600 hover:text-white transition-all duration-150">
														<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h10"/>
														</svg>
														Usuń
													</button>
												</form>
											</div>
										</td>
									</tr>
								@endforeach
								</tbody>
							</table>
						</div>
					@endif
				</div>
			</div>
		
		</div>
	</div>
</x-app-layout>