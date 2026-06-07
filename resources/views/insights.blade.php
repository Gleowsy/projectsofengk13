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
            --bg-outer:  #0f0d1e;
            --bg-nav:    #13112299;
            --bg-card:   #161430;
            --bg-widget: #1c1a38;
            --border:    #2a2750;
            --text-primary: #ffffff;
            --text-muted:   #7e7aaa;
            --text-dim:     #4e4a70;
            --accent:       #6c63ff;
            --green:        #34d399;
            --yellow:       #fbbf24;
            --red:          #f87171;
            --shadow-card:  0 24px 60px rgba(0,0,0,.5);
            --r-card:       20px;
            --r-widget:     14px;
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

        .page { display: flex; justify-content: center; padding: 40px 24px 60px; }

        .container {
            width: 100%; max-width: 860px;
            display: flex; flex-direction: column; gap: 20px;
            animation: fadeUp .45s cubic-bezier(.4,0,.2,1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Page Header ── */
        .page-header {
            display: flex; align-items: center; position: relative; margin-bottom: 4px;
        }
        .back-btn {
            display: flex; align-items: center; gap: 6px;
            font-size: .9rem; font-weight: 500; color: var(--text-muted);
            text-decoration: none; transition: color var(--transition); position: absolute; left: 0;
        }
        .back-btn:hover { color: var(--text-primary); }
        .back-btn svg { width: 18px; height: 18px; }
        .page-title { flex: 1; text-align: center; }
        .page-title h1 { font-size: 1.7rem; font-weight: 800; letter-spacing: -.01em; }
        .page-title p { font-size: .85rem; color: var(--text-muted); margin-top: 4px; }

        /* ── No Checkin State ── */
        .empty-state {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--r-card);
            padding: 60px 40px;
            text-align: center;
            box-shadow: var(--shadow-card);
        }
        .empty-state__icon { margin-bottom: 20px; }
        .empty-state__icon svg { width: 56px; height: 56px; color: var(--text-dim); }
        .empty-state h2 { font-size: 1.3rem; font-weight: 700; margin-bottom: 10px; }
        .empty-state p { font-size: .9rem; color: var(--text-muted); max-width: 360px; margin: 0 auto 28px; line-height: 1.6; }
        .btn-checkin {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--accent); color: white; text-decoration: none;
            padding: 14px 28px; border-radius: 12px;
            font-weight: 700; font-size: .9rem;
            box-shadow: 0 8px 28px rgba(108,99,255,.45);
            transition: all var(--transition);
        }
        .btn-checkin:hover { filter: brightness(1.1); transform: translateY(-1px); }
        .btn-checkin svg { width: 18px; height: 18px; }

        /* ── Card ── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--r-card);
            padding: 28px 32px;
            box-shadow: var(--shadow-card);
        }
        .card-header {
            display: flex; align-items: center; gap: 12px; margin-bottom: 20px;
        }
        .card-icon {
            width: 40px; height: 40px; border-radius: 11px;
            background: rgba(108,99,255,.15); border: 1px solid rgba(108,99,255,.25);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .card-icon svg { width: 20px; height: 20px; color: var(--accent); }
        .card-title { font-size: 1rem; font-weight: 700; }
        .card-sub { font-size: .78rem; color: var(--text-muted); margin-top: 2px; }

        /* ── Condition Banner ── */
        .condition-banner {
            display: flex; align-items: center; gap: 20px;
            padding: 20px 24px;
            background: var(--bg-widget);
            border: 1px solid var(--border);
            border-radius: var(--r-widget);
            margin-bottom: 20px;
        }
        .condition-banner__icon svg { width: 52px; height: 52px; }
        .condition-banner__body .label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--text-muted); }
        .condition-banner__body .value { font-size: 1.8rem; font-weight: 800; letter-spacing: -.02em; }
        .condition-banner__body .desc  { font-size: .82rem; color: var(--text-muted); margin-top: 4px; line-height: 1.4; }
        .condition-low    .value { color: var(--red); }
        .condition-medium .value { color: var(--yellow); }
        .condition-high   .value { color: var(--green); }

        /* ── Score Grid ── */
        .scores-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 20px;
        }
        .score-pill {
            background: var(--bg-widget); border: 1px solid var(--border);
            border-radius: 12px; padding: 14px 14px 12px; text-align: center;
        }
        .score-pill .s-label { font-size: .72rem; color: var(--text-muted); font-weight: 600; margin-bottom: 5px; }
        .score-pill .s-value { font-size: 1.3rem; font-weight: 800; }
        .score-pill .s-bar { height: 3px; background: var(--border); border-radius: 3px; margin-top: 7px; overflow: hidden; }
        .score-pill .s-fill { height: 100%; border-radius: 3px; }

        /* ── Recommendations ── */
        .recs-list { display: flex; flex-direction: column; gap: 10px; }
        .rec-item {
            background: var(--bg-widget); border: 1px solid var(--border);
            border-radius: 12px; padding: 14px 16px;
            display: flex; align-items: flex-start; gap: 12px;
        }
        .rec-item__num {
            width: 28px; height: 28px; border-radius: 8px;
            background: rgba(108,99,255,.15); border: 1px solid rgba(108,99,255,.25);
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; font-weight: 800; color: #a89eff; flex-shrink: 0;
        }
        .rec-item__body { flex: 1; }
        .rec-item__name { font-size: .9rem; font-weight: 600; margin-bottom: 5px; }
        .rec-item__meta { display: flex; flex-wrap: wrap; gap: 6px; }
        .tag { font-size: .7rem; font-weight: 600; padding: 2px 8px; border-radius: 20px; }
        .tag--high   { background: rgba(248,113,113,.15); color: var(--red);    border: 1px solid rgba(248,113,113,.3); }
        .tag--medium { background: rgba(251,191,36,.12);  color: var(--yellow); border: 1px solid rgba(251,191,36,.3); }
        .tag--low    { background: rgba(52,211,153,.12);  color: var(--green);  border: 1px solid rgba(52,211,153,.3); }
        .tag--time   { background: rgba(255,255,255,.06); color: var(--text-muted); border: 1px solid var(--border); }
        .tag--type   { background: rgba(108,99,255,.12); color: #a89eff; border: 1px solid rgba(108,99,255,.3); }
        .rec-item__reason { font-size: .78rem; color: var(--text-muted); margin-top: 6px; line-height: 1.4; }

        /* ── Focus Window ── */
        .focus-row {
            display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
        }
        .focus-block {
            background: var(--bg-widget); border: 1px solid var(--border);
            border-radius: 12px; padding: 16px;
            display: flex; align-items: center; gap: 14px;
        }
        .focus-block__ico {
            width: 42px; height: 42px; border-radius: 11px;
            background: rgba(52,211,153,.1); border: 1px solid rgba(52,211,153,.2);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .focus-block__ico svg { width: 20px; height: 20px; color: var(--green); }
        .focus-block__label { font-size: .72rem; color: var(--text-muted); font-weight: 600; }
        .focus-block__value { font-size: .95rem; font-weight: 700; margin-top: 3px; }

        /* ── Tips ── */
        .tips-list { display: flex; flex-direction: column; gap: 10px; }
        .tip-item {
            display: flex; align-items: flex-start; gap: 12px;
            background: var(--bg-widget); border: 1px solid var(--border);
            border-radius: 12px; padding: 14px 16px;
        }
        .tip-item__num {
            width: 26px; height: 26px; border-radius: 7px;
            background: rgba(108,99,255,.15); border: 1px solid rgba(108,99,255,.25);
            display: flex; align-items: center; justify-content: center;
            font-size: .72rem; font-weight: 800; color: #a89eff; flex-shrink: 0;
        }
        .tip-item__text { font-size: .85rem; color: var(--text-muted); line-height: 1.55; padding-top: 3px; }
        .tip-item__text strong { color: var(--text-primary); font-weight: 600; }

        /* ── Implement ── */
        .implement-card {
            background: linear-gradient(135deg, rgba(108,99,255,.12) 0%, rgba(74,67,212,.06) 100%);
            border: 1px solid rgba(108,99,255,.3);
            border-radius: var(--r-card); padding: 28px 32px;
        }
        .implement-note {
            font-size: .84rem; color: var(--text-muted); line-height: 1.55; margin-bottom: 18px;
            padding: 12px 14px; background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.06); border-radius: 10px;
        }
        .implement-opts { display: flex; flex-direction: column; gap: 10px; }
        .opt {
            display: flex; align-items: center; gap: 14px;
            background: var(--bg-widget); border: 1px solid var(--border);
            border-radius: 12px; padding: 14px 16px;
            cursor: pointer; transition: border-color var(--transition), background var(--transition);
        }
        .opt:hover { border-color: rgba(108,99,255,.45); background: rgba(108,99,255,.07); }
        .opt.selected { border-color: var(--accent); background: rgba(108,99,255,.12); }
        .opt__radio {
            width: 20px; height: 20px; border-radius: 50%;
            border: 2px solid var(--border); flex-shrink: 0;
            transition: all var(--transition); position: relative;
        }
        .opt.selected .opt__radio { border-color: var(--accent); background: var(--accent); }
        .opt.selected .opt__radio::after {
            content: ''; position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 8px; height: 8px; border-radius: 50%; background: white;
        }
        .opt__title { font-size: .9rem; font-weight: 600; }
        .opt__desc  { font-size: .78rem; color: var(--text-muted); margin-top: 2px; }

        .btn-apply {
            width: 100%; padding: 16px; margin-top: 16px;
            background: var(--accent); color: white; border: none;
            border-radius: 12px; font-family: inherit; font-size: .95rem; font-weight: 700;
            cursor: pointer; box-shadow: 0 8px 28px rgba(108,99,255,.4);
            transition: all var(--transition);
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-apply:hover { filter: brightness(1.08); transform: translateY(-1px); }
        .btn-apply:disabled { opacity: .5; cursor: not-allowed; transform: none; }

        /* ── Toast ── */
        .toast {
            position: fixed; bottom: 28px; left: 50%; transform: translateX(-50%) translateY(80px);
            background: #1e1c38; border: 1px solid rgba(108,99,255,.4);
            border-radius: 14px; padding: 14px 22px;
            display: flex; align-items: center; gap: 10px;
            font-size: .9rem; font-weight: 600;
            box-shadow: 0 16px 48px rgba(0,0,0,.5);
            transition: transform .4s cubic-bezier(.34,1.56,.64,1);
            z-index: 999;
        }
        .toast.show { transform: translateX(-50%) translateY(0); }
        .toast__icon svg { width: 20px; height: 20px; color: var(--green); }

        .loading-overlay {
            position: fixed; inset: 0; background: rgba(10,8,28,.8);
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px;
            z-index: 1000; opacity: 0; pointer-events: none; transition: opacity .3s;
        }
        .loading-overlay.active { opacity: 1; pointer-events: all; }
        .spinner { width: 40px; height: 40px; border-radius: 50%; border: 3px solid var(--border); border-top-color: var(--accent); animation: spin .8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-text { font-size: .88rem; color: var(--text-muted); }

        @media (max-width: 640px) {
            nav { padding: 0 18px; }
            .card { padding: 22px 18px; }
            .implement-card { padding: 22px 18px; }
            .focus-row { grid-template-columns: 1fr; }
            .scores-grid { grid-template-columns: repeat(3, 1fr); }
            .condition-banner { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
    <p class="loading-text">Applying to your schedule…</p>
</div>

<div class="toast" id="toast">
    <span class="toast__icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
    </span>
    <span id="toastMsg">Schedule updated!</span>
</div>

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
    <div class="container">

        <div class="page-header">
            <a href="{{ url()->previous() }}" class="back-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Back
            </a>
            <div class="page-title">
                <h1>Adaptive Productivity</h1>
                <p>Personalized plan based on your daily check-in</p>
            </div>
        </div>

        @if(!$latestCheckin)
        {{-- No check-in yet --}}
        <div class="empty-state">
            <div class="empty-state__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
            </div>
            <h2>No Check-In Found</h2>
            <p>Complete your daily check-in first to get personalized task recommendations and productivity insights.</p>
            <a href="{{ route('checkin.index') }}" class="btn-checkin">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
                Do Today's Check-In
            </a>
        </div>

        @else

        {{-- ── Today's Condition ── --}}
        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                    </svg>
                </div>
                <div>
                    <p class="card-title">Today's Condition</p>
                    <p class="card-sub">{{ now()->format('l, d M Y') }}</p>
                </div>
            </div>

            <div class="condition-banner condition-{{ $condition['class'] }}">
                <div class="condition-banner__icon">
                    @if ($condition['level'] === 'low')
                        <svg viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="6" width="18" height="12" rx="2"/><path d="M23 11v2"/>
                            <rect x="3" y="8" width="3" height="8" rx="1" fill="#f87171" stroke="none"/>
                        </svg>
                    @elseif ($condition['level'] === 'medium')
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="6" width="18" height="12" rx="2"/><path d="M23 11v2"/>
                            <rect x="3" y="8" width="5" height="8" rx="1" fill="#fbbf24" stroke="none"/>
                            <rect x="9" y="8" width="5" height="8" rx="1" fill="#fbbf24" stroke="none"/>
                        </svg>
                    @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="6" width="18" height="12" rx="2"/><path d="M23 11v2"/>
                            <rect x="3" y="8" width="14" height="8" rx="1" fill="#34d399" stroke="none"/>
                        </svg>
                    @endif
                </div>
                <div class="condition-banner__body">
                    <p class="label">Energy Level</p>
                    <p class="value">{{ $condition['label'] }}</p>
                    <p class="desc">{{ $condition['description'] }}</p>
                </div>
            </div>

            {{-- Score pills --}}
            <div class="scores-grid">
                @php
                    $metrics = [
                        ['label' => 'Energy',     'val' => $latestCheckin->energy_level,  'invert' => false],
                        ['label' => 'Focus',      'val' => $latestCheckin->focus_level,   'invert' => false],
                        ['label' => 'Motivation', 'val' => $latestCheckin->motivation,    'invert' => false],
                        ['label' => 'Mood',       'val' => $latestCheckin->mood,          'invert' => false],
                        ['label' => 'Stress',     'val' => $latestCheckin->stress_level,  'invert' => true],
                        ['label' => 'Time',       'val' => $latestCheckin->available_time,'invert' => false, 'text' => $timeLabels[$latestCheckin->available_time - 1]],
                    ];
                @endphp
                @foreach($metrics as $m)
                @php
                    $v = $m['val'];
                    $inv = $m['invert'] ?? false;
                    $good = $inv ? ($v <= 2) : ($v >= 4);
                    $bad  = $inv ? ($v >= 4) : ($v <= 2);
                    $col  = $good ? '#34d399' : ($bad ? '#f87171' : '#fbbf24');
                    $colFixed = isset($m['text']) ? '#a89eff' : $col;
                @endphp
                <div class="score-pill">
                    <p class="s-label">{{ $m['label'] }}</p>
                    <p class="s-value" style="color:{{ $colFixed }}">{{ $m['text'] ?? ($v . '/5') }}</p>
                    <div class="s-bar">
                        <div class="s-fill" style="width:{{ $v * 20 }}%; background:{{ isset($m['text']) ? '#6c63ff' : $col }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ── Task Recommendations ── --}}
        @if(count($recommendations) > 0)
        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                    </svg>
                </div>
                <div>
                    <p class="card-title">Recommended Tasks</p>
                    <p class="card-sub">Prioritized based on your condition today</p>
                </div>
            </div>
            <div class="recs-list">
                @foreach($recommendations as $i => $rec)
                <div class="rec-item">
                    <div class="rec-item__num">{{ $i + 1 }}</div>
                    <div class="rec-item__body">
                        <p class="rec-item__name">{{ $rec['task_name'] }} — {{ $rec['subtask_name'] }}</p>
                        <div class="rec-item__meta">
                            <span class="tag tag--{{ $rec['priority'] }}">{{ ucfirst($rec['priority']) }}</span>
                            @if($rec['scheduled_time'])
                                <span class="tag tag--time">{{ $rec['scheduled_time'] }}</span>
                            @endif
                            <span class="tag tag--type">{{ $rec['type'] }}</span>
                        </div>
                        <p class="rec-item__reason">{{ $rec['reason'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="card" style="text-align:center; padding: 40px;">
            <p style="color:var(--text-muted); font-size:.9rem;">No tasks scheduled — add tasks to get personalized recommendations.</p>
            <a href="{{ route('tasks.index') }}" style="display:inline-block; margin-top:16px; color:var(--accent); font-weight:600; text-decoration:none; font-size:.88rem;">+ Add Tasks</a>
        </div>
        @endif

        {{-- ── Focus Window ── --}}
        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div>
                    <p class="card-title">Optimal Focus Window</p>
                    <p class="card-sub">Best time to work based on your energy today</p>
                </div>
            </div>
            <div class="focus-row">
                <div class="focus-block">
                    <div class="focus-block__ico">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <div>
                        <p class="focus-block__label">Focus Window</p>
                        <p class="focus-block__value">{{ $focusWindow['label'] }}</p>
                    </div>
                </div>
                <div class="focus-block" style="border-color: rgba(108,99,255,.25);">
                    <div class="focus-block__ico" style="background: rgba(108,99,255,.1); border-color: rgba(108,99,255,.2);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#a89eff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                        </svg>
                    </div>
                    <div>
                        <p class="focus-block__label">Session Length</p>
                        <p class="focus-block__value" style="color:#a89eff">{{ $focusWindow['session_duration'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Tips ── --}}
        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>
                    </svg>
                </div>
                <div>
                    <p class="card-title">Productivity Tips</p>
                    <p class="card-sub">Tailored for your {{ $condition['level'] }} energy state</p>
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
        <div class="implement-card">
            <div class="card-header">
                <div class="card-icon" style="background:rgba(108,99,255,.2); border-color:rgba(108,99,255,.35);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                    </svg>
                </div>
                <div>
                    <p class="card-title">Implement to Schedule</p>
                    <p class="card-sub">Apply recommendations to your current schedule now</p>
                </div>
            </div>

            <p class="implement-note">Choose how you want to adjust your tasks based on today's condition. Changes apply immediately.</p>

            <div class="implement-opts" id="implementOpts">
                @if($condition['level'] === 'low')
                <div class="opt" data-action="defer_all" onclick="selectOpt(this)">
                    <div class="opt__radio"></div>
                    <div><p class="opt__title">Defer All Tasks to Tomorrow</p><p class="opt__desc">Move all today's tasks to tomorrow at your focus time. Take today to rest.</p></div>
                </div>
                <div class="opt" data-action="keep_essential" onclick="selectOpt(this)">
                    <div class="opt__radio"></div>
                    <div><p class="opt__title">Keep Only High-Priority Tasks</p><p class="opt__desc">Defer medium/low priority tasks, keep urgent ones for today.</p></div>
                </div>
                @elseif($condition['level'] === 'medium')
                <div class="opt" data-action="reschedule_focus" onclick="selectOpt(this)">
                    <div class="opt__radio"></div>
                    <div><p class="opt__title">Move High-Priority to Focus Window</p><p class="opt__desc">Reschedule important tasks to {{ $focusWindow['label'] }} for best output.</p></div>
                </div>
                <div class="opt" data-action="space_out" onclick="selectOpt(this)">
                    <div class="opt__radio"></div>
                    <div><p class="opt__title">Space Out Tasks with Breaks</p><p class="opt__desc">Add buffer time between tasks to avoid mental overload.</p></div>
                </div>
                @else
                <div class="opt" data-action="keep_schedule" onclick="selectOpt(this)">
                    <div class="opt__radio"></div>
                    <div><p class="opt__title">Keep Current Schedule</p><p class="opt__desc">You're in great shape! Stick to the plan as-is.</p></div>
                </div>
                <div class="opt" data-action="advance_tasks" onclick="selectOpt(this)">
                    <div class="opt__radio"></div>
                    <div><p class="opt__title">Pull Forward Future Tasks</p><p class="opt__desc">High energy day — tackle tasks planned for later this week.</p></div>
                </div>
                @endif
                <div class="opt" data-action="no_change" onclick="selectOpt(this)">
                    <div class="opt__radio"></div>
                    <div><p class="opt__title">No Changes</p><p class="opt__desc">Skip schedule adjustment for now.</p></div>
                </div>
            </div>

            <button class="btn-apply" id="btnApply" onclick="applySchedule()" disabled>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Apply to Schedule
            </button>
        </div>

        @endif

    </div>
</div>

<script>
// Live clock
function updateClock() {
    const now = new Date();
    document.getElementById('nav-date').textContent = now.toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });
    document.getElementById('nav-time').textContent = now.toLocaleTimeString('en-GB', { hour:'2-digit', minute:'2-digit' }).replace(':','.');
}
updateClock(); setInterval(updateClock, 1000);

let selectedAction = null;
function selectOpt(el) {
    document.querySelectorAll('.opt').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    selectedAction = el.dataset.action;
    document.getElementById('btnApply').disabled = false;
}

function applySchedule() {
    if (!selectedAction) return;
    document.getElementById('loadingOverlay').classList.add('active');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('{{ route("checkin.apply_schedule") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ action: selectedAction })
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('loadingOverlay').classList.remove('active');
        showToast(data.message || 'Schedule updated!');
        if (data.success) {
            document.getElementById('btnApply').disabled = true;
            document.getElementById('btnApply').innerHTML = '✓  Applied';
        }
    })
    .catch(() => {
        document.getElementById('loadingOverlay').classList.remove('active');
        showToast('Connection error. Try again.', true);
    });
}

function showToast(msg, err = false) {
    const t = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    t.style.borderColor = err ? 'rgba(248,113,113,.4)' : 'rgba(108,99,255,.4)';
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3500);
}
</script>

</body>
</html>
