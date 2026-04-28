<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Zentra – Sign In</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg-outer:       #1a0f2e;
            --bg-card:        #141228;
            --bg-input:       #1c1a35;
            --border-input:   #2b2850;
            --border-focus:   #5b52e8;
            --text-primary:   #ffffff;
            --text-muted:     #8884a8;
            --placeholder:    #4e4a6a;
            --accent-btn:     #5b52e8;
            --accent-btn-h:   #4a43d4;
            --accent-link:    #2ec4ff;
            --divider:        #2b2850;
            --shadow-card:    0 32px 80px rgba(0,0,0,.55);
            --shadow-btn:     0 8px 28px rgba(91,82,232,.45);
            --radius-card:    28px;
            --radius-input:   12px;
            --radius-btn:     14px;
            --transition:     .22s cubic-bezier(.4,0,.2,1);
        }

        html, body {
            min-height: 100vh;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background: radial-gradient(ellipse 80% 60% at 15% 10%, #2d1b5e 0%, transparent 60%),
                        radial-gradient(ellipse 60% 50% at 85% 80%, #1b0e44 0%, transparent 60%),
                        var(--bg-outer);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* ── Card ────────────────────────────────────── */
        .card {
            background: var(--bg-card);
            border-radius: var(--radius-card);
            padding: 52px 48px 48px;
            width: 100%;
            max-width: 520px;
            box-shadow: var(--shadow-card);
            border: 1px solid rgba(255,255,255,.04);
            animation: fadeUp .5s cubic-bezier(.4,0,.2,1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0);    }
        }

        /* ── Logo ────────────────────────────────────── */
        .logo-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-bottom: 36px;
        }

        .logo-icon {
            width: 150px;
            height: 150px;
            object-fit: contain;
        }

        .logo-wordmark {
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .35em;
            color: var(--text-primary);
            text-transform: uppercase;
        }

        /* ── Heading ─────────────────────────────────── */
        .heading { text-align: center; margin-bottom: 32px; }

        .heading h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -.01em;
            line-height: 1.2;
        }

        .heading p {
            margin-top: 8px;
            font-size: .95rem;
            color: var(--text-muted);
            font-weight: 400;
        }

        /* ── Alert (Laravel session errors) ─────────── */
        .alert-error {
            background: rgba(239,68,68,.12);
            border: 1px solid rgba(239,68,68,.3);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #f87171;
            font-size: .875rem;
            line-height: 1.5;
        }

        .alert-error ul { padding-left: 18px; }

        /* ── Form ────────────────────────────────────── */
        .form { display: flex; flex-direction: column; gap: 16px; }

        .field { display: flex; flex-direction: column; gap: 6px; }

        .field label {
            font-size: .82rem;
            font-weight: 500;
            color: var(--text-muted);
            letter-spacing: .03em;
            display: none; /* hidden — placeholder acts as label */
        }

        .field input {
            background: var(--bg-input);
            border: 1.5px solid var(--border-input);
            border-radius: var(--radius-input);
            padding: 18px 20px;
            font-family: inherit;
            font-size: .95rem;
            font-weight: 400;
            color: var(--text-primary);
            outline: none;
            transition: border-color var(--transition), box-shadow var(--transition);
        }

        .field input::placeholder { color: var(--placeholder); }

        .field input:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(91,82,232,.18);
        }

        .field input.is-invalid {
            border-color: rgba(239,68,68,.6);
        }

        .field-error {
            font-size: .8rem;
            color: #f87171;
            margin-top: 2px;
        }

        /* ── Forgot link (optional) ──────────────────── */
        .forgot-row {
            display: flex;
            justify-content: flex-end;
            margin-top: -4px;
        }

        .forgot-link {
            font-size: .83rem;
            color: var(--accent-link);
            text-decoration: none;
            font-weight: 500;
            transition: opacity var(--transition);
        }

        .forgot-link:hover { opacity: .75; }

        /* ── Button ──────────────────────────────────── */
        .btn-login {
            margin-top: 8px;
            width: 100%;
            padding: 18px;
            background: var(--accent-btn);
            color: #fff;
            border: none;
            border-radius: var(--radius-btn);
            font-family: inherit;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: .02em;
            cursor: pointer;
            box-shadow: var(--shadow-btn);
            transition: background var(--transition), transform var(--transition), box-shadow var(--transition);
        }

        .btn-login:hover {
            background: var(--accent-btn-h);
            transform: translateY(-1px);
            box-shadow: 0 12px 36px rgba(91,82,232,.55);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* ── Divider ─────────────────────────────────── */
        .divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 20px 0 16px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--divider);
        }

        .divider span {
            font-size: .85rem;
            color: var(--text-muted);
            font-weight: 400;
        }

        /* ── Register link ───────────────────────────── */
        .register-row {
            text-align: center;
            font-size: .9rem;
            color: var(--text-muted);
        }

        .register-row a {
            color: var(--accent-link);
            font-weight: 600;
            text-decoration: none;
            transition: opacity var(--transition);
        }

        .register-row a:hover { opacity: .75; }

        /* ── Responsive ──────────────────────────────── */
        @media (max-width: 480px) {
            .card { padding: 40px 28px 36px; }
            .heading h1 { font-size: 1.65rem; }
        }
    </style>
</head>
<body>

<div class="card">

    {{-- Logo --}}
    <div class="logo-wrap">
        <img src="{{ asset('images/zentralogo.png') }}" alt="Zentra Logo" class="logo-icon">

    </div>

    {{-- Heading --}}
    <div class="heading">
        <h1>Welcome back</h1>
        <p>Please enter your details to sign in.</p>
    </div>

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Login Form --}}
    <form class="form" method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="field">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="Email"
                value="{{ old('email') }}"
                autocomplete="email"
                autofocus
                class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                required
            >
            @error('email')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Password --}}
        <div class="field">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Password"
                autocomplete="current-password"
                class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                required
            >
            @error('password')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Forgot Password (opsional — hapus jika tidak dipakai) --}}
        {{-- 
        <div class="forgot-row">
            <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
        </div>
        --}}

        {{-- Remember Me (hidden, opsional) --}}
        <input type="hidden" name="remember" value="1">

        {{-- Submit --}}
        <button type="submit" class="btn-login">Log In</button>
    </form>

    {{-- Divider --}}
    <div class="divider"><span>Or</span></div>

    {{-- Register --}}
    <p class="register-row">
        Don't have an account?&nbsp;
        <a href="{{ route('register') }}">Create Account</a>
    </p>

</div>

</body>
</html>