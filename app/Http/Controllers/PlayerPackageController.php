<?php

namespace App\Http\Controllers;

use App\Models\PlayerPackage;
use App\Models\Package;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerPackageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'player_id' => 'required|exists:players,id',
            'package_id' => 'required|exists:packages,id',
            'total_amount' => 'required|integer|min:0',
            'remarks' => 'nullable|string',
        ]);

        PlayerPackage::create([
            'player_id' => $request->player_id,
            'package_id' => $request->package_id,
            'total_amount' => $request->total_amount,
            'package_status' => 'not played',
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('players.show', $request->player_id)->with('success', 'Package purchased successfully.');
    }

    public function update(Request $request, PlayerPackage $playerPackage)
    {
        $request->validate([
            'total_amount' => 'required|integer|min:0',
            'package_status' => 'required|in:not played,started,completed',
            'remarks' => 'nullable|string',
        ]);

        $playerPackage->update($request->only(['total_amount', 'package_status', 'remarks']));

        return redirect()->route('players.show', $playerPackage->player_id)->with('success', 'Player package updated successfully.');
    }

    public function destroy(PlayerPackage $playerPackage)
    {
        $playerId = $playerPackage->player_id;
        if ($playerPackage->payments()->exists() || $playerPackage->playRecords()->exists()) {
            return redirect()->route('players.show', $playerId)->with('error', 'Cannot delete player package because it has associated payments or play sessions.');
        }
        $playerPackage->delete();

        return redirect()->route('players.show', $playerId)->with('success', 'Player package removed successfully.');
    }
}
