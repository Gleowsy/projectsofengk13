<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckinController;

use App\Http\Controllers\TaskController;
Route::get('/', fn () => redirect()->route('login'));


Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register'])->name('register.post'); // ← ini yang kurang


Route::middleware('auth')->group(function () {
    Route::get('/dashboard',      [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/checkin',        [CheckinController::class, 'index'])->name('checkin.index');
    Route::post('/checkin',       [CheckinController::class, 'store'])->name('checkin.store');
    Route::get('/checkin/result', [CheckinController::class, 'result'])->name('result');
   
    Route::get('/Task',           [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks/store',   [TaskController::class, 'store'])->name('tasks.store');
Route::get('/schedule', function () {
    return 'Halaman Schedule Belum Dibuat';
})->name('schedule.index');
});
