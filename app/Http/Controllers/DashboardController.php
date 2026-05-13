<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'calendar'         => $this->getCalendar(),
            'upcomingTasks'    => $this->getUpcomingTasks(),
            'weeklyStats'      => $this->getWeeklyStats(),
            'goals'            => $this->getGoals(),
            'currentCondition' => $this->getCurrentCondition(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────

    private function getCalendar(): array
    {
        $now   = Carbon::now();
        $first = Carbon::create($now->year, $now->month, 1);

        $days = [];

        // Padding hari bulan lalu
        for ($i = $first->dayOfWeek - 1; $i >= 0; $i--) {
            $days[] = [
                'day'     => $first->copy()->subDays($i + 1)->day,
                'current' => false,
                'today'   => false,
            ];
        }

        // Hari bulan ini
        for ($d = 1; $d <= $now->daysInMonth; $d++) {
            $days[] = [
                'day'     => $d,
                'current' => true,
                'today'   => $d === $now->day,
            ];
        }

        // Padding hari bulan depan
        $remaining = 42 - count($days);
        for ($d = 1; $d <= $remaining; $d++) {
            $days[] = [
                'day'     => $d,
                'current' => false,
                'today'   => false,
            ];
        }

        return [
            'monthLabel' => $now->format('F Y'),
            'year'       => $now->year,
            'month'      => $now->month,
            'today'      => $now->day,
            'days'       => $days,
        ];
    }

    private function getUpcomingTasks(): array
    {
        // Nanti ganti dengan query Eloquent:
        // return Task::where('user_id', auth()->id())
        //     ->where('due_date', '>=', now())
        //     ->orderBy('due_date')
        //     ->limit(5)
        //     ->get()
        //     ->map(fn($t) => [
        //         'title'    => $t->title,
        //         'subtitle' => 'Starts in ' . now()->diffForHumans($t->due_date, true),
        //         'due_date' => Carbon::parse($t->due_date)->format('d M'),
        //     ])->toArray();

        return [
            [
                'title'    => 'Learning Mathematics',
                'subtitle' => 'Starts in 13 Hours',
                'due_date' => Carbon::now()->addDay()->format('d M'),
            ],
            [
                'title'    => 'Team Stand-up',
                'subtitle' => 'Starts in 2 Hours',
                'due_date' => Carbon::now()->format('d M'),
            ],
        ];
    }

    private function getWeeklyStats(): array
    {
        // Nanti ganti dengan query aggregasi dari DB:
        // $stats = DailyCheckin::where('user_id', auth()->id())
        //     ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
        //     ->orderBy('date')
        //     ->pluck('energy_level', 'date');

        return [
            'labels' => ['Sun', 'Mon', 'Tue', 'Wed', 'Thr', 'Fri', 'Sat'],
            'energy' => [30, 55, 45, 70, 60, 80, 65],
        ];
    }

    private function getGoals(): array
    {
        // Nanti ganti dengan:
        // return Goal::where('user_id', auth()->id())
        //     ->get()
        //     ->map(fn($g) => ['type' => $g->type, 'text' => $g->text])
        //     ->toArray();

        return [
            ['type' => 'Daily',  'text' => 'Do something with my life'],
            ['type' => 'Weekly', 'text' => 'Get a Girlfriend'],
        ];
    }

    private function getCurrentCondition(): array
    {
        // Nanti ganti dengan:
        // $checkin = DailyCheckin::where('user_id', auth()->id())
        //     ->whereDate('date', today())
        //     ->first();
        // $level = $checkin?->energy_level ?? 1;

        return [
            'label' => 'Medium Energy',
            'level' => 2,   // 1 = low, 2 = medium, 3 = high
            'max'   => 3,
        ];
    }
}