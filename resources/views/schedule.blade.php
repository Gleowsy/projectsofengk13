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
            --accent-purple:#6c63ff;
            --accent-green: #22c55e;
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

        .page { display: flex; justify-content: center; padding: 48px 24px 60px; }

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
        .card-title { font-size: 1.9rem; font-weight: 700; letter-spacing: -.01em; }
        .edit-pref-btn {
            display: flex; align-items: center; gap: 6px;
            font-size: .85rem; font-weight: 500;
            color: var(--text-muted); text-decoration: none;
            transition: color var(--transition);
        }
        .edit-pref-btn:hover { color: var(--text-primary); }
        .edit-pref-btn svg { width: 16px; height: 16px; }

        /* ── Calendar ── */
        .calendar-wrap {
            background: var(--bg-widget);
            border: 1px solid var(--border);
            border-radius: var(--r-widget);
            padding: 24px 28px;
            margin-bottom: 28px;
        }
        .cal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .cal-month  { font-size: 1.2rem; font-weight: 700; }
        .cal-nav    { display: flex; gap: 8px; }
        .cal-nav button {
            background: transparent; border: 1px solid var(--border-light);
            border-radius: 8px; color: var(--text-muted);
            width: 32px; height: 32px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: background var(--transition), color var(--transition);
        }
        .cal-nav button:hover { background: var(--bg-widget-h); color: var(--text-primary); }
        .cal-table { width: 100%; border-collapse: collapse; }
        .cal-table thead th {
            font-size: .85rem; font-weight: 600; color: var(--text-primary);
            padding: 0 0 14px; text-align: center;
            border-bottom: 1px solid var(--border);
        }
        .cal-table tbody td {
            text-align: center; padding: 10px 4px;
            border-right: 1px solid var(--border);
            font-size: 1.05rem; font-weight: 500;
            cursor: pointer; position: relative;
            transition: color var(--transition);
        }
        .cal-table tbody td:last-child { border-right: none; }
        .cal-table tbody td.other-month { color: var(--text-dim); pointer-events: none; }
        .cal-table tbody td:not(.other-month):hover .day-num { background: var(--bg-widget-h); border-radius: 50%; }
        .day-num {
            display: inline-flex; align-items: center; justify-content: center;
            width: 38px; height: 38px; border-radius: 50%;
            transition: background var(--transition);
        }
        .cal-table tbody td.today .day-num {
            background: var(--today-bg); color: var(--today-text);
            font-weight: 700; box-shadow: 0 0 16px rgba(30,184,240,.4);
        }
        .cal-table tbody td.selected:not(.today) .day-num { background: var(--accent-purple); color: #fff; }
        .task-dot { width: 5px; height: 5px; border-radius: 50%; background: var(--accent-purple); margin: 2px auto 0; }

        /* ── Task section ── */
        .task-section-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 14px;
        }
        .task-section-title {
            font-size: .8rem; font-weight: 600; color: var(--text-muted);
            letter-spacing: .08em; text-transform: uppercase;
        }
        .task-count { font-size: .75rem; font-weight: 600; color: var(--text-dim); }

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

        /* ── Task card ── */
        .task-card {
            background: var(--bg-widget);
            border: 1px solid var(--border);
            border-radius: var(--r-widget);
            padding: 14px 16px;
            display: flex; align-items: flex-start; gap: 14px;
            transition: border-color var(--transition), background var(--transition);
        }
        .task-card:hover { background: var(--bg-widget-h); }
        .task-card.overdue { border-color: rgba(239,68,68,.35); }

        .task-icon {
            width: 44px; height: 44px; flex-shrink: 0;
            background: var(--bg-outer);
            border: 1px solid var(--border-light);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-muted);
            margin-top: 2px;
        }
        .task-icon svg { width: 22px; height: 22px; }

        .task-info { flex: 1; min-width: 0; }

        .task-name {
            font-size: .95rem; font-weight: 600; color: var(--text-primary);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            transition: color var(--transition);
        }
        /* Strikethrough saat done */
        .task-card.done .task-name {
            text-decoration: line-through;
            color: var(--text-muted);
        }
        .task-card.done { opacity: .6; }

        .task-time { font-size: .78rem; color: var(--text-muted); margin-top: 3px; }
        .task-time.overdue-time { color: #f87171; }

        /* Priority badge */
        .priority-badge {
            display: inline-block;
            font-size: .65rem; font-weight: 700;
            padding: 2px 7px; border-radius: 4px;
            text-transform: uppercase; letter-spacing: .05em;
            margin-top: 5px;
        }
        .priority-badge.high   { background: rgba(239,68,68,.15);  color: #f87171; }
        .priority-badge.medium { background: rgba(251,191,36,.15); color: #fbbf24; }
        .priority-badge.low    { background: rgba(108,99,255,.15); color: #a78bfa; }

        /* Auto Reschedule button */
        .reschedule-btn {
            display: inline-flex; align-items: center; gap: 5px;
            margin-top: 7px;
            font-family: inherit; font-size: .75rem; font-weight: 600;
            color: var(--accent);
            background: rgba(46,196,255,.08);
            border: 1px solid rgba(46,196,255,.25);
            border-radius: 6px;
            padding: 4px 10px;
            cursor: pointer;
            transition: background var(--transition), border-color var(--transition);
        }
        .reschedule-btn:hover:not(:disabled) {
            background: rgba(46,196,255,.16);
            border-color: rgba(46,196,255,.5);
        }
        .reschedule-btn:disabled { opacity: .5; cursor: not-allowed; }
        .reschedule-btn.done-state {
            color: var(--accent-green);
            background: rgba(34,197,94,.08);
            border-color: rgba(34,197,94,.25);
        }
        .reschedule-btn svg { width: 12px; height: 12px; flex-shrink: 0; }

        /* Checkbox — di sebelah kanan, vertically centered */
        .task-checkbox-wrap { flex-shrink: 0; margin-top: 2px; }
        .task-checkbox-wrap input { display: none; }
        .check-box {
            width: 28px; height: 28px;
            border: 2px solid var(--border-light);
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: background var(--transition), border-color var(--transition);
        }
        .check-box svg { width: 16px; height: 16px; stroke: #fff; opacity: 0; transition: opacity var(--transition); }
        .task-checkbox-wrap input:checked + .check-box { background: var(--checked-color); border-color: var(--checked-color); }
        .task-checkbox-wrap input:checked + .check-box svg { opacity: 1; }

        /* Empty / loading states */
        .empty-state {
            grid-column: 1/-1; text-align: center;
            padding: 40px 0; color: var(--text-muted); font-size: .9rem;
        }
        .empty-state svg { width: 36px; height: 36px; margin: 0 auto 10px; display: block; opacity: .25; }

        .skeleton {
            grid-column: 1/-1; display: flex; gap: 14px;
        }
        .skeleton-card {
            flex: 1; height: 86px;
            background: var(--bg-widget);
            border: 1px solid var(--border);
            border-radius: var(--r-widget);
            animation: shimmer 1.4s ease-in-out infinite;
        }
        @keyframes shimmer { 0%,100%{opacity:.4} 50%{opacity:.75} }

        /* Toast */
        .toast {
            position: fixed; bottom: 28px; left: 50%;
            transform: translateX(-50%) translateY(12px);
            background: #1e1c38; color: #fff;
            border: 1px solid var(--border-light);
            padding: 11px 22px; border-radius: 50px;
            font-size: .875rem; font-weight: 500;
            opacity: 0; pointer-events: none;
            transition: opacity .25s, transform .25s;
            z-index: 300; white-space: nowrap;
        }
        .toast.show   { opacity: 1; transform: translateX(-50%) translateY(0); }
        .toast.ok     { border-color: rgba(34,197,94,.4);  color: #4ade80; }
        .toast.err    { border-color: rgba(239,68,68,.4);  color: #f87171; }
    </style>
</head>
<body>

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
        <a href="{{ route('dashboard') }}" class="back-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
        <h1 class="card-title">Schedule</h1>
        <a href="{{ route('schedule.preferences') }}" class="edit-pref-btn">
            Edit Time Preference
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
    </div>

    {{-- Calendar --}}
    <div class="calendar-wrap">
        <div class="cal-header">
            <span class="cal-month" id="cal-month-label"></span>
            <div class="cal-nav">
                <button id="cal-prev">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <button id="cal-next">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
        </div>
        <table class="cal-table">
            <thead>
                <tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr>
            </thead>
            <tbody id="cal-body"></tbody>
        </table>
    </div>

    {{-- Task list header --}}
    <div class="task-section-header">
        <div class="task-section-title" id="task-date-label">Today's Tasks</div>
        <div class="task-count" id="task-count"></div>
    </div>

    {{-- Task grid — server-side render untuk initial load --}}
    <div class="task-grid" id="task-list">
        @forelse($todayTasks as $task)
            @php
                $cardId = 'tc-' . $task['task_id'] . '-' . $task['subtask_num'];
            @endphp
            <div class="task-card {{ $task['overdue'] ? 'overdue' : '' }} {{ $task['done'] ? 'done' : '' }}"
                 id="{{ $cardId }}">

                <div class="task-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="3"/>
                        <line x1="3" y1="9" x2="21" y2="9"/>
                        <line x1="9" y1="21" x2="9" y2="9"/>
                    </svg>
                </div>

                <div class="task-info">
                    <div class="task-name">{{ $task['name'] }}</div>
                    <div class="task-time {{ $task['overdue'] ? 'overdue-time' : '' }}">{{ $task['time'] }}</div>

                    @if($task['priority'])
                        <span class="priority-badge {{ $task['priority'] }}">{{ $task['priority'] }}</span>
                    @endif

                    @if($task['overdue'] && !$task['done'])
                        <div>
                            <button class="reschedule-btn"
                                    id="rb-{{ $task['task_id'] }}-{{ $task['subtask_num'] }}"
                                    onclick="rescheduleTask(this, {{ $task['task_id'] }}, {{ $task['subtask_num'] }})">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 4v6h6"/><path d="M3.51 15a9 9 0 1 0 .49-4.5"/>
                                </svg>
                                Auto Reschedule
                            </button>
                        </div>
                    @endif
                </div>

                <label class="task-checkbox-wrap">
                    <input type="checkbox"
                           id="cb-{{ $task['task_id'] }}-{{ $task['subtask_num'] }}"
                           {{ $task['done'] ? 'checked' : '' }}
                           onchange="markDone(this, {{ $task['task_id'] }}, {{ $task['subtask_num'] }})">
                    <span class="check-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </span>
                </label>
            </div>
        @empty
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="3"/>
                    <line x1="3" y1="9" x2="21" y2="9"/>
                    <line x1="9" y1="21" x2="9" y2="9"/>
                </svg>
                No tasks for today.
            </div>
        @endforelse
    </div>

</div>
</div>

<div class="toast" id="toast"></div>

<script>
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
const R      = {
    tasks:       '{{ route("schedule.tasks") }}',
    reschedule:  '{{ route("schedule.reschedule") }}',
    done:        '{{ route("schedule.done") }}',
};
let taskDates = @json($taskDates);

// ── Clock ──────────────────────────────────────────────────────
(function tick() {
    const n = new Date();
    document.getElementById('nav-date').textContent =
        n.toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'});
    document.getElementById('nav-time').textContent =
        n.toLocaleTimeString('en-GB', {hour:'2-digit',minute:'2-digit'}).replace(':','.');
    setTimeout(tick, 1000);
})();

// ── Calendar ───────────────────────────────────────────────────
const MONTHS  = ['January','February','March','April','May','June','July','August','September','October','November','December'];
const now     = new Date();
let viewYear  = now.getFullYear();
let viewMonth = now.getMonth();
const todayStr = fmtDate(now);
let selected   = '{{ $date }}';

function fmtDate(d) {
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}

function renderCalendar() {
    document.getElementById('cal-month-label').textContent = `${MONTHS[viewMonth]} ${viewYear}`;
    const body        = document.getElementById('cal-body');
    body.innerHTML    = '';
    const firstDay    = new Date(viewYear, viewMonth, 1).getDay();
    const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
    const daysInPrev  = new Date(viewYear, viewMonth, 0).getDate();
    let day = 1, nextDay = 1, cell = 0;

    for (let row = 0; row < 6; row++) {
        if (day > daysInMonth && row > 0) break;
        const tr = document.createElement('tr');
        for (let col = 0; col < 7; col++) {
            const td = document.createElement('td');
            let dateStr, dayNum;

            if (cell < firstDay) {
                dayNum  = daysInPrev - firstDay + cell + 1;
                const pm = viewMonth === 0 ? 11 : viewMonth - 1;
                const py = viewMonth === 0 ? viewYear - 1 : viewYear;
                dateStr = `${py}-${String(pm + 1).padStart(2,'0')}-${String(dayNum).padStart(2,'0')}`;
                td.classList.add('other-month');
            } else if (day > daysInMonth) {
                dayNum      = nextDay++;
                const nm    = viewMonth === 11 ? 0 : viewMonth + 1;
                const ny    = viewMonth === 11 ? viewYear + 1 : viewYear;
                dateStr     = `${ny}-${String(nm + 1).padStart(2,'0')}-${String(dayNum).padStart(2,'0')}`;
                td.classList.add('other-month');
            } else {
                dayNum  = day++;
                dateStr = `${viewYear}-${String(viewMonth+1).padStart(2,'0')}-${String(dayNum).padStart(2,'0')}`;
            }

            if (dateStr === todayStr) td.classList.add('today');
            if (dateStr === selected) td.classList.add('selected');
            td.innerHTML = `<span class="day-num">${dayNum}</span>`;
            if (!td.classList.contains('other-month') && taskDates.includes(dateStr)) {
                td.innerHTML += `<div class="task-dot"></div>`;
            }
            td.dataset.date = dateStr;
            if (!td.classList.contains('other-month')) {
                td.addEventListener('click', () => selectDate(dateStr, td));
            }
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

    const label = document.getElementById('task-date-label');
    if (dateStr === todayStr) {
        label.textContent = "Today's Tasks";
    } else {
        const d = new Date(dateStr + 'T00:00:00');
        label.textContent = `Tasks – ${d.toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'})}`;
    }

    loadTasks(dateStr);
}

document.getElementById('cal-prev').addEventListener('click', () => {
    if (--viewMonth < 0) { viewMonth = 11; viewYear--; }
    renderCalendar();
});
document.getElementById('cal-next').addEventListener('click', () => {
    if (++viewMonth > 11) { viewMonth = 0; viewYear++; }
    renderCalendar();
});

renderCalendar();
updateCount();   // hitung dari server-side render

// ── Load tasks (AJAX) ──────────────────────────────────────────
function loadTasks(date) {
    const list = document.getElementById('task-list');
    list.innerHTML = `<div class="skeleton"><div class="skeleton-card"></div><div class="skeleton-card"></div></div>`;
    document.getElementById('task-count').textContent = '';

    fetch(`${R.tasks}?date=${encodeURIComponent(date)}`, {
        headers: { 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => {
        if (!r.ok) throw new Error('Failed to load tasks');
        return r.json();
    })
    .then(data => renderTasks(data.tasks))
    .catch(() => {
        list.innerHTML = `<div class="empty-state">Failed to load tasks.</div>`;
        document.getElementById('task-count').textContent = '';
    });
}

function renderTasks(tasks) {
    const list = document.getElementById('task-list');
    if (!tasks || tasks.length === 0) {
        list.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="3"/>
                    <line x1="3" y1="9" x2="21" y2="9"/>
                    <line x1="9" y1="21" x2="9" y2="9"/>
                </svg>
                No tasks for this day.
            </div>`;
        document.getElementById('task-count').textContent = '';
        return;
    }
    list.innerHTML = tasks.map(buildCard).join('');
    updateCount();

    // Pasang ulang event listener ketika fungsi tersedia.
    if (typeof initDynamicTaskEvents === 'function') {
        initDynamicTaskEvents();
    }
}

function buildCard(t) {
    const cardId = `tc-${t.task_id}-${t.subtask_num}`;
    const rbId   = `rb-${t.task_id}-${t.subtask_num}`;
    const cbId   = `cb-${t.task_id}-${t.subtask_num}`;

    const badge = t.priority
        ? `<span class="priority-badge ${esc(t.priority)}">${esc(t.priority)}</span>` : '';

    const rescheduleBtn = (t.overdue && !t.done)
        ? `<div><button class="reschedule-btn" id="${rbId}"
                onclick="rescheduleTask(this,${t.task_id},${t.subtask_num})">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 4v6h6"/><path d="M3.51 15a9 9 0 1 0 .49-4.5"/>
                </svg>
                Auto Reschedule
           </button></div>` : '';

    return `
        <div class="task-card${t.overdue?' overdue':''}${t.done?' done':''}" id="${cardId}">
            <div class="task-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="3"/>
                    <line x1="3" y1="9" x2="21" y2="9"/>
                    <line x1="9" y1="21" x2="9" y2="9"/>
                </svg>
            </div>
            <div class="task-info">
                <div class="task-name">${esc(t.name)}</div>
                <div class="task-time${t.overdue?' overdue-time':''}">${esc(t.time)}</div>
                ${badge}
                ${rescheduleBtn}
            </div>
            <label class="task-checkbox-wrap">
                <input type="checkbox" id="${cbId}" ${t.done?'checked':''}
                       onchange="markDone(this,${t.task_id},${t.subtask_num})">
                <span class="check-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </span>
            </label>
        </div>`;
}

// ── Reschedule ─────────────────────────────────────────────────
function rescheduleTask(btn, taskId, subtaskNum) {
    btn.disabled    = true;
    btn.textContent = 'Rescheduling…';

    fetch(R.reschedule, {
        method:  'POST',
        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF },
        body:    JSON.stringify({ task_id: taskId, subtask_num: subtaskNum }),
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) throw new Error(data.message || 'Failed');

        // Update tombol jadi "Rescheduled ✓"
        btn.classList.add('done-state');
        btn.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px">
                <polyline points="20 6 9 17 4 12"/>
            </svg> Rescheduled`;

        // Update waktu di card
        const card   = document.getElementById(`tc-${taskId}-${subtaskNum}`);
        const timeEl = card && card.querySelector('.task-time');
        if (timeEl) {
            timeEl.textContent = `Starts at ${data.new_time} · ${fmtDateLabel(data.new_date)}`;
            timeEl.classList.remove('overdue-time');
        }
        if (card) card.classList.remove('overdue');

        // Tambah dot kalender untuk tanggal baru
        if (data.new_date && !taskDates.includes(data.new_date)) {
            taskDates.push(data.new_date);
            renderCalendar();
        }

        showToast(data.message, 'ok');
    })
    .catch(err => {
        btn.disabled    = false;
        btn.textContent = 'Auto Reschedule';
        showToast('Reschedule failed. Try again.', 'err');
        console.error(err);
    });
}

// ── Mark done ──────────────────────────────────────────────────
function markDone(checkbox, taskId, subtaskNum) {
    const card = document.getElementById(`tc-${taskId}-${subtaskNum}`);

    // Langsung apply visual — optimistic update
    if (card) {
        card.classList.toggle('done', checkbox.checked);
        // Sembunyikan tombol reschedule saat done
        const rb = document.getElementById(`rb-${taskId}-${subtaskNum}`);
        if (rb) rb.closest('div').style.display = checkbox.checked ? 'none' : '';
    }
    updateCount();

    fetch(R.done, {
        method:  'POST',
        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF },
        body:    JSON.stringify({ task_id: taskId, subtask_num: subtaskNum, done: checkbox.checked }),
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            // Rollback kalau gagal
            checkbox.checked = !checkbox.checked;
            if (card) card.classList.toggle('done', checkbox.checked);
            updateCount();
            showToast('Failed to save. Try again.', 'err');
        }
    })
    .catch(() => {
        checkbox.checked = !checkbox.checked;
        if (card) card.classList.toggle('done', checkbox.checked);
        updateCount();
        showToast('Network error.', 'err');
    });
}

// ── Helpers ────────────────────────────────────────────────────
function updateCount() {
    const all  = document.querySelectorAll('#task-list .task-card');
    const done = document.querySelectorAll('#task-list .task-card.done');
    const el   = document.getElementById('task-count');
    if (el) el.textContent = all.length > 0 ? `${done.length}/${all.length} done` : '';
}

function esc(s) {
    return String(s ?? '')
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function fmtDateLabel(str) {
    if (!str) return '';
    const d = new Date(str + 'T00:00:00');
    return d.toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'});
}

let _toastTimer;
function showToast(msg, type = '') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className   = `toast show ${type}`;
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => t.classList.remove('show'), 3000);
}
</script>

</body>
</html>