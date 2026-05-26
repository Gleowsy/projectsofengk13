<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Zentra – Task Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-outer:     #1a0f2e;
            --bg-card:      #141228;
            --bg-input:     #1c1a35;
            --border:       #2b2850;
            --text:         #ffffff;
            --muted:        #8884a8;
            --placeholder:  #4e4a6a;
            --accent:       #5b52e8;
            --accent-h:     #4a43d4;
            --accent-link:  #2ec4ff;
            --divider:      #2b2850;
            --r:            20px;
            --ease:         .22s cubic-bezier(.4,0,.2,1);
        }

        html, body {
            min-height: 100vh;
            font-family: 'Outfit', sans-serif;
            background:
                radial-gradient(ellipse 80% 60% at 15% 5%,  #2d1b5e 0%, transparent 55%),
                radial-gradient(ellipse 60% 50% at 85% 90%, #1b0e44 0%, transparent 55%),
                var(--bg-outer);
            color: var(--text);
        }

        /* ── TOP BAR ── */
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

        /* ── WRAPPER ── */
        .task-workspace {
            padding: 28px 36px 52px;
            max-width: 720px;
            margin: 0 auto;
            animation: fadeUp .45s cubic-bezier(.4,0,.2,1) both;
        }
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(18px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .w {
            background: var(--bg-card);
            border: 1px solid rgba(255,255,255,.05);
            border-radius: var(--r);
            padding: 28px;
            box-shadow: 0 8px 32px rgba(0,0,0,.4);
        }

        /* ── HEADER ── */
        .workspace-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--divider);
            padding-bottom: 16px;
        }
        .workspace-header--form { justify-content: center; position: relative; }
        .btn-back, .btn-cancel {
            display: flex; align-items: center; gap: 6px;
            color: var(--muted); text-decoration: none;
            font-size: .9rem; font-weight: 500;
            background: none; border: none; cursor: pointer;
            transition: color var(--ease);
        }
        .btn-cancel { position: absolute; left: 0; }
        .btn-back:hover, .btn-cancel:hover { color: var(--text); }
        .btn-back svg, .btn-cancel svg { width: 18px; height: 18px; }
        .workspace-title { font-size: 1.4rem; font-weight: 700; letter-spacing: -.01em; }
        .btn-add-task {
            display: flex; align-items: center; gap: 6px;
            background: none; border: none;
            color: var(--muted); font-family: inherit;
            font-size: .9rem; font-weight: 500; cursor: pointer;
            transition: color var(--ease);
        }
        .btn-add-task:hover { color: var(--accent-link); }
        .btn-add-task svg { width: 18px; height: 18px; }

        /* ── LIST VIEW ── */
        .tm-list { display: flex; flex-direction: column; gap: 14px; }

        .tm-item {
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 16px 20px;
            transition: border-color var(--ease);
        }
        .tm-item:hover { border-color: rgba(91,82,232,.4); }

        .tm-item__header { display: flex; justify-content: space-between; align-items: center; }
        .tm-item__left   { display: flex; align-items: center; gap: 14px; }
        .tm-item__ico {
            width: 42px; height: 42px; flex-shrink: 0;
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 10px; display: flex; align-items: center;
            justify-content: center; color: var(--muted);
        }
        .tm-item__ico svg { width: 20px; height: 20px; }
        .tm-item__name { font-size: .93rem; font-weight: 600; }
        .tm-item__meta { font-size: .74rem; color: var(--muted); margin-top: 2px; }
        .tm-item__right { display: flex; align-items: center; gap: 10px; }
        .tm-action {
            background: none; border: none; color: var(--muted);
            cursor: pointer; padding: 4px;
            display: flex; align-items: center; justify-content: center;
            transition: color var(--ease);
        }
        .tm-action:hover { color: var(--text); }
        .tm-action--edit:hover { color: var(--accent-link); }
        .tm-action svg { width: 17px; height: 17px; }

        /* subtask chips */
        .subtask-chips {
            display: flex; flex-wrap: wrap; gap: 8px;
            margin-top: 14px; padding-top: 14px;
            border-top: 1px solid var(--divider);
        }
        .subtask-chip {
            display: flex; align-items: center; gap: 10px;
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 10px; padding: 8px 12px; min-width: 130px;
        }
        .chip-dot { width: 14px; height: 14px; border-radius: 50%; flex-shrink: 0; }
        .chip-dot--low    { background:#2ecc71; box-shadow:0 0 7px rgba(46,204,113,.45); }
        .chip-dot--medium { background:#e67e22; box-shadow:0 0 7px rgba(230,126,34,.45); }
        .chip-dot--high   { background:#e74c3c; box-shadow:0 0 7px rgba(231,76,60,.45); }
        .chip-dot--none   { background: var(--border); }
        .chip-name { font-size: .8rem; font-weight: 600; color: var(--text); }
        .chip-date { font-size: .7rem; color: var(--muted); margin-top: 1px; }
        .tm-footer { margin-top: 24px; font-size: .76rem; color: var(--placeholder); font-weight: 500; letter-spacing: .05em; }

        /* ── FORM VIEW ── */
        /* Task header card */
        .ef-task-card {
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 14px;
        }
        .ef-task-header {
            display: flex; align-items: center; gap: 14px;
            padding: 16px 20px;
            background: var(--bg-input);
        }
        .ef-task-ico {
            width: 42px; height: 42px; flex-shrink: 0;
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 10px; display: flex; align-items: center;
            justify-content: center; color: var(--muted);
        }
        .ef-task-ico svg { width: 20px; height: 20px; }
        .ef-task-info { flex: 1; display: flex; flex-direction: column; gap: 3px; }
        .ef-inline-field { display: flex; align-items: center; gap: 6px; }
        .ef-input-name {
            background: transparent; border: none; outline: none;
            font-family: inherit; font-size: .95rem; font-weight: 600;
            color: var(--text); min-width: 60px; max-width: 260px;
        }
        .ef-input-name::placeholder { color: var(--placeholder); }
        .ef-sub-hint { font-size: .72rem; color: var(--muted); }
        .ef-pencil { color: var(--muted); display: flex; align-items: center; opacity: .7; }
        .ef-pencil svg { width: 13px; height: 13px; }
        .ef-pencil--sm svg { width: 11px; height: 11px; }
        .ef-arrow-toggle {
            background: none; border: none; color: var(--muted);
            cursor: pointer; display: flex; align-items: center;
            transition: color var(--ease), transform var(--ease);
            flex-shrink: 0;
        }
        .ef-arrow-toggle:hover { color: var(--text); }
        .ef-arrow-toggle svg { width: 22px; height: 22px; }
        .ef-arrow-toggle.open { transform: rotate(180deg); }

        /* subtask panel */
        .ef-subtasks-panel { padding: 0 20px 20px; background: var(--bg-input); }
        .ef-divider {
            text-align: center; font-size: .72rem; color: var(--placeholder);
            letter-spacing: .05em; font-weight: 500;
            display: flex; align-items: center; gap: 12px;
            padding: 14px 0 16px;
        }
        .ef-divider::before, .ef-divider::after { content:''; flex:1; border-bottom:1px solid var(--border); }

        .ef-subtask-row {
            display: flex; justify-content: space-between; align-items: center;
            gap: 14px; padding: 12px 0;
        }
        .ef-subtask-left { display: flex; flex-direction: column; gap: 7px; }

        .ef-input-subname {
            background: transparent; border: none; outline: none;
            font-family: inherit; font-size: .88rem; font-weight: 600;
            color: var(--text); width: 150px;
        }
        .ef-input-subname::placeholder { color: var(--placeholder); }
        .ef-input-date, .ef-input-time {
            background: transparent; border: none; outline: none;
            font-family: inherit; font-size: .82rem; color: var(--muted);
            color-scheme: dark;
        }
        .ef-input-date { width: 150px; }
        .ef-input-time { width: 100px; }

        /* priority */
        .ef-priority-col { display: flex; flex-direction: column; align-items: center; gap: 8px; }
        .ef-priority-label { font-size: .74rem; color: var(--text); font-weight: 500; }
        .ef-priority-dots { display: flex; gap: 14px; }
        .ef-priority-dots label {
            display: flex; flex-direction: column; align-items: center;
            gap: 5px; cursor: pointer;
        }
        .ef-priority-dots input[type="radio"] { display: none; }
        .epdot { width: 20px; height: 20px; border-radius: 50%; display: block; transition: all var(--ease); }
        .epdot--low    { border: 2px solid #2ecc71; }
        .epdot--medium { border: 2px solid #e67e22; }
        .epdot--high   { border: 2px solid #e74c3c; }
        .ef-priority-dots input:checked + .epdot--low    { background:#2ecc71; box-shadow:0 0 10px rgba(46,204,113,.5); }
        .ef-priority-dots input:checked + .epdot--medium { background:#e67e22; box-shadow:0 0 10px rgba(230,126,34,.5); }
        .ef-priority-dots input:checked + .epdot--high   { background:#e74c3c; box-shadow:0 0 10px rgba(231,76,60,.5); }
        .epdot-text { font-size: .68rem; color: var(--muted); }

        .ef-row-sep { border: none; border-top: 1px solid var(--divider); }

        /* submit */
        .btn-submit {
            width: 100%; background: var(--accent); border: none; border-radius: 14px;
            color: #fff; font-family: inherit; font-size: 1.05rem; font-weight: 600;
            padding: 14px; cursor: pointer;
            transition: background var(--ease), transform var(--ease);
            box-shadow: 0 8px 24px rgba(91,82,232,.3);
        }
        .btn-submit:hover  { background: var(--accent-h); }
        .btn-submit:active { transform: scale(.997); }

        .hidden { display: none !important; }
    </style>
</head>
<body>


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

<main class="task-workspace">
<div class="w">

    {{-- ════════════════════════════
         VIEW 1 : LIST TASK
    ════════════════════════════ --}}
    <div id="section-list">
        <div class="workspace-header">
            <a href="{{ route('dashboard') }}" class="btn-back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                Back
            </a>
            <h1 class="workspace-title">Task Management</h1>
            <button class="btn-add-task" id="js-btn-add">
                <span>Add Task</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </button>
        </div>

        <div class="tm-list">
            @forelse($tasks as $task)
            <div class="tm-item" id="task-{{ $task->id }}"
                 data-name="{{ $task->name }}"
                 data-s1n="{{ $task->subtask_name }}"  data-s1d="{{ $task->subtask_date }}"  data-s1t="{{ $task->subtask_time }}"  data-s1p="{{ $task->subtask_priority }}"
                 data-s2n="{{ $task->subtask2_name }}" data-s2d="{{ $task->subtask2_date }}" data-s2t="{{ $task->subtask2_time }}" data-s2p="{{ $task->subtask2_priority }}"
                 data-s3n="{{ $task->subtask3_name }}" data-s3d="{{ $task->subtask3_date }}" data-s3t="{{ $task->subtask3_time }}" data-s3p="{{ $task->subtask3_priority }}">

                <div class="tm-item__header">
                    <div class="tm-item__left">
                        <div class="tm-item__ico">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <div>
                            <div class="tm-item__name">{{ $task->name }}</div>
                            <div class="tm-item__meta">
                                {{ $task->subtask_date ? \Carbon\Carbon::parse($task->subtask_date)->format('d M') : 'No date' }}
                            </div>
                        </div>
                    </div>
                    <div class="tm-item__right">
                        <button class="tm-action tm-action--edit js-btn-edit" title="Edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"/></svg>
                        </button>
                        <button class="tm-action" title="Detail">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>
                </div>

                @php
                    $subtasks = [
                        ['name'=>$task->subtask_name,  'date'=>$task->subtask_date,  'priority'=>$task->subtask_priority],
                        ['name'=>$task->subtask2_name, 'date'=>$task->subtask2_date, 'priority'=>$task->subtask2_priority],
                        ['name'=>$task->subtask3_name, 'date'=>$task->subtask3_date, 'priority'=>$task->subtask3_priority],
                    ];
                    $hasAny = collect($subtasks)->contains(fn($s) => !empty($s['name']));
                @endphp

                @if($hasAny)
                <div class="subtask-chips">
                    @foreach($subtasks as $st)
                        @if(!empty($st['name']))
                        <div class="subtask-chip">
                            <span class="chip-dot chip-dot--{{ $st['priority'] ?? 'none' }}"></span>
                            <div>
                                <div class="chip-name">{{ $st['name'] }}</div>
                                <div class="chip-date">
                                    {{ $st['date'] ? \Carbon\Carbon::parse($st['date'])->format('d M Y') : '' }}
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>
            @empty
                <p style="color:var(--muted);text-align:center;padding:32px 0;">No tasks yet. Click <strong>Add Task</strong> to get started!</p>
            @endforelse
        </div>

        <div class="tm-footer">Sub-Tasks</div>
    </div>

    {{-- ════════════════════════════
         VIEW 2 : FORM (ADD / EDIT)
    ════════════════════════════ --}}
    <div id="section-form" class="hidden">
        <div class="workspace-header workspace-header--form">
            <button type="button" class="btn-cancel" id="js-btn-cancel">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                Back
            </button>
            <h1 class="workspace-title">Task Management</h1>
        </div>

        <form action="{{ route('tasks.store') }}" method="POST">
            @csrf
            <input type="hidden" name="task_id" id="js-task-id">

            <div class="ef-task-card">

                {{-- Task Name Header --}}
                <div class="ef-task-header">
                    <div class="ef-task-ico">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <div class="ef-task-info">
                        <div class="ef-inline-field">
                            <input type="text" name="name" id="js-input-name"
                                   class="ef-input-name" placeholder="Task Name" required>
                            <span class="ef-pencil">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"/></svg>
                            </span>
                        </div>
                        <span class="ef-sub-hint">Add up to 3 sub-tasks below</span>
                    </div>
                    <button type="button" class="ef-arrow-toggle open" id="js-subtask-toggle">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                </div>

                {{-- Sub-tasks Panel --}}
                <div class="ef-subtasks-panel" id="js-subtasks-panel">
                    <div class="ef-divider">Tasks</div>

                    {{-- Row 1 --}}
                    <div class="ef-subtask-row">
                        <div class="ef-subtask-left">
                            <div class="ef-inline-field">
                                <input type="text" name="subtask_name" id="js-s1-name" class="ef-input-subname" placeholder="Sub-task 1">
                                <span class="ef-pencil ef-pencil--sm"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"/></svg></span>
                            </div>
                            <div class="ef-inline-field">
                                <input type="date" name="subtask_date" id="js-s1-date" class="ef-input-date">
                                <span class="ef-pencil ef-pencil--sm"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"/></svg></span>
                            </div>
                            <div class="ef-inline-field">
                                <input type="time" name="subtask_time" id="js-s1-time" class="ef-input-time">
                                <span class="ef-pencil ef-pencil--sm"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"/></svg></span>
                            </div>
                        </div>
                        <div class="ef-priority-col">
                            <span class="ef-priority-label">Priority</span>
                            <div class="ef-priority-dots">
                                <label><input type="radio" name="subtask_priority" id="js-s1-plow"    value="low">   <span class="epdot epdot--low"></span>   <span class="epdot-text">Low</span></label>
                                <label><input type="radio" name="subtask_priority" id="js-s1-pmed"    value="medium" checked> <span class="epdot epdot--medium"></span> <span class="epdot-text">Medium</span></label>
                                <label><input type="radio" name="subtask_priority" id="js-s1-phigh"   value="high">  <span class="epdot epdot--high"></span>  <span class="epdot-text">High</span></label>
                            </div>
                        </div>
                    </div>

                    <hr class="ef-row-sep">

                    {{-- Row 2 --}}
                    <div class="ef-subtask-row">
                        <div class="ef-subtask-left">
                            <div class="ef-inline-field">
                                <input type="text" name="subtask2_name" id="js-s2-name" class="ef-input-subname" placeholder="Sub-task 2">
                                <span class="ef-pencil ef-pencil--sm"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"/></svg></span>
                            </div>
                            <div class="ef-inline-field">
                                <input type="date" name="subtask2_date" id="js-s2-date" class="ef-input-date">
                                <span class="ef-pencil ef-pencil--sm"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"/></svg></span>
                            </div>
                            <div class="ef-inline-field">
                                <input type="time" name="subtask2_time" id="js-s2-time" class="ef-input-time">
                                <span class="ef-pencil ef-pencil--sm"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"/></svg></span>
                            </div>
                        </div>
                        <div class="ef-priority-col">
                            <span class="ef-priority-label">Priority</span>
                            <div class="ef-priority-dots">
                                <label><input type="radio" name="subtask2_priority" id="js-s2-plow"   value="low">   <span class="epdot epdot--low"></span>   <span class="epdot-text">Low</span></label>
                                <label><input type="radio" name="subtask2_priority" id="js-s2-pmed"   value="medium" checked> <span class="epdot epdot--medium"></span> <span class="epdot-text">Medium</span></label>
                                <label><input type="radio" name="subtask2_priority" id="js-s2-phigh"  value="high">  <span class="epdot epdot--high"></span>  <span class="epdot-text">High</span></label>
                            </div>
                        </div>
                    </div>

                    <hr class="ef-row-sep">

                    {{-- Row 3 --}}
                    <div class="ef-subtask-row">
                        <div class="ef-subtask-left">
                            <div class="ef-inline-field">
                                <input type="text" name="subtask3_name" id="js-s3-name" class="ef-input-subname" placeholder="Sub-task 3">
                                <span class="ef-pencil ef-pencil--sm"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"/></svg></span>
                            </div>
                            <div class="ef-inline-field">
                                <input type="date" name="subtask3_date" id="js-s3-date" class="ef-input-date">
                                <span class="ef-pencil ef-pencil--sm"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"/></svg></span>
                            </div>
                            <div class="ef-inline-field">
                                <input type="time" name="subtask3_time" id="js-s3-time" class="ef-input-time">
                                <span class="ef-pencil ef-pencil--sm"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"/></svg></span>
                            </div>
                        </div>
                        <div class="ef-priority-col">
                            <span class="ef-priority-label">Priority</span>
                            <div class="ef-priority-dots">
                                <label><input type="radio" name="subtask3_priority" id="js-s3-plow"   value="low">   <span class="epdot epdot--low"></span>   <span class="epdot-text">Low</span></label>
                                <label><input type="radio" name="subtask3_priority" id="js-s3-pmed"   value="medium" checked> <span class="epdot epdot--medium"></span> <span class="epdot-text">Medium</span></label>
                                <label><input type="radio" name="subtask3_priority" id="js-s3-phigh"  value="high">  <span class="epdot epdot--high"></span>  <span class="epdot-text">High</span></label>
                            </div>
                        </div>
                    </div>

                </div>{{-- end ef-subtasks-panel --}}
            </div>{{-- end ef-task-card --}}

            <button type="submit" class="btn-submit" id="js-btn-submit">Save</button>
        </form>
    </div>

</div>
</main>

<script>
/*LIVE CLOCK*/
(function () {
    const elDate = document.getElementById('js-date');
    const elTime = document.getElementById('js-time');
    function tick() {
        const now = new Date();
        elDate.textContent = now.toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });
        elTime.textContent = String(now.getHours()).padStart(2,'0') + '.' + String(now.getMinutes()).padStart(2,'0');
    }
    tick(); setInterval(tick, 1000);
})();

/*VIEW SWITCHING*/
(function () {
    const listView  = document.getElementById('section-list');
    const formView  = document.getElementById('section-form');
    const btnAdd    = document.getElementById('js-btn-add');
    const btnCancel = document.getElementById('js-btn-cancel');
    const btnSubmit = document.getElementById('js-btn-submit');
    const taskIdInput = document.getElementById('js-task-id');
    const nameInput   = document.getElementById('js-input-name');

    
    btnAdd.addEventListener('click', () => {
        clearForm();
        taskIdInput.value = '';
        btnSubmit.textContent = 'Save';
        listView.classList.add('hidden');
        formView.classList.remove('hidden');
    });

    
    btnCancel.addEventListener('click', () => {
        formView.classList.add('hidden');
        listView.classList.remove('hidden');
    });

    
    document.querySelectorAll('.js-btn-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            const item = this.closest('.tm-item');
            const d    = item.dataset;

            clearForm();
            taskIdInput.value  = item.id.replace('task-', '');
            nameInput.value    = d.name || '';

            // Subtask 1
            setField('js-s1-name', d.s1n);
            setField('js-s1-date', d.s1d);
            setField('js-s1-time', d.s1t);
            setRadio('subtask_priority',  d.s1p || 'medium');

            // Subtask 2
            setField('js-s2-name', d.s2n);
            setField('js-s2-date', d.s2d);
            setField('js-s2-time', d.s2t);
            setRadio('subtask2_priority', d.s2p || 'medium');

            // Subtask 3
            setField('js-s3-name', d.s3n);
            setField('js-s3-date', d.s3d);
            setField('js-s3-time', d.s3t);
            setRadio('subtask3_priority', d.s3p || 'medium');

            btnSubmit.textContent = 'Update Task';
            listView.classList.add('hidden');
            formView.classList.remove('hidden');
        });
    });

    function setField(id, val) {
        const el = document.getElementById(id);
        if (el) el.value = val || '';
    }
    function setRadio(name, val) {
        const radio = document.querySelector(`input[name="${name}"][value="${val}"]`);
        if (radio) radio.checked = true;
    }
    function clearForm() {
        document.querySelectorAll('#section-form input[type="text"]').forEach(i => i.value = '');
        document.querySelectorAll('#section-form input[type="date"]').forEach(i => i.value = '');
        document.querySelectorAll('#section-form input[type="time"]').forEach(i => i.value = '');
        ['subtask_priority','subtask2_priority','subtask3_priority'].forEach(n => setRadio(n,'medium'));
    }
})();

/*TOGGLE SUBTASK PANEL*/
(function () {
    const toggle = document.getElementById('js-subtask-toggle');
    const panel  = document.getElementById('js-subtasks-panel');
    toggle.addEventListener('click', () => {
        const isOpen = !panel.classList.contains('hidden');
        panel.classList.toggle('hidden', isOpen);
        toggle.classList.toggle('open', !isOpen);
    });
})();
</script>
</body>
</html>