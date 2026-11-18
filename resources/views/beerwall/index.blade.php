<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<!-- Meta tagi przeciw cache -->
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	<title>Beer Wall — Twin Brothers</title>
	@include('beerwall.partials.styles')
</head>
<body>

<main class="page">
	@include('beerwall.partials.closed_overlay')
	<header class="header">
		<div class="brand"><img src="https://twinbrothersbrewery.com/twinbrothers00_white.png" alt="Logo"></div>
		<div class="title-center"><div class="rule">KARTA DOSTĘPNYCH PIW</div></div>
		<div class="clock">
			<div class="time" id="time">--:--</div>
			<div class="date" id="date">—</div>
		</div>
	</header>
	
	<!-- Kontener na piwa -->
	<section class="list" id="list">
		<!-- Dane zostaną wczytane przez JavaScript -->
	</section>
	
	<section class="snacks-section">
		<div class="snacks-grid" id="snacks-list">
			<div class="snack-card">
				<div class="snack-name">Przekąski</div>
				<div class="snack-price">12 zł</div>
			</div>
			<div class="snack-card">
				<div class="snack-name">Woda G/NG 330ml</div>
				<div class="snack-price">10 zł</div>
			</div>
			<div class="snack-card">
				<div class="snack-name">Coca-Cola 330ml</div>
				<div class="snack-price">12 zł</div>
			</div>
			<div class="snack-card">
				<div class="snack-name">Kufel firmowy</div>
				<div class="snack-price">39 zł</div>
			</div></div>
	</section>
</main>

@include('beerwall.partials.scripts')

</body>
</html>