<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyCheckin;
use App\Models\Task;
use App\Models\TimePreference;
use Carbon\Carbon;

class CheckinController extends Controller
{
    public function index()
    {
        return view('checkin');
    }

    public function store(Request $request)
    {
        $request->validate([
            'energy_level'   => 'required|integer|min:1|max:5',
            'focus_level'    => 'required|integer|min:1|max:5',
            'mood'           => 'required|integer|min:1|max:5',
            'motivation'     => 'required|integer|min:1|max:5',
            'available_time' => 'required|integer|min:1|max:5',
            'stress_level'   => 'required|integer|min:1|max:5',
        ]);

        DailyCheckin::create([
            'user_id'        => auth()->id(),
            'energy_level'   => $request->energy_level,
            'focus_level'    => $request->focus_level,
            'mood'           => $request->mood,
            'motivation'     => $request->motivation,
            'available_time' => $request->available_time,
            'stress_level'   => $request->stress_level,
            'date'           => today(),
        ]);

        // Hitung skor rata-rata (stress di-invert)
        $score = (
            $request->energy_level   +
            $request->focus_level    +
            $request->mood           +
            $request->motivation     +
            $request->available_time +
            (6 - $request->stress_level)
        ) / 6;

        if ($score <= 2) {
            $condition = [
                'level' => 'low',
                'label' => 'Low Energy',
                'class' => 'low',
                'description' => 'Your energy is depleted today. Focus on light tasks and remember to take care of yourself.',
            ];
        } elseif ($score <= 3.5) {
            $condition = [
                'level' => 'medium',
                'label' => 'Medium Energy',
                'class' => 'medium',
                'description' => 'You have moderate energy today. Prioritize important tasks during your peak focus window.',
            ];
        } else {
            $condition = [
                'level' => 'high',
                'label' => 'High Energy',
                'class' => 'high',
                'description' => 'You\'re at your best today! Great time to tackle challenging tasks and make meaningful progress.',
            ];
        }

        $checkinData = [
            'energy_level'   => $request->energy_level,
            'focus_level'    => $request->focus_level,
            'mood'           => $request->mood,
            'motivation'     => $request->motivation,
            'available_time' => $request->available_time,
            'stress_level'   => $request->stress_level,
            'score'          => round($score, 2),
        ];

        // Generate task recommendations
        $recommendations = $this->generateRecommendations(auth()->id(), $condition['level'], $checkinData);

        // Generate focus window
        $focusWindow = $this->getFocusWindow(auth()->id(), $condition['level'], $request->energy_level);

        // Generate tips
        $tips = $this->getTips($condition['level'], $checkinData);

        session([
            'checkin_condition'    => $condition,
            'checkin_data'         => $checkinData,
            'checkin_recommendations' => $recommendations,
            'checkin_focus_window' => $focusWindow,
            'checkin_tips'         => $tips,
        ]);

        return redirect()->route('result');
    }

