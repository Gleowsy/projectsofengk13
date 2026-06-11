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
    // 1. Validasi input utama (opsional, sesuaikan dengan kebutuhanmu)
    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    // 2. Ambil semua input kecuali data subtasks
    $data = $request->except('subtasks');

    // 3. Pastikan user_id ikut disimpan (mengambil ID user yang sedang login)
    $data['user_id'] = auth()->id();

    // 4. Ambil data subtasks dari form (default array kosong jika tidak ada)
    $subtasksInput = $request->input('subtasks', []);

    // 5. Bersihkan subtask yang tidak ada namanya (mencegah row kosong masuk DB)
    $cleanedSubtasks = [];
    foreach ($subtasksInput as $sub) {
        if (!empty($sub['name'])) {
            $cleanedSubtasks[] = [
                'name'     => $sub['name'],
                'date'     => $sub['date'] ?? null,
                'time'     => $sub['time'] ?? null,
                'priority' => $sub['priority'] ?? 'medium',
                'done'     => isset($sub['done']) ? (bool)$sub['done'] : false,
            ];
        }
    }

    // 6. Masukkan array yang sudah bersih ke dalam key 'subtasks'
    $data['subtasks'] = $cleanedSubtasks;

    // 7. Simpan data task baru ke database
    Task::create($data);

    return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
}
    public function update(Request $request, $id)
{
    $task = Task::findOrFail($id);

    // 1. Ambil semua input kecuali subtasks dulu
    $data = $request->except('subtasks');

    // 2. Ambil data subtasks dari form, pastikan dalam bentuk array
    $subtasksInput = $request->input('subtasks', []);

    // 3. Bersihkan data subtask kosong agar tidak mengotori database
    $cleanedSubtasks = [];
    foreach ($subtasksInput as $sub) {
        // Hanya simpan subtask yang minimal punya Nama Tugas
        if (!empty($sub['name'])) {
            $cleanedSubtasks[] = [
                'name'     => $sub['name'],
                'date'     => $sub['date'] ?? null,
                'time'     => $sub['time'] ?? null,
                'priority' => $sub['priority'] ?? 'medium',
                'done'     => isset($sub['done']) ? (bool)$sub['done'] : false,
            ];
        }
    }

    // 4. Masukkan array yang sudah bersih ke dalam key 'subtasks'
    $data['subtasks'] = $cleanedSubtasks;

    // 5. Eksekusi update ke database
    $task->update($data);

    return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
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
