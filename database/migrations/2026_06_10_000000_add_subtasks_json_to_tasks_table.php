<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->json('subtasks')->nullable()->after('name');
        });

        DB::table('tasks')->orderBy('id')->chunk(100, function ($tasks) {
            foreach ($tasks as $task) {
                $subtasks = [];

                $subtasks[] = [
                    'name' => $task->subtask_name,
                    'date' => $task->subtask_date,
                    'time' => $task->subtask_time,
                    'priority' => $task->subtask_priority,
                    'done' => $task->subtask_done ?? false,
                ];
                $subtasks[] = [
                    'name' => $task->subtask2_name,
                    'date' => $task->subtask2_date,
                    'time' => $task->subtask2_time,
                    'priority' => $task->subtask2_priority,
                    'done' => $task->subtask2_done ?? false,
                ];
                $subtasks[] = [
                    'name' => $task->subtask3_name,
                    'date' => $task->subtask3_date,
                    'time' => $task->subtask3_time,
                    'priority' => $task->subtask3_priority,
                    'done' => $task->subtask3_done ?? false,
                ];

                $subtasks = array_values(array_filter($subtasks, function ($sub) {
                    return !empty($sub['name']) || !empty($sub['date']) || !empty($sub['time']) || !empty($sub['priority']);
                }));

                if (!empty($subtasks)) {
                    DB::table('tasks')->where('id', $task->id)->update([
                        'subtasks' => json_encode($subtasks),
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('subtasks');
        });
    }
};
