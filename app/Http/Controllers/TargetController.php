<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Target;

class TargetController extends Controller
{
    public function index()
    {
        $saved = Target::where('user_id', auth()->id())
            ->pluck('content', 'key')
            ->toArray();

        $targets = [
            'daily'  => ['label' => 'Daily',  'content' => $saved['daily']  ?? 'No target set yet.'],
            'weekly' => ['label' => 'Weekly', 'content' => $saved['weekly'] ?? 'No target set yet.'],
        ];

        return view('targets', compact('targets'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'key'     => 'required|string|in:daily,weekly',
            'content' => 'required|string',
        ]);

        Target::updateOrCreate(
            ['user_id' => auth()->id(), 'key' => $request->key],
            ['content' => $request->content]
        );

        return response()->json(['success' => true]);
    }
}