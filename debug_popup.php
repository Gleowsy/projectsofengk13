<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::first();
if ($user) {
    echo "User: " . $user->email . "\n";
    $tasks = \App\Models\Task::where('user_id', $user->id)->get();
    echo "Total tasks: " . count($tasks) . "\n";
    
    $dateCounts = [];
    foreach ($tasks as $task) {
        foreach ($task->formattedSubtasks() as $sub) {
            if (empty($sub['name']) || empty($sub['date'])) {
                continue;
            }
            $date = \Carbon\Carbon::parse($sub['date'])->toDateString();
            $dateCounts[$date] = ($dateCounts[$date] ?? 0) + 1;
        }
    }
    
    echo "Task counts by date:\n";
    foreach ($dateCounts as $date => $count) {
        echo "  $date: $count tasks\n";
    }
    
    echo "\nDates with >6 tasks:\n";
    $hasOverload = false;
    foreach ($dateCounts as $date => $count) {
        if ($count > 6) {
            echo "  $date: $count tasks (POPUP SHOULD SHOW)\n";
            $hasOverload = true;
        }
    }
    
    if (!$hasOverload) {
        echo "  NONE - No dates with >6 tasks, so popup will NOT show\n";
    }
} else {
    echo "No user found\n";
}
