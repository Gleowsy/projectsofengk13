<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Zentra – Time Preference</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-outer:     #0f0d1e;
            --bg-nav:       #13112299;
            --bg-card:      #161430;
            --bg-widget:    #1c1a38;
            --bg-widget-h:  #201e42;
            --bg-input:     #13112a;
            --border:       #2a2750;
            --border-focus: #6c63ff;
            --text-primary: #ffffff;
            --text-muted:   #7e7aaa;
            --text-time:    #3e3a6a;
            --accent:       #6c63ff;
            --accent-h:     #5a52ee;
            --shadow-card:  0 24px 60px rgba(0,0,0,.5);
            --r-card:       20px;
            --r-widget:     16px;
            --transition:   .2s cubic-bezier(.4,0,.2,1);
        }

        html, body {
            min-height: 100vh;
            font-family: 'Outfit', sans-serif;
            background:
                radial-gradient(ellipse 70% 55% at 10% 5%,  #2a1d5e 0%, transparent 55%),
                radial-gradient(ellipse 55% 45% at 90% 90%, #1a0e44 0%, transparent 55%),
                var(--bg-outer);
            color: var(--text-primary);
        }

        /* ── Navbar ───────────────────────────── */
        nav {
            position: sticky; top: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 40px; height: 72px;
            background: var(--bg-nav);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid var(--border);
        }
        .nav-brand { display: flex; align-items: center; gap: 14px; }
        .nav-brand img { height: 44px; width: 44px; object-fit: contain; }
        .nav-brand span { font-size: 1rem; font-weight: 700; letter-spacing: .28em; text-transform: uppercase; }
        .nav-datetime { text-align: right; line-height: 1.35; }
        .nav-datetime .date { font-size: 1rem; font-weight: 600; }
        .nav-datetime .time { font-size: .9rem; color: var(--text-muted); }

        /* ── Page ─────────────────────────────── */
        .page { display: flex; justify-content: center; padding: 48px 24px 60px; }

        /* ── Main card ────────────────────────── */
        .card {
            background: var(--bg-card);
            border-radius: var(--r-card);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-card);
            padding: 36px 40px 44px;
            width: 100%; max-width: 1000px;
            animation: fadeUp .45s cubic-bezier(.4,0,.2,1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Card header ──────────────────────── */
        .card-header {
            display: flex; align-items: center;
            margin-bottom: 36px; position: relative;
        }
        .back-btn {
            display: flex; align-items: center; gap: 6px;
            font-size: .9rem; font-weight: 500;
            color: var(--text-muted); text-decoration: none;
            transition: color var(--transition); position: absolute; left: 0;
        }
        .back-btn:hover { color: var(--text-primary); }
        .back-btn svg { width: 18px; height: 18px; }
        .card-title { flex: 1; text-align: center; font-size: 1.9rem; font-weight: 700; letter-spacing: -.01em; }

        /* ── Grid ─────────────────────────────── */
        .pref-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .span-center {
            grid-column: 1 / -1;
            display: flex;
            justify-content: center;
        }

        .span-center .pref-widget {
            width: calc(50% - 10px);
        }

        @media (max-width: 640px) {
            .pref-grid { grid-template-columns: 1fr; }
            .span-center .pref-widget { width: 100%; }
            .card { padding: 24px 18px 28px; }
        }

        /* ── Preference widget ────────────────── */
        .pref-widget {
            background: var(--bg-widget);
            border: 1.5px solid var(--border);
            border-radius: var(--r-widget);
            padding: 20px 24px 24px;
            transition: border-color var(--transition), background var(--transition);
            position: relative;
            min-height: 120px;
        }
        .pref-widget:hover { background: var(--bg-widget-h); }
        .pref-widget.editing { border-color: var(--border-focus); }

        /* Label top-left */
        .pref-label {
            font-size: .9rem; font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 20px;
        }

        /* Time display row */
        .pref-display {
            display: flex; align-items: center; justify-content: space-between;
        }

        /* Big dim time text like the design */
        .pref-time {
            font-size: 1.65rem; font-weight: 300;
            color: var(--text-time);
            letter-spacing: .04em;
            transition: color var(--transition);
        }

        .pref-widget.editing .pref-time {
            font-size: 1rem;
            color: var(--text-muted);
        }

        /* Pencil button */
        .edit-btn {
            background: transparent; border: none;
            color: #ffffff;
            cursor: pointer;
            padding: 6px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            transition: opacity var(--transition), background var(--transition);
            flex-shrink: 0;
        }
        .edit-btn:hover { opacity: .7; background: rgba(255,255,255,.07); }
        .edit-btn svg { width: 20px; height: 20px; }

        /* ── Inline edit form ─────────────────── */
        .pref-edit-form { display: none; margin-top: 18px; }
        .pref-widget.editing .pref-edit-form { display: block; }

        .time-inputs {
            display: flex; align-items: center; gap: 10px; margin-bottom: 14px;
        }
        .time-inputs span { color: var(--text-muted); font-size: .9rem; font-weight: 500; }

        .time-input {
            flex: 1;
            background: var(--bg-input);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 10px 14px;
            font-family: inherit; font-size: .95rem;
            color: var(--text-primary);
            outline: none;
            transition: border-color var(--transition);
        }
        .time-input:focus { border-color: var(--border-focus); }

        .edit-actions { display: flex; gap: 10px; }

        .btn-save {
            flex: 1; padding: 10px;
            background: var(--accent); color: #fff;
            border: none; border-radius: 10px;
            font-family: inherit; font-size: .875rem; font-weight: 600;
            cursor: pointer; transition: background var(--transition);
        }
        .btn-save:hover { background: var(--accent-h); }

        .btn-cancel {
            flex: 1; padding: 10px;
            background: transparent; color: var(--text-muted);
            border: 1px solid var(--border); border-radius: 10px;
            font-family: inherit; font-size: .875rem; font-weight: 500;
            cursor: pointer; transition: border-color var(--transition), color var(--transition);
        }
        .btn-cancel:hover { border-color: var(--text-muted); color: var(--text-primary); }

        /* ── Toast ────────────────────────────── */
        .toast {
            position: fixed; bottom: 32px; left: 50%;
            transform: translateX(-50%) translateY(20px);
            background: #22c55e; color: #fff;
            padding: 14px 28px; border-radius: 50px;
            font-size: .9rem; font-weight: 600;
            opacity: 0; pointer-events: none;
            transition: opacity .3s, transform .3s; z-index: 200;
        }
        .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
    </style>
</head>
<body>

{{-- Navbar --}}
<nav>
    <div class="nav-brand">
        <img src="{{ asset('images/zentralogo.png') }}" alt="Zentra">
        <span>Zentra</span>
    </div>
    <div class="nav-datetime">
        <div class="date" id="nav-date"></div>
        <div class="time" id="nav-time"></div>
    </div>
</nav>

<div class="page">
    <div class="card">

        {{-- Header --}}
        <div class="card-header">
            <a href="{{ route('schedule.index') }}" class="back-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Back
            </a>
            <h1 class="card-title">Time Preference</h1>
        </div>

        {{-- Preference widgets --}}
        @php
            $prefs = $preferences ?? [
                'sleep'     => ['label' => 'Optimal Sleep Time', 'start' => '20.00', 'end' => '09.00'],
                'lunch'     => ['label' => 'Lunch',              'start' => '12.00', 'end' => '13.00'],
                'breakfast' => ['label' => 'Breakfast',          'start' => '09.00', 'end' => '10.00'],
                'dinner'    => ['label' => 'Dinner',             'start' => '17.00', 'end' => '19.00'],
                'focus'     => ['label' => 'Focus',              'start' => '14.00', 'end' => '17.00'],
            ];
        @endphp

        <div class="pref-grid">
            @foreach($prefs as $key => $pref)

                @if($key === 'focus')<div class="span-center">@endif

                <div class="pref-widget" id="widget-{{ $key }}">
                    <div class="pref-label">{{ $pref['label'] }}</div>
                    <div class="pref-display">
                        <span class="pref-time" id="time-display-{{ $key }}">
                            {{ $pref['start'] }} &ndash; {{ $pref['end'] }}
                        </span>
                        <button class="edit-btn" onclick="startEdit('{{ $key }}')" title="Edit">
                            {{-- Pencil icon --}}
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20h9"/>
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Inline edit --}}
                    <div class="pref-edit-form">
                        <div class="time-inputs">
                            <input type="time" class="time-input" id="start-{{ $key }}"
                                value="{{ \Carbon\Carbon::createFromFormat('H.i', $pref['start'])->format('H:i') }}">
                            <span>–</span>
                            <input type="time" class="time-input" id="end-{{ $key }}"
                                value="{{ \Carbon\Carbon::createFromFormat('H.i', $pref['end'])->format('H:i') }}">
                        </div>
                        <div class="edit-actions">
                            <button class="btn-save"   onclick="saveEdit('{{ $key }}')">Save</button>
                            <button class="btn-cancel" onclick="cancelEdit('{{ $key }}')">Cancel</button>
                        </div>
                    </div>
                </div>

                @if($key === 'focus')</div>@endif

            @endforeach
        </div>

    </div>
</div>

<div class="toast" id="toast">✓ Saved!</div>

<script>
function updateClock() {
    const now = new Date();
    document.getElementById('nav-date').textContent = now.toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'});
    document.getElementById('nav-time').textContent = now.toLocaleTimeString('en-GB', {hour:'2-digit',minute:'2-digit'}).replace(':','.');
}
updateClock(); setInterval(updateClock, 1000);

function startEdit(key) {
    document.getElementById('widget-' + key).classList.add('editing');
}

function cancelEdit(key) {
    document.getElementById('widget-' + key).classList.remove('editing');
}

function saveEdit(key) {
    const s = document.getElementById('start-' + key).value;
    const e = document.getElementById('end-'   + key).value;
    if (!s || !e) return;

    const fmt = v => v.replace(':', '.');
    document.getElementById('time-display-' + key).innerHTML = fmt(s) + ' &ndash; ' + fmt(e);

    fetch('{{ route("schedule.preferences.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ key, start: s, end: e })
    })
    .then(() => showToast('✓ ' + key.charAt(0).toUpperCase() + key.slice(1) + ' saved!'))
    .catch(() => showToast('✓ Saved!'));

    document.getElementById('widget-' + key).classList.remove('editing');
}

function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
}
</script>

</body>
</html>