    /**
     * Generate task recommendations based on condition.
     */
    private function generateRecommendations(int $userId, string $level, array $checkinData): array
    {
        $today = today()->toDateString();
        $tasks = Task::where('user_id', $userId)->get();
        $recommendations = [];

        $timeLabels = ['15 Min', '30 Min', '1 Hr', '3-5 Hr', 'All Day'];
        $availableTime = $checkinData['available_time'];

        foreach ($tasks as $task) {
            foreach ($task->formattedSubtasks() as $sub) {
                $name     = $sub['name'];
                $date     = $sub['date'];
                $time     = $sub['time'];
                $priority = $sub['priority'] ?? 'medium';

                if (empty($name) || empty($date)) continue;

                $taskDate = Carbon::parse($date)->toDateString();
                $isToday  = $taskDate === $today;
                $isUpcoming = Carbon::parse($date)->isFuture() && Carbon::parse($date)->diffInDays(today()) <= 3;

                $shouldInclude = false;
                $type = '';
                $reason = '';

                if ($level === 'low') {
                    // Low energy: only show low-priority tasks OR quick ones
                    if ($priority === 'low' && $availableTime >= 1) {
                        $shouldInclude = true;
                        $type = 'Light Task';
                        $reason = 'Low-effort task suitable for your current energy level. Good to keep momentum without overloading yourself.';
                    } elseif ($isToday && $priority === 'high') {
                        $shouldInclude = true;
                        $type = 'Urgent — Consider Deferring';
                        $reason = 'This high-priority task is due today, but given your low energy it may be worth considering deferring to tomorrow.';
                    }
                } elseif ($level === 'medium') {
                    // Medium: recommend high priority today + medium priority with enough time
                    if ($isToday && $priority === 'high') {
                        $shouldInclude = true;
                        $type = 'High Priority Today';
                        $reason = 'Important task due today. Schedule this during your optimal focus window for best results.';
                    } elseif (($isToday || $isUpcoming) && $priority === 'medium' && $availableTime >= 3) {
                        $shouldInclude = true;
                        $type = 'Steady Progress';
                        $reason = 'Medium-priority task you can work on steadily. Pair with short breaks to maintain your energy.';
                    }
                } else {
                    // High energy: recommend high priority + ambitious tasks
                    if ($priority === 'high') {
                        $shouldInclude = true;
                        $type = 'Tackle Now';
                        $reason = 'You have great energy today — perfect time to knock out this high-priority task efficiently.';
                    } elseif ($isUpcoming && $priority === 'medium' && $availableTime >= 3) {
                        $shouldInclude = true;
                        $type = 'Get Ahead';
                        $reason = 'High energy day! Consider getting ahead on this upcoming task to free up future schedule.';
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

    /**
     * Get optimal focus window based on condition.
     */
    private function getFocusWindow(int $userId, string $level, int $energyLevel): array
    {
        $prefs = TimePreference::where('user_id', $userId)->get()->keyBy('key');

        $focusStart = $prefs->has('focus')
            ? Carbon::createFromFormat('H:i', $prefs['focus']->start_time)
            : Carbon::createFromTimeString('14:00');

        if ($level === 'low') {
            // Short window in the afternoon
            $start = $focusStart->copy()->addHours(2);
            $end   = $start->copy()->addHours(1);
            $session = '25 min (Pomodoro)';
        } elseif ($level === 'medium') {
            $start   = $focusStart->copy();
            $end     = $start->copy()->addHours(2);
            $session = '45 min with 15 min break';
        } else {
            // High: earlier start, longer window
            $start   = $focusStart->copy()->subHour();
            $end     = $start->copy()->addHours(3);
            $session = '90 min deep work sessions';
        }

        return [
            'label'            => $start->format('H:i') . ' – ' . $end->format('H:i'),
            'session_duration' => $session,
        ];
    }

    /**
     * Get productivity tips based on condition and metrics.
     */
    private function getTips(string $level, array $data): array
    {
        if ($level === 'low') {
            return [
                '<strong>Rest is productive.</strong> Taking time to recover now means more capacity tomorrow. Don\'t push through exhaustion.',
                '<strong>Micro-tasks only.</strong> Limit yourself to tasks under 15 minutes. Small wins help without draining you further.',
                '<strong>Hydrate & move.</strong> Drink water and take a 10-minute walk. Physical state heavily influences mental energy.',
                $data['stress_level'] >= 4
                    ? '<strong>High stress detected.</strong> Try a 5-minute breathing exercise: inhale 4 counts, hold 4, exhale 6.'
                    : '<strong>Protect your schedule.</strong> Avoid taking on new commitments today — focus on recovery first.',
            ];
        } elseif ($level === 'medium') {
            return [
                '<strong>Batch similar tasks.</strong> Group tasks that require the same type of thinking to reduce cognitive switching.',
                $data['focus_level'] <= 2
                    ? '<strong>Focus is low today.</strong> Try the 2-minute rule: if a task takes less than 2 minutes, do it now to clear your list.'
                    : '<strong>Time-block your work.</strong> Set 45-minute focused sprints with 15-minute breaks between them.',
                '<strong>Eat the frog.</strong> Tackle your most dreaded task first — it\'ll free up mental energy for the rest of the day.',
                $data['motivation'] <= 2
                    ? '<strong>Motivation is low.</strong> Connect tasks to your bigger goals. Remind yourself why each task matters.'
                    : '<strong>Momentum matters.</strong> Start with a quick win to build momentum before tackling harder tasks.',
            ];
        } else {
            return [
                '<strong>Deep work opportunity.</strong> Your high energy is perfect for complex, cognitively demanding tasks. Protect this time.',
                '<strong>Go further.</strong> Consider tackling tasks you\'ve been postponing — you have the capacity today.',
                $data['motivation'] >= 4
                    ? '<strong>Capitalize on motivation.</strong> Use this motivated state to make progress on long-term goals, not just urgent tasks.'
                    : '<strong>Energy without direction.</strong> Write down your top 3 priorities before starting so you channel energy effectively.',
                '<strong>Plan for the dip.</strong> Energy levels fluctuate. Schedule lighter tasks for late afternoon when energy naturally drops.',
            ];
        }
    }

    public function result()
    {
        $condition       = session('checkin_condition');
        $checkinData     = session('checkin_data');
        $recommendations = session('checkin_recommendations', []);
        $focusWindow     = session('checkin_focus_window');
        $tips            = session('checkin_tips', []);

        if (!$condition || !$checkinData) {
            return redirect()->route('checkin.index');
        }

        $timeLabels = ['15 Min', '30 Min', '1 Hr', '3-5 Hr', 'All Day'];

        return view('result', compact(
            'condition', 'checkinData', 'recommendations',
            'focusWindow', 'tips', 'timeLabels'
        ));
    }

    /**
     * Apply selected schedule action via AJAX.
     */
    public function applySchedule(Request $request)
    {
        $action    = $request->input('action');
        $userId    = auth()->id();
        $today     = today()->toDateString();
        $tomorrow  = today()->addDay()->toDateString();        $targetDate = null;

        if ($action === 'balance_day') {
            $request->validate(['target_date' => 'required|date']);
            $targetDate = Carbon::parse($request->input('target_date'))->toDateString();
        }
        $prefs = TimePreference::where('user_id', $userId)->get()->keyBy('key');
        $focusStart = $prefs->has('focus')
            ? Carbon::createFromFormat('H:i', $prefs['focus']->start_time)
            : Carbon::createFromTimeString('14:00');

        $tasks   = Task::where('user_id', $userId)->get();
        $changed = 0;

        $subtaskQueue = [];
        $rawSubtasksByTask = [];

        foreach ($tasks as $task) {
            $rawSubtasksByTask[$task->id] = $task->subtasks ?: $task->formattedSubtasks();

            foreach ($rawSubtasksByTask[$task->id] as $index => $sub) {
                if (empty($sub['name']) || empty($sub['date']) || !empty($sub['done'])) {
                    continue;
                }

                $subtaskQueue[] = [
                    'task'     => $task,
                    'taskId'   => $task->id,
                    'index'    => $index,
                    'sub'      => $sub,
                    'date'     => Carbon::parse($sub['date'])->toDateString(),
                    'time'     => $sub['time'] ?? null,
                    'priority' => $sub['priority'] ?? 'medium',
                ];
            }
        }

        usort($subtaskQueue, function ($a, $b) {
            if ($a['date'] !== $b['date']) {
                return strcmp($a['date'], $b['date']);
            }
            if ($a['time'] !== $b['time']) {
                if (empty($a['time'])) return 1;
                if (empty($b['time'])) return -1;
                return strcmp($a['time'], $b['time']);
            }
            return $this->priorityValue($a['priority']) <=> $this->priorityValue($b['priority']);
        });

        $highestPriorityOfDay = null;
        foreach ($subtaskQueue as $entry) {
            if ($entry['date'] === $targetDate) {
                $priorityValue = $this->priorityValue($entry['priority']);
                if ($highestPriorityOfDay === null || $priorityValue < $highestPriorityOfDay) {
                    $highestPriorityOfDay = $priorityValue;
                }
            }
        }

        foreach ($subtaskQueue as $entry) {
            $task      = $entry['task'];
            $taskId    = $entry['taskId'];
            $index     = $entry['index'];
            $sub       = $entry['sub'];
            $subDate   = $entry['date'];
            $priority  = $entry['priority'];
            $subtasks  = $rawSubtasksByTask[$taskId];
            $taskChanged = false;

            switch ($action) {
                    case 'defer_all':
                        if ($subDate === $today) {
                            $subtasks[$index]['date'] = $tomorrow;
                            $subtasks[$index]['time'] = $focusStart->format('H:i:s');
                            $taskChanged = true; $changed++;
                        }
                        break;

                    case 'keep_essential':
                        if ($subDate === $today && $priority !== 'high') {
                            $subtasks[$index]['date'] = $tomorrow;
                            $subtasks[$index]['time'] = $focusStart->format('H:i:s');
                            $taskChanged = true; $changed++;
                        }
                        break;

                    case 'reschedule_focus':
                        if ($subDate === $today && $priority === 'high') {
                            $subtasks[$index]['time'] = $focusStart->format('H:i:s');
                            $taskChanged = true; $changed++;
                        }
                        break;

                    case 'space_out':
                        if ($subDate === $today) {
                            $currentTime = !empty($sub['time'])
                                ? Carbon::parse($sub['time'])->addMinutes(30)
                                : $focusStart->copy()->addHour();
                            $subtasks[$index]['time'] = $currentTime->format('H:i:s');
                            $taskChanged = true; $changed++;
                        }
                        break;

                    case 'advance_tasks':
                        $diffDays = Carbon::parse($subDate)->diffInDays(today(), false);
                        if ($diffDays < 0 && abs($diffDays) <= 3) {
                            $subtasks[$index]['date'] = $today;
                            $subtasks[$index]['time'] = $focusStart->copy()->addHour()->format('H:i:s');
                            $taskChanged = true; $changed++;
                        }
                        break;

                    case 'balance_day':
                        if ($subDate === $targetDate && $highestPriorityOfDay !== null && $this->priorityValue($priority) > $highestPriorityOfDay) {
                            $subtasks[$index]['date'] = Carbon::parse($targetDate)->addDay()->toDateString();
                            $subtasks[$index]['time'] = $focusStart->format('H:i:s');
                            $taskChanged = true; $changed++;
                        }
                        break;

                    case 'no_change':
                    case 'keep_schedule':
                    default:
                        break;
                }

            if ($taskChanged) {
                $rawSubtasksByTask[$taskId] = $subtasks;
                $task->subtasks = $subtasks;
                $task->save();
            }
        }

        $messages = [
            'defer_all'        => "All today's tasks moved to tomorrow. Rest up!",
            'keep_essential'   => "Non-essential tasks deferred. Focus on what matters.",
            'reschedule_focus' => "High-priority tasks rescheduled to your focus window.",
            'space_out'        => "Tasks spaced out with buffer time added.",
            'advance_tasks'    => "Upcoming tasks pulled forward to today.",
            'balance_day'      => "Lower-priority tasks were shifted to make room for the highest priorities.",
            'keep_schedule'    => "Schedule kept as-is. Keep it up!",
            'no_change'        => "No changes made to your schedule.",
        ];

        $response = [
            'success' => true,
            'message' => $messages[$action] ?? 'Schedule updated.',
            'changed' => $changed,
        ];

        if (!$request->expectsJson()) {
            return redirect()->route('schedule.index')->with('status', $response['message']);
        }

        return response()->json($response);
    }

    /**
     * Reschedule otomatis task hari ini berdasarkan kondisi user.
     * (Kept for legacy use if needed)
     */
    private function priorityValue(string $priority): int
    {
        return match ($priority) {
            'high' => 1,
            'medium' => 2,
            'low' => 3,
            default => 4,
        };
    }

    private function autoReschedule(int $userId, string $level, float $score): array
    {
        return []; // Now handled via applySchedule endpoint
    }
}
