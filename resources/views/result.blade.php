<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zentra – Check-In Result</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-outer:     #1a0f2e;
            --bg-card:      #141228;
            --bg-input:     #1c1a35;
            --border-input: #2b2850;
            --text-primary: #ffffff;
            --text-muted:   #8884a8;
            --accent:       #5b52e8;
            --accent-h:     #4a43d4;
            --ease:         .22s cubic-bezier(.4,0,.2,1);
        }

        html, body {
            min-height: 100vh;
            font-family: 'Outfit', sans-serif;
            background:
                radial-gradient(ellipse 80% 60% at 15% 5%,  #2d1b5e 0%, transparent 55%),
                radial-gradient(ellipse 60% 50% at 85% 90%, #1b0e44 0%, transparent 55%),
                var(--bg-outer);
            color: var(--text-primary);
        }

        /* Top Bar */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 36px;
            border-bottom: 1px solid rgba(255,255,255,.05);
        }
        .topbar__brand { display: flex; align-items: center; gap: 14px; }
        .topbar__logo  { width: 42px; height: 42px; object-fit: contain; }
        .topbar__name  { font-size: .95rem; font-weight: 700; letter-spacing: .32em; }
        .topbar__clock { text-align: right; line-height: 1.25; }
        .topbar__date  { display: block; font-size: .88rem; font-weight: 500; }
        .topbar__time  { display: block; font-size: 1.45rem; font-weight: 700; }

        /* ── Page ── */
        .page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 75px);
            padding: 40px 24px;
            animation: fadeUp .45s cubic-bezier(.4,0,.2,1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Card ── */
        .card {
            background: var(--bg-card);
            border: 1px solid rgba(255,255,255,.05);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 680px;
            box-shadow: 0 32px 80px rgba(0,0,0,.55);
            text-align: center;
        }

        .card__label {
            font-size: .82rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 48px;
        }

        /* ── Result Display ── */
        .result {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 28px;
            margin-bottom: 64px;
        }

        .result__icon {
            color: var(--text-primary);
            flex-shrink: 0;
        }

        .result__icon svg { width: 72px; height: 72px; }

        .result__label {
            font-size: 2.8rem;
            font-weight: 700;
            letter-spacing: -.02em;
        }

       
        .result--low    .result__label { color: #f87171; }
        .result--medium .result__label { color: #ffffff; }
        .result--high   .result__label { color: #34d399; }

        /* Action Buttons */
        .actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .btn {
            padding: 18px;
            border-radius: 14px;
            font-family: inherit;
            font-size: .95rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background var(--ease), transform var(--ease), box-shadow var(--ease);
        }

        .btn--primary {
            background: var(--accent);
            color: #fff;
            border: none;
            box-shadow: 0 8px 28px rgba(91,82,232,.45);
        }

        .btn--primary:hover {
            background: var(--accent-h);
            transform: translateY(-1px);
            box-shadow: 0 12px 36px rgba(91,82,232,.55);
        }

        .btn--secondary {
            background: var(--accent);
            color: #fff;
            border: none;
            box-shadow: 0 8px 28px rgba(91,82,232,.45);
        }

        .btn--secondary:hover {
            background: var(--accent-h);
            transform: translateY(-1px);
        }

        @media (max-width: 480px) {
            .result__label { font-size: 2rem; }
            .result__icon svg { width: 52px; height: 52px; }
            .actions { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

{{-- Top Bar --}}
<header class="topbar">
    <div class="topbar__brand">
        <img src="{{ asset('images/zentralogo.png') }}" alt="Zentra" class="topbar__logo">
        <span class="topbar__name">ZENTRA</span>
    </div>
    <div class="topbar__clock">
        <span class="topbar__date" id="js-date"></span>
        <span class="topbar__time" id="js-time"></span>
    </div>
</header>

{{-- Result Page --}}
<div class="page">
    <div class="card">

        <p class="card__label">Results</p>

        <div class="result result--{{ $condition['class'] }}">
           
            <span class="result__icon">
                @if ($condition['level'] === 'low')
                    {{-- Battery low --}}
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="6" width="18" height="12" rx="2"/>
                        <path d="M23 11v2"/>
                        <rect x="3" y="8" width="3" height="8" rx="1" fill="currentColor" stroke="none"/>
                    </svg>
                @elseif ($condition['level'] === 'medium')
                    {{-- Battery medium --}}
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="6" width="18" height="12" rx="2"/>
                        <path d="M23 11v2"/>
                        <rect x="3" y="8" width="5" height="8" rx="1" fill="currentColor" stroke="none"/>
                        <rect x="9" y="8" width="5" height="8" rx="1" fill="currentColor" stroke="none"/>
                    </svg>
                @else
                    {{-- Battery high / full --}}
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="6" width="18" height="12" rx="2"/>
                        <path d="M23 11v2"/>
                        <rect x="3" y="8" width="14" height="8" rx="1" fill="currentColor" stroke="none"/>
                    </svg>
                @endif
            </span>
            <span class="result__label">{{ $condition['label'] }}</span>
        </div>

        <div class="actions">
            <a href="{{ route('dashboard') }}" class="btn btn--secondary">Home</a>
            <a href="{{ route('checkin.index') }}" class="btn btn--primary">Try Again?</a>
        </div>

    </div>
</div>

<script>
    (function () {
        var elDate = document.getElementById('js-date');
        var elTime = document.getElementById('js-time');
        function tick() {
            var now = new Date();
            elDate.textContent = now.toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });
            var h = String(now.getHours()).padStart(2,'0');
            var m = String(now.getMinutes()).padStart(2,'0');
            elTime.textContent = h + '.' + m;
        }
        tick();
        setInterval(tick, 1000);
    })();
</script>

</body>
</html>