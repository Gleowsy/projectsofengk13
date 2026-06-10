<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('email', 'test@example.com')->first();
if (!$user) {
    echo "User not found\n";
    exit;
}

// Create multiple tasks with subtasks on the same date
$today = \Carbon\Carbon::today()->toDateString();

// Task 1
$task1 = App\Models\Task::create([
    'user_id' => $user->id,
    'name' => 'Test Task 1',
    'subtasks' => [
        ['name' => 'Subtask 1.1', 'date' => $today, 'time' => '09:00', 'priority' => 'high', 'done' => false],
        ['name' => 'Subtask 1.2', 'date' => $today, 'time' => '10:00', 'priority' => 'medium', 'done' => false],
    ],
]);

// Task 2
$task2 = App\Models\Task::create([
    'user_id' => $user->id,
    'name' => 'Test Task 2',
    'subtasks' => [
        ['name' => 'Subtask 2.1', 'date' => $today, 'time' => '11:00', 'priority' => 'medium', 'done' => false],
        ['name' => 'Subtask 2.2', 'date' => $today, 'time' => '12:00', 'priority' => 'low', 'done' => false],
    ],
]);

// Task 3
$task3 = App\Models\Task::create([
    'user_id' => $user->id,
    'name' => 'Test Task 3',
    'subtasks' => [
        ['name' => 'Subtask 3.1', 'date' => $today, 'time' => '13:00', 'priority' => 'low', 'done' => false],
    ],
]);

echo "Created 3 tasks with 5 subtasks on $today\n";
echo "This should trigger the popup\n";
