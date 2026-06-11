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
            'dailyWarning'     => $this->getDailyTaskWarning(),
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
            foreach ($task->formattedSubtasks() as $sub) {
                if (empty($sub['name']) || empty($sub['date'])) continue;

                $date = Carbon::parse($sub['date']);

                $dueTimestamp = $date->copy();

                if (!empty($sub['time'])) {
                    $dueTimestamp->setTimeFromTimeString($sub['time']);
                } else {
                    $dueTimestamp->endOfDay();
                }

                $minutes = now()->diffInMinutes($dueTimestamp, false);

                if ($minutes < 0) {
                    $subtitle = 'Overdue';
                } elseif ($minutes < 60) {
                    $subtitle = "Starts in {$minutes} Minutes";
                } elseif ($minutes < 1440) {
                    $subtitle = "Starts in " . floor($minutes / 60) . " Hours";
                } else {
                    $daysLeft = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($dueTimestamp)->startOfDay(), false);
                    $subtitle = "Starts in {$daysLeft} Days";
                }

                $upcoming[] = [
                    'title'    => $task->name . ' — ' . $sub['name'],
                    'subtitle' => $subtitle,
                    'due_date' => $date->format('d M'),
                    'sort_datetime' => $dueTimestamp->timestamp,
                    'priority' => $sub['priority'] ?? 'medium',
                ];
            }
        }

        // Sort: nearest deadline first, then priority
        usort($upcoming, fn($a, $b) =>
            $a['sort_datetime'] === $b['sort_datetime']
                ? $this->dashboardPriorityValue($a['priority']) <=> $this->dashboardPriorityValue($b['priority'])
                : $a['sort_datetime'] <=> $b['sort_datetime']
        );

        // Hapus sort_date sebelum dikirim ke view
        return array_map(function($t) {
            unset($t['sort_datetime']);
            return $t;
        }, $upcoming);
    }

    private function dashboardPriorityValue(string $priority): int
    {
        return match ($priority) {
            'high' => 1,
            'medium' => 2,
            'low' => 3,
            default => 4,
        };
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

    private function getDailyTaskWarning(): ?array
    {
        // Check if user dismissed warning today
        $dismissedDate = session('popup_dismissed_date');
        $dismissedCount = session('popup_dismissed_count', 0);

        $dateCounts = [];

        $tasks = Task::where('user_id', auth()->id())->get();
        foreach ($tasks as $task) {
            foreach ($task->formattedSubtasks() as $sub) {
                if (empty($sub['name']) || empty($sub['date'])) {
                    continue;
                }
                $date = Carbon::parse($sub['date'])->toDateString();
                $dateCounts[$date] = ($dateCounts[$date] ?? 0) + 1;
            }
        }

        if (empty($dateCounts)) {
            return null;
        }

        ksort($dateCounts);

       foreach ($dateCounts as $date => $count) {

            if (
                $dismissedDate === today()->toDateString()
                 && $count <= $dismissedCount
         ) {
            continue;
        }

        if ($count >= 5) {
        return [
            'date'  => Carbon::parse($date)->format('d M Y'),
            'count' => $count,
        ];
    }
}

        return null;
    }
}
