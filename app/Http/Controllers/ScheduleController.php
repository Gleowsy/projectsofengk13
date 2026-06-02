<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimePreference;

class ScheduleController extends Controller
{
    public function index()
    {
        return view('schedule');
    }

    public function preferences()
    {
        $defaults = [
            'sleep'     => ['label' => 'Optimal Sleep Time', 'start' => '20.00', 'end' => '09.00'],
            'lunch'     => ['label' => 'Lunch',              'start' => '12.00', 'end' => '13.00'],
            'breakfast' => ['label' => 'Breakfast',          'start' => '09.00', 'end' => '10.00'],
            'dinner'    => ['label' => 'Dinner',             'start' => '17.00', 'end' => '19.00'],
            'focus'     => ['label' => 'Focus',              'start' => '14.00', 'end' => '17.00'],
        ];

        $prefs = TimePreference::where('user_id', auth()->id())->get()
            ->keyBy('key')
            ->map(fn($p, $k) => [
                'label' => $defaults[$k]['label'],
                'start' => str_replace(':', '.', $p->start_time),
                'end'   => str_replace(':', '.', $p->end_time),
            ])
            ->toArray();

        $preferences = array_merge($defaults, $prefs);

        return view('schedule-preferences', compact('preferences'));
    }

    public function updatePreference(Request $request)
    {
        $request->validate([
            'key'   => 'required|string|in:sleep,lunch,breakfast,dinner,focus',
            'start' => 'required',
            'end'   => 'required',
        ]);

        TimePreference::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'key'     => $request->key,
            ],
            [
                'start_time' => $request->start,
                'end_time'   => $request->end,
            ]
        );

        return response()->json(['success' => true]);
    }

    public function toggle(Request $request)
    {
        return response()->json(['success' => true]);
    }
}