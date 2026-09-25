<x-app-layout>
	
	<x-slot name="header">
		<div class="flex justify-between items-center">
			<div>
				<h2 class="font-semibold text-xl text-gray-800 leading-tight">
					{{ __('Webhooki GoPos') }}
				</h2>
				
				<p class="text-sm text-gray-500 mt-1">
					Historia webhooków otrzymanych z systemu GoPos
				</p>
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
						Wszystkie webhooki
					</div>
					
					<div class="mt-2 text-3xl font-bold text-gray-800">
						{{ $webhooks->count() }}
					</div>
					
					<div class="text-xs text-gray-400 mt-1">
						otrzymanych webhooków
					</div>
				
				</div>
				
				
				{{-- Przetworzone --}}
				<div class="bg-white rounded-lg shadow-sm border border-green-100 p-6">
					
					<div class="text-sm font-medium text-gray-500 uppercase">
						Przetworzone
					</div>
					
					<div class="mt-2 text-3xl font-bold text-green-600">
						{{ $webhooks->where('status', 'processed')->count() }}
					</div>
					
					<div class="text-xs text-gray-400 mt-1">
						poprawnie obsłużone
					</div>
				
				</div>
				
				
				{{-- Błędy --}}
				<div class="bg-white rounded-lg shadow-sm border border-red-100 p-6">
					
					<div class="text-sm font-medium text-gray-500 uppercase">
						Błędy
					</div>
					
					<div class="mt-2 text-3xl font-bold text-red-600">
						{{ $webhooks->where('status', 'failed')->count() }}
					</div>
					
					<div class="text-xs text-gray-400 mt-1">
						webhooków z błędem
					</div>
				
				</div>
				
				
				{{-- Zamówienia --}}
				<div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6">
					
					<div class="text-sm font-medium text-gray-500 uppercase">
						Zamówienia
					</div>
					
					<div class="mt-2 text-3xl font-bold text-blue-600">
						{{ $webhooks->where('type', 'ORDER')->count() }}
					</div>
					
					<div class="text-xs text-gray-400 mt-1">
						webhooków typu ORDER
					</div>
				
				</div>
			
			</div>
			
			
			{{-- TABELA WEBHOOKÓW --}}
			<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 mb-6">
				
				<div class="p-6 text-gray-900">
					
					<div class="flex justify-between items-center mb-6">
						
						<div>
							<h3 class="text-lg font-bold text-gray-800">
								Historia webhooków
							</h3>
							
							<p class="text-sm text-gray-500 mt-1">
								Lista webhooków otrzymanych z systemu GoPos.
							</p>
						</div>
					
					</div>
					
					
					@if($webhooks->isEmpty())
						
						<div class="text-center py-10">
							
							<div class="text-4xl mb-3">
								🔗
							</div>
							
							<p class="text-gray-500 text-lg">
								Brak webhooków.
							</p>
							
							<p class="text-sm text-gray-400 mt-1">
								Historia webhooków pojawi się tutaj po otrzymaniu pierwszego zdarzenia z GoPos.
							</p>
						
						</div>
					
					@else
						
						<div class="overflow-x-auto">
							
							<table class="min-w-full">
								
								<thead>
								
								<tr class="border-b-2 border-gray-200 bg-gray-50">
									
									<th class="px-6 py-4 text-left text-xs font-extrabold text-gray-600 uppercase tracking-wider">
										ID
									</th>
									
									<th class="px-6 py-4 text-left text-xs font-extrabold text-gray-600 uppercase tracking-wider">
										Event
									</th>
									
									<th class="px-6 py-4 text-left text-xs font-extrabold text-gray-600 uppercase tracking-wider">
										Resource ID
									</th>
									
									<th class="px-6 py-4 text-center text-xs font-extrabold text-gray-600 uppercase tracking-wider">
										Status
									</th>
									
									<th class="px-6 py-4 text-center text-xs font-extrabold text-gray-600 uppercase tracking-wider">
										Otrzymano
									</th>
									
									<th class="px-6 py-4 text-right text-xs font-extrabold text-gray-600 uppercase tracking-wider">
										Akcje
									</th>
								
								</tr>
								
								</thead>
								
								
								<tbody class="divide-y divide-gray-100 bg-white">
								
								@foreach($webhooks as $webhook)
									
									<tr class="group transition-colors duration-150 hover:bg-blue-50">
										
										{{-- ID --}}
										<td class="px-6 py-4 whitespace-nowrap">

                                            <span class="inline-flex items-center px-2.5 py-1
                                                         rounded-md
                                                         bg-gray-100
                                                         text-gray-700
                                                         border border-gray-200
                                                         text-xs font-mono font-bold">

                                                #{{ $webhook->id }}

                                            </span>
										
										</td>
										
										
										{{-- EVENT --}}
										<td class="px-6 py-4">
											
											<div class="flex items-center gap-2">
												
												@if($webhook->type === 'ORDER')
													
													<span class="inline-flex items-center px-2.5 py-1
                                                                 rounded-md
                                                                 bg-blue-50
                                                                 text-blue-700
                                                                 border border-blue-200
                                                                 text-xs font-bold">

                                                        ORDER

                                                    </span>
												
												@else
													
													<span class="inline-flex items-center px-2.5 py-1
                                                                 rounded-md
                                                                 bg-gray-100
                                                                 text-gray-600
                                                                 border border-gray-200
                                                                 text-xs font-bold">

                                                        {{ $webhook->type ?? '-' }}

                                                    </span>
												
												@endif
												
												
												<span class="text-sm font-bold text-gray-800">
                                                    {{ $webhook->event_type ?? '-' }}
                                                </span>
											
											</div>
										
										</td>
										
										
										{{-- RESOURCE ID --}}
										<td class="px-6 py-4">
											
											@if($webhook->resource_id)
												
												<span
														class="inline-flex items-center px-2.5 py-1
                                                           rounded-md
                                                           bg-gray-50
                                                           text-gray-600
                                                           border border-gray-200
                                                           text-xs font-mono"
														title="{{ $webhook->resource_id }}"
												>
                                                    {{ \Illuminate\Support\Str::limit($webhook->resource_id, 18) }}
                                                </span>
											
											@else
												
												<span class="text-gray-400">
                                                    -
                                                </span>
											
											@endif
										
										</td>
										
										
										{{-- STATUS --}}
										<td class="px-6 py-4 text-center whitespace-nowrap">
											
											@if($webhook->status === 'processed')
												
												<span class="inline-flex items-center gap-1.5
                                                             px-3 py-1.5
                                                             rounded-lg
                                                             bg-emerald-50
                                                             text-emerald-700
                                                             border border-emerald-200
                                                             text-sm font-bold">

                                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>

                                                    Przetworzony

                                                </span>
											
											@elseif($webhook->status === 'failed')
												
												<span class="inline-flex items-center gap-1.5
                                                             px-3 py-1.5
                                                             rounded-lg
                                                             bg-red-50
                                                             text-red-700
                                                             border border-red-200
                                                             text-sm font-bold">

                                                    <span class="w-2 h-2 rounded-full bg-red-500"></span>

                                                    Błąd

                                                </span>
											
											@elseif($webhook->status === 'received')
												
												<span class="inline-flex items-center gap-1.5
                                                             px-3 py-1.5
                                                             rounded-lg
                                                             bg-yellow-50
                                                             text-yellow-700
                                                             border border-yellow-200
                                                             text-sm font-bold">

                                                    <span class="w-2 h-2 rounded-full bg-yellow-500"></span>

                                                    Otrzymany

                                                </span>
											
											@else
												
												<span class="inline-flex items-center gap-1.5
                                                             px-3 py-1.5
                                                             rounded-lg
                                                             bg-gray-100
                                                             text-gray-600
                                                             border border-gray-200
                                                             text-sm font-semibold">

                                                    {{ $webhook->status }}

                                                </span>
											
											@endif
										
										</td>
										
										
										{{-- DATA --}}
										<td class="px-6 py-4 text-center whitespace-nowrap">
											
											<div class="text-sm text-gray-600">
												{{ $webhook->created_at?->format('d.m.Y H:i:s') }}
											</div>
											
											@if($webhook->created_at)
												
												<div class="text-[11px] text-gray-400">
													{{ $webhook->created_at->diffForHumans() }}
												</div>
											
											@endif
										
										</td>
										
										
										{{-- AKCJE --}}
										<td class="px-6 py-4 text-right whitespace-nowrap">
											
											<button
													type="button"
													onclick="document.getElementById('webhook-{{ $webhook->id }}').classList.toggle('hidden')"
													class="inline-flex items-center gap-2
                                                       px-3 py-2
                                                       bg-gray-50
                                                       border border-gray-200
                                                       rounded-md
                                                       text-xs font-semibold text-gray-700
                                                       hover:bg-gray-100
                                                       transition"
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
															d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
													/>
													
													<path
															stroke-linecap="round"
															stroke-linejoin="round"
															d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
													/>
												</svg>
												
												Payload
											
											</button>
										
										</td>
									
									</tr>
									
									
									{{-- SZCZEGÓŁY WEBHOOKA --}}
									<tr
											id="webhook-{{ $webhook->id }}"
											class="hidden bg-gray-50"
									>
										
										<td colspan="6" class="px-6 py-6">
											
											<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
												
												{{-- INFORMACJE --}}
												<div>
													
													<h4 class="text-sm font-bold text-gray-800 mb-3">
														Informacje o webhooku
													</h4>
													
													<div class="bg-white border border-gray-200 rounded-lg p-4 space-y-3">
														
														<div class="flex justify-between gap-4">
                                                            <span class="text-sm text-gray-500">
                                                                Event type
                                                            </span>
															
															<span class="text-sm font-semibold text-gray-800">
                                                                {{ $webhook->event_type ?? '-' }}
                                                            </span>
														</div>
														
														
														<div class="flex justify-between gap-4">
                                                            <span class="text-sm text-gray-500">
                                                                Type
                                                            </span>
															
															<span class="text-sm font-semibold text-gray-800">
                                                                {{ $webhook->type ?? '-' }}
                                                            </span>
														</div>
														
														
														<div class="flex justify-between gap-4">
                                                            <span class="text-sm text-gray-500">
                                                                Organization ID
                                                            </span>
															
															<span class="text-sm font-mono text-gray-800">
                                                                {{ $webhook->organization_id ?? '-' }}
                                                            </span>
														</div>
														
														
														<div class="flex justify-between gap-4">
                                                            <span class="text-sm text-gray-500">
                                                                Resource ID
                                                            </span>
															
															<span class="text-sm font-mono text-gray-800 break-all text-right">
                                                                {{ $webhook->resource_id ?? '-' }}
                                                            </span>
														</div>
														
														
														<div class="flex justify-between gap-4">
                                                            <span class="text-sm text-gray-500">
                                                                Otrzymano
                                                            </span>
															
															<span class="text-sm text-gray-800">
                                                                {{ $webhook->created_at?->format('d.m.Y H:i:s') }}
                                                            </span>
														</div>
														
														
														<div class="flex justify-between gap-4">
                                                            <span class="text-sm text-gray-500">
                                                                Przetworzono
                                                            </span>
															
															<span class="text-sm text-gray-800">
                                                                {{ $webhook->processed_at?->format('d.m.Y H:i:s') ?? '-' }}
                                                            </span>
														</div>
													
													</div>
													
													
													{{-- BŁĄD --}}
													@if($webhook->error_message)
														
														<div class="mt-4">
															
															<h4 class="text-sm font-bold text-red-700 mb-2">
																Błąd przetwarzania
															</h4>
															
															<div class="bg-red-50 border border-red-200 rounded-lg p-4">
																
																<div class="text-sm text-red-700 font-mono break-words">
																	{{ $webhook->error_message }}
																</div>
															
															</div>
														
														</div>
													
													@endif
												
												</div>
												
												
												{{-- PAYLOAD --}}
												<div>
													
													<h4 class="text-sm font-bold text-gray-800 mb-3">
														Payload
													</h4>
													
													<div class="bg-gray-900 rounded-lg overflow-hidden">
														
														<pre class="p-5 text-xs text-green-400 overflow-x-auto leading-relaxed"><code>{{ json_encode($webhook->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</code></pre>
													
													</div>
												
												</div>
												
												@if($webhook->description) <div class="mt-4"> <h4 class="text-sm font-bold text-gray-800 mb-2"> Wykonane operacje </h4> <div class="bg-blue-50 border border-blue-200 rounded-lg p-4"> @foreach(explode("\n", $webhook->description) as $line) <div class="flex gap-2 text-sm text-gray-700 mb-1 last:mb-0"> <span class="text-blue-500">•</span> <span>{{ $line }}</span> </div> @endforeach </div> </div> @endif
											
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
