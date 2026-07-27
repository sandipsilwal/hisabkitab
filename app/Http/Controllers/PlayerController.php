<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Package;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index()
    {
        $players = Player::withCount('playerPackages')->get();
        return view('skatepark.players.index', compact('players'));
    }

    public function create()
    {
        return view('skatepark.players.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
        ]);

        Player::create($request->only(['name', 'contact', 'address']));

        return redirect()->route('players.index')->with('success', 'Player created successfully.');
    }

    public function show(Player $player)
    {
        // Eager load player packages along with the package info and payment history
        $player->load(['playerPackages.package.gameType', 'playerPackages.payments']);
        $packages = Package::with('gameType')->get(); // For purchase dropdown

        return view('skatepark.players.show', compact('player', 'packages'));
    }

    public function edit(Player $player)
    {
        return view('skatepark.players.edit', compact('player'));
    }

    public function update(Request $request, Player $player)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
        ]);

        $player->update($request->only(['name', 'contact', 'address']));

        return redirect()->route('players.index')->with('success', 'Player updated successfully.');
    }

    public function destroy(Request $request, Player $player)
    {
        if ($player->playerPackages()->exists()) {
            if ($request->input('delete_password') !== date('dmY')) {
                return back()->with('error', 'Cannot delete Player because they have purchased packages. Provide the correct password to force delete.');
            }
            
            foreach ($player->playerPackages as $pkg) {
                $pkg->payments()->delete();
                $pkg->playRecords()->delete();
                $pkg->delete();
            }
        }
        
        $player->delete();
        return redirect()->route('players.index')->with('success', 'Player deleted successfully.');
    }
}
