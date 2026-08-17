<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8">
	<title>Drukuj recepturę: {{ $recipe->name }}</title>
	@vite(['resources/css/app.css']) {{-- Tailwind do stylizacji --}}
	<style>
        @media print {
            body { background: white; padding: 0; margin: 0; }
            .no-print { display: none; }
            @page { size: A4; margin: 1cm; }
        }
        /* Kompaktowanie tabel do druku */
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #e5e7eb; padding: 4px 8px; font-size: 11px; }
        th { background-color: #f9fafb; }
	</style>
</head>
<body class="bg-white text-gray-900 p-8" onload="window.print()">

@php
	// Obliczenia identyczne jak w Twoim kodzie
	$w1TotalKg = $recipe->malts->where('batch_number', 1)->sum('kg');
	$w2TotalKg = $recipe->malts->where('batch_number', 2)->sum('kg');
	$totalExtractKg = $recipe->malts->where('is_active', true)->where('batch_number', 1)->sum(fn($m) => $m->kg * ($m->extract / 100));
	$blg = $recipe->volume > 0 ? ($totalExtractKg / ($recipe->volume / 2) * 100 * (($recipe->efficiency) / 100)) : 0;
@endphp
		
		<!-- Nagłówek -->
<div class="flex justify-between items-center border-b-2 border-gray-800 pb-4 mb-6">
	<div>
		<h1 class="text-2xl font-black uppercase">{{ $recipe->name }}</h1>
		<p class="text-sm font-bold text-gray-600">Numer tanka: {{ $recipe->tank_number }} | Data druku: {{ now()->format('d.m.Y') }}</p>
	</div>
	<div class="text-right">
		<p class="text-lg font-black">{{ number_format($blg, 1) }} °Blg</p>
		<p class="text-xs uppercase text-gray-500 font-bold">Gęstość początkowa</p>
	</div>
</div>

<!-- Podsumowanie parametrów -->
<div class="grid grid-cols-3 gap-4 mb-6">
	<div class="border p-2 rounded text-center">
		<p class="text-[10px] uppercase font-bold text-gray-500">Objętość</p>
		<p class="text-lg font-black">{{ $recipe->volume }} L</p>
	</div>
	<div class="border p-2 rounded text-center">
		<p class="text-[10px] uppercase font-bold text-gray-500">Wydajność</p>
		<p class="text-lg font-black">{{ $recipe->efficiency }} %</p>
	</div>
	<div class="border p-2 rounded text-center">
		<p class="text-[10px] uppercase font-bold text-gray-500">Łącznie słodów</p>
		<p class="text-lg font-black">{{ number_format($w1TotalKg + $w2TotalKg, 1) }} kg</p>
	</div>
</div>

<!-- Słody - Dwie kolumny (Warka 1 i Warka 2) -->
<div class="grid grid-cols-2 gap-6 mb-6">
	<div>
		<h3 class="text-xs font-black uppercase bg-gray-100 p-2 mb-2 border">Zasyp Warka 1</h3>
		<table>
			<thead>
			<tr><th class="text-left">Słód</th><th class="w-16">kg</th><th class="w-16">Worki</th></tr>
			</thead>
			<tbody>
			@foreach($recipe->malts->where('batch_number', 1) as $malt)
				<tr>
					<td>{{ $malt->name }}</td>
					<td class="text-center font-bold">{{ number_format($malt->kg, 1) }}</td>
					<td class="text-center text-gray-600">{{ ceil($malt->kg / 25) }}</td>
				</tr>
			@endforeach
			</tbody>
			<tfoot class="font-bold bg-gray-50">
			<tr><td>SUMA</td><td class="text-center">{{ $w1TotalKg }}</td><td class="text-center">{{ ceil($w1TotalKg / 25) }}</td></tr>
			</tfoot>
		</table>
	</div>
	
	<div>
		<h3 class="text-xs font-black uppercase bg-gray-100 p-2 mb-2 border">Zasyp Warka 2</h3>
		<table>
			<thead>
			<tr><th class="text-left">Słód</th><th class="w-16">kg</th><th class="w-16">Worki</th></tr>
			</thead>
			<tbody>
			@foreach($recipe->malts->where('batch_number', 2) as $malt)
				<tr>
					<td>{{ $malt->name }}</td>
					<td class="text-center font-bold">{{ number_format($malt->kg, 1) }}</td>
					<td class="text-center text-gray-600">{{ ceil($malt->kg / 25) }}</td>
				</tr>
			@endforeach
			</tbody>
			<tfoot class="font-bold bg-gray-50">
			<tr><td>SUMA</td><td class="text-center">{{ $w2TotalKg }}</td><td class="text-center">{{ ceil($w2TotalKg / 25) }}</td></tr>
			</tfoot>
		</table>
	</div>
</div>

<!-- Chmiele -->
<div class="grid grid-cols-2 gap-6">
	@foreach($recipe->hops->groupBy('batch_number') as $batch => $hops)
		<div>
			<h3 class="text-xs font-black uppercase bg-amber-600 text-white p-2 mb-2">Chmielenie Warka {{ $batch }}</h3>
			<table>
				<thead>
				<tr><th class="text-left">Chmiel</th><th class="w-16">Ilość</th><th class="w-16">Czas</th></tr>
				</thead>
				<tbody>
				@foreach($hops as $hop)
					<tr>
						<td>{{ $hop->name }} ({{ $hop->alpha_acids }}%)</td>
						<td class="text-center font-bold">{{ number_format($hop->amount, 0) }}g</td>
						<td class="text-center font-bold">{{ $hop->time }}'</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	@endforeach
</div>

<!-- Miejsce na notatki dla piwowara -->
<div class="mt-8 border-t pt-4">
	<h4 class="text-[10px] font-bold uppercase text-gray-400 mb-4">Notatki piwowara:</h4>
	<div class="border-b border-dashed border-gray-300 h-8"></div>
	<div class="border-b border-dashed border-gray-300 h-8"></div>
	<div class="border-b border-dashed border-gray-300 h-8"></div>
</div>

<button onclick="window.print()" class="no-print mt-10 bg-black text-white px-6 py-2 rounded">
	Kliknij jeśli okno drukowania się nie otworzyło
</button>

</body>
</html>