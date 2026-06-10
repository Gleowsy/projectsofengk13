<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Zentra – Dashboard</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-outer:      #1a0f2e;
            --bg-card:       #141228;
            --bg-input:      #1c1a35;
            --border-input:  #2b2850;
            --border-focus:  #5b52e8;
            --text-primary:  #ffffff;
            --text-muted:    #8884a8;
            --placeholder:   #4e4a6a;
            --accent:        #5b52e8;
            --accent-h:      #4a43d4;
            --accent-link:   #2ec4ff;
            --divider:       #2b2850;
            --shadow-card:   0 32px 80px rgba(0,0,0,.55);
            --shadow-widget: 0 8px 32px rgba(0,0,0,.4);
            --shadow-btn:    0 8px 28px rgba(91,82,232,.45);
            --r-card:        20px;
            --r-input:       12px;
            --r-btn:         14px;
            --ease:          .22s cubic-bezier(.4,0,.2,1);
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

        .topbar__brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .topbar__logo {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .topbar__name {
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: .32em;
            color: var(--text-primary);
        }

        .topbar__right {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .topbar__clock { text-align: right; line-height: 1.25; }

        .topbar__date {
            display: block;
            font-size: .88rem;
            font-weight: 500;
            color: var(--text-primary);
        }

        .topbar__time {
            display: block;
            font-size: 1.45rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .topbar__logout {
            background: none;
            border: 1px solid var(--border-input);
            border-radius: 10px;
            color: var(--text-muted);
            font-family: inherit;
            font-size: .8rem;
            font-weight: 500;
            padding: 8px 16px;
            cursor: pointer;
            transition: border-color var(--ease), color var(--ease), background var(--ease);
        }

        .topbar__logout:hover {
            border-color: rgba(91,82,232,.5);
            color: var(--text-primary);
            background: rgba(91,82,232,.1);
        }

        /* Dashboard Wrapper*/
        .dash {
            padding: 28px 36px 52px;
            max-width: 1400px;
            margin: 0 auto;
            animation: fadeUp .5s cubic-bezier(.4,0,.2,1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .dash__title {
            font-size: 1.8rem;
            font-weight: 700;
            text-align: center;
            letter-spacing: -.015em;
            margin-bottom: 26px;
        }

        /* 3-Column Grid*/
        .dash__grid {
            display: grid;
            grid-template-columns: 1fr 1.05fr 1fr;
            gap: 18px;
            align-items: start;
        }

        .col { display: flex; flex-direction: column; gap: 14px; }

        /*Shared Widget Shell*/
        .w {
            background: var(--bg-card);
            border: 1px solid rgba(255,255,255,.05);
            border-radius: var(--r-card);
            padding: 20px;
            box-shadow: var(--shadow-widget);
        }

        .w__title {
            font-size: .88rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 14px;
        }

        /* search */
        .w--search { padding: 14px 18px; }

        .search-input {
            width: 100%;
            background: transparent;
            border: none;
            outline: none;
            font-family: inherit;
            font-size: .9rem;
            color: var(--placeholder);
        }
        .search-input::placeholder { color: var(--placeholder); }

        /* Calendar */
        .cal__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .cal__label {
            font-size: .88rem;
            font-weight: 600;
        }

        .cal__nav { display: flex; gap: 2px; }

        .cal__btn {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.1rem;
            padding: 2px 8px;
            border-radius: 6px;
            cursor: pointer;
            transition: background var(--ease), color var(--ease);
            line-height: 1;
        }
        .cal__btn:hover { background: rgba(255,255,255,.07); color: var(--text-primary); }

        .cal__table { width: 100%; border-collapse: collapse; table-layout: fixed; }

        .cal__table thead th {
            font-size: .7rem;
            font-weight: 500;
            color: var(--text-muted);
            text-align: center;
            padding-bottom: 8px;
        }

        .cal__table tbody td {
            text-align: center;
            font-size: .8rem;
            color: var(--text-primary);
            padding: 5px 0;
            cursor: default;
            transition: background var(--ease);
        }

        .cal__table tbody td:hover:not(.other):not(.today) {
            background: rgba(91,82,232,.2);
            border-radius: 50%;
            cursor: pointer;
        }

        .cal__table tbody td.other { color: var(--placeholder); }

        .cal__table tbody td.today {
            background: var(--accent);
            border-radius: 50%;
            color: #fff;
            font-weight: 700;
        }

        /* Upcoming tasks */
        .task-list {
            max-height: 150px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(91,82,232,.4) transparent;
        }

        .task-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .task-item__ico {
            width: 36px;
            height: 36px;
            flex-shrink: 0;
            background: var(--bg-input);
            border: 1px solid var(--border-input);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
        }

        .task-item__ico svg { width: 17px; height: 17px; }

        .task-item__body { flex: 1; display: flex; flex-direction: column; gap: 1px; }

        .task-item__name {
            font-size: .86rem;
            font-weight: 600;
        }

        .task-item__sub {
            font-size: .74rem;
            color: var(--text-muted);
        }

        .task-item__date {
            font-size: .76rem;
            color: var(--text-muted);
            white-space: nowrap;
        }

        /*Center column = menu cards */
        .menu-card {
            display: flex;
            align-items: center;
            gap: 15px;
            background: var(--bg-card);
            border: 1px solid rgba(255,255,255,.05);
            border-radius: var(--r-card);
            padding: 18px 20px;
            text-decoration: none;
            color: var(--text-primary);
            box-shadow: var(--shadow-widget);
            transition: background var(--ease), border-color var(--ease), transform var(--ease);
        }

        .menu-card:hover {
            background: rgba(91,82,232,.12);
            border-color: rgba(91,82,232,.35);
            transform: translateX(4px);
        }

        .menu-card__ico {
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            background: var(--bg-input);
            border: 1px solid var(--border-input);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            transition: border-color var(--ease), color var(--ease);
        }

        .menu-card:hover .menu-card__ico {
            border-color: rgba(91,82,232,.5);
            color: var(--accent);
        }

        .menu-card__ico svg { width: 19px; height: 19px; }
        .menu-card__lbl { flex: 1; font-size: .93rem; font-weight: 600; }

        .menu-card__arrow {
            font-size: 1.25rem;
            color: var(--text-muted);
            transition: color var(--ease);
        }
        .menu-card:hover .menu-card__arrow { color: var(--text-primary); }

        /* Goals */
        .goals-list { list-style: none; display: flex; flex-direction: column; gap: 14px; }

        .goal__divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 5px;
        }

        .goal__divider::before,
        .goal__divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--divider);
        }

        .goal__type {
            font-size: .7rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .08em;
            white-space: nowrap;
        }

        .goal__text {
            text-align: center;
            font-size: .86rem;
        }

        /* Current condition*/
        .condition {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .condition__lbl { font-size: .95rem; font-weight: 700; }

        .condition__bars { display: flex; align-items: flex-end; gap: 4px; }

        .condition__bar {
            width: 11px;
            border-radius: 3px;
            background: var(--bg-input);
            border: 1.5px solid var(--border-input);
            transition: background var(--ease), border-color var(--ease);
        }

        .condition__bar:nth-child(1) { height: 14px; }
        .condition__bar:nth-child(2) { height: 20px; }
        .condition__bar:nth-child(3) { height: 27px; }

        .condition__bar.on {
            background: var(--accent);
            border-color: var(--border-focus);
        }

        .warning-popup {
            position: fixed;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.55);
            z-index: 50;
        }

        .warning-popup__card {
            background: rgba(20, 18, 40, 0.98);
            border: 1px solid rgba(255,255,255,.12);
            box-shadow: 0 24px 64px rgba(0,0,0,.55);
            border-radius: 24px;
            padding: 26px 26px 22px;
            max-width: 460px;
            width: min(92vw, 460px);
            position: relative;
        }

        .warning-popup__head {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 16px;
        }

        .warning-popup__icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #ff6b6b;
            color: #fff;
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .warning-popup__label {
            color: var(--accent);
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .18em;
            margin-bottom: 4px;
        }

        .warning-popup__title {
            margin: 0;
            font-size: 1.15rem;
            line-height: 1.3;
            color: var(--text-primary);
            font-weight: 700;
        }

        .warning-popup__message {
            color: var(--text-muted);
            font-size: .92rem;
            line-height: 1.6;
            margin: 0 0 18px;
        }

        .warning-popup__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .warning-popup__stmt {
            font-size: .88rem;
            color: var(--text-muted);
            margin-top: 6px;
        }

        .warning-popup__close {
            appearance: none;
            background: rgba(255,255,255,.08);
            border: none;
            color: var(--text-primary);
            font-size: 1.15rem;
            cursor: pointer;
            padding: 10px 12px;
            line-height: 1;
            position: absolute;
            top: 16px;
            right: 16px;
            border-radius: 12px;
        }

        .warning-popup__close:hover {
            background: rgba(255,255,255,.15);
        }

        .warning-popup__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 16px;
        }

        .warning-popup__btn {
            border: none;
            border-radius: 14px;
            padding: 12px 18px;
            cursor: pointer;
            font-weight: 700;
            transition: transform var(--ease), box-shadow var(--ease), background var(--ease);
        }

        .warning-popup__btn--primary {
            background: var(--accent);
            color: #fff;
            box-shadow: var(--shadow-btn);
        }

        .warning-popup__btn--secondary {
            background: rgba(255,255,255,.08);
            color: var(--text-primary);
        }

        .warning-popup__btn:hover {
            transform: translateY(-1px);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .dash__grid { grid-template-columns: 1fr 1fr; }
            .col--right { grid-column: 1 / -1; display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; }
            .col--right .w { margin: 0; }
        }

        @media (max-width: 680px) {
            .topbar { padding: 14px 18px; }
            .topbar__name { display: none; }
            .dash { padding: 18px 16px 40px; }
            .dash__title { font-size: 1.3rem; }
            .dash__grid { grid-template-columns: 1fr; }
            .col--right { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

{{-- Top bar --}}
<header class="topbar">
    <div class="topbar__brand">
        <img src="{{ asset('images/zentralogo.png') }}" alt="Zentra" class="topbar__logo">
        <span class="topbar__name">ZENTRA</span>
    </div>

    <div class="topbar__right">
        <div class="topbar__clock">
            <span class="topbar__date" id="js-date"></span>
            <span class="topbar__time" id="js-time"></span>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="topbar__logout">Log Out</button>
        </form>
    </div>
</header>

{{-- Dashboard body --}}
<main class="dash">
    <h1 class="dash__title">Your Personalized Work Dashboard</h1>

    @if (!empty($dailyWarning))
        <div class="warning-popup" id="warningPopup">
            <div class="warning-popup__card">
                <button class="warning-popup__close" id="warningPopupClose" aria-label="Close warning">×</button>
                <div class="warning-popup__head">
                    <div class="warning-popup__icon">!</div>
                    <div>
                        <div class="warning-popup__label">Task overload</div>
                        <h2 class="warning-popup__title">{{ $dailyWarning['count'] }} tasks on {{ $dailyWarning['date'] }}</h2>
                    </div>
                </div>
                <p class="warning-popup__message">Some lower-priority tasks can be moved away so your most important work stays on track.</p>
                <form method="POST" action="{{ route('checkin.apply_schedule') }}" class="warning-popup__actions">
                    @csrf
                    <input type="hidden" name="action" value="balance_day">
                    <input type="hidden" name="target_date" value="{{ $dailyWarning['date'] }}">
                    <button type="submit" class="warning-popup__btn warning-popup__btn--primary" id="warningPopupReschedule">Reschedule now</button>
                    <button type="button" class="warning-popup__btn warning-popup__btn--secondary" id="warningPopupDismiss">Not now</button>
                </form>
            </div>
        </div>
    @endif

    <div class="dash__grid">

        {{-- left column - Search, Kalender, Tasks--}}
        <div class="col col--left">

            {{-- Search --}}
            <div class="w w--search">
                <input
                    type="text"
                    id="taskSearch"
                    class="search-input"
                    placeholder="Search Tasks"
                    autocomplete="off"
                >
            </div>

            {{-- Kalender --}}
            <div class="w">
                <div class="cal__head">
                    <span class="cal__label" id="cal-label">{{ $calendar['monthLabel'] }}</span>
                    <div class="cal__nav">
                        <button class="cal__btn" id="cal-prev" aria-label="Bulan sebelumnya">&#8249;</button>
                        <button class="cal__btn" id="cal-next" aria-label="Bulan berikutnya">&#8250;</button>
                    </div>
                </div>

                <table class="cal__table">
                    <thead>
                        <tr>
                            @foreach (['Sun','Mon','Tue','Wed','Thr','Fri','Sat'] as $h)
                                <th>{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="cal-body">
                        @foreach (array_chunk($calendar['days'], 7) as $week)
                            <tr>
                                @foreach ($week as $cell)
                                    <td class="{{ !$cell['current'] ? 'other' : '' }} {{ $cell['today'] ? 'today' : '' }}">
                                        {{ $cell['day'] }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Upcoming Tasks --}}
            <div class="w">
                <h2 class="w__title">Upcoming Tasks</h2>
                <ul class="task-list" id="task-list">
                    @forelse ($upcomingTasks as $task)
                        <li class="task-item" data-title="{{ strtolower($task['title']) }}">
                            <span class="task-item__ico">
                                {{-- Task icon SVG --}}
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <path d="M3 9h18M9 21V9M15 13h3M15 17h3"/>
                                </svg>
                            </span>
                            <div class="task-item__body">
                                <span class="task-item__name">{{ $task['title'] }}</span>
                                <span class="task-item__sub">{{ $task['subtitle'] }}</span>
                            </div>
                            <span class="task-item__date">{{ $task['due_date'] }}</span>
                        </li>
                    @empty
                        <li style="font-size:.86rem;color:var(--text-muted)">No upcoming tasks.</li>
                    @endforelse
                </ul>
            </div>

        </div>{{-- /col--left --}}

        {{-- mid columns - menu item--}}
        <div class="col col--center">

            {{-- Daily Check-In --}}
            <a href="{{ route('checkin.index') }}" class="menu-card">
                <span class="menu-card__ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2"/>
                        <path d="M8 21h8M12 17v4M7 9l3 3 5-5"/>
                    </svg>
                </span>
                <span class="menu-card__lbl">Daily Check-In</span>
                <span class="menu-card__arrow">&#8250;</span>
            </a>

            {{-- Task Management --}}
            <a href="{{ route('tasks.index') }}" class="menu-card">
                <span class="menu-card__ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <path d="M3 9h18M9 21V9M15 13h3M15 17h3"/>
                    </svg>
                </span>
                <span class="menu-card__lbl">Task Management</span>
                <span class="menu-card__arrow">&#8250;</span>
            </a>

            {{-- Schedule --}}
            <a href="{{ route('schedule.index') }}" class="menu-card">
                <span class="menu-card__ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <path d="M16 2v4M8 2v4M3 10h18"/>
                        <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/>
                    </svg>
                </span>
                <span class="menu-card__lbl">Schedule</span>
                <span class="menu-card__arrow">&#8250;</span>
            </a>

            {{-- Target Setting --}}
            <a href="{{ route('targets.index') }}" class="menu-card">
                <span class="menu-card__ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"/>
                        <circle cx="12" cy="12" r="5"/>
                        <circle cx="12" cy="12" r="1" fill="currentColor"/>
                        <path d="M12 3v3M12 18v3M3 12h3M18 12h3"/>
                    </svg>
                </span>
                <span class="menu-card__lbl">Target Setting</span>
                <span class="menu-card__arrow">&#8250;</span>
            </a>

            {{-- Adaptive Productivity --}}
            <a href="{{ route('insights.index') }}" class="menu-card">
                <span class="menu-card__ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                    </svg>
                </span>
                <span class="menu-card__lbl">Adaptive Productivity</span>
                <span class="menu-card__arrow">&#8250;</span>
            </a>

        </div>{{-- /col--center --}}

        {{--right columns - Stats, Goals, Condition --}}
        <div class="col col--right">

            {{-- Goals --}}
            <div class="w">
                <h2 class="w__title">Goals</h2>
                <ul class="goals-list">
                    @foreach ($goals as $goal)
                        <li>
                            <div class="goal__divider">
                                <span class="goal__type">{{ $goal['type'] }}</span>
                            </div>
                            <p class="goal__text">{{ $goal['text'] }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Current Condition --}}
            <div class="w">
                <h2 class="w__title">Current Condition</h2>
                <div class="condition">
                    <span class="condition__lbl">{{ $currentCondition['label'] }}</span>
                    <div class="condition__bars">
                        @for ($i = 1; $i <= $currentCondition['max']; $i++)
                            <span class="condition__bar {{ $i <= $currentCondition['level'] ? 'on' : '' }}"></span>
                        @endfor
                    </div>
                </div>
            </div>

        </div>{{-- /col--right --}}

    </div>{{-- /.dash__grid --}}
</main>


<script>
    window.ZENTRA = {
        calendar: {
            year:  {{ $calendar['year'] }},
            month: {{ $calendar['month'] }},
            today: {{ $calendar['today'] }},
        },
    };
</script>

<script>
/*1. Live Clock */
(function () {
    const elDate = document.getElementById('js-date');
    const elTime = document.getElementById('js-time');

    function tick() {
        const now = new Date();
        elDate.textContent = now.toLocaleDateString('en-GB', {
            day: '2-digit', month: 'short', year: 'numeric'
        });
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        elTime.textContent = h + '.' + m;
    }

    tick();
    setInterval(tick, 1000);
})();

/* 2. Calendar */
(function () {
    const MONTHS = [
        'January','February','March','April','May','June',
        'July','August','September','October','November','December'
    ];

    const elLabel = document.getElementById('cal-label');
    const elBody  = document.getElementById('cal-body');
    const btnPrev = document.getElementById('cal-prev');
    const btnNext = document.getElementById('cal-next');

    let { year, month } = window.ZENTRA.calendar;


    function renderCalendar(y, m) {
        const firstDow   = new Date(y, m - 1, 1).getDay(); // 0 = Minggu
        const totalDays  = new Date(y, m, 0).getDate();
        const prevTotal  = new Date(y, m - 1, 0).getDate();

        const today     = new Date();
        const isCurMon  = (y === today.getFullYear() && m === today.getMonth() + 1);
        const todayNum  = isCurMon ? today.getDate() : -1;

        const cells = [];


        for (let i = firstDow - 1; i >= 0; i--) {
            cells.push({ d: prevTotal - i, cur: false, today: false });
        }

        for (let d = 1; d <= totalDays; d++) {
            cells.push({ d, cur: true, today: d === todayNum });
        }


        const rem = 42 - cells.length;
        for (let d = 1; d <= rem; d++) {
            cells.push({ d, cur: false, today: false });
        }


        elLabel.textContent = MONTHS[m - 1] + ' ' + y;

        let html = '';
        for (let r = 0; r < 6; r++) {
            html += '<tr>';
            for (let c = 0; c < 7; c++) {
                const cell = cells[r * 7 + c];
                let cls = '';
                if (!cell.cur)   cls += ' other';
                if (cell.today)  cls += ' today';
                html += `<td class="${cls.trim()}">${cell.d}</td>`;
            }
            html += '</tr>';
        }
        elBody.innerHTML = html;
    }

    renderCalendar(year, month);

    btnPrev.addEventListener('click', function () {
        month--;
        if (month < 1) { month = 12; year--; }
        renderCalendar(year, month);
    });

    btnNext.addEventListener('click', function () {
        month++;
        if (month > 12) { month = 1; year++; }
        renderCalendar(year, month);
    });
})();

/* 3. Task Search */
(function () {
    const searchInput = document.getElementById('taskSearch');
    const taskList = document.getElementById('task-list');
    const taskItems = taskList ? taskList.querySelectorAll('.task-item') : [];

    if (!searchInput || !taskList) {
        return;
    }

    searchInput.addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase().trim();

        taskItems.forEach(item => {
            const taskTitle = item.getAttribute('data-title') || '';

            if (searchTerm === '' || taskTitle.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
})();

@if (!empty($dailyWarning))
<script>
    (function () {
        const popup = document.getElementById('warningPopup');

        const closePopup = function () {
            if (popup) {
                popup.style.display = 'none';
            }
        };

        document.addEventListener('click', function (event) {
            const dismissBtn = event.target.closest('#warningPopupDismiss');
            const closeBtn = event.target.closest('#warningPopupClose');

            if (dismissBtn || closeBtn) {
                closePopup();
            }
        });
    })();
</script>
@endif

</body>
</html>
