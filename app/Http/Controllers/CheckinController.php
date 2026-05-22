<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyCheckin;

class CheckinController extends Controller
{

    public function index()
    {
        return view('checkin');
    }

    public function store(Request $request)
    {
        $request->validate([
            'energy_level'   => 'required|integer|min:1|max:5',
            'focus_level'    => 'required|integer|min:1|max:5',
            'mood'           => 'required|integer|min:1|max:5',
            'motivation'     => 'required|integer|min:1|max:5',
            'available_time' => 'required|integer|min:1|max:5',
            'stress_level'   => 'required|integer|min:1|max:5',
        ]);

        DailyCheckin::create([
            'user_id'        => auth()->id(),
            'energy_level'   => $request->energy_level,
            'focus_level'    => $request->focus_level,
            'mood'           => $request->mood,
            'motivation'     => $request->motivation,
            'available_time' => $request->available_time,
            'stress_level'   => $request->stress_level,
            'date'           => today(),
        ]);

        //  Hitung skor rata-rata
        $score = (
            $request->energy_level +
            $request->focus_level  +
            $request->mood         +
            $request->motivation   +
            $request->available_time +
            (6 - $request->stress_level) // invert stress
        ) / 6;

        //Tentukan kondisi berdasarkan skor
        if ($score <= 2) {
            $condition = ['level' => 'low',    'label' => 'Low Energy',    'class' => 'low'];
        } elseif ($score <= 3.5) {
            $condition = ['level' => 'medium', 'label' => 'Medium Energy', 'class' => 'medium'];
        } else {
            $condition = ['level' => 'high',   'label' => 'High Energy',   'class' => 'high'];
        }

        // Simpan ke session supaya bisa diakses di halaman result
        session(['checkin_condition' => $condition]);

        return redirect()->route('result');
    }

    public function result()
    {

        $condition = session('checkin_condition');

        if (!$condition) {
            return redirect()->route('checkin.index');
        }

        return view('result', compact('condition'));
    }
}
