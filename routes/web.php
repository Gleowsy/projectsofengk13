<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\ScheduleController;

Route::get('/', fn () => redirect()->route('login'));

// Auth
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register'])->name('register.post'); // ← ini yang kurang

// Protected
Route::middleware('auth')->group(function () {
    Route::get('/dashboard',      [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/checkin',        [CheckinController::class, 'index'])->name('checkin.index');
    Route::post('/checkin',       [CheckinController::class, 'store'])->name('checkin.store');
    Route::get('/checkin/result', [CheckinController::class, 'result'])->name('result');
});
// schedule
    Route::get('/schedule',             [ScheduleController::class, 'index'])->name('schedule.index');
    Route::get('/schedule/preferences', [ScheduleController::class, 'preferences'])->name('schedule.preferences');
    Route::post('/schedule/preferences/update',[ScheduleController::class, 'updatePreference'])->name('schedule.preferences.update');
    Route::post('/schedule/toggle',     [ScheduleController::class, 'toggle'])->name('schedule.toggle');

    Route::get('/targets',        [TargetController::class, 'index'])->name('targets.index');
    Route::post('/targets/update',[TargetController::class, 'update'])->name('targets.update');