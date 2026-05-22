<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Zentra – Task Management</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        /* Container pembungkus subtask */
.subtask-container {
    margin-top: 10px;
}

/* Wujud 1: Tombol Placeholder sesuai screenshot */
.subtask-placeholder {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--bg-input);
    border: 1px solid var(--border-input);
    border-radius: 12px;
    padding: 10px 16px;
    width: 240px;
    color: var(--text-muted);
    font-family: inherit;
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: border-color var(--ease), color var(--ease);
}

.subtask-placeholder:hover {
    border-color: rgba(91, 82, 232, 0.4);
    color: var(--text-primary);
}

.subtask-placeholder svg {
    width: 20px;
    height: 20px;
    color: var(--text-muted);
}

.subtask-placeholder:hover svg {
    color: var(--text-primary);
}
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
            --shadow-widget: 0 8px 32px rgba(0,0,0,.4);
            --r-card:        20px;
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

        .topbar__brand { display: flex; align-items: center; gap: 14px; }
        .topbar__logo { width: 42px; height: 42px; object-fit: contain; }
        .topbar__name { font-size: .95rem; font-weight: 700; letter-spacing: .32em; color: var(--text-primary); }
        .topbar__right { display: flex; align-items: center; gap: 24px; }
        .topbar__clock { text-align: right; line-height: 1.25; }
        .topbar__date { display: block; font-size: .88rem; font-weight: 500; color: var(--text-primary); }
        .topbar__time { display: block; font-size: 1.45rem; font-weight: 700; color: var(--text-primary); }

        /* Workspace Wrapper */
        .task-workspace {
            padding: 28px 36px 52px;
            max-width: 1000px;
            margin: 0 auto;
            animation: fadeUp .5s cubic-bezier(.4,0,.2,1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .w {
            background: var(--bg-card);
            border: 1px solid rgba(255,255,255,.05);
            border-radius: var(--r-card);
            padding: 28px;
            box-shadow: var(--shadow-widget);
        }

        /* Workspace Header */
        .workspace-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--divider);
            padding-bottom: 16px;
        }

        /* Header khusus Form (Judul di tengah) */
        .workspace-header--form {
            justify-content: center;
            position: relative;
        }

        .btn-back, .btn-cancel-form {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: .9rem;
            font-weight: 500;
            background: none;
            border: none;
            cursor: pointer;
            transition: color var(--ease);
        }
        .btn-cancel-form { position: absolute; left: 0; }
        .btn-back:hover, .btn-cancel-form:hover { color: var(--text-primary); }
        .btn-back svg, .btn-cancel-form svg { width: 18px; height: 18px; }

        .workspace-title { font-size: 1.4rem; font-weight: 700; letter-spacing: -.01em; }

        .btn-add-task {
            display: flex;
            align-items: center;
            gap: 6px;
            background: none;
            border: none;
            color: var(--text-muted);
            font-family: inherit;
            font-size: .9rem;
            font-weight: 500;
            cursor: pointer;
            transition: color var(--ease);
        }
        .btn-add-task:hover { color: var(--accent-link); }
        .btn-add-task svg { width: 18px; height: 18px; }

        /* --- TAMPILAN VIEW 1: LIST TASK --- */
        .tm-list { display: flex; flex-direction: column; gap: 12px; }
        .tm-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--bg-input);
            border: 1px solid var(--border-input);
            border-radius: 14px;
            padding: 16px 20px;
            transition: border-color var(--ease), background var(--ease), transform var(--ease);
        }
        .tm-item:hover {
            background: rgba(91,82,232,.06);
            border-color: rgba(91,82,232,.35);
            transform: translateX(2px);
        }
        .tm-item__left { display: flex; align-items: center; gap: 16px; }
        .tm-item__ico {
            width: 42px; height: 42px; flex-shrink: 0; background: var(--bg-card);
            border: 1px solid var(--border-input); border-radius: 10px;
            display: flex; align-items: center; justify-content: center; color: var(--text-muted);
            transition: border-color var(--ease), color var(--ease);
        }
        .tm-item:hover .tm-item__ico { border-color: rgba(91,82,232,.5); color: var(--accent); }
        .tm-item__ico svg { width: 20px; height: 20px; }
        .tm-item__body { display: flex; flex-direction: column; gap: 2px; }
        .tm-item__name { font-size: .92rem; font-weight: 600; color: var(--text-primary); }
        .tm-item__sub { font-size: .78rem; color: var(--text-muted); }
        .tm-item__date { font-size: .74rem; color: rgba(136, 132, 168, 0.6); margin-top: 1px; }
        .tm-item__right { display: flex; align-items: center; gap: 18px; }
        .tm-action { background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px; display: flex; align-items: center; justify-content: center; transition: color var(--ease); }
        .tm-action:hover { color: var(--text-primary); }
        .tm-action--edit:hover { color: var(--accent-link); }
        .tm-action svg { width: 18px; height: 18px; }
        .tm-action--arrow svg { width: 22px; height: 22px; }
        .tm-footer { margin-top: 24px; font-size: .78rem; color: var(--placeholder); font-weight: 500; letter-spacing: .05em; }

        /* --- TAMPILAN VIEW 2: FORM INPUT --- */
        .form-card { border: 1px solid var(--border-input); border-radius: 14px; padding: 24px; margin-bottom: 24px; }
        .form-card__row { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; }
        .input-task-name-wrapper { display: flex; align-items: center; gap: 8px; flex: 1; }
        .input-task-name {
            background: transparent; border: none; outline: none; font-family: inherit;
            font-size: .95rem; font-weight: 600; color: var(--text-primary); width: 70px;
        }
        .ico-pencil { color: var(--text-muted); display: flex; align-items: center; }
        .ico-pencil svg { width: 14px; height: 14px; }

        .form-divider { display: flex; align-items: center; text-align: center; color: var(--placeholder); font-size: 0.72rem; font-weight: 500; letter-spacing: 0.05em; margin-bottom: 20px; }
        .form-divider::before, .form-divider::after { content: ''; flex: 1; border-bottom: 1px solid var(--border-input); }
        .form-divider:not(:empty)::before { margin-right: 12px; }
        .form-divider:not(:empty)::after { margin-left: 12px; }

        .subtask-box {
            display: flex; justify-content: space-between; align-items: center; background: var(--bg-input);
            border: 1px solid var(--border-input); border-radius: 12px; padding: 10px 16px; width: 240px; transition: border-color var(--ease);
        }
        .subtask-box:focus-within { border-color: var(--border-focus); }
        .input-subtask { background: transparent; border: none; outline: none; font-family: inherit; font-size: 0.85rem; color: var(--text-primary); width: 80%; }
        .input-subtask::placeholder { color: var(--placeholder); }
        .btn-add-subtask { background: none; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; transition: color var(--ease); }
        .btn-add-subtask svg { width: 22px; height: 22px; }

        .btn-submit-task {
            width: 100%; background: var(--accent); border: none; border-radius: 14px; color: #ffffff;
            font-family: inherit; font-size: 1.05rem; font-weight: 600; padding: 14px; cursor: pointer;
            transition: background var(--ease), transform var(--ease); text-align: center; box-shadow: 0 8px 24px rgba(91, 82, 232, 0.25);
        }
        .btn-submit-task:hover { background: var(--accent-h); }
        .btn-submit-task:active { transform: scale(0.995); }

        /* KUNCI UTAMA: Class pembantu untuk menyembunyikan halaman */
        .hidden { display: none !important; }
    </style>
