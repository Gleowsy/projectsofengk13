<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Zentra – Insight</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-outer:     #0f0d1e;
            --bg-nav:       #13112299;
            --bg-card:      #161430;
            --bg-widget:    #1c1a38;
            --bg-widget-h:  #201e42;
            --border:       #2a2750;
            --text-primary: #ffffff;
            --text-muted:   #7e7aaa;
            --text-dim:     #4e4a70;
            --accent:       #6c63ff;
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
            width: 100%; max-width: 1080px;
            animation: fadeUp .45s cubic-bezier(.4,0,.2,1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Card header ──────────────────────── */
        .card-header {
            display: flex; align-items: center;
            margin-bottom: 32px; position: relative;
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

        /* ── Charts grid ──────────────────────── */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 700px) {
            .charts-grid { grid-template-columns: 1fr; }
            .card { padding: 24px 18px 28px; }
        }

        .chart-widget {
            background: var(--bg-widget);
            border: 1px solid var(--border);
            border-radius: var(--r-widget);
            padding: 22px 22px 18px;
        }

        .chart-title {
            font-size: .95rem; font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 18px;
        }

        .chart-wrap {
            position: relative;
            height: 220px;
        }

        /* ── Summary bar ──────────────────────── */
        .summary-bar {
            background: var(--bg-widget);
            border: 1px solid var(--border);
            border-radius: var(--r-widget);
            padding: 24px 32px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
        }

        .summary-item {
            text-align: center;
            position: relative;
        }

        .summary-item:not(:last-child)::after {
            content: '';
            position: absolute; right: 0; top: 10%; height: 80%;
            width: 1px; background: var(--border);
        }

        .summary-label {
            font-size: .9rem; font-weight: 700;
            color: var(--text-primary); margin-bottom: 8px;
        }

        .summary-value {
            font-size: 1rem; font-weight: 400;
            color: var(--text-muted);
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
            <h1 class="card-title">Insight</h1>
        </div>

        {{-- Charts --}}
        @php
            $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thr', 'Fri', 'Sat'];

            // Energy data from DailyCheckin — fallback to sample data
            $energyData    = $energyData    ?? [1, 3, 2, 3, 4, 3, 4];
            $screenData    = $screenData    ?? [4, 3, 3, 4, 3, 4, 2];

            // Summary
            $mostProductive = $mostProductive ?? 'Thursday';
            $mostFocused    = $mostFocused    ?? 'Saturday';
            $mostEnergetic  = $mostEnergetic  ?? 'Saturday';
        @endphp

        <div class="charts-grid">

            {{-- Energy Level --}}
            <div class="chart-widget">
                <div class="chart-title">This Week's Energy Level</div>
                <div class="chart-wrap">
                    <canvas id="energyChart"></canvas>
                </div>
            </div>

            {{-- Screen Time / Productivity --}}
            <div class="chart-widget">
                <div class="chart-title">This Week's Screen Time</div>
                <div class="chart-wrap">
                    <canvas id="screenChart"></canvas>
                </div>
            </div>

        </div>

        {{-- Summary --}}
        <div class="summary-bar">
            <div class="summary-item">
                <div class="summary-label">Most Productive</div>
                <div class="summary-value">{{ $mostProductive }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Most Focused</div>
                <div class="summary-value">{{ $mostFocused }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Most Energetic</div>
                <div class="summary-value">{{ $mostEnergetic }}</div>
            </div>
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
updateClock(); setInterval(updateClock, 1000);

// ── Chart defaults ───────────────────────────────────
const days        = @json($days);
const energyData  = @json($energyData);
const screenData  = @json($focusData);

const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: '#1c1a38',
            borderColor: '#2a2750',
            borderWidth: 1,
            titleColor: '#ffffff',
            bodyColor: '#7e7aaa',
            padding: 10,
        }
    },
    scales: {
        x: {
            grid: { color: 'rgba(42,39,80,.5)', drawBorder: false },
            ticks: { color: '#7e7aaa', font: { family: 'Outfit', size: 12 } },
            border: { display: false },
        },
        y: {
            grid: { color: 'rgba(42,39,80,.5)', drawBorder: false },
            ticks: { color: '#7e7aaa', font: { family: 'Outfit', size: 11 }, maxTicksLimit: 5 },
            border: { display: false },
            min: 0, max: 5,
        }
    }
};

function makeDataset(data, label) {
    return {
        label,
        data,
        borderColor: 'rgba(255,255,255,.7)',
        borderWidth: 1.8,
        pointBackgroundColor: 'rgba(255,255,255,.0)',
        pointBorderColor: 'rgba(255,255,255,.0)',
        pointHoverBackgroundColor: '#ffffff',
        pointHoverRadius: 5,
        tension: 0.3,
        fill: false,
    };
}

// ── Energy chart ─────────────────────────────────────
new Chart(document.getElementById('energyChart'), {
    type: 'line',
    data: {
        labels: days,
        datasets: [makeDataset(energyData, 'Energy')]
    },
    options: {
        ...chartDefaults,
        scales: {
            ...chartDefaults.scales,
            x: { ...chartDefaults.scales.x,
                title: { display: true, text: 'Date', color: '#7e7aaa', font: { family: 'Outfit', size: 11 } }
            },
            y: { ...chartDefaults.scales.y,
                title: { display: true, text: 'Energy', color: '#7e7aaa', font: { family: 'Outfit', size: 11 } }
            }
        }
    }
});

// ── Screen time chart ────────────────────────────────
new Chart(document.getElementById('screenChart'), {
    type: 'line',
    data: {
        labels: days,
        datasets: [makeDataset(screenData, 'Productivity')]
    },
    options: {
        ...chartDefaults,
        scales: {
            ...chartDefaults.scales,
            x: { ...chartDefaults.scales.x,
                title: { display: true, text: 'Date', color: '#7e7aaa', font: { family: 'Outfit', size: 11 } }
            },
            y: { ...chartDefaults.scales.y,
                title: { display: true, text: 'Productivity', color: '#7e7aaa', font: { family: 'Outfit', size: 11 } }
            }
        }
    }
});
</script>

</body>
</html>