<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\GameType;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function index()
    {
        $tokens = Token::with('gameType')->orderBy('display_order')->get();
        return view('skatepark.tokens.index', compact('tokens'));
    }

    public function create()
    {
        $gameTypes = GameType::all();
        return view('skatepark.tokens.create', compact('gameTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'game_type_id' => 'required|exists:game_types,id',
            'display_order' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ]);

        Token::create([
            'name' => $request->name,
            'game_type_id' => $request->game_type_id,
            'display_order' => $request->display_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('tokens.index')->with('success', 'Token created successfully.');
    }

    public function edit(Token $token)
    {
        $gameTypes = GameType::all();
        return view('skatepark.tokens.edit', compact('token', 'gameTypes'));
    }

    public function update(Request $request, Token $token)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'game_type_id' => 'required|exists:game_types,id',
            'display_order' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $token->update([
            'name' => $request->name,
            'game_type_id' => $request->game_type_id,
            'display_order' => $request->display_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('tokens.index')->with('success', 'Token updated successfully.');
    }

    public function destroy(Token $token)
    {
        if ($token->playRecords()->exists()) {
            return back()->with('error', 'Cannot delete Token because it has associated play records.');
        }
        $token->delete();
        return redirect()->route('tokens.index')->with('success', 'Token deleted successfully.');
    }
}
