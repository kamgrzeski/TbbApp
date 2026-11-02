<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Panel Analizy Sprzedaży Piwa</title>
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
	
	<!-- PASEK GÓRNY & FORMULARZ UPLOADU -->
	<header class="glass-card rounded-2xl p-6 flex flex-col md:flex-row justify-between items-center gap-4 border-l-4 border-amber-500">
		<div>
			<div class="flex items-center gap-2 text-amber-500 font-semibold tracking-wider text-xs uppercase mb-1">
				<span>🍺 Craft Brewery Analytics</span>
				@if($report)
					<span>•</span>
					<span class="text-emerald-400">Dane wczytane poprawnie</span>
				@endif
			</div>
			<h1 class="text-2xl md:text-3xl font-extrabold text-white">Generator Prezentacji Sprzedaży</h1>
		</div>
		
		<!-- Formularz Uploadu -->
		<form action="{{ route('beer.report.upload') }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
			@csrf
			<input type="file" name="csv_file" required accept=".csv,.txt"
				   class="text-xs text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-500 file:text-black hover:file:bg-amber-400 cursor-pointer">
			<button type="submit" class="px-5 py-2.5 bg-gray-800 hover:bg-gray-700 text-amber-400 border border-amber-500/30 rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-lg">
				📊 Wygeneruj Raport
			</button>
		</form>
	</header>
	
	@if($report)
		@php
			$otherRevenue = $report['totalRevenue'] - $report['beerRevenue'];
		@endphp
				
				<!-- KARTY GŁÓWNYCH STATYSTYK (KPI) - ZGODNE Z KASĄ -->
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
			
			<!-- 1. CAŁKOWITY PRZYCHÓD (ZGODNY Z PLIKIEM CSV CO DO GROSZA) -->
			<div class="glass-card rounded-2xl p-6 border-t-2 border-emerald-500">
				<div class="text-gray-400 text-xs uppercase font-bold tracking-wider">Całkowity Przychód Lokalu</div>
				<div class="text-3xl font-black text-emerald-400 mt-2">{{ number_format($report['totalRevenue'], 2, ',', ' ') }} zł</div>
				<div class="text-xs text-gray-400 mt-2">
					100% zgodności z raportem kasowym
				</div>
			</div>
			
			<!-- 2. W TYM SAMO PIWO -->
			<div class="glass-card rounded-2xl p-6 border-t-2 border-amber-500">
				<div class="text-gray-400 text-xs uppercase font-bold tracking-wider">W tym Sprzedaż Piwa</div>
				<div class="text-3xl font-black text-amber-400 mt-2">{{ number_format($report['beerRevenue'], 2, ',', ' ') }} zł</div>
				<div class="text-xs text-gray-400 mt-2 flex justify-between">
					<span>Udział piwa: <strong class="text-amber-300">{{ round(($report['beerRevenue'] / $report['totalRevenue']) * 100, 1) }}%</strong></span>
					<span>Inne: <strong class="text-gray-300">{{ number_format($otherRevenue, 2, ',', ' ') }} zł</strong></span>
				</div>
			</div>
			
			<!-- 3. WYKORZYSTANY WOLUMEN -->
			<div class="glass-card rounded-2xl p-6 border-t-2 border-sky-400">
				<div class="text-gray-400 text-xs uppercase font-bold tracking-wider">Wylany Wolumen Piwa</div>
				<div class="text-3xl font-black text-white mt-2">{{ number_format($report['totalLiters'], 1, ',', ' ') }} L</div>
				<div class="text-xs text-gray-400 mt-2">
					Odpowiednik ok. <strong class="text-sky-300">{{ round($report['totalLiters'] / 50, 1) }} beczki (50L)</strong>
				</div>
			</div>
			
			<!-- 4. BESTSELLER -->
			<div class="glass-card rounded-2xl p-6 border-t-2 border-purple-500">
				<div class="text-gray-400 text-xs uppercase font-bold tracking-wider">Główny Bestseller</div>
				@php $topBeer = reset($report['beers']); @endphp
				<div class="text-2xl font-black text-purple-400 mt-2 truncate">{{ $topBeer['name'] }}</div>
				<div class="text-xs text-gray-400 mt-2">
					{{ number_format($topBeer['liters'], 1, ',', ' ') }} L ({{ number_format($topBeer['revenue'], 2, ',', ' ') }} zł)
				</div>
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
		
		<!-- POKOLOROWANE KARTY SZCZEGÓŁOWE KAŻDEGO PIWA -->
		<h2 class="text-xl font-bold text-white pt-2 flex items-center gap-2">
			<span>🍻</span> Podsumowanie poszczególnych piw
		</h2>
		
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
			@foreach($report['beers'] as $beer)
				<div class="glass-card rounded-2xl p-6 border-l-4 {{ $beer['theme']['border'] }} hover:scale-[1.01] transition-transform duration-200">
					<div class="flex justify-between items-start mb-4">
						<div>
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded {{ $beer['theme']['badge_bg'] }} {{ $beer['theme']['badge_text'] }}">
                                    @if($beer['rank'] === 1) 🥇 TOP 1 Bestseller @elseif($beer['rank'] === 2) 🥈 TOP 2 Wolumen @else Pozycja #{{ $beer['rank'] }} @endif
                                </span>
							<h4 class="text-lg font-bold text-white mt-1.5">{{ $beer['name'] }}</h4>
							<span class="text-xs text-gray-400">{{ $beer['units'] }} wydanych jednostek</span>
						</div>
						<div class="text-right">
							<div class="text-lg font-extrabold {{ $beer['theme']['text'] }}">{{ number_format($beer['revenue'], 2, ',', ' ') }} zł</div>
							<div class="text-xs font-medium text-gray-400">{{ number_format($beer['liters'], 1, ',', ' ') }} Litrów</div>
						</div>
					</div>
					
					<div class="space-y-2 text-xs border-t border-gray-700/60 pt-3">
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
		
		<!-- DYNAMICZNE WNIOSKI BIZNESOWE NA PREZENTACJĘ -->
		<div class="glass-card rounded-2xl p-6 border-l-4 border-indigo-500 shadow-xl">
			<h3 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
				<span>📌</span> Kluczowe wnioski biznesowe na prezentację:
			</h3>
			<ul class="list-disc list-inside space-y-2 text-sm text-gray-300">
				<li>
					<strong class="text-white">Wynik łączny lokalu:</strong> Całkowity utarg wyniósł <strong class="text-emerald-400">{{ number_format($report['totalRevenue'], 2, ',', ' ') }} zł</strong>, z czego aż <strong class="text-amber-400">{{ number_format($report['beerRevenue'], 2, ',', ' ') }} zł ({{ round(($report['beerRevenue'] / $report['totalRevenue']) * 100, 1) }}%)</strong> stanowiło piwo rzemieślnicze.
				</li>
				<li>
					<strong class="text-white">Dominacja liderów ({{ $report['insights']['top1'] }} & {{ $report['insights']['top2'] }}):</strong>
					Te dwa piwa wygenerowały łącznie <strong class="text-amber-400">{{ number_format($report['insights']['topTwoRevenue'], 2, ',', ' ') }} zł</strong>,
					co stanowi aż <strong class="text-emerald-400">{{ $report['insights']['topTwoShare'] }}%</strong> całej sprzedaży piwa!
				</li>
				<li>
					<strong class="text-white">Kufel 500 ml filarem sprzedaży:</strong>
					Wydano łącznie <strong class="text-amber-400">{{ $report['insights']['total500mlCount'] }} kufli/butelek 0.5L</strong>, co pokazuje, że goście najchętniej sięgają po standardową pojemność.
				</li>
				<li>
					<strong class="text-white">Potencjał sprzedaży na wynos (1L PET):</strong>
					W butelkach PET sprzedano <strong class="text-amber-400">{{ number_format($report['insights']['petLiters'], 1, ',', ' ') }} L</strong> piwa,
					co przyniosło <strong class="text-emerald-400">{{ number_format($report['insights']['petRevenue'], 2, ',', ' ') }} zł</strong> dodatkowego obrotu.
				</li>
				<li>
					<strong class="text-white">Rotacja beczek:</strong> Łącznie z kranów i butelek zeszło <strong class="text-white">{{ number_format($report['totalLiters'], 1, ',', ' ') }} Litrów</strong>, czyli równowartość <strong class="text-sky-400">{{ round($report['totalLiters'] / 50, 1) }} pełnych kegów (50L)</strong>.
				</li>
			</ul>
		</div>
		
		<!-- SPRZEDAŻ POZOSTAŁA -->
		@if(count($report['others']) > 0)
			<div class="glass-card rounded-2xl p-6">
				<div class="flex justify-between items-center mb-4">
					<h3 class="text-lg font-bold text-white flex items-center gap-2">
						<span>🍿</span> Przekąski, Napoje bezalkoholowe i Akcesoria
					</h3>
					<span class="text-sm font-bold text-amber-400">Razem: {{ number_format($otherRevenue, 2, ',', ' ') }} zł</span>
				</div>
				<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 text-center">
					@foreach($report['others'] as $item)
						<div class="bg-gray-800/40 p-3 rounded-xl border border-gray-800">
							<div class="text-xs text-gray-400 truncate">{{ $item['name'] }}</div>
							<div class="font-bold text-white text-sm mt-1">{{ $item['qty'] }} szt.</div>
							<div class="text-xs text-amber-400">{{ number_format($item['revenue'], 2, ',', ' ') }} zł</div>
						</div>
					@endforeach
				</div>
			</div>
		@endif
		
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
			<p class="text-sm text-gray-400 max-w-md mx-auto mb-6">Wybierz plik CSV wyeksportowany z systemu sprzedaży, aby wygenerować pełną prezentację.</p>
		</div>
	@endif

</div>
</body>
</html>