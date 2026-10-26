<!-- ================= HALLOWEEN FULL OVERHAUL (START) ================= -->
<style>
    /* 1. GLOBALNE NADPISANIE KOLORÓW NA HALLOWEEN */
    :root {
        --gold: #ff7518 !important;                    /* Dyniowy, jaskrawy pomarańcz */
        --gold-soft: rgba(255, 117, 24, 0.3) !important;
        --blue: #b55fe6 !important;                    /* Magiczny, upiorny fiolet */
        --border: rgba(255, 117, 24, 0.25) !important; /* Pomarańczowe ramki */
    }

    /* Gwarancja, że całe menu leży nad dynią w tle */
    .page {
        position: relative;
        z-index: 1;
    }

    /* Mroczne tło z krwisto-pomarańczową poświatą */
    html, body {
        background: radial-gradient(ellipse at top, #1e0902 0%, #0b0503 50%, #000000 100%) !important;
    }
    body::before {
        background: radial-gradient(circle at 50% -10%, rgba(255, 70, 0, 0.22), transparent 60%) !important;
    }

    /* Płonący / krwisty nagłówek */
    .title-center .rule {
        text-shadow: 0 0 15px rgba(255, 80, 0, 0.8), 0 0 30px rgba(220, 20, 0, 0.6) !important;
        letter-spacing: 0.25em !important;
    }

    /* Karty piw w klimacie lochu (dynia z tła subtelnie przez nie przebija) */
    .row {
        background: rgba(25, 12, 6, 0.6) !important;
        border: 1px solid rgba(255, 117, 24, 0.25) !important;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.7), inset 0 0 15px rgba(255, 80, 0, 0.06) !important;
        backdrop-filter: blur(4px);
    }

    /* Numerki piwa w dyniowym gradiencie */
    .beer-badge {
        background: linear-gradient(145deg, #ff9d00, #e65100) !important;
        color: #000 !important;
        box-shadow: 0 0 12px rgba(255, 100, 0, 0.6) !important;
    }

    /* Kafelki z cenami */
    .price {
        background: rgba(255, 117, 24, 0.08) !important;
        border: 1px solid rgba(255, 117, 24, 0.2) !important;
    }

    /* Przekąski - nawiedzony klimat */
    .snack-card {
        background: rgba(35, 10, 45, 0.45) !important;
        border-color: rgba(181, 95, 230, 0.3) !important;
        box-shadow: inset 0 0 10px rgba(181, 95, 230, 0.1) !important;
    }

    /* ================= 2. OGROMNA DYNIA W TLE NA ŚRODKU ================= */
    .hw-bg-pumpkin {
        position: fixed;
        top: 54%;
        left: 50%;
        transform: translate(-50%, -50%);
        height: 75vh;          /* Wysokość dyni na 75% ekranu */
        width: auto;
        opacity: 0.12;         /* <-- REGULACJA WIDOCZNOŚCI (np. 0.08 = ciemniejsza, 0.18 = wyraźniejsza) */
        pointer-events: none;
        z-index: 0;            /* Leży za kartami menu */
        filter: drop-shadow(0 0 45px rgba(255, 85, 0, 0.7));
        animation: hw-pulse-bg 4s ease-in-out infinite alternate;
    }
    @keyframes hw-pulse-bg {
        0%   { filter: drop-shadow(0 0 30px rgba(255, 60, 0, 0.5)); transform: translate(-50%, -50%) scale(0.98); }
        100% { filter: drop-shadow(0 0 55px rgba(255, 110, 0, 0.8)); transform: translate(-50%, -50%) scale(1.02); }
    }

    /* ================= 3. POZOSTAŁE EFEKTY I ANIMACJE ================= */
    .hw-overlay-box {
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 95;
        overflow: hidden;
        box-shadow: inset 0 0 120px rgba(255, 60, 0, 0.28), inset 0 0 40px rgba(0, 0, 0, 0.9);
    }

    /* Ściekająca krew/śluz z góry */
    .hw-drips {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 55px;
        fill: #8b0000;
        filter: drop-shadow(0 6px 8px rgba(0,0,0,0.9));
        opacity: 0.9;
    }

    /* Pajęczyny w rogach */
    .hw-corner-web {
        position: absolute;
        width: 130px;
        height: 130px;
        opacity: 0.35;
    }
    .hw-web-l { top: 0; left: 0; }
    .hw-web-r { top: 0; right: 0; transform: scaleX(-1); }

    /* Schodzący i bujający się pająk */
    .hw-spider-wrap {
        position: absolute;
        top: 0;
        left: 9vw;
        animation: spider-drop 14s ease-in-out infinite;
        transform-origin: top center;
    }
    .hw-spider-thread {
        width: 1.5px;
        height: 200px;
        background: rgba(255, 255, 255, 0.4);
        margin: 0 auto;
    }
    .hw-spider-body {
        width: 46px;
        height: 46px;
        fill: #0c0c0c;
        filter: drop-shadow(0 0 8px rgba(255, 60, 0, 0.8));
    }
    @keyframes spider-drop {
        0%, 100% { transform: translateY(-180px); }
        35%      { transform: translateY(0px) rotate(8deg); }
        55%      { transform: translateY(40px) rotate(-8deg); }
        75%      { transform: translateY(-60px) rotate(4deg); }
    }

    /* Płynący duch */
    .hw-ghost {
        position: absolute;
        width: 90px;
        height: 100px;
        opacity: 0;
        filter: drop-shadow(0 0 25px rgba(100, 255, 218, 0.85));
        animation: ghost-float 24s ease-in-out infinite;
    }
    @keyframes ghost-float {
        0%   { left: -120px; top: 65vh; opacity: 0; transform: scale(0.6) rotate(10deg); }
        15%  { opacity: 0.7; }
        50%  { top: 40vh; transform: scale(1.05) rotate(-10deg); }
        85%  { opacity: 0.7; }
        100% { left: 105vw; top: 55vh; opacity: 0; transform: scale(0.7) rotate(15deg); }
    }

    /* Chmara nietoperzy */
    .hw-bats {
        position: absolute;
        top: 15vh;
        left: -150px;
        animation: bats-fly 18s linear infinite;
    }
    .hw-bat-single {
        width: 48px;
        height: 28px;
        fill: #111;
        filter: drop-shadow(0 0 6px rgba(0,0,0,0.9));
        display: inline-block;
        margin-right: 10px;
    }
    @keyframes bats-fly {
        0%   { left: -160px; top: 25vh; transform: scale(0.6) rotate(10deg); opacity: 0; }
        10%  { opacity: 0.9; }
        50%  { top: 12vh; transform: scale(0.9) rotate(-15deg); }
        90%  { opacity: 0.9; }
        100% { left: 105vw; top: 8vh; transform: scale(0.7) rotate(10deg); opacity: 0; }
    }
</style>

<!-- 1. DUŻA DYNIA W TLE NA ŚRODKU (SVG) -->
<svg class="hw-bg-pumpkin" viewBox="0 0 100 100">
	<!-- Ogon dyni -->
	<path d="M48 20 Q50 6 62 8 Q54 18 52 20 Z" fill="#2e7d32"/>
	<!-- Kształt i żebra dyni -->
	<ellipse cx="50" cy="58" rx="46" ry="38" fill="#e65100"/>
	<ellipse cx="50" cy="58" rx="34" ry="38" fill="#f57c00"/>
	<ellipse cx="50" cy="58" rx="18" ry="37" fill="#ff9800"/>
	<!-- Złowieszcze wycięte oczy i nos -->
	<polygon points="30,46 42,49 35,37" fill="#0b0503"/>
	<polygon points="70,46 58,49 65,37" fill="#0b0503"/>
	<polygon points="50,52 44,60 56,60" fill="#0b0503"/>
	<!-- Straszne zębiska -->
	<path d="M24,67 Q50,88 76,67 Q70,78 65,69 Q60,80 54,70 Q50,80 46,70 Q40,80 35,69 Q30,78 24,67 Z" fill="#0b0503"/>
	<!-- Żarzące się wnętrze ust i oczu -->
	<polygon points="32,46 40,48 35,39" fill="#ffee55"/>
	<polygon points="68,46 60,48 65,39" fill="#ffee55"/>
	<polygon points="50,54 46,59 54,59" fill="#ffee55"/>
	<path d="M26,68 Q50,85 74,68" stroke="#ffee55" stroke-width="2.5" fill="none"/>
</svg>

<!-- 2. WARSTWA NAWIERZCHNIOWA (ŚLUZ, PAJĘCZYNY, PAJĄK, DUCH, NIETOPERZE) -->
<div class="hw-overlay-box">
	<!-- Ściekająca krew -->
	<svg class="hw-drips" viewBox="0 0 1200 55" preserveAspectRatio="none">
		<path d="M0,0 L1200,0 L1200,12
                 C1150,12 1140,42 1130,42 C1120,42 1110,12 1060,12
                 C1020,12 1010,50 995,50 C980,50 970,12 920,12
                 C880,12 870,32 860,32 C850,32 840,12 780,12
                 C730,12 720,54 705,54 C690,54 680,12 620,12
                 C570,12 560,38 550,38 C540,38 530,12 480,12
                 C440,12 430,52 415,52 C400,52 390,12 330,12
                 C290,12 280,32 270,32 C260,32 250,12 190,12
                 C140,12 130,48 115,48 C100,48 90,12 0,12 Z"/>
	</svg>
	
	<!-- Pajęczyny w narożnikach -->
	<svg class="hw-corner-web hw-web-l" viewBox="0 0 100 100">
		<path d="M0,0 L100,0 M0,0 L80,50 M0,0 L50,80 M0,0 L0,100" stroke="#fff" stroke-width="1.2"/>
		<path d="M25,0 Q22,22 0,25 M50,0 Q45,45 0,50 M75,0 Q68,68 0,75" fill="none" stroke="#fff" stroke-width="1"/>
	</svg>
	<svg class="hw-corner-web hw-web-r" viewBox="0 0 100 100">
		<path d="M0,0 L100,0 M0,0 L80,50 M0,0 L50,80 M0,0 L0,100" stroke="#fff" stroke-width="1.2"/>
		<path d="M25,0 Q22,22 0,25 M50,0 Q45,45 0,50 M75,0 Q68,68 0,75" fill="none" stroke="#fff" stroke-width="1"/>
	</svg>
	
	<!-- Pająk na nitce -->
	<div class="hw-spider-wrap">
		<div class="hw-spider-thread"></div>
		<svg class="hw-spider-body" viewBox="0 0 100 100">
			<ellipse cx="50" cy="55" rx="16" ry="20"/>
			<circle cx="50" cy="32" r="10"/>
			<circle cx="46" cy="30" r="2.5" fill="#ff0000"/>
			<circle cx="54" cy="30" r="2.5" fill="#ff0000"/>
			<path d="M40,35 Q20,15 5,30 M40,40 Q15,30 2,50 M40,45 Q15,55 8,75 M42,50 Q25,75 18,90" stroke="#0c0c0c" stroke-width="4" fill="none"/>
			<path d="M60,35 Q80,15 95,30 M60,40 Q85,30 98,50 M60,45 Q85,55 92,75 M58,50 Q75,75 82,90" stroke="#0c0c0c" stroke-width="4" fill="none"/>
		</svg>
	</div>
	
	<!-- Nawiedzony duch -->
	<svg class="hw-ghost" viewBox="0 0 100 120">
		<path d="M50 10 C30 10 15 28 15 55 C15 75 10 95 20 110 C25 100 35 100 40 110 C45 100 55 100 60 110 C65 100 75 100 80 110 C90 95 85 75 85 55 C85 28 70 10 50 10 Z" fill="rgba(215, 255, 245, 0.7)"/>
		<ellipse cx="38" cy="45" rx="5" ry="8" fill="#000"/>
		<ellipse cx="62" cy="45" rx="5" ry="8" fill="#000"/>
		<ellipse cx="50" cy="65" rx="7" ry="12" fill="#000"/>
	</svg>
	
	<!-- Nietoperze -->
	<div class="hw-bats">
		<svg class="hw-bat-single" viewBox="0 0 64 32"><path d="M32,18 C28,12 18,6 4,10 C1,11 0,14 2,16 C7,18 12,23 16,30 C20,24 26,23 32,24 C38,23 44,24 48,30 C52,23 57,18 62,16 C64,14 63,11 60,10 C46,6 36,12 32,18 Z"/></svg>
		<svg class="hw-bat-single" style="transform: scale(0.7) translateY(-20px);" viewBox="0 0 64 32"><path d="M32,18 C28,12 18,6 4,10 C1,11 0,14 2,16 C7,18 12,23 16,30 C20,24 26,23 32,24 C38,23 44,24 48,30 C52,23 57,18 62,16 C64,14 63,11 60,10 C46,6 36,12 32,18 Z"/></svg>
		<svg class="hw-bat-single" style="transform: scale(0.8) translateY(15px);" viewBox="0 0 64 32"><path d="M32,18 C28,12 18,6 4,10 C1,11 0,14 2,16 C7,18 12,23 16,30 C20,24 26,23 32,24 C38,23 44,24 48,30 C52,23 57,18 62,16 C64,14 63,11 60,10 C46,6 36,12 32,18 Z"/></svg>
	</div>
</div>
<!-- ================= HALLOWEEN FULL OVERHAUL (END) ================= -->