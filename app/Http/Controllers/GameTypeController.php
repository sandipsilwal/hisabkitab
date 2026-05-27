<?php

namespace App\Http\Controllers;

use App\Models\GameType;
use Illuminate\Http\Request;

class GameTypeController extends Controller
{
    public function index()
    {
        $gameTypes = GameType::all();
        return view('skatepark.game_types.index', compact('gameTypes'));
    }

    public function create()
    {
        return view('skatepark.game_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'game_name' => 'required|string|max:255',
        ]);

        GameType::create($request->only('game_name'));

        return redirect()->route('game_types.index')->with('success', 'Game Type created successfully.');
    }

    public function edit(GameType $gameType)
    {
        return view('skatepark.game_types.edit', compact('gameType'));
    }

    public function update(Request $request, GameType $gameType)
    {
        $request->validate([
            'game_name' => 'required|string|max:255',
        ]);

        $gameType->update($request->only('game_name'));

        return redirect()->route('game_types.index')->with('success', 'Game Type updated successfully.');
    }

    public function destroy(GameType $gameType)
    {
        if ($gameType->tokens()->exists() || $gameType->rates()->exists() || $gameType->packages()->exists()) {
            return back()->with('error', 'Cannot delete Game Type because it has associated tokens, rates, or packages.');
        }
        $gameType->delete();
        return redirect()->route('game_types.index')->with('success', 'Game Type deleted successfully.');
    }
}
