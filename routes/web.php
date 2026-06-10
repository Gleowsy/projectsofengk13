<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\InsightController;

Route::get('/', fn () => redirect()->route('login'));

// Auth
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register'])->name('register.post');

// Protected
Route::middleware('auth')->group(function () {
    Route::get('/dashboard',      [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/checkin',        [CheckinController::class, 'index'])->name('checkin.index');
    Route::post('/checkin',       [CheckinController::class, 'store'])->name('checkin.store');
    Route::get('/checkin/result', [CheckinController::class, 'result'])->name('result');
    Route::post('/checkin/dismiss-warning', [CheckinController::class, 'dismissWarning'])->name('checkin.dismiss_warning');
    Route::post('/checkin/apply-schedule', [CheckinController::class, 'applySchedule'])->name('checkin.apply_schedule');

    // Task
    Route::get('/task',           [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks/store',   [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}',   [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Schedule
    Route::get('/schedule',                    [ScheduleController::class, 'index'])->name('schedule.index');
    Route::get('/schedule/preferences',        [ScheduleController::class, 'preferences'])->name('schedule.preferences');
    Route::post('/schedule/preferences/update',[ScheduleController::class, 'updatePreference'])->name('schedule.preferences.update');
    Route::post('/schedule/toggle',            [ScheduleController::class, 'toggle'])->name('schedule.toggle');
    Route::get('schedule/tasks', [ScheduleController::class, 'tasksByDate'])->name('schedule.tasks');
    Route::post('schedule/reschedule', [ScheduleController::class, 'rescheduleTask'])->name('schedule.reschedule');
    Route::post('/schedule/done', [ScheduleController::class, 'markDone'])->name('schedule.done');


    // Targets
    Route::get('/targets',        [TargetController::class, 'index'])->name('targets.index');
    Route::post('/targets/update',[TargetController::class, 'update'])->name('targets.update');

    // Adaptive Productivity (was Insights)
    Route::get('/insights', [InsightController::class, 'index'])->name('insights.index');

    // Settings (sementara)
    Route::get('/settings', function () {
        return view('dashboard');
    })->name('settings.index');
});