</head>
<body>

{{-- Top Bar --}}
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
    </div>
</header>

<main class="task-workspace">
    <div class="w">

        <div id="section-view-list">
            <div class="workspace-header">
                <a href="{{ route('dashboard') }}" class="btn-back">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    Back
                </a>
                <h1 class="workspace-title">Task Management</h1>
                <button class="btn-add-task" id="js-btn-trigger-add">
                    <span>Add Task</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </button>
            </div>

            <div class="tm-list">
                <div class="tm-item">
                    <div class="tm-item__left">
                        <div class="tm-item__ico">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        </div>
                        <div class="tm-item__body">
                            <span class="tm-item__name">Learning Mathematics</span>
                            <span class="tm-item__sub">Starts in 13 Hours</span>
                            <span class="tm-item__date">15 Apr</span>
                        </div>
                    </div>
                    <div class="tm-item__right">
                        <button class="tm-action tm-action--edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"></path></svg></button>
                        <button class="tm-action tm-action--arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg></button>
                    </div>
                </div>
            </div>
            <div class="tm-footer">Sub-Tasks</div>
        </div>

        <div id="section-view-form" class="hidden">
            <div class="workspace-header workspace-header--form">
                <button type="button" class="btn-cancel-form" id="js-btn-cancel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    Back
                </button>
                <h1 class="workspace-title">Task Management</h1>
            </div>

            <form action="#" method="POST">
                @csrf
                <div class="form-card">
                    <div class="form-card__row">
                        <div class="form-card__ico">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        </div>
                        <div class="input-task-name-wrapper">
                            <input type="text" class="input-task-name" placeholder="Name">
                            <span class="ico-pencil"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"></path></svg></span>
                        </div>
                    </div>

                    <div class="form-divider">Tasks</div>

                   <div class="subtask-container">

    <button type="button" class="subtask-placeholder" id="js-subtask-placeholder">
        <span>Add Sub-Task</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
    </button>

    <div class="subtask-box hidden" id="js-subtask-real-input">
        <input type="text" class="input-subtask" id="js-input-subtask-field" placeholder="Type sub-task name...">
        <button type="button" class="btn-add-subtask" id="js-btn-submit-subtask">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
        </button>
    </div>

</div>
                </div>
                <button type="submit" class="btn-submit-task">Add</button>
            </form>
        </div>

    </div>
</main>

<script>
/* 1. Live Clock */
(function () {
    const elDate = document.getElementById('js-date');
    const elTime = document.getElementById('js-time');
    function tick() {
        const now = new Date();
        elDate.textContent = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        elTime.textContent = String(now.getHours()).padStart(2, '0') + '.' + String(now.getMinutes()).padStart(2, '0');
    }
    tick(); setInterval(tick, 1000);
})();

/* 2. SAKELAR INTERAKTIF (LOGIKA PINDAH HALAMAN) */
(function () {
    const btnTriggerAdd = document.getElementById('js-btn-trigger-add');
    const btnCancel = document.getElementById('js-btn-cancel');
    const sectionList = document.getElementById('section-view-list');
    const sectionForm = document.getElementById('section-view-form');

    // Jika tombol "Add Task (+)" diklik -> Sembunyikan list, munculkan form input
    btnTriggerAdd.addEventListener('click', function () {
        sectionList.classList.add('hidden');
        sectionForm.classList.remove('hidden');
    });

    // Jika tombol "Back" di dalam form diklik -> Sembunyikan form, balikkan ke list utama
    btnCancel.addEventListener('click', function () {
        sectionForm.classList.add('hidden');
        sectionList.classList.remove('hidden');
    });
})();

/* 3. Auto-stretch lebar input nama task */
const nameInput = document.querySelector('.input-task-name');
if(nameInput) {
    nameInput.addEventListener('input', function() {
        this.style.width = this.value.length > 0 ? (this.value.length + 1) + 'ch' : '70px';
    });
}
</script>
</body>
</html>
