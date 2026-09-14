<x-app-layout>
	<x-slot name="header">
		<div class="flex justify-between items-center">
			<div>
				<h2 class="font-semibold text-xl text-gray-800 leading-tight">
					{{ __('Zarządzanie Beer Wall') }}
				</h2>
				
				<p class="text-sm text-gray-500 mt-1">
					Wszystkie dostępne piwa na beer wallu
				</p>
			</div>
			
			<div class="flex gap-2">
				
				<a href="{{ route('beerwall.admin.create') }}"
				   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
				>
					+  Dodaj piwo na kran
				</a>
			</div>
		</div>
	</x-slot>
	
	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			
			@if(session('success'))
				<div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm">
					{{ session('success') }}
				</div>
			@endif
			
			<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
				<div class="p-6 text-gray-900">
					@if($beers->isEmpty())
						<div class="text-center py-8">
							<p class="text-gray-500 text-lg">Karta piw jest obecnie pusta.</p>
							<a href="{{ route('beerwall.admin.create') }}" class="text-blue-600 hover:underline">Dodaj pierwsze piwo!</a>
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
								@foreach($beers as $beer)
									<tr class="hover:bg-blue-50/50 transition duration-150 {{ $beer->is_ended ? 'opacity-50' : '' }}">
										<td class="px-6 py-4">
											<div class="flex flex-col gap-1.5">
												
												{{-- Nazwa piwa --}}
												<div class="text-base font-bold text-gray-900 leading-tight">
													{{ $beer->beer_name }}
												</div>
												
												{{-- Styl --}}
												<div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-md
                         bg-amber-50 text-amber-700
                         border border-amber-200
                         text-xs font-bold uppercase tracking-wide">
                {{ $beer->beer_style }}
            </span>
												</div>
												
												{{-- Parametry --}}
												<div class="flex items-center gap-2 mt-0.5 text-xs text-gray-500">
            <span class="inline-flex items-center gap-1">
                <span class="font-semibold text-gray-700">{{ $beer->beer_blg }}°</span>
                BLG
            </span>
													
													<span class="text-gray-300">•</span>
													
													<span class="inline-flex items-center gap-1">
                <span class="font-semibold text-gray-700">{{ $beer->beer_alc }}%</span>
                alkoholu
            </span>
												</div>
											
											</div>
										</td>
										<td class="px-6 py-4 whitespace-nowrap text-center">
											<div class="flex items-center justify-center gap-2">
												
												{{-- Małe --}}
												<div class="flex flex-col items-center min-w-[52px] px-2.5 py-1.5
                    rounded-lg bg-slate-50 border border-slate-200">
            <span class="text-[10px] font-bold uppercase tracking-wide text-slate-500">
                Małe
            </span>
													<span class="text-sm font-bold text-slate-900">
                {{ $beer->beer_price_small }} zł
            </span>
												</div>
												
												{{-- Średnie --}}
												<div class="flex flex-col items-center min-w-[52px] px-2.5 py-1.5
                    rounded-lg bg-blue-50 border border-blue-200">
            <span class="text-[10px] font-bold uppercase tracking-wide text-blue-600">
                Średnie
            </span>
													<span class="text-sm font-bold text-blue-900">
                {{ $beer->beer_price_medium }} zł
            </span>
												</div>
												
												{{-- Duże --}}
												<div class="flex flex-col items-center min-w-[52px] px-2.5 py-1.5
                    rounded-lg bg-amber-50 border border-amber-200">
            <span class="text-[10px] font-bold uppercase tracking-wide text-amber-600">
                Duże
            </span>
													<span class="text-sm font-bold text-amber-900">
                {{ $beer->beer_price_large }} zł
            </span>
												</div>
											
											</div>
										</td>
										<td class="px-6 py-4 whitespace-nowrap text-center">
											<div class="flex flex-col items-center gap-2">
												
												@if($beer->is_premiere)
													<span class="inline-flex items-center px-3 py-1 rounded-full
                         text-xs font-bold uppercase
                         bg-red-100 text-red-800 border border-red-300">
                🔥 Premiera
            </span>
												@endif
												
												@if($beer->is_coming_soon)
													<span class="inline-flex items-center px-3 py-1 rounded-full
                         text-xs font-bold uppercase
                         bg-yellow-100 text-yellow-800 border border-yellow-300">
                ⏳ Wkrótce
            </span>
												@endif
												
												@if($beer->is_ended)
													<span class="inline-flex items-center px-3 py-1 rounded-full
                         text-xs font-bold uppercase
                         bg-gray-200 text-gray-800 border border-gray-400">
                ✕ Wyprzedane
            </span>
												@endif
												
												<span class="inline-flex items-center px-3 py-1 rounded-md bg-[#1b1b18]
                     text-sm font-bold
                     bg-slate-800 text-white shadow-sm">
            Pozycja&nbsp;#{{ $beer->position }}
        </span>
											
											</div>
										</td>
										<td class="px-6 py-4 whitespace-nowrap text-right">
											<div class="flex justify-end items-center gap-2">
												
												{{-- Edytuj --}}
												<a href="{{ route('beerwall.admin.edit', $beer) }}"
												   class="inline-flex items-center gap-1.5 px-3 py-2
                  rounded-lg bg-blue-50 text-blue-700
                  border border-blue-200
                  text-sm font-semibold
                  hover:bg-blue-600 hover:text-white hover:border-blue-600
                  transition-all duration-150">
													<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
														 viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round"
															  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/>
														<path stroke-linecap="round" stroke-linejoin="round"
															  d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
													</svg>
													Edytuj
												</a>
												
												{{-- Usuń --}}
												<form action="{{ route('beerwall.admin.destroy', $beer) }}"
													  method="POST"
													  onsubmit="return confirm('Usunąć {{ $beer->beer_name }} z karty?');"
													  class="inline">
													@csrf
													@method('DELETE')
													
													<button type="submit"
															class="inline-flex items-center gap-1.5 px-3 py-2
                           rounded-lg bg-red-50 text-red-700
                           border border-red-200
                           text-sm font-semibold
                           hover:bg-red-600 hover:text-white hover:border-red-600
                           transition-all duration-150">
														<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
															 viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round"
																  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h10"/>
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