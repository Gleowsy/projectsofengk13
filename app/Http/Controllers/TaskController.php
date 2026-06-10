<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::where('user_id', auth()->id())->latest()->get();
        return view('Task', compact('tasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subtasks' => 'array',
            'subtasks.*.name' => 'nullable|string|max:255',
            'subtasks.*.date' => 'nullable|date',
            'subtasks.*.time' => 'nullable|date_format:H:i',
            'subtasks.*.priority' => 'nullable|in:low,medium,high',
            'subtasks.*.done' => 'nullable|boolean',
        ]);

        if ($request->filled('task_id')) {
            $task = Task::where('id', $request->task_id)
                        ->where('user_id', auth()->id())
                        ->firstOrFail();
        } else {
            $task = new Task();
            $task->user_id = auth()->id();
        }

        $task->name = $request->name;

        $task->subtasks = collect($request->input('subtasks', []))
            ->map(function ($sub) {
                return [
                    'name' => trim($sub['name'] ?? '') ?: null,
                    'date' => $this->parseDate($sub['date'] ?? null),
                    'time' => trim($sub['time'] ?? '') ?: null,
                    'priority' => in_array($sub['priority'] ?? null, ['low','medium','high']) ? $sub['priority'] : null,
                    'done' => isset($sub['done']) ? (bool) $sub['done'] : false,
                ];
            })
            ->filter(function ($sub) {
                return !empty($sub['name']) || !empty($sub['date']) || !empty($sub['time']) || !empty($sub['priority']);
            })
            ->values()
            ->all();

        $task->save();

        return redirect()->route('tasks.index')->with('success', 'Task saved successfully!');
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $request->merge(['task_id' => $task->id]);
        return $this->store($request);
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
    }

    private function parseDate($date)
    {
        if (!$date) return null;
        try {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
