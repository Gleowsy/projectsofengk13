<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyCheckin;
use Carbon\Carbon;

class InsightController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $start  = Carbon::now()->startOfWeek(Carbon::SUNDAY);
        $end    = Carbon::now()->endOfWeek(Carbon::SATURDAY);

        $checkins = DailyCheckin::where('user_id', $userId)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')
            ->get()
            ->keyBy(fn($c) => Carbon::parse($c->date)->dayOfWeek);

        // Bangun data per hari (0=Sun ... 6=Sat)
        $energyData = [];
        $focusData  = [];

        for ($dow = 0; $dow <= 6; $dow++) {
            $c = $checkins->get($dow);
            $energyData[] = $c ? $c->energy_level    : 0;
            $focusData[]  = $c ? $c->focus_level      : 0;
        }

        // Most productive = hari dengan motivation tertinggi
        $mostProductive = $this->bestDay($checkins, 'motivation');

        // Most focused = hari dengan focus_level tertinggi
        $mostFocused = $this->bestDay($checkins, 'focus_level');

        // Most energetic = hari dengan energy_level tertinggi
        $mostEnergetic = $this->bestDay($checkins, 'energy_level');

        return view('insights', compact(
            'energyData', 'focusData',
            'mostProductive', 'mostFocused', 'mostEnergetic'
        ));
    }

    private function bestDay($checkins, string $field): string
    {
        if ($checkins->isEmpty()) return 'No data';

        $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        $best = $checkins->sortByDesc($field)->first();

        return $best ? $days[Carbon::parse($best->date)->dayOfWeek] : 'No data';
    }
}