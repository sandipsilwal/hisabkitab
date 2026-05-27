<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use App\Models\GameType;
use App\Models\DefaultTime;
use Illuminate\Http\Request;

class RateController extends Controller
{
    public function index()
    {
        $rates = Rate::with(['gameType', 'defaultTime'])->get();
        return view('skatepark.rates.index', compact('rates'));
    }

    public function create()
    {
        $gameTypes = GameType::all();
        $defaultTimes = DefaultTime::orderBy('display_order')->get();
        return view('skatepark.rates.create', compact('gameTypes', 'defaultTimes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'game_type_id' => 'required|exists:game_types,id',
            'default_time_id' => 'required|exists:default_times,id',
            'rate' => 'required|integer|min:0',
        ]);

        // Check if rate already exists for this combination
        $exists = Rate::where('game_type_id', $request->game_type_id)
            ->where('default_time_id', $request->default_time_id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'A rate already exists for this Game Type and Duration combination.');
        }

        Rate::create($request->only(['game_type_id', 'default_time_id', 'rate']));

        return redirect()->route('rates.index')->with('success', 'Rate created successfully.');
    }

    public function edit(Rate $rate)
    {
        $gameTypes = GameType::all();
        $defaultTimes = DefaultTime::orderBy('display_order')->get();
        return view('skatepark.rates.edit', compact('rate', 'gameTypes', 'defaultTimes'));
    }

    public function update(Request $request, Rate $rate)
    {
        $request->validate([
            'game_type_id' => 'required|exists:game_types,id',
            'default_time_id' => 'required|exists:default_times,id',
            'rate' => 'required|integer|min:0',
        ]);

        // Check if another rate already exists for this combination
        $exists = Rate::where('game_type_id', $request->game_type_id)
            ->where('default_time_id', $request->default_time_id)
            ->where('id', '!=', $rate->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Another rate already exists for this Game Type and Duration combination.');
        }

        $rate->update($request->only(['game_type_id', 'default_time_id', 'rate']));

        return redirect()->route('rates.index')->with('success', 'Rate updated successfully.');
    }

    public function destroy(Rate $rate)
    {
        $rate->delete();
        return redirect()->route('rates.index')->with('success', 'Rate deleted successfully.');
    }
}
