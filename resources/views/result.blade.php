<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Zentra – Adaptive Productivity</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-outer:     #1a0f2e;
            --bg-card:      #141228;
            --bg-widget:    #1c1a35;
            --border:       #2b2850;
            --border-focus: #5b52e8;
            --text-primary: #ffffff;
            --text-muted:   #8884a8;
            --accent:       #5b52e8;
            --accent-h:     #4a43d4;
            --green:        #34d399;
            --yellow:       #fbbf24;
            --red:          #f87171;
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
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 36px;
            border-bottom: 1px solid rgba(255,255,255,.05);
        }
        .topbar__brand { display: flex; align-items: center; gap: 14px; }
        .topbar__logo  { width: 42px; height: 42px; object-fit: contain; }
        .topbar__name  { font-size: .95rem; font-weight: 700; letter-spacing: .32em; }
        .topbar__clock { text-align: right; line-height: 1.25; }
        .topbar__date  { display: block; font-size: .88rem; font-weight: 500; }
        .topbar__time  { display: block; font-size: 1.45rem; font-weight: 700; }

        /* Page */
        .page {
            display: flex; justify-content: center;
            padding: 36px 24px 60px;
            animation: fadeUp .45s cubic-bezier(.4,0,.2,1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .container { width: 100%; max-width: 780px; display: flex; flex-direction: column; gap: 18px; }

        /* ── Condition Banner ── */
        .banner {
            background: var(--bg-card);
            border: 1px solid rgba(255,255,255,.05);
            border-radius: 20px;
            padding: 28px 32px;
            display: flex;
            align-items: center;
            gap: 24px;
            box-shadow: 0 24px 60px rgba(0,0,0,.5);
        }

        .banner__icon svg { width: 60px; height: 60px; }
        .banner__body { flex: 1; }
        .banner__label {
            font-size: .75rem; font-weight: 700;
            letter-spacing: .1em; text-transform: uppercase;
            color: var(--text-muted); margin-bottom: 6px;
        }
        .banner__title {
            font-size: 2.2rem; font-weight: 800; letter-spacing: -.02em;
        }
        .banner__sub {
            font-size: .9rem; color: var(--text-muted); margin-top: 6px; line-height: 1.5;
        }

        .banner--low    .banner__title { color: var(--red); }
        .banner--medium .banner__title { color: var(--yellow); }
        .banner--high   .banner__title { color: var(--green); }

        /* ── Score Pills ── */
        .scores {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;
        }
        .score-pill {
            background: var(--bg-widget);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 14px 16px;
            text-align: center;
        }
        .score-pill__label { font-size: .75rem; color: var(--text-muted); font-weight: 600; margin-bottom: 6px; }
        .score-pill__value { font-size: 1.4rem; font-weight: 800; }
        .score-pill__bar {
            height: 4px; background: var(--border); border-radius: 4px; margin-top: 8px; overflow: hidden;
        }
        .score-pill__fill { height: 100%; border-radius: 4px; transition: width .6s ease; }

        /* ── Section Card ── */
        .section {
            background: var(--bg-card);
            border: 1px solid rgba(255,255,255,.05);
            border-radius: 20px;
            padding: 28px 32px;
            box-shadow: 0 16px 40px rgba(0,0,0,.4);
        }
        .section__header {
            display: flex; align-items: center; gap: 12px; margin-bottom: 20px;
        }
        .section__icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: rgba(91,82,232,.15); border: 1px solid rgba(91,82,232,.25);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .section__icon svg { width: 18px; height: 18px; color: var(--accent); }
        .section__title { font-size: 1rem; font-weight: 700; }
        .section__subtitle { font-size: .8rem; color: var(--text-muted); margin-top: 2px; }

        /* ── Task Recommendations ── */
        .task-list { display: flex; flex-direction: column; gap: 10px; }

        .task-item {
            background: var(--bg-widget);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px 16px;
            display: flex; align-items: flex-start; gap: 14px;
            transition: border-color var(--ease);
        }
        .task-item:hover { border-color: rgba(91,82,232,.4); }

        .task-item__check {
            width: 22px; height: 22px; border-radius: 6px;
            border: 2px solid var(--border);
            flex-shrink: 0; margin-top: 1px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all var(--ease);
        }
        .task-item__check:hover { border-color: var(--accent); background: rgba(91,82,232,.15); }
        .task-item__check.checked { background: var(--accent); border-color: var(--accent); }
        .task-item__check.checked::after {
            content: '✓'; color: white; font-size: .75rem; font-weight: 700;
        }

        .task-item__body { flex: 1; }
        .task-item__name { font-size: .9rem; font-weight: 600; margin-bottom: 4px; }
        .task-item__meta { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

        .tag {
            font-size: .72rem; font-weight: 600; padding: 2px 8px;
            border-radius: 20px; white-space: nowrap;
        }
        .tag--high     { background: rgba(248,113,113,.15); color: var(--red); border: 1px solid rgba(248,113,113,.3); }
        .tag--medium   { background: rgba(251,191,36,.12);  color: var(--yellow); border: 1px solid rgba(251,191,36,.3); }
        .tag--low      { background: rgba(52,211,153,.12);  color: var(--green); border: 1px solid rgba(52,211,153,.3); }
        .tag--time     { background: rgba(255,255,255,.06); color: var(--text-muted); border: 1px solid var(--border); }
        .tag--type     { background: rgba(91,82,232,.12); color: #a89eff; border: 1px solid rgba(91,82,232,.3); }

        .task-item__reason { font-size: .8rem; color: var(--text-muted); margin-top: 6px; line-height: 1.45; }

        /* ── Tips ── */
        .tips-list { display: flex; flex-direction: column; gap: 10px; }
        .tip-item {
            display: flex; align-items: flex-start; gap: 12px;
            background: var(--bg-widget); border: 1px solid var(--border);
            border-radius: 12px; padding: 14px 16px;
        }
        .tip-item__num {
            width: 26px; height: 26px; border-radius: 8px;
            background: rgba(91,82,232,.2); border: 1px solid rgba(91,82,232,.3);
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; font-weight: 800; color: #a89eff; flex-shrink: 0;
        }
        .tip-item__text { font-size: .875rem; color: var(--text-muted); line-height: 1.55; padding-top: 3px; }
        .tip-item__text strong { color: var(--text-primary); font-weight: 600; }

        /* ── Focus Mode ── */
        .focus-block {
            background: var(--bg-widget); border: 1px solid var(--border);
            border-radius: 12px; padding: 16px 18px;
            display: flex; align-items: center; gap: 16px;
        }
        .focus-block__icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: rgba(52,211,153,.1); border: 1px solid rgba(52,211,153,.2);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .focus-block__icon svg { width: 22px; height: 22px; color: var(--green); }
        .focus-block__label { font-size: .78rem; color: var(--text-muted); font-weight: 600; }
        .focus-block__value { font-size: 1rem; font-weight: 700; margin-top: 2px; }

        /* ── Implement to Schedule ── */
        .implement-section {
            background: linear-gradient(135deg, rgba(91,82,232,.15) 0%, rgba(74,67,212,.08) 100%);
            border: 1px solid rgba(91,82,232,.3);
            border-radius: 20px; padding: 28px 32px;
        }
        .implement-section .section__icon {
            background: rgba(91,82,232,.2); border-color: rgba(91,82,232,.4);
        }

        .implement-note {
            font-size: .85rem; color: var(--text-muted); line-height: 1.5;
            margin-bottom: 20px;
            padding: 12px 14px;
            background: rgba(255,255,255,.04); border-radius: 10px;
            border: 1px solid rgba(255,255,255,.06);
        }

        .implement-options { display: flex; flex-direction: column; gap: 10px; }

        .implement-opt {
            display: flex; align-items: center; gap: 14px;
            background: var(--bg-widget); border: 1px solid var(--border);
            border-radius: 12px; padding: 14px 16px;
            cursor: pointer; transition: border-color var(--ease), background var(--ease);
        }
        .implement-opt:hover { border-color: rgba(91,82,232,.5); background: rgba(91,82,232,.08); }
        .implement-opt.selected { border-color: var(--accent); background: rgba(91,82,232,.12); }

        .implement-opt__radio {
            width: 20px; height: 20px; border-radius: 50%;
            border: 2px solid var(--border); flex-shrink: 0;
            transition: all var(--ease); position: relative;
        }
        .implement-opt.selected .implement-opt__radio {
            border-color: var(--accent); background: var(--accent);
        }
        .implement-opt.selected .implement-opt__radio::after {
            content: ''; position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 8px; height: 8px; border-radius: 50%; background: white;
        }
        .implement-opt__body { flex: 1; }
        .implement-opt__title { font-size: .9rem; font-weight: 600; }
        .implement-opt__desc { font-size: .78rem; color: var(--text-muted); margin-top: 2px; }

        .btn-implement {
            width: 100%; padding: 16px; margin-top: 16px;
            background: var(--accent); color: #fff; border: none;
            border-radius: 14px; font-family: inherit; font-size: .95rem; font-weight: 700;
            cursor: pointer; box-shadow: 0 8px 28px rgba(91,82,232,.45);
            transition: background var(--ease), transform var(--ease), box-shadow var(--ease);
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-implement:hover {
            background: var(--accent-h); transform: translateY(-1px);
            box-shadow: 0 12px 36px rgba(91,82,232,.55);
        }
        .btn-implement:disabled {
            opacity: .5; cursor: not-allowed; transform: none;
        }
        .btn-implement svg { width: 18px; height: 18px; }

        /* ── Bottom Actions ── */
        .actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .btn {
            padding: 16px; border-radius: 14px; font-family: inherit;
            font-size: .9rem; font-weight: 700; cursor: pointer;
            text-decoration: none; display: flex; align-items: center; justify-content: center;
            transition: all var(--ease);
        }
        .btn--ghost {
            background: transparent; color: var(--text-muted);
            border: 1px solid var(--border);
        }
        .btn--ghost:hover { border-color: rgba(91,82,232,.4); color: var(--text-primary); background: rgba(91,82,232,.06); }
        .btn--primary {
            background: var(--accent); color: #fff; border: none;
            box-shadow: 0 8px 28px rgba(91,82,232,.35);
        }
        .btn--primary:hover { background: var(--accent-h); transform: translateY(-1px); }

        /* Success toast */
        .toast {
            position: fixed; bottom: 28px; left: 50%; transform: translateX(-50%) translateY(80px);
            background: #1e1c38; border: 1px solid rgba(91,82,232,.4);
            border-radius: 14px; padding: 14px 22px;
            display: flex; align-items: center; gap: 10px;
            font-size: .9rem; font-weight: 600;
            box-shadow: 0 16px 48px rgba(0,0,0,.5);
            transition: transform .4s cubic-bezier(.34,1.56,.64,1);
            z-index: 999;
        }
        .toast.show { transform: translateX(-50%) translateY(0); }
        .toast__icon { color: var(--green); }
        .toast__icon svg { width: 20px; height: 20px; }

        /* Loading state */
        .loading-overlay {
            position: fixed; inset: 0; background: rgba(10,8,28,.8);
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px;
            z-index: 1000; opacity: 0; pointer-events: none; transition: opacity .3s;
        }
        .loading-overlay.active { opacity: 1; pointer-events: all; }
        .spinner {
            width: 44px; height: 44px; border-radius: 50%;
            border: 3px solid var(--border); border-top-color: var(--accent);
            animation: spin .8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-text { font-size: .9rem; color: var(--text-muted); }

        @media (max-width: 600px) {
            .topbar { padding: 14px 18px; }
            .banner { flex-direction: column; text-align: center; padding: 22px 20px; }
            .banner__title { font-size: 1.8rem; }
            .scores { grid-template-columns: repeat(3, 1fr); }
            .section { padding: 22px 18px; }
            .implement-section { padding: 22px 18px; }
            .actions { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

{{-- Loading Overlay --}}
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
    <p class="loading-text">Applying to your schedule…</p>
</div>

{{-- Toast --}}
<div class="toast" id="toast">
    <span class="toast__icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
    </span>
    <span id="toastMsg">Schedule updated successfully!</span>
</div>

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

<div class="page">
    <div class="container">

        {{-- ── Condition Banner ── --}}
        <div class="banner banner--{{ $condition['class'] }}">
            <span class="banner__icon">
                @if ($condition['level'] === 'low')
                    <svg viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="6" width="18" height="12" rx="2"/>
                        <path d="M23 11v2"/>
                        <rect x="3" y="8" width="3" height="8" rx="1" fill="#f87171" stroke="none"/>
                    </svg>
                @elseif ($condition['level'] === 'medium')
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="6" width="18" height="12" rx="2"/>
                        <path d="M23 11v2"/>
                        <rect x="3" y="8" width="5" height="8" rx="1" fill="#fbbf24" stroke="none"/>
                        <rect x="9" y="8" width="5" height="8" rx="1" fill="#fbbf24" stroke="none"/>
                    </svg>
                @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="6" width="18" height="12" rx="2"/>
                        <path d="M23 11v2"/>
                        <rect x="3" y="8" width="14" height="8" rx="1" fill="#34d399" stroke="none"/>
                    </svg>
                @endif
            </span>
            <div class="banner__body">
                <p class="banner__label">Today's Condition</p>
                <h1 class="banner__title">{{ $condition['label'] }}</h1>
                <p class="banner__sub">{{ $condition['description'] }}</p>
            </div>
        </div>

        {{-- ── Score Breakdown ── --}}
        <div class="scores">
            <div class="score-pill">
                <p class="score-pill__label">Energy</p>
                <p class="score-pill__value" style="color: {{ $checkinData['energy_level'] >= 4 ? '#34d399' : ($checkinData['energy_level'] <= 2 ? '#f87171' : '#fbbf24') }}">
                    {{ $checkinData['energy_level'] }}/5
                </p>
                <div class="score-pill__bar">
                    <div class="score-pill__fill" style="width:{{ $checkinData['energy_level'] * 20 }}%; background:{{ $checkinData['energy_level'] >= 4 ? '#34d399' : ($checkinData['energy_level'] <= 2 ? '#f87171' : '#fbbf24') }}"></div>
                </div>
            </div>
            <div class="score-pill">
                <p class="score-pill__label">Focus</p>
                <p class="score-pill__value" style="color: {{ $checkinData['focus_level'] >= 4 ? '#34d399' : ($checkinData['focus_level'] <= 2 ? '#f87171' : '#fbbf24') }}">
                    {{ $checkinData['focus_level'] }}/5
                </p>
                <div class="score-pill__bar">
                    <div class="score-pill__fill" style="width:{{ $checkinData['focus_level'] * 20 }}%; background:{{ $checkinData['focus_level'] >= 4 ? '#34d399' : ($checkinData['focus_level'] <= 2 ? '#f87171' : '#fbbf24') }}"></div>
                </div>
            </div>
            <div class="score-pill">
                <p class="score-pill__label">Motivation</p>
                <p class="score-pill__value" style="color: {{ $checkinData['motivation'] >= 4 ? '#34d399' : ($checkinData['motivation'] <= 2 ? '#f87171' : '#fbbf24') }}">
                    {{ $checkinData['motivation'] }}/5
                </p>
                <div class="score-pill__bar">
                    <div class="score-pill__fill" style="width:{{ $checkinData['motivation'] * 20 }}%; background:{{ $checkinData['motivation'] >= 4 ? '#34d399' : ($checkinData['motivation'] <= 2 ? '#f87171' : '#fbbf24') }}"></div>
                </div>
            </div>
            <div class="score-pill">
                <p class="score-pill__label">Mood</p>
                <p class="score-pill__value" style="color: {{ $checkinData['mood'] >= 4 ? '#34d399' : ($checkinData['mood'] <= 2 ? '#f87171' : '#fbbf24') }}">
                    {{ $checkinData['mood'] }}/5
                </p>
                <div class="score-pill__bar">
                    <div class="score-pill__fill" style="width:{{ $checkinData['mood'] * 20 }}%; background:{{ $checkinData['mood'] >= 4 ? '#34d399' : ($checkinData['mood'] <= 2 ? '#f87171' : '#fbbf24') }}"></div>
                </div>
            </div>
            <div class="score-pill">
                <p class="score-pill__label">Stress</p>
                <p class="score-pill__value" style="color: {{ $checkinData['stress_level'] >= 4 ? '#f87171' : ($checkinData['stress_level'] <= 2 ? '#34d399' : '#fbbf24') }}">
                    {{ $checkinData['stress_level'] }}/5
                </p>
                <div class="score-pill__bar">
                    <div class="score-pill__fill" style="width:{{ $checkinData['stress_level'] * 20 }}%; background:{{ $checkinData['stress_level'] >= 4 ? '#f87171' : ($checkinData['stress_level'] <= 2 ? '#34d399' : '#fbbf24') }}"></div>
                </div>
            </div>
            <div class="score-pill">
                <p class="score-pill__label">Time</p>
                <p class="score-pill__value" style="color:#a89eff">
                    {{ $timeLabels[$checkinData['available_time'] - 1] }}
                </p>
                <div class="score-pill__bar">
                    <div class="score-pill__fill" style="width:{{ $checkinData['available_time'] * 20 }}%; background:#5b52e8"></div>
                </div>
            </div>
        </div>

        {{-- ── Task Recommendations ── --}}
        @if(count($recommendations) > 0)
        <div class="section">
            <div class="section__header">
                <div class="section__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                    </svg>
                </div>
                <div>
                    <p class="section__title">Recommended Tasks</p>
                    <p class="section__subtitle">Based on your condition today</p>
                </div>
            </div>

            <div class="task-list">
                @foreach($recommendations as $rec)
                <div class="task-item" data-task-id="{{ $rec['task_id'] }}" data-subtask="{{ $rec['subtask_field'] }}">
                    <div class="task-item__check" onclick="toggleCheck(this)"></div>
                    <div class="task-item__body">
                        <p class="task-item__name">{{ $rec['task_name'] }} — {{ $rec['subtask_name'] }}</p>
                        <div class="task-item__meta">
                            <span class="tag tag--{{ $rec['priority'] }}">{{ ucfirst($rec['priority']) }}</span>
                            @if($rec['scheduled_time'])
                                <span class="tag tag--time">{{ $rec['scheduled_time'] }}</span>
                            @endif
                            <span class="tag tag--type">{{ $rec['type'] }}</span>
                        </div>
                        <p class="task-item__reason">{{ $rec['reason'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Focus Window ── --}}
        <div class="section">
            <div class="section__header">
                <div class="section__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div>
                    <p class="section__title">Optimal Focus Window</p>
                    <p class="section__subtitle">Best time to work based on your energy</p>
                </div>
            </div>

            <div class="focus-block">
                <div class="focus-block__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <div>
                    <p class="focus-block__label">Recommended Focus Time</p>
                    <p class="focus-block__value">{{ $focusWindow['label'] }}</p>
                </div>
            </div>

            @if($focusWindow['session_duration'])
            <div class="focus-block" style="margin-top: 10px;">
                <div class="focus-block__icon" style="background: rgba(91,82,232,.1); border-color: rgba(91,82,232,.2);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#a89eff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                    </svg>
                </div>
                <div>
                    <p class="focus-block__label">Suggested Session Length</p>
                    <p class="focus-block__value" style="color: #a89eff;">{{ $focusWindow['session_duration'] }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- ── Productivity Tips ── --}}
        <div class="section">
            <div class="section__header">
                <div class="section__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>
                    </svg>
                </div>
                <div>
                    <p class="section__title">Productivity Tips for Today</p>
                    <p class="section__subtitle">Tailored to your {{ $condition['level'] }} energy state</p>
                </div>
            </div>

            <div class="tips-list">
                @foreach($tips as $i => $tip)
                <div class="tip-item">
                    <div class="tip-item__num">{{ $i + 1 }}</div>
                    <p class="tip-item__text">{!! $tip !!}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ── Implement to Schedule ── --}}
        <div class="implement-section">
            <div class="section__header">
                <div class="section__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                    </svg>
                </div>
                <div>
                    <p class="section__title">Implement to Schedule</p>
                    <p class="section__subtitle">Apply these recommendations to your current schedule</p>
                </div>
            </div>

            <p class="implement-note">
                Choose how you want to adjust your schedule based on today's condition.
                Changes will be applied immediately to your tasks.
            </p>

            <div class="implement-options" id="implementOptions">

                @if($condition['level'] === 'low')
                <div class="implement-opt" data-action="defer_all" onclick="selectOption(this)">
                    <div class="implement-opt__radio"></div>
                    <div class="implement-opt__body">
                        <p class="implement-opt__title">Defer All Tasks to Tomorrow</p>
                        <p class="implement-opt__desc">Move all today's tasks to tomorrow at your focus time. Take today to rest.</p>
                    </div>
                </div>
                <div class="implement-opt" data-action="keep_essential" onclick="selectOption(this)">
                    <div class="implement-opt__radio"></div>
                    <div class="implement-opt__body">
                        <p class="implement-opt__title">Keep Only High-Priority Tasks</p>
                        <p class="implement-opt__desc">Keep urgent tasks, defer medium/low priority to tomorrow.</p>
                    </div>
                </div>
                @elseif($condition['level'] === 'medium')
                <div class="implement-opt" data-action="reschedule_focus" onclick="selectOption(this)">
                    <div class="implement-opt__radio"></div>
                    <div class="implement-opt__body">
                        <p class="implement-opt__title">Move High-Priority to Focus Window</p>
                        <p class="implement-opt__desc">Reschedule important tasks to {{ $focusWindow['label'] }} for best performance.</p>
                    </div>
                </div>
                <div class="implement-opt" data-action="space_out" onclick="selectOption(this)">
                    <div class="implement-opt__radio"></div>
                    <div class="implement-opt__body">
                        <p class="implement-opt__title">Space Out Tasks with Breaks</p>
                        <p class="implement-opt__desc">Add buffer time between tasks to avoid overloading yourself.</p>
                    </div>
                </div>
                @else
                <div class="implement-opt" data-action="keep_schedule" onclick="selectOption(this)">
                    <div class="implement-opt__radio"></div>
                    <div class="implement-opt__body">
                        <p class="implement-opt__title">Keep Current Schedule</p>
                        <p class="implement-opt__desc">You're in great shape! Stick to the plan as-is.</p>
                    </div>
                </div>
                <div class="implement-opt" data-action="advance_tasks" onclick="selectOption(this)">
                    <div class="implement-opt__radio"></div>
                    <div class="implement-opt__body">
                        <p class="implement-opt__title">Pull Forward Future Tasks</p>
                        <p class="implement-opt__desc">High energy day — tackle some tasks planned for later this week.</p>
                    </div>
                </div>
                @endif

                <div class="implement-opt" data-action="no_change" onclick="selectOption(this)">
                    <div class="implement-opt__radio"></div>
                    <div class="implement-opt__body">
                        <p class="implement-opt__title">No Changes</p>
                        <p class="implement-opt__desc">Skip schedule adjustment for now.</p>
                    </div>
                </div>
            </div>

            <button class="btn-implement" id="btnImplement" onclick="applyToSchedule()" disabled>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Apply to Schedule
            </button>
        </div>

        {{-- ── Bottom Actions ── --}}
        <div class="actions">
            <a href="{{ route('dashboard') }}" class="btn btn--ghost">Back to Home</a>
            <a href="{{ route('schedule.index') }}" class="btn btn--primary">View Schedule</a>
        </div>

    </div>
</div>

<script>
// Live clock
(function () {
    const elDate = document.getElementById('js-date');
    const elTime = document.getElementById('js-time');
    function tick() {
        const now = new Date();
        elDate.textContent = now.toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });
        const h = String(now.getHours()).padStart(2,'0');
        const m = String(now.getMinutes()).padStart(2,'0');
        elTime.textContent = h + '.' + m;
    }
    tick(); setInterval(tick, 1000);
})();

// Checkbox toggle
function toggleCheck(el) {
    el.classList.toggle('checked');
}

// Select implement option
let selectedAction = null;
function selectOption(el) {
    document.querySelectorAll('.implement-opt').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    selectedAction = el.dataset.action;
    document.getElementById('btnImplement').disabled = false;
}

// Apply to schedule
function applyToSchedule() {
    if (!selectedAction) return;

    const overlay = document.getElementById('loadingOverlay');
    overlay.classList.add('active');

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('{{ route("checkin.apply_schedule") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ action: selectedAction })
    })
    .then(res => res.json())
    .then(data => {
        overlay.classList.remove('active');
        if (data.success) {
            showToast(data.message || 'Schedule updated successfully!');
            document.getElementById('btnImplement').disabled = true;
            document.getElementById('btnImplement').textContent = '✓ Applied';
        } else {
            showToast(data.message || 'Something went wrong.', true);
        }
    })
    .catch(() => {
        overlay.classList.remove('active');
        showToast('Connection error. Please try again.', true);
    });
}

function showToast(msg, isError = false) {
    const toast = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    toast.style.borderColor = isError ? 'rgba(248,113,113,.4)' : 'rgba(91,82,232,.4)';
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3500);
}
</script>

</body>
</html>
