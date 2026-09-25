<x-app-layout>
	
	<x-slot name="header">
		<div class="flex justify-between items-center">
			<div>
				<h2 class="font-semibold text-xl text-gray-800 leading-tight">
					{{ __('Produkty GoPos') }}
				</h2>
				
				<p class="text-sm text-gray-500 mt-1">
					Lista produktów zsynchronizowanych z systemu GoPos
				</p>
			</div>
			
			<div class="flex gap-2">
				<a
						href="{{ route('gopos.admin.items.sync') }}"
						class="inline-flex items-center gap-2 px-4 py-2
                           bg-blue-600 border border-transparent
                           rounded-md font-semibold text-xs text-white
                           uppercase tracking-widest
                           hover:bg-blue-700
                           active:bg-blue-900
                           focus:outline-none
                           focus:border-blue-900
                           focus:ring ring-blue-300
                           transition ease-in-out duration-150"
				>
					<svg
							class="w-4 h-4"
							fill="none"
							stroke="currentColor"
							stroke-width="2"
							viewBox="0 0 24 24"
					>
						<path
								stroke-linecap="round"
								stroke-linejoin="round"
								d="M4 4v5h5"
						/>
						<path
								stroke-linecap="round"
								stroke-linejoin="round"
								d="M20 20v-5h-5"
						/>
						<path
								stroke-linecap="round"
								stroke-linejoin="round"
								d="M5.5 9A7 7 0 0117.5 5.5L20 9"
						/>
						<path
								stroke-linecap="round"
								stroke-linejoin="round"
								d="M18.5 15A7 7 0 016.5 18.5L4 15"
						/>
					</svg>
					
					Synchronizuj
				</a>
			</div>
		</div>
	</x-slot>
	
	
	<div class="py-12">
		
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			
			{{-- SUCCESS --}}
			@if(session('success'))
				<div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm">
					{{ session('success') }}
				</div>
			@endif
			
			
			{{-- ERROR --}}
			@if(session('error'))
				<div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
					{{ session('error') }}
				</div>
			@endif
			
			
			{{-- PODSUMOWANIE --}}
			<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
				
				{{-- Wszystkie --}}
				<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
					
					<div class="text-sm font-medium text-gray-500 uppercase">
						Wszystkie produkty
					</div>
					
					<div class="mt-2 text-3xl font-bold text-gray-800">
						{{ $items->count() }}
					</div>
					
					<div class="text-xs text-gray-400 mt-1">
						produktów w GoPos
					</div>
				
				</div>
				
				
				{{-- Aktywne --}}
				<div class="bg-white rounded-lg shadow-sm border border-green-100 p-6">
					
					<div class="text-sm font-medium text-gray-500 uppercase">
						Aktywne
					</div>
					
					<div class="mt-2 text-3xl font-bold text-green-600">
						{{ $items->where('gopos_status', 'ENABLED')->count() }}
					</div>
					
					<div class="text-xs text-gray-400 mt-1">
						dostępne produkty
					</div>
				
				</div>
				
				
				{{-- Nieaktywne --}}
				<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
					
					<div class="text-sm font-medium text-gray-500 uppercase">
						Nieaktywne
					</div>
					
					<div class="mt-2 text-3xl font-bold text-gray-600">
						{{ $items->where('gopos_status', '!=', 'ENABLED')->count() }}
					</div>
					
					<div class="text-xs text-gray-400 mt-1">
						wyłączone produkty
					</div>
				
				</div>
				
				
				{{-- Piwa lane --}}
				<div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6">
					
					<div class="text-sm font-medium text-gray-500 uppercase">
						Piwa lane
					</div>
					
					<div class="mt-2 text-3xl font-bold text-blue-600">
						{{ $items->where('gopos_catgory_id', 2)->count() }}
					</div>
					
					<div class="text-xs text-gray-400 mt-1">
						produkty z kategorii piw lanych
					</div>
				
				</div>
			
			</div>
			
			
			{{-- TABELA PRODUKTÓW --}}
			<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 mb-6">
				
				<div class="p-6 text-gray-900">
					
					<div class="flex justify-between items-center mb-6">
						
						<div>
							<h3 class="text-lg font-bold text-gray-800">
								Produkty GoPos
							</h3>
							
							<p class="text-sm text-gray-500 mt-1">
								Lista produktów pobranych z systemu GoPos.
							</p>
						</div>
					
					</div>
					
					
					@if($items->isEmpty())
						
						<div class="text-center py-10">
							
							<div class="text-4xl mb-3">
								🍺
							</div>
							
							<p class="text-gray-500 text-lg">
								Brak produktów.
							</p>
							
							<p class="text-sm text-gray-400 mt-1">
								Wykonaj synchronizację z GoPos.
							</p>
						
						</div>
					
					@else
						
						<div class="overflow-x-auto">
							
							<table class="min-w-full">
								
								<thead>
								
								<tr class="border-b-2 border-gray-200 bg-gray-50">
									
									<th class="px-6 py-4 text-left text-xs font-extrabold text-gray-600 uppercase tracking-wider">
										ID GoPos
									</th>
									
									<th class="px-6 py-4 text-left text-xs font-extrabold text-gray-600 uppercase tracking-wider">
										Produkt
									</th>
									
									<th class="px-6 py-4 text-center text-xs font-extrabold text-gray-600 uppercase tracking-wider">
										Kategoria
									</th>
									
									<th class="px-6 py-4 text-center text-xs font-extrabold text-gray-600 uppercase tracking-wider">
										Cena
									</th>
									
									<th class="px-6 py-4 text-center text-xs font-extrabold text-gray-600 uppercase tracking-wider">
										Status
									</th>
									
									<th class="px-6 py-4 text-right text-xs font-extrabold text-gray-600 uppercase tracking-wider">
										Aktualizacja
									</th>
								
								</tr>
								
								</thead>
								
								
								<tbody class="divide-y divide-gray-100 bg-white">
								
								@foreach($items as $item)
									
									<tr class="group transition-colors duration-150 hover:bg-blue-50">
										
										{{-- ID --}}
										<td class="px-6 py-4 whitespace-nowrap">

                                            <span class="inline-flex items-center px-2.5 py-1
                                                         rounded-md
                                                         bg-gray-100
                                                         text-gray-700
                                                         border border-gray-200
                                                         text-xs font-mono font-bold">
                                                {{ $item->gopos_id }}
                                            </span>
										
										</td>
										
										
										{{-- NAZWA --}}
										<td class="px-6 py-4">
											
											<div class="font-bold text-gray-900 text-base">
												{{ $item->gopos_name }}
											</div>
										
										</td>
										
										
										{{-- KATEGORIA --}}
										<td class="px-6 py-4 text-center whitespace-nowrap">
											
											@if($item->gopos_catgory_id == 2)
												
												<span class="inline-flex items-center px-3 py-1.5
                                                             rounded-lg
                                                             bg-blue-50 text-blue-700
                                                             border border-blue-200
                                                             text-sm font-bold">

                                                    🍺 Piwa lane

                                                </span>
											
											@else
												
												<span class="inline-flex items-center px-3 py-1.5
                                                             rounded-lg
                                                             bg-gray-50 text-gray-600
                                                             border border-gray-200
                                                             text-sm font-semibold">

                                                    #{{ $item->gopos_catgory_id }}

                                                </span>
											
											@endif
										
										</td>
										
										
										{{-- CENA --}}
										<td class="px-6 py-4 text-center whitespace-nowrap">

                                            <span class="inline-flex items-center px-3 py-1.5
                                                         rounded-lg
                                                         bg-slate-50 text-slate-700
                                                         border border-slate-200
                                                         text-sm font-bold">

                                                {{ number_format($item->gopos_price, 2, ',', ' ') }} zł

                                            </span>
										
										</td>
										
										
										{{-- STATUS --}}
										<td class="px-6 py-4 text-center whitespace-nowrap">
											
											@if($item->gopos_status === 'ENABLED')
												
												<span class="inline-flex items-center gap-1.5
                                                             px-3 py-1.5
                                                             rounded-lg
                                                             bg-emerald-50
                                                             text-emerald-700
                                                             border border-emerald-200
                                                             text-sm font-bold">

                                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>

                                                    Aktywny

                                                </span>
											
											@else
												
												<span class="inline-flex items-center gap-1.5
                                                             px-3 py-1.5
                                                             rounded-lg
                                                             bg-gray-100
                                                             text-gray-500
                                                             border border-gray-200
                                                             text-sm font-semibold">

                                                    <span class="w-2 h-2 rounded-full bg-gray-400"></span>

                                                    {{ $item->gopos_status }}

                                                </span>
											
											@endif
										
										</td>
										
										
										{{-- AKTUALIZACJA --}}
										<td class="px-6 py-4 text-right whitespace-nowrap">
											
											<div class="text-sm text-gray-600">
												{{ $item->updated_at?->format('d.m.Y H:i') }}
											</div>
											
											@if($item->updated_at)
												
												<div class="text-[11px] text-gray-400">
													{{ $item->updated_at->diffForHumans() }}
												</div>
											
											@endif
										
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