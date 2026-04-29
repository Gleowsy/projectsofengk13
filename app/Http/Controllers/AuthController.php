<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //halaman login
    public function showLogin()
    {
        return view('login');
    }

    //buat proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    //buat halaman register
    public function showRegister()
    {
        return view('register'); // sesuaikan path jika di subfolder
    }

    //buat proses register
    public function register(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        //Buat user baru
        \App\Models\User::create([
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth::attempt($request->only('email', 'password'));
        return redirect('/dashboard');
    }

    //Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}