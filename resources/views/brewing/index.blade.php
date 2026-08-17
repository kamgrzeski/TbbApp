<x-app-layout>
	<x-slot name="header">
		<div class="flex justify-between items-center">
			<h2 class="font-semibold text-xl text-gray-800 leading-tight">
				{{ __('Warki') }}
			</h2>
			<a href="{{ route('brewing.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
				+ Nowa receptura
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
					@if($recipes->isEmpty())
						<div class="text-center py-8">
							<p class="text-gray-500 text-lg">Nie masz jeszcze żadnych zapisanych receptur.</p>
							<a href="{{ route('brewing.create') }}" class="text-blue-600 hover:underline">Stwórz swoją pierwszą recepturę teraz!</a>
						</div>
					@else
						<div class="overflow-x-auto">
							<table class="min-w-full divide-y divide-gray-200">
								<thead class="bg-gray-50 text-gray-500 uppercase text-xs">
								<tr>
									<th class="text-left font-bold tracking-wider">Numer warki</th>
									<th class="px-6 py-3 text-left font-bold tracking-wider">Nazwa</th>
									<th class="px-6 py-3 text-left font-bold tracking-wider">Objętość</th>
									<th class="px-6 py-3 text-left font-bold tracking-wider">Wydajność</th>
									<th class="px-6 py-3 text-left font-bold tracking-wider">Data utworzenia</th>
									<th class="px-6 py-3 text-right font-bold tracking-wider">Akcje</th>
								</tr>
								</thead>
								<tbody class="bg-white divide-y divide-gray-200">
								@foreach($recipes as $recipe)
									<tr
											onclick="window.location='{{ route('brewing.show', $recipe) }}'"
											class="cursor-pointer hover:bg-blue-50/50 transition duration-150 group"
									>
										<td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-400 group-hover:text-blue-600">
											#{{ $recipe->number }}
										</td>
										<td class="px-6 py-4 whitespace-nowrap font-bold text-gray-800">
											{{ $recipe->name }}
										</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
											{{ $recipe->volume }} L
										</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
											{{ $recipe->efficiency }} %
										</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 italic">
											{{ $recipe->created_at->format('d.m.Y H:i') }}
										</td>
										<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
											<!-- onclick="event.stopPropagation()" jest kluczowe, aby kliknięcie w przycisk usuwania nie otwierało receptury -->
											<form
													action="{{ route('brewing.destroy', $recipe) }}"
													method="POST"
													onclick="event.stopPropagation()"
													onsubmit="return confirm('Czy na pewno chcesz usunąć recepturę: {{ $recipe->name }}?');"
													class="inline-block"
											>
												@csrf
												@method('DELETE')
												<button type="submit" class="text-red-400 hover:text-red-700 font-bold transition px-3 py-1 rounded-md hover:bg-red-50">
													Usuń
												</button>
											</form>
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