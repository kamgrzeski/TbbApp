<x-app-layout>
	
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Rozlew do kegów') }}
		</h2>
	</x-slot>
	
	
	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white shadow-sm sm:rounded-lg p-6">
				
				<div class="p-6">
					
					<form
							action="{{ route('kegs.admin.production.store') }}"
							method="POST"
					>
						
						@csrf
						
						
						<!-- Piwo -->
						<div class="mb-6">
							
							<label class="block text-sm font-bold text-gray-700 mb-1">
								Warka
							</label>
							
							<select
									name="recipe_id"
									class="w-full rounded-md border-gray-300 bg-white px-3 py-2 text-sm shadow-sm
           focus:border-blue-500 focus:ring-blue-500"
									required
							>
								<option value="">-- wybierz warkę --</option>
								
								@foreach($recipes as $recipe)
									<option
											value="{{ $recipe->id }}"
											@selected(old('recipe_id') == $recipe->id)
									>
										Warka {{ $recipe->number }} — {{ $recipe->name }}
										| Zbiornik {{ $recipe->tank_number }}
										| {{ number_format($recipe->volume, 0, ',', ' ') }} L
										| {{ $recipe->blg }}°Blg
									</option>
								@endforeach
							</select>
							
							@error('recipe_id')
							<p class="mt-1 text-sm text-red-600">
								{{ $message }}
							</p>
							@enderror
						
						</div>
						
						
						<!-- Liczba kegów -->
						<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
							
							<div>
								
								<label class="block text-sm font-bold text-gray-700 mb-1">
									Liczba kegów
								</label>
								
								<input
										type="number"
										name="quantity"
										value="{{ old('quantity') }}"
										min="1"
										max="20"
										class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
										required
								>
								
								@error('quantity')
								<p class="mt-1 text-sm text-red-600">
									{{ $message }}
								</p>
								@enderror
							
							</div>
							
							
						
						</div>
						
						
						<!-- Informacja -->
						<div class="mb-6 p-4 bg-blue-50 border border-blue-100 rounded-lg">
							
							<div class="flex items-start">
								
								<div class="text-blue-600 mr-3 text-lg">
									ℹ
								</div>
								
								<div>
									
									<div class="font-bold text-blue-800">
										Napełnienie kegów
									</div>
									
									<p class="text-sm text-blue-700 mt-1">
										Rozlew pobierze puste kegi z magazynu
										i doda je do stanu pełnych kegów wybranego piwa.
									</p>
								
								</div>
							
							</div>
						
						</div>
						
						
						<!-- Notatka -->
						<div class="mb-8">
							
							<label class="block text-sm font-bold text-gray-700 mb-1">
								Notatka
							</label>
							
							<textarea
									name="note"
									rows="3"
									class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
									placeholder="Opcjonalna notatka dotycząca rozlewu..."
							>{{ old('note') }}</textarea>
							
							@error('note')
							<p class="mt-1 text-sm text-red-600">
								{{ $message }}
							</p>
							@enderror
						
						</div>
						
						
						<!-- Przyciski -->
						<div class="flex items-center justify-between gap-4">
							
							<a
									href="{{ route('kegs.admin.index') }}"
									class="text-gray-500 hover:text-gray-700 hover:underline"
							>
								Anuluj
							</a>
							
							<button
									type="submit"
									class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-bold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring ring-green-300 transition duration-150 shadow-md"
							>
								Rozlej do kegów
							</button>
						
						</div>
					
					</form>
				
				</div>
			
			</div>
		
		</div>
	
	</div>

</x-app-layout>
