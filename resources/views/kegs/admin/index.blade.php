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
			
			<div class="flex gap-2">
				
				<a
						href="{{ route('kegs.admin.production.create') }}"
				   		class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
				>
					+ Zgłoś napełnione kegi
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
						{{ $summary['empty'] - 4 }}
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
						
						<div class="flex justify-between items-center mb-6">
							
							<div>
								<h3 class="text-lg font-bold text-gray-800">
									Aktualny stan pełnych kegów w chłodni
								</h3>
								
								<p class="text-sm text-gray-500 mt-1">
									Każdy keg ma pojemność 50 litrów. Kegi są napełnione i oznaczone.
								</p>
							</div>
						
						</div>
						
						
						@if($stocks->where('is_archived', 0)->isEmpty())
							
							<div class="text-center py-10">
								
								<div class="text-4xl mb-3">
									🍺
								</div>
								
								<p class="text-gray-500 text-lg">
									Brak pełnych kegów.
								</p>
								
								<a
										href="{{ route('kegs.admin.production.create') }}"
										class="inline-block mt-3 text-green-600 hover:underline font-medium"
								>
									Zgłoś napełnione kegi
								</a>
							
							</div>
						
						@else
							
							<div class="overflow-x-auto">
								
								<div class="overflow-x-auto">
									<table class="min-w-full">
										<thead>
										<tr class="border-b-2 border-gray-200 bg-gray-50">
											
											<th class="px-6 py-4 text-left text-xs font-extrabold text-gray-600 uppercase tracking-wider">
												Data rozlewu
											</th>
											
											<th class="px-6 py-4 text-left text-xs font-extrabold text-gray-600 uppercase tracking-wider">
												Warka
											</th>
											
											<th class="px-6 py-4 text-center text-xs font-extrabold text-gray-600 uppercase tracking-wider">
												Pełne kegi
											</th>
											
											<th class="px-6 py-4 text-center text-xs font-extrabold text-gray-600 uppercase tracking-wider">
												Ilość piwa
											</th>
											
											<th class="px-6 py-4 text-right text-xs font-extrabold text-gray-600 uppercase tracking-wider">
												Akcja
											</th>
										
										</tr>
										</thead>
										
										<tbody class="divide-y divide-gray-100 bg-white">
										
										@foreach($stocks->where('is_archived', 0) as $stock)
											
											<tr class="group transition-colors duration-150 hover:bg-blue-50">
												
												{{-- Data rozlewu --}}
												<td class="px-6 py-4 whitespace-nowrap">
													
													<div class="text-sm font-semibold text-gray-800">
														{{ $stock->created_at->format('d.m.Y') }}
													</div>
													
													<div class="text-xs text-gray-400 mt-0.5">
														{{ \Carbon\Carbon::parse($stock->created_at)->diffForHumans() }}
													</div>
												
												</td>
												
												
												{{-- Warka --}}
												<td class="px-6 py-4">
													
													<div class="font-bold text-gray-900 text-base">
														{{ $stock->recipe->name }}
													</div>
												
												</td>
												
												
												{{-- Pełne kegi --}}
												<td class="px-6 py-4 text-center whitespace-nowrap">
													
													@if($stock->full_kegs > 0)
														
														<span class="inline-flex items-center justify-center
															 min-w-[42px] px-3 py-1.5
															 rounded-lg
															 bg-emerald-50 text-emerald-700
															 border border-emerald-200
															 text-sm font-bold">
						
															{{ $stock->full_kegs }}
								
														</span>
													
													@else
														
														<span class="inline-flex items-center justify-center
																 px-3 py-1.5
																 rounded-lg
																 bg-gray-100 text-gray-500
																 border border-gray-200
																 text-sm font-semibold">
							
														0
							
													</span>
													
													@endif
												
												</td>
												
												
												{{-- Ilość piwa --}}
												<td class="px-6 py-4 text-center whitespace-nowrap">

                    <span class="inline-flex items-center px-3 py-1.5
                                 rounded-lg
                                 bg-slate-50 text-slate-700
                                 border border-slate-200
                                 text-sm font-bold">

                        {{ $stock->full_kegs * 50 }} L

                    </span>
												
												</td>
												
												
												{{-- Akcja --}}
												<td class="px-6 py-4 text-right whitespace-nowrap">
													
													<div class="inline-flex items-center gap-2">
														
														{{-- Podłącz pod kran --}}
														<form
																method="POST"
																action="{{ route('kegs.admin.issue.store') }}"
																onsubmit="return confirm('Czy na pewno chcesz podłączyć keg pod kran?')"
																class="inline-block"
														>
															@csrf
															
															<input
																	type="hidden"
																	name="recipe_id"
																	value="{{ $stock->recipe_id }}"
															>
															
															<input
																	type="hidden"
																	name="quantity"
																	value="1"
															>
															
															<button
																	type="submit"
																	@disabled($stock->full_kegs <= 0)
																	class="inline-flex items-center gap-2
                    px-4 py-2.5
                    rounded-lg
                    bg-blue-600 text-white
                    border border-blue-600
                    text-sm font-bold
                    shadow-sm
                    hover:bg-blue-700
                    hover:border-blue-700
                    active:scale-[0.98]
                    transition-all duration-150
                    disabled:bg-gray-100
                    disabled:text-gray-400
                    disabled:border-gray-200
                    disabled:shadow-none
                    disabled:cursor-not-allowed"
																	title="{{ $stock->full_kegs > 0 ? 'Podłącz 1 keg pod kran' : 'Brak pełnych kegów' }}"
															>
																
																<svg class="w-4 h-4"
																	 fill="none"
																	 stroke="currentColor"
																	 stroke-width="2"
																	 viewBox="0 0 24 24">
																	<path
																			stroke-linecap="round"
																			stroke-linejoin="round"
																			d="M6 9h12M8 9V6a4 4 0 018 0v3M12 9v6m-4 0h8m-7 0v3m6-3v3"/>
																	<path
																			stroke-linecap="round"
																			stroke-linejoin="round"
																			d="M5 21h14"/>
																</svg>
																
																Podłącz
															
															</button>
														
														</form>
														
														
														{{-- Dodaj keg do puli --}}
														<form
																method="POST"
																action="{{ route('kegs.admin.pool.store') }}"
																onsubmit="return confirm('Czy na pewno chcesz dodać keg do puli?')"
																class="inline-block"
														>
															@csrf
															
															<input
																	type="hidden"
																	name="recipe_id"
																	value="{{ $stock->recipe_id }}"
															>
															
															<input
																	type="hidden"
																	name="quantity"
																	value="1"
															>
															
															<button
																	type="submit"
																	@disabled($stock->full_kegs <= 0)
																	class="inline-flex items-center gap-2
                    px-4 py-2.5
                    rounded-lg
                    bg-green-600 text-white
                    border border-green-600
                    text-sm font-bold
                    shadow-sm
                    hover:bg-green-700
                    hover:border-green-700
                    active:scale-[0.98]
                    transition-all duration-150
                    disabled:bg-gray-100
                    disabled:text-gray-400
                    disabled:border-gray-200
                    disabled:shadow-none
                    disabled:cursor-not-allowed"
																	title="{{ $stock->full_kegs > 0 ? 'Dodaj 1 keg do puli' : 'Brak pełnych kegów' }}"
															>
																
																<svg class="w-4 h-4"
																	 fill="none"
																	 stroke="currentColor"
																	 stroke-width="2"
																	 viewBox="0 0 24 24">
																	<path
																			stroke-linecap="round"
																			stroke-linejoin="round"
																			d="M12 5v14M5 12h14"/>
																</svg>
																
																Dodaj
															
															</button>
														
														</form>
													
													</div>
												
												</td>
											
											</tr>
										
										@endforeach
										
										</tbody>
									
									</table>
								</div>
							</div>
						
						@endif
					
					</div>
				
				</div>
				
				<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100  mb-6">
					
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
										Piwo
									</th>
									
									<th class="px-4 py-2.5 text-right font-bold tracking-wider">
										Podłączono
									</th>
								
								</tr>
								</thead>
								
								<tbody class="bg-white divide-y divide-gray-100">
								
								@forelse($stocksMovements as $movement)
									
									<tr class="hover:bg-blue-50/50 transition-colors duration-100">
										
										{{-- Piwo --}}
										<td class="px-4 py-2.5">
											
											<div class="font-semibold text-gray-800">
												{{ $movement->recipe->name }}
											</div>
											
											<div class="text-xs text-gray-400">
												Warka #{{ $movement->recipe->number }}
											</div>
										
										</td>
										
										{{-- Data --}}
										<td class="px-4 py-2.5 text-right whitespace-nowrap">
											
											<div class="text-sm text-gray-600">
												{{ $movement->created_at->format('d.m.Y H:i') }}
											</div>
											
											<div class="text-[11px] text-gray-400">
												{{ $movement->created_at->diffForHumans() }}
											</div>
										
										</td>
									
									</tr>
								
								@empty
									
									<tr>
										<td colspan="2" class="px-4 py-8 text-center text-sm text-gray-400">
											Brak historii podłączeń.
										</td>
									</tr>
								
								@endforelse
								
								</tbody>
							
							</table>						</div>
					
					</div>
				
				</div>
				
				<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100  mb-6">
					
					<div class="p-6 text-gray-900">
						
						<div class="flex justify-between items-center mb-6">
							
							<div>
								<h3 class="text-lg font-bold text-gray-800">
									Stan archiwalny rozlewów
								</h3>
								
								<p class="text-sm text-gray-500 mt-1">
									Lista do podgladu archiwalnych uzupełnien i wykorzystań kegów.
								</p>
							</div>
						
						</div>
						
						
						@if($stocks->where('is_archived', 1)->isEmpty())
							
							<div class="text-center py-10">
								
								<p class="text-gray-500 text-lg">
									Brak zarchiwizowanych rozlewów.
								</p>
							
							</div>
						
						@else
							
							<div class="overflow-x-auto">
								
								<table class="min-w-full divide-y divide-gray-200">
									
									<thead class="bg-gray-50 text-gray-500 uppercase text-xs">
									
									<tr>
										
										<th class="px-6 py-3 text-left font-bold tracking-wider">
											Piwo
										</th>
										
										<th class="px-6 py-3 text-center font-bold tracking-wider">
											Było pełnych kegów
										</th>
										
										<th class="px-6 py-3 text-center font-bold tracking-wider">
											Ilość piwa
										</th>
										
										<th class="px-6 py-3 text-center font-bold tracking-wider">
											Data ostatniego podłaczenia
										</th>
									
									</tr>
									
									</thead>
									
									
									<tbody class="bg-white divide-y divide-gray-200">
									
									@foreach($stocks->where('is_archived', 1) as $stock)
										
										<tr class="hover:bg-blue-50/50 transition duration-150">
											
											<td class="px-6 py-4">
												
												<div class="font-bold text-gray-800 text-lg">
													{{ $stock->recipe->name }}
												</div>
											
											</td>
											
											<td class="px-6 py-4 text-center">

                                            <span class="font-mono text-gray-600 bg-gray-100 px-3 py-1 rounded">
                                                {{ $stock->movements->first()->quantity }}
                                            </span>
											
											</td>
											
											<td class="px-6 py-4 text-center">

                                            <span class="font-mono text-gray-600 bg-gray-100 px-3 py-1 rounded">
                                                {{ $stock->movements->first()->quantity * 50 }} L
                                            </span>
											
											</td>
											
											<td class="px-6 py-4 text-center">

                                            <span class="font-mono text-gray-600 bg-gray-100 px-3 py-1 rounded">
                                                {{ $stock->movements->last()->created_at }}
												({{ \Carbon\Carbon::parse($stock->movements->last()->created_at)->diffForHumans() }})
                                            </span>
											
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
