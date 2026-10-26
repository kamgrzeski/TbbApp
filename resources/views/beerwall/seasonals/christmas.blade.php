<!-- ================= CHRISTMAS FULL OVERHAUL (START) ================= -->
<style>
    /* 1. GLOBALNE NADPISANIE KOLORÓW NA ŚWIĘTA */
    :root {
        --gold: #ffc048 !important;                    /* Ciepłe, świąteczne złoto */
        --gold-soft: rgba(255, 192, 72, 0.25) !important;
        --blue: #48dbfb !important;                    /* Mroźny, lodowy błękit */
        --border: rgba(255, 255, 255, 0.18) !important;/* Śnieżnobiałe, subtelne ramki */
    }

    /* Gwarancja, że całe menu leży nad choinką w tle */
    .page {
        position: relative;
        z-index: 1;
    }

    /* Tło - zimowa noc */
    html, body {
        background: radial-gradient(ellipse at top, #0f1c2e 0%, #080d14 55%, #020406 100%) !important;
    }
    body::before {
        background: radial-gradient(circle at 50% -10%, rgba(72, 219, 251, 0.15), transparent 60%) !important;
    }

    /* Świąteczny tytuł - rubinowa czerwień i złoty blask */
    .title-center .rule {
        color: #ff3838 !important;
        text-shadow: 0 0 15px rgba(255, 56, 56, 0.7), 0 0 30px rgba(255, 192, 72, 0.5) !important;
        letter-spacing: 0.25em !important;
    }

    /* Karty piw - oszronione szkło (choinka delikatnie przez nie przebija) */
    .row {
        background: rgba(15, 28, 46, 0.6) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.6), inset 0 0 15px rgba(72, 219, 251, 0.05) !important;
        backdrop-filter: blur(4px);
    }

    /* Numerki piwa - styl czapki Mikołaja */
    .beer-badge {
        background: linear-gradient(145deg, #ff3838, #c0392b) !important;
        color: #ffffff !important;
        border: 2px solid #ffffff !important;
        box-shadow: 0 0 12px rgba(255, 56, 56, 0.6) !important;
    }

    /* Kafelki z cenami */
    .price {
        background: rgba(255, 255, 255, 0.06) !important;
        border: 1px solid rgba(255, 192, 72, 0.3) !important;
    }

    /* Przekąski - zimowy błękit */
    .snack-card {
        background: rgba(15, 35, 55, 0.5) !important;
        border-color: rgba(72, 219, 251, 0.3) !important;
        box-shadow: inset 0 0 12px rgba(72, 219, 251, 0.08) !important;
    }

    /* ================= 2. CHOINKA I PREZENTY W TLE NA ŚRODKU ================= */
    .xm-bg-tree {
        position: fixed;
        top: 54%;
        left: 50%;
        transform: translate(-50%, -50%);
        height: 75vh;
        width: auto;
        opacity: 0.11;         /* Regulacja przezroczystości choinki w tle */
        pointer-events: none;
        z-index: 0;
        filter: drop-shadow(0 0 35px rgba(46, 213, 115, 0.5));
    }

    /* ================= 3. PREZENTY W ROGU EKRANU ================= */
    .xm-gifts {
        position: absolute;
        bottom: 1.2vh;
        right: 1.5vw;
        width: 100px;
        height: 80px;
        filter: drop-shadow(0 0 15px rgba(255, 192, 72, 0.5));
        animation: xm-gift-bounce 4s ease-in-out infinite alternate;
    }
    @keyframes xm-gift-bounce {
        0%   { transform: scale(1); }
        100% { transform: scale(1.05) translateY(-3px); filter: drop-shadow(0 0 22px rgba(255, 56, 56, 0.7)); }
    }

    /* ================= 4. RESZTA EFEKTÓW ŚWIĄTECZNYCH ================= */
    .xm-overlay-box {
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 95;
        overflow: hidden;
        box-shadow: inset 0 0 100px rgba(72, 219, 251, 0.15), inset 0 0 30px rgba(0, 0, 0, 0.8);
    }

    /* Girlanda ze światełkami na górze */
    .xm-lights {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        display: flex;
        justify-content: space-around;
        padding: 0 1vw;
        border-top: 2px solid rgba(255,255,255,0.2);
    }
    .xm-bulb {
        width: 12px;
        height: 18px;
        border-radius: 50% 50% 45% 45%;
        margin-top: 3px;
        animation: xm-glow 1.6s infinite alternate ease-in-out;
    }
    .xm-b-red    { background: #ff3838; box-shadow: 0 0 14px #ff3838; animation-delay: 0s; }
    .xm-b-gold   { background: #ffc048; box-shadow: 0 0 14px #ffc048; animation-delay: 0.4s; }
    .xm-b-green  { background: #2ed573; box-shadow: 0 0 14px #2ed573; animation-delay: 0.8s; }
    .xm-b-blue   { background: #1e90ff; box-shadow: 0 0 14px #1e90ff; animation-delay: 1.2s; }

    @keyframes xm-glow {
        0%   { opacity: 0.35; transform: scale(0.85); }
        100% { opacity: 1;    transform: scale(1.15); }
    }

    /* Bujająca się bombka po lewej */
    .xm-bauble-wrap {
        position: absolute;
        top: 0;
        left: 3vw;
        transform-origin: top center;
        animation: xm-swing 5s ease-in-out infinite alternate;
    }
    .xm-bauble-string {
        width: 1.5px;
        height: 90px;
        background: rgba(255, 215, 0, 0.6);
        margin: 0 auto;
    }
    .xm-bauble {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: radial-gradient(circle at 35% 35%, #ff6b81, #c0392b 70%, #570000 100%);
        box-shadow: 0 0 18px rgba(255, 56, 56, 0.6);
        border: 1px solid rgba(255, 215, 0, 0.6);
    }
    @keyframes xm-swing {
        0%   { transform: rotate(8deg); }
        100% { transform: rotate(-8deg); }
    }

    /* Padające płatki śniegu */
    .xm-snow-wrap { position: absolute; inset: 0; }
    .xm-flake {
        position: absolute;
        top: -20px;
        color: #ffffff;
        font-size: 1.8vh;
        opacity: 0.8;
        animation: xm-fall linear infinite;
    }
    @keyframes xm-fall {
        0%   { transform: translateY(0) rotate(0deg); opacity: 0; }
        10%  { opacity: 0.85; }
        90%  { opacity: 0.85; }
        100% { transform: translateY(105vh) rotate(360deg); opacity: 0; }
    }

    .xm-f1  { left: 5vw;  font-size: 2.2vh; animation-duration: 9s;  animation-delay: 0s; }
    .xm-f2  { left: 14vw; font-size: 1.4vh; animation-duration: 13s; animation-delay: 2s; opacity: 0.5; }
    .xm-f3  { left: 24vw; font-size: 2.6vh; animation-duration: 8s;  animation-delay: 4s; }
    .xm-f4  { left: 36vw; font-size: 1.8vh; animation-duration: 11s; animation-delay: 1s; }
    .xm-f5  { left: 48vw; font-size: 1.2vh; animation-duration: 14s; animation-delay: 5s; opacity: 0.4; }
    .xm-f6  { left: 58vw; font-size: 2.4vh; animation-duration: 9.5s; animation-delay: 2.5s; }
    .xm-f7  { left: 69vw; font-size: 1.6vh; animation-duration: 12s; animation-delay: 0.5s; }
    .xm-f8  { left: 79vw; font-size: 2.0vh; animation-duration: 10s; animation-delay: 3.5s; }
    .xm-f9  { left: 88vw; font-size: 1.3vh; animation-duration: 15s; animation-delay: 6s; opacity: 0.5; }
    .xm-f10 { left: 96vw; font-size: 2.5vh; animation-duration: 8.5s; animation-delay: 1.8s; }
</style>

<!-- 1. DUŻA CHOINKA Z PREZENTAMI W TLE NA ŚRODKU (SVG) -->
<svg class="xm-bg-tree" viewBox="0 0 100 135">
	<polygon points="50,4 53,13 62,13 55,19 57,28 50,23 43,28 45,19 38,13 47,13" fill="#ffd32a"/>
	<polygon points="50,20 28,48 72,48" fill="#2ed573"/>
	<polygon points="50,38 20,76 80,76" fill="#20bf6b"/>
	<polygon points="50,60 12,110 88,110" fill="#10ac84"/>
	<rect x="44" y="110" width="12" height="15" fill="#5d4037" rx="2"/>
	<!-- Światełka na choince -->
	<circle cx="44" cy="40" r="2.5" fill="#ff4757"/>
	<circle cx="56" cy="44" r="2.5" fill="#ffa502"/>
	<circle cx="34" cy="68" r="3" fill="#1e90ff"/>
	<circle cx="66" cy="70" r="3" fill="#ff4757"/>
	<circle cx="50" cy="88" r="3.5" fill="#ffa502"/>
	<circle cx="28" cy="100" r="3.5" fill="#2ed573"/>
	<circle cx="72" cy="98" r="3.5" fill="#1e90ff"/>
	<!-- Prezenty pod choinką w tle -->
	<rect x="22" y="114" width="16" height="14" fill="#ff4757" rx="1"/>
	<rect x="28" y="114" width="4" height="14" fill="#ffd32a"/>
	<rect x="62" y="112" width="18" height="16" fill="#1e90ff" rx="1"/>
	<rect x="69" y="112" width="4" height="16" fill="#ffffff"/>
</svg>

<!-- 2. WARSTWA NAWIERZCHNIOWA (ŚNIEG, LAMPKI, BOMBKA, PREZENTY W ROGU) -->
<div class="xm-overlay-box">
	<!-- Lampki choinkowe na górze -->
	<div class="xm-lights">
		<div class="xm-bulb xm-b-red"></div>
		<div class="xm-bulb xm-b-gold"></div>
		<div class="xm-bulb xm-b-green"></div>
		<div class="xm-bulb xm-b-blue"></div>
		<div class="xm-bulb xm-b-red"></div>
		<div class="xm-bulb xm-b-gold"></div>
		<div class="xm-bulb xm-b-green"></div>
		<div class="xm-bulb xm-b-blue"></div>
		<div class="xm-bulb xm-b-red"></div>
		<div class="xm-bulb xm-b-gold"></div>
		<div class="xm-bulb xm-b-green"></div>
		<div class="xm-bulb xm-b-blue"></div>
		<div class="xm-bulb xm-b-red"></div>
		<div class="xm-bulb xm-b-gold"></div>
		<div class="xm-bulb xm-b-green"></div>
		<div class="xm-bulb xm-b-blue"></div>
	</div>
	
	<!-- Bujająca się bombka po lewej -->
	<div class="xm-bauble-wrap">
		<div class="xm-bauble-string"></div>
		<div class="xm-bauble"></div>
	</div>
	
	<!-- PREZENTY Z KOKARDAMI W PRAWYM DOLNYM ROGU (Ostry wektor SVG) -->
	<svg class="xm-gifts" viewBox="0 0 120 100">
		<!-- Prezent 1: Duży czerwony z tyłu -->
		<rect x="45" y="35" width="60" height="55" fill="#e74c3c" rx="4"/>
		<rect x="45" y="30" width="60" height="10" fill="#c0392b" rx="2"/>
		<rect x="71" y="30" width="8" height="60" fill="#f1c40f"/>
		<rect x="45" y="58" width="60" height="8" fill="#f1c40f"/>
		<!-- Złota kokarda dużego prezentu -->
		<ellipse cx="69" cy="27" rx="7" ry="5" fill="#f39c12"/>
		<ellipse cx="81" cy="27" rx="7" ry="5" fill="#f39c12"/>
		<circle cx="75" cy="28" r="4" fill="#f1c40f"/>
		
		<!-- Prezent 2: Zielony z przodu po lewej -->
		<rect x="10" y="50" width="45" height="40" fill="#27ae60" rx="3"/>
		<rect x="10" y="46" width="45" height="8" fill="#2ecc71" rx="2"/>
		<rect x="29" y="46" width="7" height="44" fill="#ffffff"/>
		<rect x="10" y="66" width="45" height="7" fill="#ffffff"/>
		<!-- Czerwona kokardka zielonego prezentu -->
		<ellipse cx="27" cy="43" rx="6" ry="4" fill="#e74c3c"/>
		<ellipse cx="38" cy="43" rx="6" ry="4" fill="#e74c3c"/>
		<circle cx="32.5" cy="44" r="3.5" fill="#c0392b"/>
		
		<!-- Prezent 3: Mały niebieski na wierzchu -->
		<rect x="68" y="62" width="38" height="28" fill="#2980b9" rx="3"/>
		<rect x="68" y="59" width="38" height="6" fill="#3498db" rx="2"/>
		<rect x="84" y="59" width="6" height="31" fill="#f1c40f"/>
		<!-- Złota kokardka małego prezentu -->
		<circle cx="87" cy="58" r="3.5" fill="#f39c12"/>
	</svg>
	
	<!-- Padające płatki śniegu -->
	<div class="xm-snow-wrap">
		<div class="xm-flake xm-f1">❄</div>
		<div class="xm-flake xm-f2">❅</div>
		<div class="xm-flake xm-f3">❄</div>
		<div class="xm-flake xm-f4">•</div>
		<div class="xm-flake xm-f5">❅</div>
		<div class="xm-flake xm-f6">❄</div>
		<div class="xm-flake xm-f7">•</div>
		<div class="xm-flake xm-f8">❅</div>
		<div class="xm-flake xm-f9">❄</div>
		<div class="xm-flake xm-f10">❅</div>
	</div>
</div>
<!-- ================= CHRISTMAS FULL OVERHAUL (END) ================= -->