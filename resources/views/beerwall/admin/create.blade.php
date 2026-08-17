<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ isset($beerwall) ? __('Edytuj piwo na kranie') : __('Dodaj nowe piwo') }}
		</h2>
	</x-slot>
	
	<div class="py-12">
		<div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
				<div class="p-6">
					<form action="{{ isset($beerwall) ? route('beerwall.admin.update', $beerwall) : route('beerwall.admin.store') }}" method="POST">
						@csrf
						@if(isset($beerwall)) @method('PUT') @endif
						
						<!-- Nazwa i Kran -->
						<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
							<div class="md:col-span-2">
								<label class="block text-sm font-bold text-gray-700 mb-1">Nazwa piwa</label>
								<input type="text" name="beer_name" value="{{ old('beer_name', $beerwall->beer_name ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
							</div>
						</div>
						
						<!-- Styl i Parametry -->
						<div class="mb-6">
							<label class="block text-sm font-bold text-gray-700 mb-1">Styl piwa</label>
							<input type="text" name="beer_style" value="{{ old('beer_style', $beerwall->beer_style ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
						</div>
						
						<div class="grid grid-cols-2 gap-4 mb-6">
							<div>
								<label class="block text-sm font-bold text-gray-700 mb-1">Ekstrakt (Blg)</label>
								<input type="text" name="beer_blg" value="{{ old('beer_blg', $beerwall->beer_blg ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
							</div>
							<div>
								<label class="block text-sm font-bold text-gray-700 mb-1">Alkohol (%)</label>
								<input type="text" name="beer_alc" value="{{ old('beer_alc', $beerwall->beer_alc ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
							</div>
						</div>
						
						<!-- Ceny -->
						<div class="grid grid-cols-3 gap-4 mb-6 p-4 bg-gray-50 rounded-lg border border-gray-100">
							<div>
								<label class="block text-xs font-bold text-gray-500 uppercase mb-1 text-center">Cena 0.25L</label>
								<input type="number" step="0.01" name="beer_price_small" value="{{ old('beer_price_small', $beerwall->beer_price_small ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm text-center font-bold" required>
							</div>
							<div>
								<label class="block text-xs font-bold text-gray-500 uppercase mb-1 text-center">Cena 0.5L</label>
								<input type="number" step="0.01" name="beer_price_medium" value="{{ old('beer_price_medium', $beerwall->beer_price_medium ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm text-center font-bold" required>
							</div>
							<div>
								<label class="block text-xs font-bold text-gray-500 uppercase mb-1 text-center">Cena 1.0L</label>
								<input type="number" step="0.01" name="beer_price_large" value="{{ old('beer_price_large', $beerwall->beer_price_large ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm text-center font-bold" required>
							</div>
						</div>
						
						<!-- Opis -->
						<div class="mb-6">
							<label class="block text-sm font-bold text-gray-700 mb-1">Opis piwa</label>
							<textarea name="beer_description" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('beer_description', $beerwall->beer_description ?? '') }}</textarea>
						</div>
						
						<!-- Statusy -->
						<div class="space-y-3 mb-8">
							<label class="flex items-center p-3 border rounded-md hover:bg-gray-50 cursor-pointer">
								<input type="checkbox" name="is_premiere" class="rounded border-gray-300 text-red-600 shadow-sm" {{ old('is_premiere', $beerwall->is_premiere ?? false) ? 'checked' : '' }}>
								<span class="ml-3 font-semibold text-gray-700 uppercase text-sm">Oznacz jako PREMIERĘ</span>
							</label>
							<label class="flex items-center p-3 border rounded-md hover:bg-gray-50 cursor-pointer">
								<input type="checkbox" name="is_coming_soon" class="rounded border-gray-300 text-amber-500 shadow-sm" {{ old('is_coming_soon', $beerwall->is_coming_soon ?? false) ? 'checked' : '' }}>
								<span class="ml-3 font-semibold text-gray-700 uppercase text-sm text-amber-600">Oznacz jako WKRÓTCE</span>
							</label>
							<label class="flex items-center p-3 border rounded-md hover:bg-red-50 border-red-100 cursor-pointer">
								<input type="checkbox" name="is_ended" class="rounded border-gray-300 text-gray-700 shadow-sm" {{ old('is_ended', $beerwall->is_ended ?? false) ? 'checked' : '' }}>
								<span class="ml-3 font-semibold text-red-700 uppercase text-sm italic">WYPRZEDANE / KONIEC KRANU</span>
							</label>
						</div>
						
						<div class="flex items-center justify-between gap-4">
							<a href="{{ route('beerwall.admin.index') }}" class="text-gray-500 hover:underline">Anuluj</a>
							<button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-bold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring ring-blue-300 transition duration-150 shadow-md">
								{{ isset($beerwall) ? 'Zaktualizuj piwo' : 'Dodaj na Beer Wall' }}
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</x-app-layout>