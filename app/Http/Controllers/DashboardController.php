<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\DailyCheckin;
use App\Models\Target;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'calendar'         => $this->getCalendar(),
            'upcomingTasks'    => $this->getUpcomingTasks(),
            'goals'            => $this->getGoals(),
            'currentCondition' => $this->getCurrentCondition(),
        ]);
    }

    private function getCalendar(): array
    {
        $now   = Carbon::now();
        $first = Carbon::create($now->year, $now->month, 1);
        $days  = [];

        for ($i = $first->dayOfWeek - 1; $i >= 0; $i--) {
            $days[] = ['day' => $first->copy()->subDays($i + 1)->day, 'current' => false, 'today' => false];
        }
        for ($d = 1; $d <= $now->daysInMonth; $d++) {
            $days[] = ['day' => $d, 'current' => true, 'today' => $d === $now->day];
        }
        $remaining = 42 - count($days);
        for ($d = 1; $d <= $remaining; $d++) {
            $days[] = ['day' => $d, 'current' => false, 'today' => false];
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
        $tasks    = Task::where('user_id', auth()->id())->latest()->get();
        $upcoming = [];

        foreach ($tasks as $task) {
            $subtasks = [
                ['name' => $task->subtask_name,  'date' => $task->subtask_date,  'priority' => $task->subtask_priority],
                ['name' => $task->subtask2_name, 'date' => $task->subtask2_date, 'priority' => $task->subtask2_priority],
                ['name' => $task->subtask3_name, 'date' => $task->subtask3_date, 'priority' => $task->subtask3_priority],
            ];

            foreach ($subtasks as $sub) {
                if (empty($sub['name']) || empty($sub['date'])) continue;

                $date = Carbon::parse($sub['date']);
                $diff = now()->diffInHours($date, false);

                if ($diff < 0) {
                    $subtitle = 'Overdue';
                } elseif ($diff < 24) {
                    $subtitle = "Starts in {$diff} Hours";
                } else {
                    $subtitle = "Starts in " . now()->diffInDays($date) . " Days";
                }

                $upcoming[] = [
                    'title'    => $task->name . ' — ' . $sub['name'],
                    'subtitle' => $subtitle,
                    'due_date' => $date->format('d M'),
                    'sort_date'=> $date->timestamp,
                    'priority' => $sub['priority'] ?? 'medium',
                ];
            }
        }

        // Sort: overdue dulu, lalu terdekat
        usort($upcoming, fn($a, $b) => $a['sort_date'] <=> $b['sort_date']);

        // Hapus sort_date sebelum dikirim ke view
        return array_map(function($t) {
            unset($t['sort_date']);
            return $t;
        }, $upcoming);
    }

    private function getGoals(): array
    {
        $targets = Target::where('user_id', auth()->id())
            ->whereIn('key', ['daily', 'weekly'])
            ->pluck('content', 'key')
            ->toArray();

        return [
            ['type' => 'Daily',  'text' => $targets['daily']  ?? 'No daily target set yet.'],
            ['type' => 'Weekly', 'text' => $targets['weekly'] ?? 'No weekly target set yet.'],
        ];
    }

    private function getCurrentCondition(): array
    {
        $checkin = DailyCheckin::where('user_id', auth()->id())
            ->whereDate('date', today())
            ->orderByDesc('created_at')
            ->first();

        if (!$checkin) {
            return ['label' => 'No Check-In Yet', 'level' => 0, 'max' => 3];
        }

        $score = (
            $checkin->energy_level +
            $checkin->focus_level  +
            $checkin->mood         +
            $checkin->motivation   +
            $checkin->available_time +
            (6 - $checkin->stress_level)
        ) / 6;

        if ($score <= 2)   return ['label' => 'Low Energy',    'level' => 1, 'max' => 3];
        if ($score <= 3.5) return ['label' => 'Medium Energy', 'level' => 2, 'max' => 3];
        return                    ['label' => 'High Energy',   'level' => 3, 'max' => 3];
    }
}
