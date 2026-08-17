<x-app-layout>
	<x-slot name="header">
		<div class="flex justify-between items-center">
			<h2 class="font-semibold text-xl text-gray-800 leading-tight">
				{{ __('Zarządzanie Beer Wall') }}
			</h2>
			<a href="{{ route('beerwall.admin.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
				+ Dodaj piwo na kran
			</a>
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
									<th class="px-6 py-3 text-left font-bold tracking-wider text-center">Statusy</th>
									<th class="px-6 py-3 text-right font-bold tracking-wider w-40">Akcje</th>
								</tr>
								</thead>
								<tbody class="bg-white divide-y divide-gray-200">
								@foreach($beers as $beer)
									<tr class="hover:bg-blue-50/50 transition duration-150 {{ $beer->is_ended ? 'opacity-50' : '' }}">
										<td class="px-6 py-4">
											<div class="font-bold text-gray-800 text-lg">{{ $beer->beer_name }}</div>
											<div class="text-sm text-amber-600 font-medium uppercase">{{ $beer->beer_style }}</div>
											<div class="text-xs text-gray-400 mt-1">{{ $beer->beer_blg }}° / {{ $beer->beer_alc }}%</div>
										</td>
										<td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="text-sm font-mono text-gray-600 bg-gray-100 px-2 py-1 rounded">
                                                {{ $beer->beer_price_small }} / {{ $beer->beer_price_medium }} / {{ $beer->beer_price_large }} zł
                                            </span>
										</td>
										<td class="px-6 py-4 whitespace-nowrap text-center">
											<div class="flex flex-col gap-1 items-center">
												@if($beer->is_premiere)
													<span class="px-2 py-0.5 text-[10px] font-bold bg-red-100 text-red-700 rounded-full uppercase border border-red-200">Premiera</span>
												@endif
												@if($beer->is_coming_soon)
													<span class="px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-700 rounded-full uppercase border border-amber-200">Wkrótce</span>
												@endif
												@if($beer->is_ended)
													<span class="px-2 py-0.5 text-[10px] font-bold bg-gray-600 text-white rounded-full uppercase">Wyprzedane</span>
												@endif
											</div>
										</td>
										<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
											<div class="flex justify-end gap-2">
												<a href="{{ route('beerwall.admin.edit', $beer) }}" class="text-blue-500 hover:text-blue-700 font-bold px-2 py-1">Edytuj</a>
												
												<form action="{{ route('beerwall.admin.clone', $beer) }}" method="POST" class="inline">
													@csrf
													<button type="submit" class="text-gray-400 hover:text-gray-700 font-bold px-2 py-1">Klonuj</button>
												</form>
												
												<form action="{{ route('beerwall.admin.destroy', $beer) }}" method="POST" onsubmit="return confirm('Usunąć {{ $beer->beer_name }} z karty?');" class="inline">
													@csrf
													@method('DELETE')
													<button type="submit" class="text-red-400 hover:text-red-600 font-bold px-2 py-1 rounded-md hover:bg-red-50">Usuń</button>
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