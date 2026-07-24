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
            'is_default' => 'nullable|boolean',
        ]);

        $isDefault = $request->boolean('is_default');

        if ($isDefault || GameType::count() === 0) {
            GameType::query()->update(['is_default' => false]);
            $isDefault = true;
        }

        GameType::create([
            'game_name' => $request->game_name,
            'is_default' => $isDefault,
        ]);

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
            'is_default' => 'nullable|boolean',
        ]);

        $isDefault = $request->boolean('is_default');

        if ($isDefault) {
            GameType::where('id', '!=', $gameType->id)->update(['is_default' => false]);
        }

        $gameType->update([
            'game_name' => $request->game_name,
            'is_default' => $isDefault,
        ]);

        if (!GameType::where('is_default', true)->exists()) {
            GameType::first()?->update(['is_default' => true]);
        }

        return redirect()->route('game_types.index')->with('success', 'Game Type updated successfully.');
    }

    public function destroy(GameType $gameType)
    {
        if ($gameType->tokens()->exists() || $gameType->rates()->exists() || $gameType->packages()->exists()) {
            return back()->with('error', 'Cannot delete Game Type because it has associated tokens, rates, or packages.');
        }

        $wasDefault = $gameType->is_default;
        $gameType->delete();

        if ($wasDefault) {
            GameType::first()?->update(['is_default' => true]);
        }

        return redirect()->route('game_types.index')->with('success', 'Game Type deleted successfully.');
    }
}
