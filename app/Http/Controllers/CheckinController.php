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
            $condition = ['level' => 'low',    'label' => 'Low Energy',    'class' => 'low'];
        } elseif ($score <= 3.5) {
            $condition = ['level' => 'medium', 'label' => 'Medium Energy', 'class' => 'medium'];
        } else {
            $condition = ['level' => 'high',   'label' => 'High Energy',   'class' => 'high'];
        }

        // ── Auto-reschedule berdasarkan kondisi & time preference ──────────
        $rescheduled = $this->autoReschedule(auth()->id(), $condition['level'], $score);

        session(['checkin_condition' => $condition]);
        session(['rescheduled_tasks' => $rescheduled]); // opsional: tampilkan di result page

        return redirect()->route('result');
    }

    /**
     * Reschedule otomatis task hari ini berdasarkan kondisi user.
     *
     * Logika:
     * - LOW   → geser semua task hari ini ke besok, mulai dari jam focus preference user
     * - MEDIUM → geser task priority 'high' ke jam focus preference user hari ini,
     *            task 'low'/'medium' tetap atau geser +1 jam
     * - HIGH  → tidak ada perubahan
     */
    private function autoReschedule(int $userId, string $level, float $score): array
    {
        if ($level === 'high') {
            return []; // Kondisi bagus, tidak perlu reschedule
        }

        $today = today()->toDateString();

        // Ambil time preference user (fallback ke default kalau belum diset)
        $prefs = TimePreference::where('user_id', $userId)->get()->keyBy('key');

        $focusStart = $prefs->has('focus')
            ? Carbon::createFromFormat('H:i', $prefs['focus']->start_time)
            : Carbon::createFromTimeString('14:00');

        $tasks = Task::where('user_id', $userId)->get();
        $rescheduled = [];

        foreach ($tasks as $task) {
            $changed = false;

            $subtaskFields = [
                ['name' => 'subtask_name',  'date' => 'subtask_date',  'time' => 'subtask_time',  'priority' => 'subtask_priority'],
                ['name' => 'subtask2_name', 'date' => 'subtask2_date', 'time' => 'subtask2_time', 'priority' => 'subtask2_priority'],
                ['name' => 'subtask3_name', 'date' => 'subtask3_date', 'time' => 'subtask3_time', 'priority' => 'subtask3_priority'],
            ];

            foreach ($subtaskFields as $fields) {
                $nameField     = $fields['name'];
                $dateField     = $fields['date'];
                $timeField     = $fields['time'];
                $priorityField = $fields['priority'];

                if (empty($task->$nameField)) continue;
                if (empty($task->$dateField)) continue;

                $subDate = Carbon::parse($task->$dateField)->toDateString();
                if ($subDate !== $today) continue;

                $priority = $task->$priorityField ?? 'medium';

                if ($level === 'low') {
                    // Geser semua task hari ini ke besok jam focus preference
                    $task->$dateField = today()->addDay()->toDateString();
                    $task->$timeField = $focusStart->format('H:i:s');
                    $changed = true;

                    $rescheduled[] = [
                        'task'    => $task->name . ' — ' . $task->$nameField,
                        'action'  => 'Moved to tomorrow at ' . $focusStart->format('H:i'),
                    ];

                } elseif ($level === 'medium') {
                    if ($priority === 'high') {
                        // Task penting: jadwalkan di jam focus hari ini
                        $task->$timeField = $focusStart->format('H:i:s');
                        $changed = true;

                        $rescheduled[] = [
                            'task'   => $task->name . ' — ' . $task->$nameField,
                            'action' => 'Rescheduled to focus time ' . $focusStart->format('H:i'),
                        ];
                    } else {
                        // Task rendah/medium: geser +1 jam dari sekarang atau dari focus
                        $newTime = $focusStart->copy()->addHour();
                        $task->$timeField = $newTime->format('H:i:s');
                        $changed = true;

                        $rescheduled[] = [
                            'task'   => $task->name . ' — ' . $task->$nameField,
                            'action' => 'Shifted to ' . $newTime->format('H:i'),
                        ];
                    }
                }
            }

            if ($changed) {
                $task->save();
            }
        }

        return $rescheduled;
    }

    public function result()
    {
        $condition = session('checkin_condition');

        if (!$condition) {
            return redirect()->route('checkin.index');
        }

        $rescheduled = session('rescheduled_tasks', []);

        return view('result', compact('condition', 'rescheduled'));
    }
}