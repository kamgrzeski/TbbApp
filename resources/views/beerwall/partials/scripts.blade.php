<!-- Firebase SDK -->
<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
    import { getDatabase, ref, onValue } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-database.js";

    // Konfiguracja Firebase
    const firebaseConfig = {
        apiKey: "AIzaSyD9VCl6GVfQ-x6ACq7h0d_7jYyH-8reDzk",
        authDomain: "tbbspzoo-e8c83.firebaseapp.com",
        databaseURL: "https://tbbspzoo-e8c83-default-rtdb.firebaseio.com",
        projectId: "tbbspzoo-e8c83",
        storageBucket: "tbbspzoo-e8c83.firebasestorage.app",
        messagingSenderId: "828358200785",
        appId: "1:828358200785:web:0eadc8f11e37c3572924f0",
        measurementId: "G-B1L955ERC3"
    };

    const app = initializeApp(firebaseConfig);
    const db = getDatabase(app);
    const beerRef = ref(db, 'beers');

    // Funkcja renderująca piwa na żywo
    onValue(beerRef, (snapshot) => {
        const data = snapshot.val();
        const listContainer = document.getElementById('list');
        listContainer.innerHTML = ''; // Wyczyść listę przed renderowaniem

        if (data) {
            // Zamień obiekt na tablicę i przejdź przez każde piwo
            const beersArray = Array.isArray(data) ? data : Object.values(data);

            beersArray.forEach((beer, index) => {
                const row = document.createElement('article');
                row.className = `row ${beer.is_ended ? 'is-end' : ''} ${beer.is_coming_soon ? 'is-coming' : ''}`;

                // Logika odznak (badges)
                let badgeHtml = '';
                if (beer.is_coming_soon) {
                    badgeHtml = '<div class="coming-soon-badge">WKRÓTCE DOSTĘPNE</div>';
                } else if (beer.is_premiere) {
                    badgeHtml = '<div class="premiere-badge">PREMIERA</div>';
                } else if (beer.is_limited) {
                    badgeHtml = '<div class="limited-badge">LIMITOWANA EDYCJA</div>';
                }

                const endOverlay = beer.is_ended ? '<div class="end-overlay">WYPRZEDANE</div>' : '';

                row.innerHTML = `
                ${badgeHtml}
                ${endOverlay}
                
                <div class="mug">
                    <div class="beer-badge">${index + 1}</div>
                    <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M14 16h28v34a6 6 0 0 1-6 6H20a6 6 0 0 1-6-6V16z"/>
                        <path d="M42 22h6a6 6 0 0 1 6 6v10a6 6 0 0 1-6 6h-6"/>
                    </svg>
                </div>
                
                <div class="info">
                    <div class="name">
                        ${beer.beer_name}
                        <span class="style-line">/ ${beer.beer_style}</span>
                    </div>
                    <div class="beer-desc">${beer.beer_description || ''}</div>
                </div>
                
                <div class="stats" style="margin-right:35px">
                    <div class="stat">
                        <div class="val">${beer.beer_blg}°</div>
                        <div class="lbl">Blg</div>
                    </div>
                    <div class="stat" style="margin-left:15px">
                        <div class="val">${beer.beer_alc}%</div>
                        <div class="lbl">Alk.</div>
                    </div>
                </div>
                
                <div class="prices">
                    <div class="price">
                        <span class="vol">0.25L</span>
                        <span class="amt">${beer.beer_price_small} <span style="font-size: 20px">zł</span></span>
                    </div>
                    <div class="price">
                        <span class="vol">0.5L</span>
                        <span class="amt">${beer.beer_price_medium} <span style="font-size: 20px">zł</span></span>
                    </div>
                    <div class="price">
                        <span class="vol">1L</span>
                        <span class="amt">${beer.beer_price_large} <span style="font-size: 20px">zł</span></span>
                    </div>
                </div>
            `;
                listContainer.appendChild(row);
            });
        }
    });
</script>

<script>
    let cachedSettings = null;

    async function checkClosingTime() {
        try {
            const response = await fetch('/api/beerwall/settings');
            const settings = await response.json();

            if (!settings) {
                console.log("No settings found in database");
                return;
            }

            const daysMap = {
                0: 'sunday', 1: 'monday', 2: 'tuesday', 3: 'wednesday', 4: 'thursday', 5: 'friday', 6: 'saturday'
            };

            const now = new Date();
            const dayKey = daysMap[now.getDay()];
            const closeTimeStr = settings[dayKey + '_close_time'];

            if (closeTimeStr) {
                // Obcinamy sekundy z bazy (np. "11:21:00" -> "11:21")
                const formattedCloseTime = closeTimeStr.substring(0, 5);

                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const currentTimeStr = `${hours}:${minutes}`;

                console.log(`API Check - Close: ${formattedCloseTime} | Now: ${currentTimeStr}`);

                if (currentTimeStr >= formattedCloseTime) {
                    document.getElementById('closed-overlay').style.display = 'flex';
                } else {
                    document.getElementById('closed-overlay').style.display = 'none';
                }
            }
        } catch (error) {
            console.error("Error fetching settings:", error);
        }
    }

    checkClosingTime();

    setInterval(checkClosingTime, 30000);

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