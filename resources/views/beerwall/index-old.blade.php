<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Beer Wall — Twin Brothers</title>
	<style>
        :root {
            --bg: #0B0B0B;
            --border: rgba(255,255,255,.12);
            --text: #F8F8F8;
            --text-muted: #CFCFCF;
            --gold: oklch(90.5% 0.182 98.111);
            --gold-soft: rgba(199,161,58,.2);
            --blue: #58AFFF;
            --radius: 1.5vh;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            background: radial-gradient(ellipse at top, #131313 0%, var(--bg) 55%, #050505 100%);
            color: var(--text);
            font-family: sans-serif;
            width: 100vw;
            height: 100vh;
            overflow: hidden; /* Brak pasków przewijania */
        }

        /* Warstwa tła */
        body::before {
            content: ""; position: fixed; inset: 0;
            background: radial-gradient(circle at 15% -10%, rgba(199,161,58,0.1), transparent 50%);
            pointer-events: none;
        }

        .page {
            position: relative;
            padding: 0vh 1vw 1vh; /* Safe Area dla TV (chroni przed ucinaniem brzegów) */
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            display: grid;
            grid-template-columns: 15vw 1fr 20vw;
            align-items: center;
            height: 145px;
        }
        .brand img { height: 14vh; width: auto; }
        .title-center .rule {
            color: var(--gold); font-size: 6.5vh; text-align: center;
            letter-spacing: 0.2em; text-transform: uppercase; font-weight: bold;
        }
        .clock { text-align: right; }
        .clock .time { font-size: 7.5vh; font-weight: bold; line-height: 1; }
        .clock .date { font-size: 2.5vh; color: var(--text-muted); text-transform: uppercase; }

        /* Beer List */
        .list {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 1.5vh;
            margin: 2vh 0;
        }

        .row {
            position: relative;
            display: grid;
            grid-template-columns: 6vh 2fr 10vw 28vw;
            align-items: center;
            gap: 2vw;
            padding: 1.5vh 2vw;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            border-radius: var(--radius);
        }

        .row.is-end { opacity: 0.3; filter: grayscale(1); }

        /* Styl dla WKRÓTCE - wyszarza wszystko oprócz badge'a */
        .row.is-coming > *:not(.coming-soon-badge) {
            opacity: 0.4;
            filter: grayscale(1);
        }

        .end-overlay {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-5deg);
            border: 0.5vh solid #ff3b3b; padding: 1vh 2vw; font-size: 4vh; font-weight: 900;
            color: #ff3b3b; background: rgba(0,0,0,0.9); z-index: 10;
        }

        .mug { width: 7vh; height: 7vh; color: var(--gold); position: relative; }
        .beer-badge {
            position: absolute; top: -1vh; left: -1vh; width: 4vh; height: 4.5vh;
            background: linear-gradient(145deg, #ffe066, #ffb300); color: #000;
            display: flex; align-items: center; justify-content: center;
            font-size: 4.5vh; font-weight: bold; border-radius: 0.5vh;
        }

        .info { overflow: hidden; }
        .name { font-size: 4.9vh; font-weight: 800; text-transform: uppercase; white-space: nowrap; }
        .style-line { color: var(--gold); font-size: 2.2vh; font-weight: 600; text-transform: uppercase; }
        .beer-desc { font-size: 2.2vh; color: var(--text-muted); margin-top: 0.5vh; }

        .stats {
            display: grid; grid-template-columns: 1fr 1fr;
            border-left: 1px solid var(--border); border-right: 1px solid var(--border);
        }
        .stat { text-align: center; }
        .stat .val { font-size: 3.5vh; font-weight: bold; }
        .stat .lbl { font-size: 2vh; color: var(--text-dim); text-transform: uppercase; }

        .prices { display: flex; gap: 1vw; justify-content: flex-end; }
        .price {
            background: rgba(255,255,255,0.06); padding: 1vh; border-radius: 1vh;
            min-width: 5.5vw; text-align: center;
        }
        .price .vol { font-size: 3vh; display: block; }
        .price .amt { font-size: 6.9vh; font-weight: bold; color: var(--gold); }

        /* Snacks */
        .snacks-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5vw; }
        .snack-card {
            background: rgba(255,255,255,0.04); border: 1px solid var(--border);
            border-radius: 1vh; padding: 1.5vh; display: flex; justify-content: space-between; align-items: center;
        }
        .snack-name { font-size: 3vh; font-weight: bold; }
        .snack-price { font-size: 5.5vh; font-weight: bold; color: var(--blue); }

        .premiere-badge {
            position: absolute; left: 1vh; top: -2vh; background: #ff3b3b;
            padding: 0.5vh 1.5vw; font-size: 2.8vh; font-weight: bold; border-radius: 0.5vh;
            z-index: 10;
        }

        .coming-soon-badge {
            position: absolute; left: 1vh; top: -2vh; background: #ff9800;
            padding: 0.5vh 1.5vw; font-size: 2.8vh; font-weight: bold; border-radius: 0.5vh;
            z-index: 10;
        }
	</style>
