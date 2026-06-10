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
        .btn-remove-subtask {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 50%;
            width: 34px;
            height: 34px;
            color: var(--muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            transition: background var(--ease), color var(--ease);
        }
        .btn-remove-subtask:hover {
            background: rgba(255,255,255,.12);
            color: var(--text);
        }

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

    {{-- VIEW 1 : LIST TASK --}}
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
                 data-subtasks="{{ e(json_encode($task->formattedSubtasks(), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP)) }}">

                <div class="tm-item__header">
                    <div class="tm-item__left">
                        <div class="tm-item__ico">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <div>
                            <div class="tm-item__name">{{ $task->name }}</div>
                            <div class="tm-item__meta">
                            @php 
                            $taskMeta = collect($task->formattedSubtasks())->first(); 
                            @endphp
                            {{ !empty($taskMeta['date']) ? \Carbon\Carbon::parse($taskMeta['date'])->format('d M') : 'No date' }}
                        </div>
                        </div>
                    </div>
                    <div class="tm-item__right">
                        <button type="button" class="tm-action tm-action--edit js-btn-edit" title="Edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"/></svg>
                        </button>
                        <button type="button" class="tm-action tm-action--delete js-btn-delete" title="Delete">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6v14a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V6"/><path d="M10 10v6"/><path d="M14 10v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                        </button>
                        <button type="button" class="tm-action" title="Detail">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>
                </div>

                @php
                    $subtasks = collect($task->formattedSubtasks());
                @endphp
                <script type="application/json" class="task-subtasks">
                    {!! json_encode($subtasks->all(), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) !!}
                </script>

                @if($subtasks->isNotEmpty())
                <div class="subtask-chips">
                    @foreach($subtasks as $st)
                        <div class="subtask-chip">
                            <span class="chip-dot chip-dot--{{ $st['priority'] ?? 'none' }}"></span>
                            <div>
                                <div class="chip-name">{{ $st['name'] }}</div>
                                <div class="chip-date">
                                    {{ $st['date'] ? \Carbon\Carbon::parse($st['date'])->format('d M Y') : '' }}
                                </div>
                            </div>
                        </div>
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

    <form id="js-delete-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    {{-- VIEW 2 : FORM (ADD / EDIT) --}}
    <div id="section-form" class="hidden">
        <div class="workspace-header workspace-header--form">
            <button type="button" class="btn-cancel" id="js-btn-cancel">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                Back
            </button>
            <h1 class="workspace-title">Task Management</h1>
        </div>

        <form action="{{ route('tasks.store') }}" method="POST" id="js-task-form">
            @csrf
            <input type="hidden" name="_method" id="js-method-input" value="POST">
            <input type="hidden" name="task_id" id="js-task-id">

            <div class="ef-task-card">
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
                        <span class="ef-sub-hint">Add as many sub-tasks as you need</span>
                    </div>
                    <button type="button" class="ef-arrow-toggle open" id="js-subtask-toggle">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                </div>

                <div class="ef-subtasks-panel" id="js-subtasks-panel">
                    <div class="ef-divider">Tasks</div>
                    <div id="js-subtasks-list"></div>
                    <button type="button" class="btn-submit" id="js-btn-add-subtask" style="margin-top: 16px; width: auto;">+ Add Sub-task</button>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="js-btn-submit">Save</button>
        </form>
    </div>

</div>
</main>

<script>
/* LIVE CLOCK */
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

// Taruh fungsi global ini di luar agar bisa di-call ulang saat render AJAX dari luar file
window.initDynamicTaskEvents = function() {
    document.querySelectorAll('.js-btn-edit').forEach(editBtn => {
        editBtn.removeEventListener('click', window.handleEditClick);
        editBtn.addEventListener('click', window.handleEditClick);
    });

    document.querySelectorAll('.js-btn-delete').forEach(deleteBtn => {
        deleteBtn.removeEventListener('click', window.handleDeleteClick);
        deleteBtn.addEventListener('click', window.handleDeleteClick);
    });
};

window.handleEditClick = function() {
    window.openTaskEdit(this);
};

window.handleDeleteClick = function() {
    const item = this.closest('.tm-item');
    const taskId = item.id.replace('task-', '');
    if (!confirm('Delete this task and all sub-tasks?')) {
        return;
    }
    const deleteForm = document.getElementById('js-delete-form');
    if (deleteForm) {
        deleteForm.action = `${window.deleteBaseUrl}/${taskId}`;
        deleteForm.submit();
    }
};

/* VIEW SWITCHING & LOGIC */
(function () {
    const listView  = document.getElementById('section-list');
    const formView  = document.getElementById('section-form');
    const btnAdd    = document.getElementById('js-btn-add');
    const btnCancel = document.getElementById('js-btn-cancel');
    const btnSubmit = document.getElementById('js-btn-submit');
    const taskIdInput = document.getElementById('js-task-id');
    const methodInput = document.getElementById('js-method-input');
    const formElement = document.getElementById('js-task-form');
    const nameInput   = document.getElementById('js-input-name');

    const subtasksList = document.getElementById('js-subtasks-list');
    const btnAddSubtask = document.getElementById('js-btn-add-subtask');

    window.deleteBaseUrl = '{{ url('/tasks') }}';
    const createTaskUrl = '{{ route('tasks.store') }}';
    const updateTaskBaseUrl = '{{ url('/tasks') }}';

    btnAdd.addEventListener('click', () => {
        clearForm();
        addSubtaskRow();
        taskIdInput.value = '';
        if (methodInput) methodInput.value = 'POST';
        if (formElement) formElement.action = createTaskUrl;
        btnSubmit.textContent = 'Save';
        listView.classList.add('hidden');
        formView.classList.remove('hidden');
        const panel = document.getElementById('js-subtasks-panel');
        if (panel) panel.classList.remove('hidden');
    });

    btnCancel.addEventListener('click', () => {
        formView.classList.add('hidden');
        listView.classList.remove('hidden');
    });

    window.openTaskEdit = function (button) {
        const item = button.closest('.tm-item');
        if (!item) return;

        const taskId = item.id.replace('task-', '');
        const subtasksScript = item.querySelector('script.task-subtasks');
        let subtasks = [];

        if (subtasksScript) {
            try {
                subtasks = JSON.parse(subtasksScript.textContent || '[]');
            } catch (error) {
                subtasks = [];
            }
        }

        clearForm();
        taskIdInput.value = taskId;
        if (methodInput) methodInput.value = 'PUT';
        if (formElement) formElement.action = `${updateTaskBaseUrl}/${taskId}`;
        nameInput.value = item.dataset.name || '';

        subtasks.forEach(sub => addSubtaskRow(sub));
        if (subtasks.length === 0) {
            addSubtaskRow();
        }

        btnSubmit.textContent = 'Update Task';
        listView.classList.add('hidden');
        formView.classList.remove('hidden');
    };

    btnAddSubtask.addEventListener('click', () => {
    // 1. Tambahkan baris subtask baru
    addSubtaskRow();
    
    // 2. PASTIKAN PANEL TIDAK TERSEMBUNYI
    const panel = document.getElementById('js-subtasks-panel');
    const toggle = document.getElementById('js-subtask-toggle');
    if (panel && toggle) {
        panel.classList.remove('hidden');
        toggle.classList.add('open');
    }
});

    function createSubtaskRow(data = {}, index = 0) {
    const wrapper = document.createElement('div');
    wrapper.className = 'ef-subtask-row';

    const nameValue = data.name || '';
    const dateValue = data.date || '';
    const timeValue = data.time || '';
    const priorityValue = data.priority || 'medium';
    const doneValue = data.done ? '1' : '0';

    wrapper.innerHTML = `
        <div class="ef-subtask-left">
            <div class="ef-inline-field" style="width:100%;">
                <input type="text" class="ef-input-subname js-subtask-name" name="subtasks[${index}][name]" placeholder="Sub-task" value="${escapeHtml(nameValue)}">
                <button type="button" class="btn-remove-subtask" title="Remove subtask">×</button>
            </div>
            <div class="ef-inline-field">
                <input type="date" class="ef-input-date js-subtask-date" name="subtasks[${index}][date]" value="${escapeHtml(dateValue)}">
            </div>
            <div class="ef-inline-field">
                <input type="time" class="ef-input-time js-subtask-time" name="subtasks[${index}][time]" value="${escapeHtml(timeValue)}">
            </div>
        </div>
        <div class="ef-priority-col">
            <span class="ef-priority-label">Priority</span>
            <div class="ef-priority-dots">
                <label><input type="radio" class="js-subtask-priority" name="subtasks[${index}][priority]" value="low" ${priorityValue === 'low' ? 'checked' : ''}>   <span class="epdot epdot--low"></span>   <span class="epdot-text">Low</span></label>
                <label><input type="radio" class="js-subtask-priority" name="subtasks[${index}][priority]" value="medium" ${priorityValue === 'medium' ? 'checked' : ''}> <span class="epdot epdot--medium"></span> <span class="epdot-text">Medium</span></label>
                <label><input type="radio" class="js-subtask-priority" name="subtasks[${index}][priority]" value="high" ${priorityValue === 'high' ? 'checked' : ''}>  <span class="epdot epdot--high"></span>  <span class="epdot-text">High</span></label>
            </div>
        </div>
        <input type="hidden" class="js-subtask-done" name="subtasks[${index}][done]" value="${doneValue}">
    `;

    wrapper.querySelector('.btn-remove-subtask').addEventListener('click', () => {
        wrapper.remove();
        refreshSubtaskRowNames();
    });

    return wrapper;
}

    function addSubtaskRow(data = {}) {
    const currentIndex = subtasksList.querySelectorAll('.ef-subtask-row').length;
    const row = createSubtaskRow(data, currentIndex);
    subtasksList.appendChild(row);
    refreshSubtaskRowNames();
}

function refreshSubtaskRowNames() {
    subtasksList.querySelectorAll('.ef-subtask-row').forEach((row, index) => {
        const nameInput = row.querySelector('.js-subtask-name');
        const dateInput = row.querySelector('.js-subtask-date');
        const timeInput = row.querySelector('.js-subtask-time');
        const priorityInputs = row.querySelectorAll('.js-subtask-priority');
        const doneInput = row.querySelector('.js-subtask-done');

        if (nameInput) nameInput.name = `subtasks[${index}][name]`;
        if (dateInput) dateInput.name = `subtasks[${index}][date]`;
        if (timeInput) timeInput.name = `subtasks[${index}][time]`;
        
        priorityInputs.forEach(input => {
            input.name = `subtasks[${index}][priority]`;
        });
        if (doneInput) doneInput.name = `subtasks[${index}][done]`;
    });
}
    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function clearForm() {
        document.querySelectorAll('#section-form input[type="text"]').forEach(i => i.value = '');
        document.querySelectorAll('#section-form input[type="date"]').forEach(i => i.value = '');
        document.querySelectorAll('#section-form input[type="time"]').forEach(i => i.value = '');
        if (subtasksList) {
            subtasksList.innerHTML = '';
        }
    }

    // Pasang event awal pas dom load
    document.addEventListener('DOMContentLoaded', () => {
        window.initDynamicTaskEvents();
    });
})();

/* TOGGLE SUBTASK PANEL */
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