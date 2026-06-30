<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Sistem Güncellemesi — Köksan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:         #eef4fb;
            --surface:    #ffffff;
            --border:     #d0dff0;
            --border2:    #b8cfe8;
            --ink:        #1a2a3a;
            --ink2:       #3a5068;
            --muted:      #7a96b0;
            --faint:      #e4eef8;
            --blue:       #2d7dd2;
            --blue-mid:   #5499d8;
            --amber:      #e8980a;
            --amber-light:#fff8e8;
            --amber-bd:   #f0d080;
            --green:      #2eac6d;
            --green-bg:   #eaf7f1;
            --green-bd:   #80d4a8;
        }

        html, body {
            height: 100%;
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--ink);
            -webkit-font-smoothing: antialiased;
        }

        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding: 2rem 1.25rem;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle, rgba(45,125,210,.12) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
        }

        body::after {
            content: '';
            position: fixed;
            width: 800px;
            height: 350px;
            border-radius: 50%;
            background: radial-gradient(ellipse, rgba(45,125,210,.08) 0%, transparent 65%);
            top: -80px;
            left: 50%;
            transform: translateX(-50%);
            pointer-events: none;
            z-index: 0;
        }

        .wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 580px;
            margin: auto;
            border-radius: 14px;
            animation: up .65s cubic-bezier(.22,.68,0,1.15) both;
        }

        @keyframes up {
            from { opacity: 0; transform: translateY(22px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Logo */
        .logo-row {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }

        .logo-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 6px rgba(45,125,210,.08);
            min-height: 80px;
        }

        .logo-box img { max-height: 60px; width: auto; object-fit: contain; display: block; }

        /* Big label */
        .big-label { text-align: center; margin-bottom: 1.25rem; }

        .maint-text {
            font-size: clamp(52px, 13vw, 90px);
            font-weight: 800;
            letter-spacing: -.04em;
            line-height: 1;
            color: transparent;
            -webkit-text-stroke: 1.8px rgba(45,125,210,.22);
            text-stroke: 1.8px rgba(45,125,210,.22);
            user-select: none;
        }

        .live-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: var(--amber-light);
            border: 1px solid var(--amber-bd);
            border-radius: 99px;
            padding: 5px 16px 5px 11px;
            margin-top: .65rem;
        }

        .dot-wrap { position: relative; width: 8px; height: 8px; flex-shrink: 0; }
        .dot-wrap::before { content: ''; position: absolute; inset: 0; border-radius: 50%; background: var(--amber); }
        .dot-wrap::after  { content: ''; position: absolute; inset: -3px; border-radius: 50%; border: 1.5px solid var(--amber); opacity: 0; animation: ping 1.8s ease-out infinite; }

        @keyframes ping {
            0%   { transform: scale(.6);  opacity: .7; }
            100% { transform: scale(2.2); opacity: 0;  }
        }

        .live-badge span { font-size: 12px; font-weight: 500; color: var(--amber); }

        /* Illustration */
        .illus-wrap { width: 100%; display: flex; justify-content: center; margin-bottom: 1.25rem; }

        /* Card */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 2.25rem 2rem 1.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 8px 28px rgba(45,125,210,.09);
        }

        h1 { font-size: 19px; font-weight: 600; color: var(--ink); letter-spacing: -.3px; line-height: 1.3; margin-bottom: .5rem; }

        .desc { font-size: 14px; font-weight: 300; color: var(--ink2); line-height: 1.75; margin-bottom: 1.4rem; }

        /* ETA */
        .eta { display: flex; align-items: center; gap: 10px; background: var(--faint); border: 1px solid var(--border); border-radius: 11px; padding: .78rem 1rem; margin-bottom: 1.3rem; }
        .eta svg { width: 16px; height: 16px; color: var(--muted); flex-shrink: 0; }
        .eta p { font-size: 13px; color: var(--ink2); }
        .eta p strong { font-weight: 500; color: var(--ink); }

        /* Progress */
        .prog-wrap { margin-bottom: 1.35rem; }
        .prog-labels { display: flex; justify-content: space-between; margin-bottom: 7px; }
        .prog-labels span { font-size: 11.5px; color: var(--muted); font-family: 'JetBrains Mono', monospace; }
        .prog-labels strong { color: var(--blue); font-weight: 500; }
        .prog-track { height: 4px; background: var(--faint); border-radius: 99px; overflow: hidden; border: 1px solid var(--border); }
        .prog-fill { height: 100%; width: 38%; background: linear-gradient(90deg, var(--blue-mid), var(--blue)); border-radius: 99px; animation: grow 2s cubic-bezier(.4,0,.2,1) both; }
        @keyframes grow { from { width: 0; } to { width: 38%; } }

        /* Steps */
        .steps { display: flex; flex-direction: column; gap: 7px; margin-bottom: 1.6rem; }
        .step { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 11px; border: 1px solid transparent; }
        .step.done    { background: var(--green-bg);    border-color: var(--green-bd); }
        .step.active  { background: var(--amber-light); border-color: var(--amber-bd); }
        .step.pending { background: var(--faint);       border-color: var(--border); }

        .sicon { width: 27px; height: 27px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .step.done   .sicon { background: rgba(46,172,109,.12); }
        .step.active .sicon { background: rgba(232,152,10,.10); }
        .step.pending .sicon{ background: var(--border); }

        .sicon svg { width: 13px; height: 13px; }
        .step.done   .sicon svg { color: var(--green); }
        .step.active .sicon svg { color: var(--amber); }
        .step.pending .sicon svg{ color: var(--muted); }

        .sname { font-size: 13px; }
        .step.done   .sname { color: var(--green); }
        .step.active .sname { color: var(--amber); }
        .step.pending .sname{ color: var(--muted); }

        .sbadge { margin-left: auto; font-size: 10px; font-weight: 500; font-family: 'JetBrains Mono', monospace; border-radius: 6px; padding: 2px 9px; letter-spacing: .05em; text-transform: uppercase; }
        .step.done   .sbadge { background: rgba(46,172,109,.12);  color: var(--green); }
        .step.active .sbadge { background: rgba(232,152,10,.12);  color: var(--amber); }
        .step.pending .sbadge{ background: var(--border);          color: var(--muted); }

        .spin { width: 13px; height: 13px; border: 1.6px solid rgba(232,152,10,.25); border-top-color: var(--amber); border-radius: 50%; animation: spin .85s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Footer */
        .foot { display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--border); padding-top: .9rem; }
        .foot-copy { font-size: 11px; color: var(--muted); }
        .foot-mono { font-family: 'JetBrains Mono', monospace; font-size: 10.5px; color: var(--muted); letter-spacing: .06em; }

        /* Refresh */
        .refresh { text-align: center; margin-top: .9rem; }
        .refresh p { font-size: 12px; color: var(--muted); }
        .refresh a { color: var(--blue); text-decoration: none; font-weight: 500; }
        .refresh a:hover { text-decoration: underline; }
        #countdown { font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--muted); margin-top: 3px; }

        /* SVG Animations */
        @keyframes rotateCW  { to { transform: rotate(360deg);  } }
        @keyframes rotateCCW { to { transform: rotate(-360deg); } }

        .gear-cw  { transform-origin: 76px  100px; animation: rotateCW   6s linear infinite; }
        .gear-ccw { transform-origin: 52px   60px; animation: rotateCCW  4s linear infinite; }
        .gear-sm  { transform-origin: 460px  78px; animation: rotateCW   9s linear infinite; }

        @keyframes swingHook { 0%,100% { transform: rotate(-3deg); } 50% { transform: rotate(3deg); } }
        .hook-group { transform-origin: 310px 35px; animation: swingHook 3s ease-in-out infinite; }

        @keyframes floatDot { 0%,100% { opacity: .45; transform: translateY(0); } 50% { opacity: 1; transform: translateY(-4px); } }
        .fd1 { animation: floatDot 2.2s ease-in-out infinite; }
        .fd2 { animation: floatDot 2.8s ease-in-out infinite .4s; }
        .fd3 { animation: floatDot 3.1s ease-in-out infinite .8s; }
        .fd4 { animation: floatDot 2.5s ease-in-out infinite 1.1s; }

        @keyframes searchRock { 0%,80%,100% { transform: rotate(0deg); } 90% { transform: rotate(18deg); } }
        .search-icon { transform-origin: 490px 52px; animation: searchRock 2.4s ease-in-out infinite; }

        @media (max-width: 480px) {
            .card { padding: 1.75rem 1.25rem 1.5rem; }
            h1 { font-size: 17px; }
            .maint-text { -webkit-text-stroke-width: 1.4px; }
        }
    </style>
</head>
<body>

<div class="wrapper">

    {{-- Logo --}}
    <div class="logo-row">
        <div class="logo-box">
            <img src="https://kys.koksan.com/iaa/storage/logos/2mIKZO0DYbIDjSJdjfN1IpO7jkTqEcSOh886xYH5.png" alt="Köksan">
        </div>
    </div>

    {{-- Big BAKIM MODU text --}}
    <div class="big-label">
        <div class="maint-text">BAKIM MODU</div>
        <div class="live-badge">
            <span class="dot-wrap"></span>
            <span>Güncelleme devam ediyor</span>
        </div>
    </div>

    {{-- SVG Illustration --}}
    <div class="illus-wrap">
        <svg viewBox="0 0 520 160" width="100%" style="max-width:520px;display:block;" fill="none" xmlns="http://www.w3.org/2000/svg">

            {{-- Ground --}}
            <line x1="20" y1="148" x2="500" y2="148" stroke="#b8cfe8" stroke-width="1.5" stroke-linecap="round"/>

            {{-- Monitor body --}}
            <rect x="100" y="72" width="130" height="90" rx="8" fill="#e8f2fc" stroke="#5499d8" stroke-width="1.5"/>
            <rect x="108" y="80" width="114" height="66" rx="4" fill="#c8dff5" stroke="#5499d8" stroke-width="1"/>
            {{-- Screen --}}
            <rect x="118" y="88" width="94" height="50" rx="3" fill="#daeaf8" stroke="#7ab5e0" stroke-width="1"/>
            <polyline points="118,138 148,110 168,125 188,100 212,138" stroke="#5499d8" stroke-width="1.3" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="135" cy="100" r="6" fill="#7ab5e0" opacity=".6"/>
            {{-- Stand --}}
            <rect x="154" y="162" width="22" height="8" rx="2" fill="#c8dff5" stroke="#5499d8" stroke-width="1.2"/>
            <rect x="162" y="155" width="6" height="10" rx="2" fill="#b0ccec" stroke="#5499d8" stroke-width="1"/>

            {{-- Big Gear --}}
            <g class="gear-cw">
                <circle cx="76" cy="100" r="22" fill="#daeaf8" stroke="#5499d8" stroke-width="1.5"/>
                <circle cx="76" cy="100" r="10" fill="#c0d8f0" stroke="#5499d8" stroke-width="1.2"/>
                <rect x="73"  y="73"  width="6" height="9" rx="2" fill="#5499d8"/>
                <rect x="73"  y="118" width="6" height="9" rx="2" fill="#5499d8"/>
                <rect x="49"  y="97"  width="9" height="6" rx="2" fill="#5499d8"/>
                <rect x="94"  y="97"  width="9" height="6" rx="2" fill="#5499d8"/>
                <rect x="57"  y="80"  width="9" height="6" rx="2" fill="#5499d8" transform="rotate(45  61.5 83)"/>
                <rect x="57"  y="111" width="9" height="6" rx="2" fill="#5499d8" transform="rotate(-45 61.5 114)"/>
                <rect x="88"  y="80"  width="9" height="6" rx="2" fill="#5499d8" transform="rotate(-45 92.5 83)"/>
                <rect x="88"  y="111" width="9" height="6" rx="2" fill="#5499d8" transform="rotate(45  92.5 114)"/>
            </g>

            {{-- Small Gear --}}
            <g class="gear-ccw">
                <circle cx="52" cy="60" r="13" fill="#e8f2fc" stroke="#5499d8" stroke-width="1.3"/>
                <circle cx="52" cy="60" r="6"  fill="#d0e6f6" stroke="#5499d8" stroke-width="1"/>
                <rect x="49.5" y="43"  width="5" height="7" rx="1.5" fill="#5499d8"/>
                <rect x="49.5" y="70"  width="5" height="7" rx="1.5" fill="#5499d8"/>
                <rect x="35"   y="57.5" width="7" height="5" rx="1.5" fill="#5499d8"/>
                <rect x="62"   y="57.5" width="7" height="5" rx="1.5" fill="#5499d8"/>
                <rect x="40"   y="48"  width="7" height="5" rx="1.5" fill="#5499d8" transform="rotate(45  43.5 50.5)"/>
                <rect x="40"   y="62"  width="7" height="5" rx="1.5" fill="#5499d8" transform="rotate(-45 43.5 64.5)"/>
                <rect x="57"   y="48"  width="7" height="5" rx="1.5" fill="#5499d8" transform="rotate(-45 60.5 50.5)"/>
                <rect x="57"   y="62"  width="7" height="5" rx="1.5" fill="#5499d8" transform="rotate(45  60.5 64.5)"/>
            </g>

            {{-- Right small gear --}}
            <g class="gear-sm">
                <circle cx="460" cy="78" r="14" fill="#e8f2fc" stroke="#5499d8" stroke-width="1.3"/>
                <circle cx="460" cy="78" r="6"  fill="#d0e6f6" stroke="#5499d8" stroke-width="1"/>
                <rect x="457.5" y="60"  width="5" height="7" rx="1.5" fill="#5499d8"/>
                <rect x="457.5" y="89"  width="5" height="7" rx="1.5" fill="#5499d8"/>
                <rect x="442"   y="75.5" width="7" height="5" rx="1.5" fill="#5499d8"/>
                <rect x="471"   y="75.5" width="7" height="5" rx="1.5" fill="#5499d8"/>
                <rect x="447"   y="65"  width="7" height="5" rx="1.5" fill="#5499d8" transform="rotate(45  450.5 67.5)"/>
                <rect x="447"   y="82"  width="7" height="5" rx="1.5" fill="#5499d8" transform="rotate(-45 450.5 84.5)"/>
                <rect x="466"   y="65"  width="7" height="5" rx="1.5" fill="#5499d8" transform="rotate(-45 469.5 67.5)"/>
                <rect x="466"   y="82"  width="7" height="5" rx="1.5" fill="#5499d8" transform="rotate(45  469.5 84.5)"/>
            </g>

            {{-- Crane vertical pole --}}
            <rect x="368" y="28" width="8" height="120" rx="3" fill="#daeaf8" stroke="#5499d8" stroke-width="1.3"/>
            {{-- Crane horizontal arm --}}
            <rect x="268" y="28" width="108" height="7" rx="3" fill="#daeaf8" stroke="#5499d8" stroke-width="1.3"/>
            {{-- Diagonal brace --}}
            <line x1="368" y1="35" x2="310" y2="55" stroke="#5499d8" stroke-width="1.2" stroke-dasharray="4 3"/>
            {{-- Counterweight --}}
            <rect x="370" y="30" width="18" height="10" rx="3" fill="#c8dff5" stroke="#5499d8" stroke-width="1.2"/>

            {{-- Hook + cable --}}
            <g class="hook-group">
                <line x1="310" y1="35" x2="310" y2="85" stroke="#5499d8" stroke-width="1.2"/>
                <path d="M304 85 Q304 96 313 96 Q322 96 322 88" stroke="#5499d8" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                <circle cx="310" cy="83" r="3" fill="#5499d8"/>
            </g>

            {{-- Ladder --}}
            <rect x="245" y="78" width="5" height="70" rx="2" fill="#daeaf8" stroke="#5499d8" stroke-width="1.2"/>
            <rect x="265" y="78" width="5" height="70" rx="2" fill="#daeaf8" stroke="#5499d8" stroke-width="1.2"/>
            <line x1="245" y1="92"  x2="270" y2="92"  stroke="#5499d8" stroke-width="1.1"/>
            <line x1="245" y1="106" x2="270" y2="106" stroke="#5499d8" stroke-width="1.1"/>
            <line x1="245" y1="120" x2="270" y2="120" stroke="#5499d8" stroke-width="1.1"/>
            <line x1="245" y1="134" x2="270" y2="134" stroke="#5499d8" stroke-width="1.1"/>

            {{-- Traffic cone --}}
            <polygon points="408,148 420,148 416,120 412,120" fill="#fde68a" stroke="#f0a832" stroke-width="1.2"/>
            <line x1="410" y1="135" x2="418" y2="135" stroke="#f0a832" stroke-width="1.2"/>

            {{-- Barrier --}}
            <rect x="430" y="138" width="50" height="10" rx="3" fill="#daeaf8" stroke="#5499d8" stroke-width="1.2"/>
            <rect x="434" y="148" width="5"  height="6"  rx="1.5" fill="#c8dff5" stroke="#5499d8" stroke-width="1"/>
            <rect x="471" y="148" width="5"  height="6"  rx="1.5" fill="#c8dff5" stroke="#5499d8" stroke-width="1"/>
            <line x1="430" y1="141" x2="480" y2="141" stroke="#7ab5e0" stroke-width="1" stroke-dasharray="6 4"/>

            {{-- Cactus --}}
            <rect x="26" y="130" width="18" height="18" rx="3" fill="#daeaf8" stroke="#5499d8" stroke-width="1.2"/>
            <rect x="32" y="100" width="6"  height="32" rx="3" fill="#2eac6d" stroke="#1a7a4e" stroke-width="1"/>
            <path d="M32 118 Q22 118 22 110 Q22 104 28 104" stroke="#2eac6d" stroke-width="4" fill="none" stroke-linecap="round"/>
            <path d="M38 122 Q48 122 48 114 Q48 108 42 108" stroke="#2eac6d" stroke-width="4" fill="none" stroke-linecap="round"/>

            {{-- Search icon --}}
            <g class="search-icon">
                <circle cx="490" cy="52" r="11" fill="#e8f2fc" stroke="#5499d8" stroke-width="1.4"/>
                <circle cx="487" cy="49" r="6"  fill="none"    stroke="#5499d8" stroke-width="1.3"/>
                <line x1="491" y1="53" x2="497" y2="59" stroke="#5499d8" stroke-width="1.4" stroke-linecap="round"/>
            </g>

            {{-- Clock --}}
            <circle cx="178" cy="48" r="16" fill="#e8f2fc" stroke="#5499d8" stroke-width="1.4"/>
            <line x1="178" y1="40" x2="178" y2="48" stroke="#5499d8" stroke-width="1.4" stroke-linecap="round"/>
            <line x1="178" y1="48" x2="185" y2="51" stroke="#5499d8" stroke-width="1.4" stroke-linecap="round"/>
            <circle cx="178" cy="48" r="2" fill="#5499d8"/>

            {{-- Floating dots --}}
            <circle class="fd1" cx="230" cy="45"  r="3"   fill="#5499d8" opacity=".4"/>
            <circle class="fd2" cx="396" cy="55"  r="2.5" fill="#5499d8" opacity=".35"/>
            <circle class="fd3" cx="44"  cy="42"  r="2"   fill="#5499d8" opacity=".3"/>
            <circle class="fd4" cx="350" cy="72"  r="2"   fill="#7ab5e0" opacity=".4"/>

        </svg>
    </div>

    {{-- Card --}}
    <div class="card">
        <h1>Altyapı İyileştirmesi Yapılıyor</h1>
        <p class="desc">
            Size daha hızlı ve güvenilir bir deneyim sunmak için sistemlerimizi güncelliyoruz.
            Kısa süre içinde geri döneceğiz, anlayışınız için teşekkür ederiz.
        </p>

        <div class="eta">
            <svg viewBox="0 0 17 17" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="8.5" cy="8.5" r="6.5"/>
                <path d="M8.5 5.5V8.8L10.8 11"/>
            </svg>
            <p>Tahmini süre: <strong>30 – 45 dakika</strong></p>
        </div>

        <div class="prog-wrap">
            <div class="prog-labels">
                <span>İlerleme</span>
                <strong>%38</strong>
            </div>
            <div class="prog-track">
                <div class="prog-fill"></div>
            </div>
        </div>

        <div class="steps">
            <div class="step done">
                <div class="sicon">
                    <svg viewBox="0 0 13 13" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 6.5L5 9.5L11 3.5"/>
                    </svg>
                </div>
                <span class="sname">Veritabanı yedeklemesi</span>
                <span class="sbadge">Tamam</span>
            </div>

            <div class="step active">
                <div class="sicon">
                    <div class="spin"></div>
                </div>
                <span class="sname">Altyapı güncellemesi</span>
                <span class="sbadge">Devam</span>
            </div>

            <div class="step pending">
                <div class="sicon">
                    <svg viewBox="0 0 13 13" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">
                        <circle cx="6.5" cy="6.5" r="3.5"/>
                    </svg>
                </div>
                <span class="sname">Sistem testi ve doğrulama</span>
                <span class="sbadge">Bekliyor</span>
            </div>
        </div>

        <div class="foot">
            <span class="foot-copy">&copy; {{ date('Y') }} Köksan Pet ve Plastik Ambalaj</span>
            <span class="foot-mono">HTTP 503</span>
        </div>
    </div>

    {{-- Refresh hint --}}
    <div class="refresh">
        <p>Sayfayı yenilemeyi deneyin: <a href="#" onclick="location.reload(); return false;">Yenile</a></p>
        <div id="countdown"></div>
    </div>

</div>

<script>
    let s = 300;
    const el = document.getElementById('countdown');

    function fmt(sec) {
        return Math.floor(sec / 60) + ':' + String(sec % 60).padStart(2, '0') + ' içinde otomatik yenilenir';
    }

    el.textContent = fmt(s);

    const t = setInterval(function () {
        s--;
        if (s <= 0) { clearInterval(t); location.reload(); }
        else { el.textContent = fmt(s); }
    }, 1000);
</script>

</body>
</html>