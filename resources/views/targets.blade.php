<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Zentra – Targets</title>

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
            --text-content: #5a5680;
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

        /* ── Target widgets ───────────────────── */
        .targets-list {
            display: flex; flex-direction: column; gap: 20px;
        }

        .target-widget {
            background: var(--bg-widget);
            border: 1.5px solid var(--border);
            border-radius: var(--r-widget);
            padding: 22px 26px 26px;
            transition: border-color var(--transition), background var(--transition);
            position: relative;
        }

        .target-widget:hover { background: var(--bg-widget-h); }
        .target-widget.editing { border-color: var(--border-focus); }

        /* Label */
        .target-label {
            font-size: .95rem; font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 14px;
        }

        /* Display row */
        .target-display {
            display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;
        }

        .target-content {
            font-size: 1rem; font-weight: 400;
            color: var(--text-content);
            line-height: 1.6;
            flex: 1;
        }

        /* Pencil button */
        .edit-btn {
            background: transparent; border: none;
            color: #ffffff; cursor: pointer;
            padding: 4px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            transition: opacity var(--transition), background var(--transition);
            flex-shrink: 0; margin-top: 2px;
        }
        .edit-btn:hover { opacity: .7; background: rgba(255,255,255,.07); }
        .edit-btn svg { width: 20px; height: 20px; }

        /* ── Inline edit form ─────────────────── */
        .target-edit-form { display: none; margin-top: 16px; }
        .target-widget.editing .target-edit-form { display: block; }

        .target-textarea {
            width: 100%;
            background: var(--bg-input);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 14px 16px;
            font-family: inherit; font-size: .95rem;
            color: var(--text-primary);
            outline: none; resize: vertical;
            min-height: 90px;
            transition: border-color var(--transition);
            margin-bottom: 14px;
        }
        .target-textarea:focus { border-color: var(--border-focus); }
        .target-textarea::placeholder { color: var(--text-content); }

        .edit-actions { display: flex; gap: 10px; }

        .btn-save {
            flex: 1; padding: 11px;
            background: var(--accent); color: #fff;
            border: none; border-radius: 10px;
            font-family: inherit; font-size: .875rem; font-weight: 600;
            cursor: pointer; transition: background var(--transition);
        }
        .btn-save:hover { background: var(--accent-h); }

        .btn-cancel {
            flex: 1; padding: 11px;
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

        @media (max-width: 560px) {
            .card { padding: 24px 18px 28px; }
        }
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
            <a href="{{ url()->previous() }}" class="back-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Back
            </a>
            <h1 class="card-title">Targets</h1>
        </div>

        {{-- Target list --}}
        @php
            $targets = $targets ?? [
                'daily'   => ['label' => 'Daily',   'content' => 'Do something with my life'],
                'weekly'  => ['label' => 'Weekly',  'content' => 'Get a Girlfriend'],
            ];
        @endphp

        <div class="targets-list">
            @foreach($targets as $key => $target)
            <div class="target-widget" id="widget-{{ $key }}">
                <div class="target-label">{{ $target['label'] }}</div>
                <div class="target-display">
                    <span class="target-content" id="content-display-{{ $key }}">
                        {{ $target['content'] ?? 'No target set yet.' }}
                    </span>
                    <button class="edit-btn" onclick="startEdit('{{ $key }}')" title="Edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20h9"/>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                        </svg>
                    </button>
                </div>

                {{-- Inline edit --}}
                <div class="target-edit-form">
                    <textarea class="target-textarea"
                        id="textarea-{{ $key }}"
                        placeholder="Write your {{ strtolower($target['label']) }} target here...">{{ $target['content'] ?? '' }}</textarea>
                    <div class="edit-actions">
                        <button class="btn-save"   onclick="saveEdit('{{ $key }}')">Save</button>
                        <button class="btn-cancel" onclick="cancelEdit('{{ $key }}')">Cancel</button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</div>

<div class="toast" id="toast">✓ Saved!</div>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        showToast('✓ {{ session('success') }}');
    });
</script>
@endif

<script>
// ── Live clock ───────────────────────────────────────
function updateClock() {
    const now = new Date();
    document.getElementById('nav-date').textContent = now.toLocaleDateString('en-GB', {
        day: '2-digit', month: 'short', year: 'numeric'
    });
    document.getElementById('nav-time').textContent = now.toLocaleTimeString('en-GB', {
        hour: '2-digit', minute: '2-digit'
    }).replace(':', '.');
}
updateClock(); setInterval(updateClock, 1000);

// ── Edit functions ───────────────────────────────────
function startEdit(key) {
    document.getElementById('widget-' + key).classList.add('editing');
    document.getElementById('textarea-' + key).focus();
}

function cancelEdit(key) {
    document.getElementById('widget-' + key).classList.remove('editing');
}

function saveEdit(key) {
    const content = document.getElementById('textarea-' + key).value.trim();
    if (!content) return;

    document.getElementById('content-display-' + key).textContent = content;

    fetch('{{ route("targets.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ key, content })
    })
    .then(res => res.json())
    .then(() => showToast('✓ ' + key.charAt(0).toUpperCase() + key.slice(1) + ' target saved!'))
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