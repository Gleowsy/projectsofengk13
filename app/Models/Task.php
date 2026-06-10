<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected $fillable = [
        'user_id',
        'name',
        'subtasks',
        'subtask_name',  'subtask_date',  'subtask_time',  'subtask_priority',  'subtask_done',
        'subtask2_name', 'subtask2_date', 'subtask2_time', 'subtask2_priority', 'subtask2_done',
        'subtask3_name', 'subtask3_date', 'subtask3_time', 'subtask3_priority', 'subtask3_done',
    ];

    protected $casts = [
        'subtasks'      => 'array',
        'subtask_date'  => 'date',
        'subtask2_date' => 'date',
        'subtask3_date' => 'date',
        'subtask_done'  => 'boolean',
        'subtask2_done' => 'boolean',
        'subtask3_done' => 'boolean',
    ];

    public function formattedSubtasks(): array
    {
        $subtasks = collect($this->subtasks ?: []);

        if ($subtasks->isEmpty()) {
            $subtasks = collect($this->extractLegacySubtasks());
        }

        return $subtasks->map(function ($sub, $index) {
            return [
                'name'     => $sub['name'] ?? null,
                'date'     => !empty($sub['date']) ? (string) $sub['date'] : null,
                'time'     => $sub['time'] ?? null,
                'priority' => $sub['priority'] ?? 'medium',
                'done'     => isset($sub['done']) ? (bool) $sub['done'] : false,
                'index'    => $index + 1,
            ];
        })->filter(function ($sub) {
            return !empty($sub['name']) || !empty($sub['date']) || !empty($sub['time']) || !empty($sub['priority']);
        })->sort(function ($a, $b) {
            if ($a['date'] === $b['date']) {
                if ($a['time'] === $b['time']) {
                    return $this->priorityValue($a['priority']) <=> $this->priorityValue($b['priority']);
                }
                if (empty($a['time'])) return 1;
                if (empty($b['time'])) return -1;
                return strcmp($a['time'], $b['time']);
            }
            if (empty($a['date'])) return 1;
            if (empty($b['date'])) return -1;
            return strcmp($a['date'], $b['date']);
        })->values()->all();
    }

    private function extractLegacySubtasks(): array
    {
        return collect([
            ['name' => $this->subtask_name,  'date' => $this->subtask_date,  'time' => $this->subtask_time,  'priority' => $this->subtask_priority, 'done' => $this->subtask_done ?? false],
            ['name' => $this->subtask2_name, 'date' => $this->subtask2_date, 'time' => $this->subtask2_time, 'priority' => $this->subtask2_priority, 'done' => $this->subtask2_done ?? false],
            ['name' => $this->subtask3_name, 'date' => $this->subtask3_date, 'time' => $this->subtask3_time, 'priority' => $this->subtask3_priority, 'done' => $this->subtask3_done ?? false],
        ])->map(function ($sub) {
            return [
                'name'     => $sub['name'] ?? null,
                'date'     => !empty($sub['date']) ? (string) $sub['date'] : null,
                'time'     => $sub['time'] ?? null,
                'priority' => $sub['priority'] ?? 'medium',
                'done'     => isset($sub['done']) ? (bool) $sub['done'] : false,
            ];
        })->filter(function ($sub) {
            return !empty($sub['name']) || !empty($sub['date']) || !empty($sub['time']) || !empty($sub['priority']);
        })->values()->all();
    }

    private function priorityValue(string $priority): int
    {
        return match ($priority) {
            'high' => 1,
            'medium' => 2,
            'low' => 3,
            default => 4,
        };
    }
}