</head>
<body>

<main class="page">
	<header class="header">
		<div class="brand"><img src="https://twinbrothersbrewery.com/twinbrothers00_white.png" alt="Logo"></div>
		<div class="title-center"><div class="rule">KARTA DOSTĘPNYCH PIW</div></div>
		<div class="clock">
			<div class="time" id="time">--:--</div>
			<div class="date" id="date">—</div>
		</div>
	</header>
	
	<section class="list" id="list">
		@foreach($beerwall as $key => $beer)
			<article class="row {{ $beer->is_ended ? 'is-end' : '' }} {{ $beer->is_coming_soon ? 'is-coming' : '' }}">
				
				@if($beer->is_coming_soon)
					<div class="coming-soon-badge">WKRÓTCE DOSTĘPNE</div>
				@elseif($beer->is_premiere)
					<div class="premiere-badge">PREMIERA</div>
				@endif
				
				@if($beer->is_ended)
					<div class="end-overlay">WYPRZEDANE</div>
				@endif
				
				<div class="mug">
					<div class="beer-badge">{{ $key + 1 }}</div>
					<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5">
						<path d="M14 16h28v34a6 6 0 0 1-6 6H20a6 6 0 0 1-6-6V16z"/>
						<path d="M42 22h6a6 6 0 0 1 6 6v10a6 6 0 0 1-6 6h-6"/>
					</svg>
				</div>
				
				<div class="info">
					<div class="name">
						{{ $beer->beer_name }}
						<span class="style-line">/ {{ $beer->beer_style }}</span>
					</div>
					<div class="beer-desc">{{ $beer->beer_description }}</div>
				</div>
				
				<div class="stats">
					<div class="stat">
						<div class="val">{{ $beer->beer_blg }}°</div>
						<div class="lbl">Blg</div>
					</div>
					<div class="stat">
						<div class="val">{{ $beer->beer_alc }}%</div>
						<div class="lbl">Alk.</div>
					</div>
				</div>
				
				<div class="prices">
					<div class="price">
						<span class="vol">0.25L</span>
						<span class="amt">{{ $beer->beer_price_small }} <span style="font-size:20px">zł</span></span>
					</div>
					<div class="price">
						<span class="vol">0.5L</span>
						<span class="amt">{{ $beer->beer_price_medium }} <span style="font-size:20px">zł</span></span>
					</div>
					<div class="price">
						<span class="vol">1L</span>
						<span class="amt">{{ $beer->beer_price_large }} <span style="font-size:20px">zł</span></span>
					</div>
				</div>
			</article>
		@endforeach
	
	</section>
	
	<section class="snacks-section">
		<div class="snacks-grid" id="snacks-list">
			<div class="snack-card">
				<div class="snack-name">Chipsy</div>
				<div class="snack-price">12 zł</div>
			</div>
			<div class="snack-card">
				<div class="snack-name">Woda 330ml</div>
				<div class="snack-price">10 zł</div>
			</div>
			<div class="snack-card">
				<div class="snack-name">Coca-Cola 200ml</div>
				<div class="snack-price">12 zł</div>
			</div>
			<div class="snack-card">
				<div class="snack-name">Kufel firmowy</div>
				<div class="snack-price">39 zł</div>
			</div></div>
	</section>
</main>

<script>
    function tick() {
        const d = new Date();
        document.getElementById('time').textContent = d.toLocaleTimeString('pl-PL', { hour: '2-digit', minute: '2-digit' });
        const months = ["stycznia","lutego","marca","kwietnia","maja","czerwca","lipca","sierpnia","września","października","listopada","grudnia"];
        const days = ["Niedz.","Pon.","Wt.","Śr.","Czw.","Pt.","Sob."];
        document.getElementById('date').textContent = `${days[d.getDay()]}, ${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
    }
    tick();
    setInterval(tick, 2000);
</script>

<video id="noSleepVideo" loop muted playsinline style="position:absolute; width:1px; height:1px; opacity: 0.01;">
	<source src="https://github.com/intel-iot-devkit/sample-videos/raw/master/bottle-detection.mp4" type="video/mp4">
</video>

<script>
    window.addEventListener('load', () => {
        const video = document.getElementById('noSleepVideo');
        video.play().catch(error => {
            document.body.addEventListener('click', () => {
                video.play();
            }, { once: true });
        });
    });
</script>

</body>
</html>