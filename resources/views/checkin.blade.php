<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Zentra – Daily Check-In</title>

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
            --border-focus: #5b52e8;
            --text-primary: #ffffff;
            --text-muted:   #8884a8;
            --placeholder:  #4e4a6a;
            --accent:       #5b52e8;
            --accent-h:     #4a43d4;
            --ease:         .22s cubic-bezier(.4,0,.2,1);
            --r-card:       20px;
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

        /*  Top Bar */
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

        /* Page Wrapper */
        .page {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            min-height: calc(100vh - 75px);
            animation: fadeUp .45s cubic-bezier(.4,0,.2,1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Card */
        .card {
            background: var(--bg-card);
            border: 1px solid rgba(255,255,255,.05);
            border-radius: var(--r-card);
            padding: 36px 40px 40px;
            width: 100%;
            max-width: 860px;
            box-shadow: 0 32px 80px rgba(0,0,0,.55);
        }

        .card__header {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin-bottom: 32px;
        }

        .btn-back {
            position: absolute;
            left: 0;
            display: flex;
            align-items: center;
            gap: 6px;
            background: none;
            border: none;
            color: var(--text-muted);
            font-family: inherit;
            font-size: .88rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: color var(--ease);
        }
        .btn-back:hover { color: var(--text-primary); }
        .btn-back svg   { width: 16px; height: 16px; }

        .card__title {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -.01em;
        }


        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 28px;
        }

        .metric {
            background: var(--bg-input);
            border: 1px solid var(--border-input);
            border-radius: 14px;
            padding: 18px 16px 16px;
            transition: border-color var(--ease);
        }

        .metric:focus-within { border-color: rgba(91,82,232,.5); }

        .metric__title {
            font-size: .82rem;
            font-weight: 600;
            text-align: center;
            color: var(--text-primary);
            margin-bottom: 14px;
        }


        .radio-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            margin-bottom: 8px;
        }

        .radio-opt { display: flex; flex-direction: column; align-items: center; cursor: pointer; }

        .radio-opt input[type="radio"] { display: none; }

        .radio-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 2px solid var(--border-input);
            background: var(--bg-card);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: border-color var(--ease), background var(--ease);
        }

        .radio-circle::after {
            content: '';
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #fff;
            opacity: 0;
            transition: opacity var(--ease);
        }

        .radio-opt input[type="radio"]:checked + .radio-circle {
            border-color: var(--accent);
            background: var(--accent);
        }

        .radio-opt input[type="radio"]:checked + .radio-circle::after { opacity: 1; }

        .metric__labels {
            display: flex;
            justify-content: space-between;
            padding: 0 2px;
        }

        .metric__labels span {
            font-size: .68rem;
            color: var(--text-muted);
        }


        .btn-submit {
            width: 100%;
            padding: 18px;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 14px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 28px rgba(91,82,232,.45);
            transition: background var(--ease), transform var(--ease), box-shadow var(--ease);
        }

        .btn-submit:hover {
            background: var(--accent-h);
            transform: translateY(-1px);
            box-shadow: 0 12px 36px rgba(91,82,232,.55);
        }

        .btn-submit:active { transform: translateY(0); }


        .alert-error {
            background: rgba(239,68,68,.12);
            border: 1px solid rgba(239,68,68,.3);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #f87171;
            font-size: .875rem;
        }

        @media (max-width: 640px) {
            .card { padding: 28px 18px 28px; }
            .metrics-grid { grid-template-columns: 1fr 1fr; }
            .card__title { font-size: 1.2rem; }
        }

        @media (max-width: 420px) {
            .metrics-grid { grid-template-columns: 1fr; }
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

{{-- Page --}}
<div class="page">
    <div class="card">

        <div class="card__header">
            <a href="{{ route('dashboard') }}" class="btn-back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 18l-6-6 6-6"/>
                </svg>
                Back
            </a>
            <h1 class="card__title">Daily Check-In</h1>
        </div>

        @if ($errors->any())
            <div class="alert-error">Please fill all fields before submitting.</div>
        @endif

        <form method="POST" action="{{ route('checkin.store') }}">
            @csrf

            <div class="metrics-grid">

                {{-- Energy Level --}}
                <div class="metric">
                    <p class="metric__title">Energy Level</p>
                    <div class="radio-row">
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="radio-opt">
                                <input type="radio" name="energy_level" value="{{ $i }}" {{ old('energy_level', 3) == $i ? 'checked' : '' }} required>
                                <span class="radio-circle"></span>
                            </label>
                        @endfor
                    </div>
                    <div class="metric__labels"><span>Lowest</span><span>Highest</span></div>
                </div>

                {{-- Focus Level --}}
                <div class="metric">
                    <p class="metric__title">Focus Level</p>
                    <div class="radio-row">
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="radio-opt">
                                <input type="radio" name="focus_level" value="{{ $i }}" {{ old('focus_level', 3) == $i ? 'checked' : '' }} required>
                                <span class="radio-circle"></span>
                            </label>
                        @endfor
                    </div>
                    <div class="metric__labels"><span>Lowest</span><span>Highest</span></div>
                </div>

                {{-- Mood --}}
                <div class="metric">
                    <p class="metric__title">Mood</p>
                    <div class="radio-row">
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="radio-opt">
                                <input type="radio" name="mood" value="{{ $i }}" {{ old('mood', 3) == $i ? 'checked' : '' }} required>
                                <span class="radio-circle"></span>
                            </label>
                        @endfor
                    </div>
                    <div class="metric__labels"><span>Sad</span><span>Happy</span></div>
                </div>

                {{-- Motivation --}}
                <div class="metric">
                    <p class="metric__title">Motivation</p>
                    <div class="radio-row">
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="radio-opt">
                                <input type="radio" name="motivation" value="{{ $i }}" {{ old('motivation', 3) == $i ? 'checked' : '' }} required>
                                <span class="radio-circle"></span>
                            </label>
                        @endfor
                    </div>
                    <div class="metric__labels"><span>Lowest</span><span>Highest</span></div>
                </div>

                {{-- Available Time --}}
                <div class="metric">
                    <p class="metric__title">Available Time</p>
                    <div class="radio-row">
                        @foreach (array(1 => "15 Min", 2 => "30 Min", 3 => "1 Hr", 4 => "3-5 Hr", 5 => "All Day") as $idx => $lbl)
                            <label class="radio-opt">
                                <input type="radio" name="available_time" value="{{ $idx }}" {{ old('available_time', 3) == $idx ? 'checked' : '' }} required>
                                <span class="radio-circle"></span>
                            </label>
                        @endforeach
                    </div>
                    <div class="metric__labels">
                        <span>15 Min</span>
                        <span>30 Min</span>
                        <span>1 Hr</span>
                        <span>3-5 Hr</span>
                        <span>All Day</span>
                    </div>
                </div>

                {{-- Stress Level --}}
                <div class="metric">
                    <p class="metric__title">Stress Level</p>
                    <div class="radio-row">
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="radio-opt">
                                <input type="radio" name="stress_level" value="{{ $i }}" {{ old('stress_level', 3) == $i ? 'checked' : '' }} required>
                                <span class="radio-circle"></span>
                            </label>
                        @endfor
                    </div>
                    <div class="metric__labels"><span>Lowest</span><span>Highest</span></div>
                </div>

            </div>

            <button type="submit" class="btn-submit">Submit</button>
        </form>

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
