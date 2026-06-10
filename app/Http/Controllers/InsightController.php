<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyCheckin;
use App\Models\Task;
use App\Models\TimePreference;
use Carbon\Carbon;

class InsightController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $today  = today()->toDateString();

        // Ambil check-in terbaru (hari ini atau yang terakhir)
        $latestCheckin = DailyCheckin::where('user_id', $userId)
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->first();

        if (!$latestCheckin) {
            return view('insights', [
                'latestCheckin'   => null,
                'condition'       => null,
                'recommendations' => [],
                'focusWindow'     => null,
                'tips'            => [],
                'timeLabels'      => ['15 Min', '30 Min', '1 Hr', '3-5 Hr', 'All Day'],
            ]);
        }

        // Hitung kondisi dari checkin terbaru
        $score = (
            $latestCheckin->energy_level   +
            $latestCheckin->focus_level    +
            $latestCheckin->mood           +
            $latestCheckin->motivation     +
            $latestCheckin->available_time +
            (6 - $latestCheckin->stress_level)
        ) / 6;

        if ($score <= 2) {
            $condition = [
                'level' => 'low', 'label' => 'Low Energy', 'class' => 'low',
                'description' => 'Your energy is depleted. Focus on light tasks and recovery.',
            ];
        } elseif ($score <= 3.5) {
            $condition = [
                'level' => 'medium', 'label' => 'Medium Energy', 'class' => 'medium',
                'description' => 'Moderate energy. Prioritize important tasks during your focus window.',
            ];
        } else {
            $condition = [
                'level' => 'high', 'label' => 'High Energy', 'class' => 'high',
                'description' => 'You\'re at your best! Great time to tackle challenging tasks.',
            ];
        }

        $checkinData = [
            'energy_level'   => $latestCheckin->energy_level,
            'focus_level'    => $latestCheckin->focus_level,
            'mood'           => $latestCheckin->mood,
            'motivation'     => $latestCheckin->motivation,
            'available_time' => $latestCheckin->available_time,
            'stress_level'   => $latestCheckin->stress_level,
            'score'          => round($score, 2),
        ];

        $recommendations = $this->generateRecommendations($userId, $condition['level'], $checkinData, $latestCheckin->date);
        $focusWindow     = $this->getFocusWindow($userId, $condition['level']);
        $tips            = $this->getTips($condition['level'], $checkinData);
        $timeLabels      = ['15 Min', '30 Min', '1 Hr', '3-5 Hr', 'All Day'];

        return view('insights', compact(
            'latestCheckin', 'condition', 'recommendations',
            'focusWindow', 'tips', 'timeLabels'
        ));
    }

    private function generateRecommendations(int $userId, string $level, array $checkinData, $checkinDate): array
    {
        $today    = today()->toDateString();
        $tasks    = Task::where('user_id', $userId)->get();
        $recommendations = [];

        foreach ($tasks as $task) {
            foreach ($task->formattedSubtasks() as $sub) {
                $name     = $sub['name'];
                $date     = $sub['date'];
                $time     = $sub['time'];
                $priority = $sub['priority'] ?? 'medium';

                if (empty($name) || empty($date)) continue;

                $taskDate   = Carbon::parse($date)->toDateString();
                $isToday    = $taskDate === $today;
                $isUpcoming = Carbon::parse($date)->isFuture() && Carbon::parse($date)->diffInDays(today()) <= 3;

                $shouldInclude = false; $type = ''; $reason = '';

                if ($level === 'low') {
                    if ($priority === 'low') {
                        $shouldInclude = true; $type = 'Light Task';
                        $reason = 'Low-effort task suitable for your current energy. Keep momentum without overloading.';
                    } elseif ($isToday && $priority === 'high') {
                        $shouldInclude = true; $type = 'Urgent — Consider Deferring';
                        $reason = 'High-priority task due today. Given low energy, consider deferring to tomorrow.';
                    }
                } elseif ($level === 'medium') {
                    if ($isToday && $priority === 'high') {
                        $shouldInclude = true; $type = 'High Priority Today';
                        $reason = 'Important task due today. Schedule during your focus window for best results.';
                    } elseif (($isToday || $isUpcoming) && $priority === 'medium' && $checkinData['available_time'] >= 3) {
                        $shouldInclude = true; $type = 'Steady Progress';
                        $reason = 'Medium-priority task you can work on steadily. Pair with short breaks.';
                    }
                } else {
                    if ($priority === 'high') {
                        $shouldInclude = true; $type = 'Tackle Now';
                        $reason = 'Great energy today — perfect time to knock out this high-priority task.';
                    } elseif ($isUpcoming && $priority === 'medium' && $checkinData['available_time'] >= 3) {
                        $shouldInclude = true; $type = 'Get Ahead';
                        $reason = 'High energy day! Get ahead on this upcoming task to free up your future schedule.';
                    }
                }

                if ($shouldInclude && count($recommendations) < 5) {
                    $recommendations[] = [
                        'task_id'       => $task->id,
                        'task_name'     => $task->name,
                        'subtask_name'  => $name,
                        'subtask_field' => 'subtask' . $sub['index'],
                        'priority'      => $priority,
                        'scheduled_time'=> $time ? Carbon::parse($time)->format('H:i') : null,
                        'scheduled_date'=> $taskDate,
                        'type'          => $type,
                        'reason'        => $reason,
                    ];
                }
            }
        }

        return $recommendations;
    }

    private function getFocusWindow(int $userId, string $level): array
    {
        $prefs = TimePreference::where('user_id', $userId)->get()->keyBy('key');
        $focusStart = $prefs->has('focus')
            ? Carbon::createFromFormat('H:i', $prefs['focus']->start_time)
            : Carbon::createFromTimeString('14:00');

        if ($level === 'low') {
            $start = $focusStart->copy()->addHours(2);
            $end   = $start->copy()->addHour();
            $sess  = '25 min (Pomodoro)';
        } elseif ($level === 'medium') {
            $start = $focusStart->copy();
            $end   = $start->copy()->addHours(2);
            $sess  = '45 min with 15 min break';
        } else {
            $start = $focusStart->copy()->subHour();
            $end   = $start->copy()->addHours(3);
            $sess  = '90 min deep work sessions';
        }

        return [
            'label'            => $start->format('H:i') . ' – ' . $end->format('H:i'),
            'session_duration' => $sess,
        ];
    }

    private function getTips(string $level, array $data): array
    {
        if ($level === 'low') {
            return [
                '<strong>Rest is productive.</strong> Recovering now means more capacity tomorrow.',
                '<strong>Micro-tasks only.</strong> Limit yourself to tasks under 15 minutes.',
                '<strong>Hydrate & move.</strong> Drink water and take a 10-minute walk.',
                $data['stress_level'] >= 4
                    ? '<strong>High stress detected.</strong> Try box breathing: inhale 4, hold 4, exhale 6.'
                    : '<strong>Protect your schedule.</strong> Avoid taking on new commitments today.',
            ];
        } elseif ($level === 'medium') {
            return [
                '<strong>Batch similar tasks.</strong> Group tasks by thinking type to reduce cognitive switching.',
                $data['focus_level'] <= 2
                    ? '<strong>Focus is low.</strong> Try the 2-minute rule: if it takes under 2 minutes, do it now.'
                    : '<strong>Time-block your work.</strong> 45-minute sprints with 15-minute breaks.',
                '<strong>Eat the frog.</strong> Tackle your most dreaded task first to free up mental energy.',
                $data['motivation'] <= 2
                    ? '<strong>Motivation is low.</strong> Reconnect tasks to your bigger goals — why does each one matter?'
                    : '<strong>Momentum matters.</strong> Start with a quick win before tackling harder tasks.',
            ];
        } else {
            return [
                '<strong>Deep work opportunity.</strong> Your high energy is perfect for complex, demanding tasks.',
                '<strong>Go further.</strong> Tackle tasks you\'ve been postponing — you have the capacity.',
                $data['motivation'] >= 4
                    ? '<strong>Capitalize on motivation.</strong> Work on long-term goals, not just urgent tasks.'
                    : '<strong>Channel your energy.</strong> Write down your top 3 priorities before starting.',
                '<strong>Plan for the dip.</strong> Schedule lighter tasks for late afternoon when energy naturally drops.',
            ];
        }
    }
}
