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
        $request->validate(['name' => 'required|string|max:255']);

        if ($request->filled('task_id')) {
            $task = Task::where('id', $request->task_id)
                        ->where('user_id', auth()->id())
                        ->firstOrFail();
        } else {
            $task = new Task();
            $task->user_id = auth()->id();
        }

        $task->name = $request->name;

        $task->subtask_name     = $request->subtask_name     ?: null;
        $task->subtask_date     = $this->parseDate($request->subtask_date);
        $task->subtask_time     = $request->subtask_time     ?: null;
        $task->subtask_priority = $request->subtask_priority ?: null;

        $task->subtask2_name     = $request->subtask2_name     ?: null;
        $task->subtask2_date     = $this->parseDate($request->subtask2_date);
        $task->subtask2_time     = $request->subtask2_time     ?: null;
        $task->subtask2_priority = $request->subtask2_priority ?: null;

        $task->subtask3_name     = $request->subtask3_name     ?: null;
        $task->subtask3_date     = $this->parseDate($request->subtask3_date);
        $task->subtask3_time     = $request->subtask3_time     ?: null;
        $task->subtask3_priority = $request->subtask3_priority ?: null;

        $task->save();

        return redirect()->route('tasks.index')->with('success', 'Task saved successfully!');
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