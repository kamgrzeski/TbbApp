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
				<div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm">
					{{ session('success') }}
				</div>
			@endif
			
			<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
				<div class="p-6 text-gray-900">
					@if($recipes->isEmpty())
						<div class="text-center py-8">
							<p class="text-gray-500 text-lg">Nie masz jeszcze żadnych zapisanych receptur.</p>
							<a href="{{ route('brewing.create') }}" class="text-blue-600 hover:underline">Stwórz swoją pierwszą recepturę teraz!</a>
						</div>
					@else
						<div class="overflow-x-auto">
							<div class="overflow-x-auto">
								<table class="min-w-full">
									<thead>
									<tr class="border-b-2 border-gray-200 bg-gray-50">
										<th class="px-6 py-4 text-left text-xs font-extrabold text-gray-600 uppercase tracking-wider">
											Numer warki
										</th>
										
										<th class="px-6 py-4 text-left text-xs font-extrabold text-gray-600 uppercase tracking-wider">
											Nazwa
										</th>
										
										<th class="px-6 py-4 text-left text-xs font-extrabold text-gray-600 uppercase tracking-wider">
											Objętość
										</th>
										
										<th class="px-6 py-4 text-left text-xs font-extrabold text-gray-600 uppercase tracking-wider">
											Ilość warek
										</th>
										
										<th class="px-6 py-4 text-left text-xs font-extrabold text-gray-600 uppercase tracking-wider">
											BLG
										</th>
										
										<th class="px-6 py-4 text-left text-xs font-extrabold text-gray-600 uppercase tracking-wider">
											AKC-PA
										</th>
										
										<th class="px-6 py-4 text-left text-xs font-extrabold text-gray-600 uppercase tracking-wider">
											Data
										</th>
										
										<th class="px-6 py-4 text-right text-xs font-extrabold text-gray-600 uppercase tracking-wider">
											Akcje
										</th>
									</tr>
									</thead>
									
									<tbody class="divide-y divide-gray-100 bg-white">
									
									@foreach($recipes->groupBy(fn($recipe) => $recipe->yeast_pitched_at->format('Y-m')) as $month => $monthRecipes)
										
										{{-- Nagłówek miesiąca --}}
										<tr class="bg-gray-100 border-y border-gray-200">
											<td colspan="9" class="px-6 py-3">
												<div class="flex justify-between items-center">
													
													<div class="text-sm font-extrabold text-gray-700 uppercase tracking-wider">
														{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}
													</div>
													
													<div class="text-sm font-bold text-red-700">
														Suma AKC-PA:
														{{ number_format($monthRecipes->sum('akcpa_value'), 2, ',', ' ') }}
													</div>
												
												</div>
											</td>
										</tr>
										
										@foreach($monthRecipes as $recipe)
											
											<tr
													onclick="window.location='{{ route('brewing.show', $recipe) }}'"
													class="group cursor-pointer transition-colors duration-150 hover:bg-blue-50"
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
													<span class="text-xs text-gray-400 ml-0.5">
                    L
                </span>
												</td>
												
												{{-- Ilość warek --}}
												<td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center justify-center min-w-[32px] h-7 px-2
                             rounded-md bg-slate-100 text-slate-700
                             border border-slate-200
                             text-sm font-bold">
                    {{ $recipe->batch_count }}
                </span>
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
                             bg-danger-50 text-danger-700
                             border border-danger-200
                             text-sm font-bold">
                    {{ $recipe->akcpa_value }}
                </span>
												</td>
												
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
																class="inline-flex items-center gap-1.5 px-3 py-2
                               rounded-lg
                               bg-red-50 text-red-700
                               border border-red-200
                               text-sm font-semibold
                               hover:bg-red-600 hover:text-white hover:border-red-600
                               transition-all duration-150"
														>
															<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
																 viewBox="0 0 24 24">
																<path stroke-linecap="round" stroke-linejoin="round"
																	  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4v3m-7 0h10"/>
															</svg>
															Usuń
														</button>
													</form>
												</td>
											
											</tr>
										
										@endforeach
									
									@endforeach
									
									</tbody>
								</table>
							</div>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</x-app-layout>