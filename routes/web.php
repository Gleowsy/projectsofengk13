<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

//halaman login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

//buat proses login
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

//Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//Register
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/', function () {
    return view('login');
});
