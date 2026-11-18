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
            overflow: hidden; 
        }

        body::before {
            content: ""; position: fixed; inset: 0;
            background: radial-gradient(circle at 15% -10%, rgba(199,161,58,0.1), transparent 50%);
            pointer-events: none;
        }

        .page {
            position: relative;
            padding: 0vh 1vw 1vh; 
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
            margin-top: -8px;
        }
        .brand {z-index: 100 !important;}
        .brand img { height: 14vh; width: auto; }
        .title-center .rule {
            color: var(--gold); font-size: 6.5vh; text-align: center;
            letter-spacing: 0.2em; text-transform: uppercase; font-weight: bold;
            z-index: 100 !important;
        }
        .title-center {z-index: 100 !important;}
        .clock { text-align: right; z-index: 100; }
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
            grid-template-columns: 6vh 2fr 12vw auto; 
            align-items: center;
            gap: 2vw;
            padding: 1.5vh 0.5vw 1.5vh 2vw; 
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            border-radius: var(--radius);
        }

        .row.is-end { opacity: 0.3; filter: grayscale(1); }

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
        .beer-desc { font-size: 2.3vh; color: var(--text-muted); margin-top: 0.5vh; }

        .stats {
            display: grid; grid-template-columns: 1fr 1fr; column-gap: 2vw;
        }
        .stat { text-align: center; }
        .stats .stat:first-child { position: relative; }
        .stats .stat:first-child::after {
            content: ""; position: absolute; top: 2%; bottom: 2%;
            right: -1vw; width: 2px; margin-right: -1px;
            background: rgba(255,255,255,.35);
        }
        .stat .val { font-size: 4.0vh; font-weight: bold; }
        .stat .lbl { font-size: 2vh; color: var(--text-muted); text-transform: uppercase; }

        .row .stats { margin-right: 0; justify-self: end; }
        .row .stats .stat:last-child { margin-left: 0; }
        .prices { display: flex; gap: 1vw; justify-content: flex-end; }
        .price {
            background: rgba(255,255,255,0.06); padding: 1vh; border-radius: 1vh;
            min-width: 5.5vw; text-align: center;
            border: 1px solid rgba(255, 192, 72, 0.3) !important;
        }
        .price .vol { font-size: 3.1vh; display: block; }
        .price .amt { font-size: 6.9vh; font-weight: bold; color: var(--gold); }

        /* Snacks */
        .snacks-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1.5vw; }
        .snack-card {
            background: rgba(255,255,255,0.04); border: 1px solid var(--border);
            border-radius: 1vh; padding: 0.5vh; display: flex; justify-content: space-between; align-items: center;
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

        .limited-badge {
            position: absolute;
            left: 1vh;
            top: -2vh;
            background: #6f42c1;
            padding: 0.5vh 1.5vw;
            font-size: 2.8vh;
            font-weight: bold;
            border-radius: 0.5vh;
            z-index: 10;
        }

        .closed-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            z-index: 100;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            font-family: sans-serif;
        }
        .closed-overlay h1 {
            font-size: 7vh;
            font-weight: 900;
            color: var(--gold);
            text-transform: uppercase;
            margin-bottom: 3vh;

            border: 3px solid var(--gold);          
            padding: 1.5vh 4vh;                     
            border-radius: 12px;                    
            box-shadow: 0 0 20px rgba(199, 161, 58, 0.25); 
            display: inline-block;                  
        }
        .closed-overlay p {
            font-size: 3.5vh;
            color: var(--text-muted);
        }

        .closed-logo {
            height: 35vh; 
            width: auto;
            margin-bottom: 4vh;
            filter: drop-shadow(0 0 20px rgba(199,161,58,0.3)); 
        }
</style>
