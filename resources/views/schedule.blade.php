<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Zentra – Schedule</title>

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
            --border:       #2a2750;
            --border-light: #33305a;
            --text-primary: #ffffff;
            --text-muted:   #7e7aaa;
            --text-dim:     #4e4a70;
            --accent:       #2ec4ff;
            --accent-teal:  #1db8f0;
            --accent-purple:#6c63ff;
            --today-bg:     #1db8f0;
            --today-text:   #ffffff;
            --checked-color:#6c63ff;
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
        .nav-brand span {
            font-size: 1rem; font-weight: 700;
            letter-spacing: .28em; text-transform: uppercase;
        }

        .nav-datetime { text-align: right; line-height: 1.35; }
        .nav-datetime .date { font-size: 1rem; font-weight: 600; }
        .nav-datetime .time { font-size: .9rem; color: var(--text-muted); }

        /* ── Page ─────────────────────────────── */
        .page {
            display: flex; justify-content: center;
            padding: 48px 24px 60px;
        }

        /* ── Main card ────────────────────────── */
        .card {
            background: var(--bg-card);
            border-radius: var(--r-card);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-card);
            padding: 36px 40px 40px;
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
            justify-content: space-between;
            margin-bottom: 32px;
        }

        .back-btn {
            display: flex; align-items: center; gap: 6px;
            font-size: .9rem; font-weight: 500;
            color: var(--text-muted); text-decoration: none;
            transition: color var(--transition);
        }
        .back-btn:hover { color: var(--text-primary); }
        .back-btn svg { width: 18px; height: 18px; }

        .card-title {
            font-size: 1.9rem; font-weight: 700; letter-spacing: -.01em;
        }

        .edit-pref-btn {
            display: flex; align-items: center; gap: 6px;
            font-size: .85rem; font-weight: 500;
            color: var(--text-muted); text-decoration: none;
            transition: color var(--transition);
        }
        .edit-pref-btn:hover { color: var(--text-primary); }
        .edit-pref-btn svg { width: 16px; height: 16px; }

        /* ── Calendar container ───────────────── */
        .calendar-wrap {
            background: var(--bg-widget);
            border: 1px solid var(--border);
            border-radius: var(--r-widget);
            padding: 24px 28px;
            margin-bottom: 28px;
        }

        .cal-header {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .cal-month {
            font-size: 1.2rem; font-weight: 700;
        }

        .cal-nav { display: flex; gap: 8px; }
        .cal-nav button {
            background: transparent;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            color: var(--text-muted);
            width: 32px; height: 32px;
            cursor: pointer;
            font-size: 1rem;
            display: flex; align-items: center; justify-content: center;
            transition: background var(--transition), color var(--transition);
        }
        .cal-nav button:hover { background: var(--bg-widget-h); color: var(--text-primary); }

        /* ── Calendar table ───────────────────── */
        .cal-table {
            width: 100%; border-collapse: collapse;
        }

        .cal-table thead th {
            font-size: .85rem; font-weight: 600;
            color: var(--text-primary);
            padding: 0 0 14px;
            text-align: center;
            border-bottom: 1px solid var(--border);
        }

        .cal-table tbody td {
            text-align: center;
            padding: 10px 4px;
            border-right: 1px solid var(--border);
            font-size: 1.05rem; font-weight: 500;
            cursor: pointer;
            transition: color var(--transition);
            position: relative;
        }

        .cal-table tbody td:last-child { border-right: none; }

        .cal-table tbody td.other-month {
            color: var(--text-dim);
        }

        .cal-table tbody td:not(.other-month):hover .day-num {
            background: var(--bg-widget-h);
            border-radius: 50%;
        }

        .day-num {
            display: inline-flex; align-items: center; justify-content: center;
            width: 38px; height: 38px; border-radius: 50%;
            transition: background var(--transition);
        }

        .cal-table tbody td.today .day-num {
            background: var(--today-bg);
            color: var(--today-text);
            font-weight: 700;
            box-shadow: 0 0 16px rgba(30,184,240,.4);
        }

        .cal-table tbody td.selected:not(.today) .day-num {
            background: var(--accent-purple);
            color: #fff;
        }

        /* has-task dot */
        .task-dot {
            width: 5px; height: 5px; border-radius: 50%;
            background: var(--accent-purple);
            margin: 2px auto 0;
        }

        /* ── Task list ────────────────────────── */
        .task-section-title {
            font-size: .8rem; font-weight: 600;
            color: var(--text-muted);
            letter-spacing: .08em; text-transform: uppercase;
            margin-bottom: 14px;
        }

        .task-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        @media (max-width: 560px) {
            .task-grid { grid-template-columns: 1fr; }
            .card { padding: 24px 18px 28px; }
            .calendar-wrap { padding: 18px 14px; }
        }

        .task-card {
            background: var(--bg-widget);
            border: 1px solid var(--border);
            border-radius: var(--r-widget);
            padding: 14px 16px;
            display: flex; align-items: center; gap: 14px;
            transition: border-color var(--transition), background var(--transition);
        }

        .task-card:hover { background: var(--bg-widget-h); }
        .task-card.overdue { border-color: rgba(239,68,68,.3); }

        .task-icon {
            width: 44px; height: 44px;
            background: var(--bg-outer);
            border: 1px solid var(--border-light);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            color: var(--text-muted);
        }
        .task-icon svg { width: 22px; height: 22px; }

        .task-info { flex: 1; min-width: 0; }

        .task-name {
            font-size: .95rem; font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        .task-time {
            font-size: .78rem; color: var(--text-muted); margin-top: 2px;
        }

        .task-reschedule {
            font-size: .78rem; font-weight: 600;
            color: var(--accent);
            cursor: pointer; margin-top: 4px;
            display: inline-block;
            transition: opacity var(--transition);
        }
        .task-reschedule:hover { opacity: .75; }

        /* Custom checkbox */
        .task-checkbox { flex-shrink: 0; }
        .task-checkbox input { display: none; }

        .check-box {
            width: 28px; height: 28px;
            border: 2px solid var(--border-light);
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: background var(--transition), border-color var(--transition);
        }

        .check-box svg {
            width: 16px; height: 16px;
            stroke: #fff;
            opacity: 0;
            transition: opacity var(--transition);
        }

        .task-checkbox input:checked + .check-box {
            background: var(--checked-color);
            border-color: var(--checked-color);
        }

        .task-checkbox input:checked + .check-box svg { opacity: 1; }
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
            <h1 class="card-title">Schedule</h1>
            <a href="{{ route('schedule.preferences') }}" class="edit-pref-btn">
                Edit Time Preference
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </a>
        </div>

        {{-- Calendar --}}
        <div class="calendar-wrap">
            <div class="cal-header">
                <span class="cal-month" id="cal-month-label"></span>
                <div class="cal-nav">
                    <button id="cal-prev" title="Previous month">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <button id="cal-next" title="Next month">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>

            <table class="cal-table">
                <thead>
                    <tr>
                        <th>Sun</th><th>Mon</th><th>Tue</th>
                        <th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
                    </tr>
                </thead>
                <tbody id="cal-body"></tbody>
            </table>
        </div>

        {{-- Tasks --}}
        <div class="task-section-title" id="task-date-label">Today's Tasks</div>

        <div class="task-grid" id="task-list">

            {{-- Task cards — dari Laravel (contoh static, ganti dengan @foreach $tasks) --}}
            @php
                $tasks = $tasks ?? [
                    ['name' => 'Reading',   'time' => '5 Hours Ago',    'done' => true,  'overdue' => false],
                    ['name' => 'Homework',  'time' => 'Starts in 2 Hr', 'done' => false, 'overdue' => false],
                    ['name' => 'Homework',  'time' => '1 Hour Ago',     'done' => false, 'overdue' => true],
                ];
            @endphp

            @foreach($tasks as $i => $task)
            <div class="task-card {{ $task['overdue'] ? 'overdue' : '' }}">
                <div class="task-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="3"/>
                        <line x1="3" y1="9" x2="21" y2="9"/>
                        <line x1="9" y1="21" x2="9" y2="9"/>
                    </svg>
                </div>
                <div class="task-info">
                    <div class="task-name">{{ $task['name'] }}</div>
                    <div class="task-time">{{ $task['time'] }}</div>
                    @if($task['overdue'])
                        <span class="task-reschedule">Automatic Reschedule?</span>
                    @endif
                </div>
                <label class="task-checkbox">
                    <input type="checkbox"
                           name="task_done[{{ $i }}]"
                           {{ $task['done'] ? 'checked' : '' }}
                           onchange="toggleTask(this, {{ $i }})">
                    <span class="check-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </span>
                </label>
            </div>
            @endforeach

        </div>

    </div>
</div>

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
updateClock();
setInterval(updateClock, 1000);

// ── Calendar ─────────────────────────────────────────
const MONTHS = ['January','February','March','April','May','June',
                'July','August','September','October','November','December'];
const DAYS   = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

// Dates that have tasks (from PHP — add real dates here)
const taskDates = @json(
    isset($taskDates) ? $taskDates : ['2026-04-14', '2026-04-17', '2026-04-21']
);

let current  = new Date();
let viewYear = current.getFullYear();
let viewMonth= current.getMonth();
let selected = `${current.getFullYear()}-${String(current.getMonth()+1).padStart(2,'0')}-${String(current.getDate()).padStart(2,'0')}`;

function renderCalendar() {
    const label = document.getElementById('cal-month-label');
    const body  = document.getElementById('cal-body');

    label.textContent = `${MONTHS[viewMonth]} ${viewYear}`;
    body.innerHTML = '';

    const firstDay = new Date(viewYear, viewMonth, 1).getDay();
    const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
    const daysInPrev  = new Date(viewYear, viewMonth, 0).getDate();

    const todayStr = `${current.getFullYear()}-${String(current.getMonth()+1).padStart(2,'0')}-${String(current.getDate()).padStart(2,'0')}`;

    let day = 1, nextDay = 1, cell = 0;

    for (let row = 0; row < 6; row++) {
        if (day > daysInMonth && row > 0) break;
        const tr = document.createElement('tr');

        for (let col = 0; col < 7; col++) {
            const td = document.createElement('td');
            let dateStr, dayNum;

            if (cell < firstDay) {
                dayNum = daysInPrev - firstDay + cell + 1;
                dateStr = `${viewYear}-${String(viewMonth).padStart(2,'0')}-${String(dayNum).padStart(2,'0')}`;
                td.classList.add('other-month');
            } else if (day > daysInMonth) {
                dayNum = nextDay++;
                const nm = viewMonth + 2 > 12 ? 1 : viewMonth + 2;
                const ny = viewMonth + 2 > 12 ? viewYear + 1 : viewYear;
                dateStr = `${ny}-${String(nm).padStart(2,'0')}-${String(dayNum).padStart(2,'0')}`;
                td.classList.add('other-month');
            } else {
                dayNum = day++;
                dateStr = `${viewYear}-${String(viewMonth+1).padStart(2,'0')}-${String(dayNum).padStart(2,'0')}`;
            }

            if (dateStr === todayStr) td.classList.add('today');
            if (dateStr === selected) td.classList.add('selected');

            td.innerHTML = `<span class="day-num">${dayNum}</span>`;

            if (taskDates.includes(dateStr) && !td.classList.contains('other-month')) {
                td.innerHTML += `<div class="task-dot"></div>`;
            }

            td.dataset.date = dateStr;
            td.addEventListener('click', () => selectDate(dateStr, td));

            tr.appendChild(td);
            cell++;
        }
        body.appendChild(tr);
    }
}

function selectDate(dateStr, td) {
    document.querySelectorAll('.cal-table td').forEach(c => c.classList.remove('selected'));
    td.classList.add('selected');
    selected = dateStr;

    const d = new Date(dateStr + 'T00:00:00');
    const label = d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    document.getElementById('task-date-label').textContent = `Tasks – ${label}`;

    // In real app: fetch tasks via AJAX for selected date
    // fetch(`/schedule/tasks?date=${dateStr}`).then(...)
}

document.getElementById('cal-prev').addEventListener('click', () => {
    viewMonth--;
    if (viewMonth < 0) { viewMonth = 11; viewYear--; }
    renderCalendar();
});

document.getElementById('cal-next').addEventListener('click', () => {
    viewMonth++;
    if (viewMonth > 11) { viewMonth = 0; viewYear++; }
    renderCalendar();
});

renderCalendar();

// ── Toggle task via AJAX ─────────────────────────────
function toggleTask(checkbox, taskId) {
    fetch('{{ route("schedule.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ task_id: taskId, done: checkbox.checked })
    });
}
</script>

</body>
</html>