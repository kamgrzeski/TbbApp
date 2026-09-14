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
						{{ $summary['empty'] }}
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
									Ajtualny stan pełnych kegów w chłodni
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
								
								<table class="min-w-full divide-y divide-gray-200">
									
									<thead class="bg-gray-50 text-gray-500 uppercase text-xs">
									
									<tr>
										
										<th class="px-6 py-3 text-left font-bold tracking-wider">
											Data rozlewu
										</th>
										
										<th class="px-6 py-3 text-left font-bold tracking-wider">
											Warka
										</th>
										
										<th class="px-6 py-3 text-center font-bold tracking-wider">
											Pełne kegi
										</th>
										
										<th class="px-6 py-3 text-center font-bold tracking-wider">
											Ilość piwa w litrach
										</th>
										
										<th class="px-6 py-3 text-right font-bold tracking-wider">
											Zgłoś podczepienie pod kran
										</th>
									
									</tr>
									
									</thead>
									
									
									<tbody class="bg-white divide-y divide-gray-200">
									
									@foreach($stocks->where('is_archived', 0) as $stock)
										
										<tr class="hover:bg-blue-50/50 transition duration-150">
											
											<td class="px-6 py-4">
												
												<div class="text-gray-800">
													{{ $stock->created_at }}
												</div>
											
											</td>
											<td class="px-6 py-4">
												
												<div class="font-bold text-gray-800 text-lg">
													{{ $stock->recipe->name }}
												</div>
											
											</td>
											
											
											<td class="px-6 py-4 text-center">

                                            <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 font-bold">
                                                {{ $stock->full_kegs }}
                                            </span>
											
											</td>
											
											
											<td class="px-6 py-4 text-center">

                                            <span class="font-mono text-gray-600 bg-gray-100 px-3 py-1 rounded">
                                                {{ $stock->full_kegs * 50 }} L
                                            </span>
											
											</td>
											
											
											<td class="px-6 py-4 text-right">
												<div class="inline-flex items-center gap-2">
													
													<form
															method="POST"
															action="{{ route('kegs.admin.issue.store') }}"
															onsubmit="return confirm('Czy na pewno chcesz podłączyć keg pod kran?')"
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
																class="inline-flex items-center justify-center px-4 h-10 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 active:scale-95 transition disabled:opacity-30 disabled:cursor-not-allowed"
																title="Wydaj 1 keg"
																@disabled($stock->full_kegs <= 0)
														>
															Podłączono pod kran
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
