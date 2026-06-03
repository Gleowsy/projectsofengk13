<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TimePreference;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    // ── Helpers ───────────────────────────────────────────────────────────────

    private function extractSubtasks(Task $task): array
    {
        $subs = [];
        $groups = [
            1 => ['name' => $task->subtask_name,  'date' => $task->subtask_date,  'time' => $task->subtask_time,  'priority' => $task->subtask_priority,  'done' => $task->subtask_done  ?? false],
            2 => ['name' => $task->subtask2_name, 'date' => $task->subtask2_date, 'time' => $task->subtask2_time, 'priority' => $task->subtask2_priority, 'done' => $task->subtask2_done ?? false],
            3 => ['name' => $task->subtask3_name, 'date' => $task->subtask3_date, 'time' => $task->subtask3_time, 'priority' => $task->subtask3_priority, 'done' => $task->subtask3_done ?? false],
        ];

        foreach ($groups as $num => $sub) {
            if (empty($sub['name'])) continue;
            $subs[] = array_merge($sub, [
                'task_id'     => $task->id,
                'task_name'   => $task->name,
                'subtask_num' => $num,
            ]);
        }

        return $subs;
    }

    private function getFocusStartTime(int $userId, string $key = 'focus', string $default = '14:00'): string
    {
        $pref = TimePreference::where('user_id', $userId)->where('key', $key)->first();
        if (!$pref) return $default;
        return str_replace('.', ':', $pref->start_time);
    }

    private function formatSubtask(array $sub, string $selectedDate): array
    {
        $dateStr   = null;
        $overdue   = false;
        $timeLabel = null;
        $now       = Carbon::now();

        if (!empty($sub['date'])) {
            $subDate = $sub['date'] instanceof Carbon
                ? $sub['date']->copy()
                : Carbon::parse($sub['date']);
            $dateStr = $subDate->toDateString();
        }

        if (!empty($sub['time'])) {
            try {
                $timeLabel = Carbon::parse($sub['time'])->format('H:i');
            } catch (\Exception $e) {}
        }

        // Overdue = gabungan tanggal + waktu sudah lewat sekarang
        if ($dateStr) {
            if ($timeLabel) {
                // Ada waktu: overdue kalau datetime sudah lewat
                $fullDatetime = Carbon::parse($dateStr . ' ' . $timeLabel);
                $overdue = $fullDatetime->lt($now);
            } else {
                // Tidak ada waktu: overdue kalau tanggalnya sebelum hari ini
                $overdue = Carbon::parse($dateStr)->startOfDay()->lt(Carbon::today());
            }
        }

        // Display time
        if ($timeLabel && $dateStr) {
            $fullDatetime = Carbon::parse($dateStr . ' ' . $timeLabel);
            if ($overdue) {
                $displayTime = 'Was at ' . $timeLabel . ' · ' . $fullDatetime->diffForHumans();
            } else {
                $displayTime = 'Starts at ' . $timeLabel;
            }
        } elseif ($overdue && $dateStr) {
            $displayTime = Carbon::parse($dateStr)->diffForHumans() . ' (overdue)';
        } elseif ($dateStr === Carbon::today()->toDateString()) {
            $displayTime = 'Today';
        } else {
            $displayTime = $dateStr ?? '—';
        }

        return [
            'task_id'     => $sub['task_id'],
            'task_name'   => $sub['task_name'],
            'subtask_num' => $sub['subtask_num'],
            'name'        => $sub['task_name'] . ' — ' . $sub['name'],
            'time'        => $displayTime,
            'done'        => (bool) ($sub['done'] ?? false),
            'overdue'     => $overdue,
            'priority'    => $sub['priority'] ?? null,
        ];
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function index()
    {
        $userId = auth()->id();
        $date   = request('date', today()->toDateString());

        $tasks      = Task::where('user_id', $userId)->get();
        $todayTasks = [];
        $taskDates  = [];

        foreach ($tasks as $task) {
            foreach ($this->extractSubtasks($task) as $sub) {
                if (empty($sub['date'])) continue;

                $subDate = $sub['date'] instanceof Carbon
                    ? $sub['date']->copy()
                    : Carbon::parse($sub['date']);

                $dateStr     = $subDate->toDateString();
                $taskDates[] = $dateStr;

                if ($dateStr === $date) {
                    $todayTasks[] = $this->formatSubtask($sub, $date);
                }
            }
        }

        $taskDates = array_values(array_unique($taskDates));

        if (request()->expectsJson()) {
            return response()->json(['tasks' => $todayTasks, 'date' => $date]);
        }

        return view('schedule', compact('todayTasks', 'taskDates', 'date'));
    }

    // ── Tasks by date (AJAX) ──────────────────────────────────────────────────

    public function tasksByDate(Request $request)
    {
        $request->validate(['date' => 'required|date']);

        $userId = auth()->id();
        $date   = Carbon::parse($request->date)->toDateString();
        $tasks  = Task::where('user_id', $userId)->get();
        $result = [];

        foreach ($tasks as $task) {
            foreach ($this->extractSubtasks($task) as $sub) {
                if (empty($sub['date'])) continue;

                $subDate = $sub['date'] instanceof Carbon
                    ? $sub['date']->copy()
                    : Carbon::parse($sub['date']);

                if ($subDate->toDateString() === $date) {
                    $result[] = $this->formatSubtask($sub, $date);
                }
            }
        }

        return response()->json(['tasks' => $result, 'date' => $date]);
    }

    // ── Reschedule satu subtask ───────────────────────────────────────────────

    public function rescheduleTask(Request $request)
    {
        $request->validate([
            'task_id'     => 'required|integer',
            'subtask_num' => 'required|integer|in:1,2,3',
        ]);

        $userId = auth()->id();
        $task   = Task::where('id', $request->task_id)
                      ->where('user_id', $userId)
                      ->firstOrFail();

        $num     = (int) $request->subtask_num;
        $dateCol = $num === 1 ? 'subtask_date' : "subtask{$num}_date";
        $timeCol = $num === 1 ? 'subtask_time' : "subtask{$num}_time";

        $focusStart = $this->getFocusStartTime($userId, 'focus', '14:00');

        $currentDate = !empty($task->$dateCol)
            ? Carbon::parse($task->$dateCol)
            : Carbon::today();

        $newDate = $currentDate->copy()->startOfDay()->lt(Carbon::today())
            ? Carbon::today()->addDay()
            : $currentDate->copy()->addDay();

        $task->$dateCol = $newDate->toDateString();
        $task->$timeCol = $focusStart;
        $task->save();

        return response()->json([
            'success'  => true,
            'new_date' => $newDate->toDateString(),
            'new_time' => $focusStart,
            'message'  => 'Rescheduled to ' . $newDate->format('d M Y') . ' at ' . $focusStart,
        ]);
    }

    // ── Mark done ─────────────────────────────────────────────────────────────

    public function markDone(Request $request)
    {
        $request->validate([
            'task_id'     => 'required|integer',
            'subtask_num' => 'required|integer|in:1,2,3',
            'done'        => 'required|boolean',
        ]);

        $userId = auth()->id();
        $task   = Task::where('id', $request->task_id)
                      ->where('user_id', $userId)
                      ->firstOrFail();

        $num     = (int) $request->subtask_num;
        $doneCol = $num === 1 ? 'subtask_done' : "subtask{$num}_done";

        $task->$doneCol = $request->done;
        $task->save();

        return response()->json(['success' => true]);
    }

    // ── Toggle (legacy) ───────────────────────────────────────────────────────

    public function toggle(Request $request)
    {
        return response()->json(['success' => true]);
    }

    // ── Preferences ───────────────────────────────────────────────────────────

    public function preferences()
    {
        $defaults = [
            'sleep'     => ['label' => 'Optimal Sleep Time', 'start' => '20.00', 'end' => '09.00'],
            'lunch'     => ['label' => 'Lunch',              'start' => '12.00', 'end' => '13.00'],
            'breakfast' => ['label' => 'Breakfast',          'start' => '09.00', 'end' => '10.00'],
            'dinner'    => ['label' => 'Dinner',             'start' => '17.00', 'end' => '19.00'],
            'focus'     => ['label' => 'Focus',              'start' => '14.00', 'end' => '17.00'],
        ];

        $prefs = TimePreference::where('user_id', auth()->id())->get()
            ->keyBy('key')
            ->map(fn($p, $k) => [
                'label' => $defaults[$k]['label'] ?? $k,
                'start' => str_replace(':', '.', $p->start_time),
                'end'   => str_replace(':', '.', $p->end_time),
            ])
            ->toArray();

        $preferences = array_merge($defaults, $prefs);

        return view('schedule-preferences', compact('preferences'));
    }

    public function updatePreference(Request $request)
    {
        $request->validate([
            'key'   => 'required|string|in:sleep,lunch,breakfast,dinner,focus',
            'start' => 'required',
            'end'   => 'required',
        ]);

        TimePreference::updateOrCreate(
            ['user_id' => auth()->id(), 'key' => $request->key],
            ['start_time' => $request->start, 'end_time' => $request->end]
        );

        return response()->json(['success' => true]);
    }
}