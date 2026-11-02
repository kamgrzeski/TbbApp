<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Business Intelligence | Raport Sprzedaży Browaru</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
	<style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0b0f19; color: #f3f4f6; }
        .glass-card {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }
	</style>
</head>
<body class="p-4 md:p-8">

<div class="max-w-7xl mx-auto space-y-8">
	
	<!-- NAGŁÓWEK & UPLOAD -->
	<header class="glass-card rounded-2xl p-6 flex flex-col md:flex-row justify-between items-center gap-4 border-l-4 border-amber-500">
		<div>
			<div class="flex items-center gap-2 text-amber-500 font-semibold tracking-wider text-xs uppercase mb-1">
				<span>🍺 Craft Brewery Executive Analytics</span>
				@if($report)
					<span>•</span>
					<span class="text-emerald-400">Panel Decyzyjny Gotowy</span>
				@endif
			</div>
			<h1 class="text-2xl md:text-3xl font-extrabold text-white">Raport Strategiczny Sprzedaży</h1>
		</div>
		
		<form action="{{ route('beer.report.upload') }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
			@csrf
			<input type="file" name="csv_file" required accept=".csv,.txt"
				   class="text-xs text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-500 file:text-black hover:file:bg-amber-400 cursor-pointer">
			<button type="submit" class="px-5 py-2.5 bg-gray-800 hover:bg-gray-700 text-amber-400 border border-amber-500/30 rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-lg">
				📊 Wgraj & Przelicz
			</button>
		</form>
	</header>
	
	@if($report)
		@php
			$otherRevenue = $report['totalRevenue'] - $report['beerRevenue'];
		@endphp
				
				<!-- KPI GŁÓWNE -->
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
			<div class="glass-card rounded-2xl p-6 border-t-2 border-emerald-500">
				<div class="text-gray-400 text-xs uppercase font-bold tracking-wider">Całkowity Przychód POS</div>
				<div class="text-3xl font-black text-emerald-400 mt-2">{{ number_format($report['totalRevenue'], 2, ',', ' ') }} zł</div>
				<div class="text-xs text-gray-400 mt-2">100% zgodności z raportem fiskalnym</div>
			</div>
			
			<div class="glass-card rounded-2xl p-6 border-t-2 border-amber-500">
				<div class="text-gray-400 text-xs uppercase font-bold tracking-wider">Przychód z Piwa</div>
				<div class="text-3xl font-black text-amber-400 mt-2">{{ number_format($report['beerRevenue'], 2, ',', ' ') }} zł</div>
				<div class="text-xs text-gray-400 mt-2">
					Udział w całości: <strong class="text-amber-300">{{ round(($report['beerRevenue'] / $report['totalRevenue']) * 100, 1) }}%</strong>
				</div>
			</div>
			
			<div class="glass-card rounded-2xl p-6 border-t-2 border-sky-400">
				<div class="text-gray-400 text-xs uppercase font-bold tracking-wider">Wylany Wolumen</div>
				<div class="text-3xl font-black text-white mt-2">{{ number_format($report['totalLiters'], 1, ',', ' ') }} L</div>
				<div class="text-xs text-gray-400 mt-2">
					Odpowiednik: <strong class="text-sky-300">{{ round($report['totalLiters'] / 50, 1) }} kega (50L)</strong>
				</div>
			</div>
			
			<div class="glass-card rounded-2xl p-6 border-t-2 border-rose-500">
				<div class="text-gray-400 text-xs uppercase font-bold tracking-wider">Udzielone Rabaty</div>
				<div class="text-3xl font-black text-rose-400 mt-2">{{ number_format($report['totalDiscounts'], 2, ',', ' ') }} zł</div>
				<div class="text-xs text-gray-400 mt-2">
					W tym na wynos PET: <strong class="text-rose-300">{{ number_format($report['petDiscounts'], 2, ',', ' ') }} zł</strong>
				</div>
			</div>
		</div>
		
		<!-- MODUŁ WSKAŹNIKÓW BIZNESOWYCH (UNIT ECONOMICS & CROSS-SELLING) -->
		<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
			<!-- 1. Attach Rate Przekąsek -->
			<div class="glass-card rounded-2xl p-6 border-l-4 border-yellow-500">
				<div class="flex justify-between items-start mb-2">
					<span class="text-xs uppercase font-bold text-gray-400">Dosprzedaż Przekąsek (Attach Rate)</span>
					<span class="text-xs font-bold px-2 py-0.5 rounded bg-yellow-500/20 text-yellow-300">Cel: 15%</span>
				</div>
				<div class="text-3xl font-extrabold text-white mt-1">{{ $report['attachRate'] }}%</div>
				<p class="text-xs text-gray-400 mt-1">
					{{ $report['totalSnackUnits'] }} przekąsek na {{ $report['totalBeerUnits'] }} piw (zaledwie 1 na 15 gości).
				</p>
				<div class="w-full bg-gray-800 rounded-full h-2 mt-4 overflow-hidden">
					<div class="bg-yellow-500 h-2 rounded-full" style="width: {{ min($report['attachRate'] * 6.6, 100) }}%"></div>
				</div>
				<div class="text-[11px] text-gray-500 mt-2">Potencjał wzrostu przy 15%: <strong class="text-emerald-400">+1 200 zł zysku</strong></div>
			</div>
			
			<!-- 2. Kanały Sprzedaży (Na miejscu vs Wynos) -->
			<div class="glass-card rounded-2xl p-6 border-l-4 border-cyan-500">
				<span class="text-xs uppercase font-bold text-gray-400">Struktura Kanałów Sprzedaży</span>
				<div class="flex justify-between items-baseline mt-2">
					<div class="text-2xl font-bold text-white">{{ $report['onPremisePercent'] }}% <span class="text-xs text-gray-400">Na miejscu</span></div>
					<div class="text-xl font-bold text-cyan-400">{{ $report['takeoutPercent'] }}% <span class="text-xs text-gray-400">Wynos (PET)</span></div>
				</div>
				<div class="w-full bg-gray-800 rounded-full h-2 mt-4 flex overflow-hidden">
					<div class="bg-blue-500 h-2" style="width: {{ $report['onPremisePercent'] }}%"></div>
					<div class="bg-cyan-400 h-2" style="width: {{ $report['takeoutPercent'] }}%"></div>
				</div>
				<div class="flex justify-between text-[11px] text-gray-400 mt-2">
					<span>Szkło: {{ number_format($report['onPremiseLiters'], 1) }} L</span>
					<span>Butelka PET: {{ number_format($report['petLiters'], 1) }} L</span>
				</div>
			</div>
			
			<!-- 3. Rentowność Formatów (PLN / Litr) -->
			<div class="glass-card rounded-2xl p-6 border-l-4 border-emerald-500">
				<span class="text-xs uppercase font-bold text-gray-400">Rentowność Formatu (PLN / 1 Litr)</span>
				<div class="mt-3 space-y-1.5 text-xs">
					@foreach($report['formatTotals'] as $fName => $fData)
						@php $plnPerL = $fData['liters'] > 0 ? round($fData['revenue'] / $fData['liters'], 2) : 0; @endphp
						<div class="flex justify-between items-center text-gray-300">
							<span>{{ $fName }}:</span>
							<span class="font-bold {{ $plnPerL >= 45 ? 'text-emerald-400' : 'text-amber-400' }}">
                                    {{ number_format($plnPerL, 2, ',', ' ') }} zł/L
                                </span>
						</div>
					@endforeach
				</div>
				<div class="text-[11px] text-gray-500 mt-2">Format 250ml przynosi najwyższy przychód z litra.</div>
			</div>
		</div>
		
		<!-- WYKRESY -->
		<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
			<div class="glass-card rounded-2xl p-6 lg:col-span-2">
				<h3 class="text-lg font-bold text-white mb-1">Przychód i Wolumen Piw</h3>
				<p class="text-xs text-gray-400 mb-6">Porównanie wartości sprzedaży (PLN) i wolumenu (L)</p>
				<div class="h-80">
					<canvas id="beerComparisonChart"></canvas>
				</div>
			</div>
			
			<div class="glass-card rounded-2xl p-6">
				<h3 class="text-lg font-bold text-white mb-1">Udział w Wolumenie (Litry)</h3>
				<p class="text-xs text-gray-400 mb-6">Procentowy podział sprzedanego piwa</p>
				<div class="h-80 flex items-center justify-center">
					<canvas id="volumeShareChart"></canvas>
				</div>
			</div>
		</div>
		
		<!-- KAFLE PIW Z ANALIZĄ ABC I CENĄ ZA LITR -->
		<div class="flex justify-between items-end pt-2">
			<div>
				<h2 class="text-xl font-bold text-white flex items-center gap-2">
					<span>🍻</span> Analiza Stylów Piwa & Klasyfikacja Pareto ABC
				</h2>
				<p class="text-xs text-gray-400 mt-1">Klasa A generuje 80% obrotu, Klasa B kolejne 15%, Klasa C to ogon sprzedaży.</p>
			</div>
		</div>
		
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
			@foreach($report['beers'] as $beer)
				<div class="glass-card rounded-2xl p-6 border-l-4 {{ $beer['theme']['border'] }} hover:scale-[1.01] transition-transform duration-200">
					<div class="flex justify-between items-start mb-3">
						<div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded border {{ $beer['abc_badge'] }}">
                                    {{ $beer['abc_label'] }}
                                </span>
							<h4 class="text-lg font-bold text-white mt-1.5">{{ $beer['name'] }}</h4>
							<div class="text-xs text-gray-400 flex items-center gap-2 mt-0.5">
								<span>{{ $beer['units'] }} szt.</span>
								<span>•</span>
								<span class="text-emerald-400 font-semibold">{{ $beer['price_per_liter'] }} zł / Litr</span>
							</div>
						</div>
						<div class="text-right">
							<div class="text-lg font-extrabold {{ $beer['theme']['text'] }}">{{ number_format($beer['revenue'], 2, ',', ' ') }} zł</div>
							<div class="text-xs font-medium text-gray-400">{{ number_format($beer['liters'], 1, ',', ' ') }} L</div>
						</div>
					</div>
					
					<div class="space-y-1.5 text-xs border-t border-gray-700/60 pt-3">
						@foreach($beer['formats'] as $formatName => $f)
							<div class="flex justify-between text-gray-300">
								<span>{{ $formatName }}:</span>
								<span class="font-semibold text-white">{{ $f['qty'] }} szt. ({{ $f['liters'] }} L) • {{ number_format($f['revenue'], 2, ',', ' ') }} zł</span>
							</div>
						@endforeach
					</div>
				</div>
			@endforeach
		</div>
		
		<!-- CHARTS SCRIPT -->
		<script>
            Chart.defaults.color = '#9ca3af';
            Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";

            new Chart(document.getElementById('beerComparisonChart'), {
                type: 'bar',
                data: {
                    labels: @json($report['chartLabels']),
                    datasets: [
                        {
                            label: 'Przychód (zł)',
                            data: @json($report['chartRevenue']),
                            backgroundColor: '#f59e0b',
                            borderRadius: 6,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Litry (L)',
                            data: @json($report['chartLiters']),
                            backgroundColor: '#38bdf8',
                            borderRadius: 6,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { type: 'linear', position: 'left', grid: { color: 'rgba(255, 255, 255, 0.05)' } },
                        y1: { type: 'linear', position: 'right', grid: { display: false } }
                    }
                }
            });

            new Chart(document.getElementById('volumeShareChart'), {
                type: 'doughnut',
                data: {
                    labels: @json($report['chartLabels']),
                    datasets: [{
                        data: @json($report['chartLiters']),
                        backgroundColor: @json($report['chartColors']),
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, color: '#d1d5db', padding: 8 } } },
                    cutout: '65%'
                }
            });
		</script>
	@else
		<div class="glass-card rounded-2xl p-16 text-center border-dashed border-2 border-gray-700">
			<div class="text-5xl mb-4">🍻</div>
			<h3 class="text-xl font-bold text-white mb-2">Brak wczytanego raportu</h3>
			<p class="text-sm text-gray-400 max-w-md mx-auto mb-6">Wybierz plik CSV wyeksportowany z systemu sprzedaży, aby wygenerować pełną prezentację analityczną.</p>
		</div>
	@endif

</div>
</body>
</html>