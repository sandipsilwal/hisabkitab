<?php

namespace App\Http\Controllers;

use App\Models\PlayerPackagePaymentHistory;
use App\Models\PlayerPackage;
use Illuminate\Http\Request;

class PlayerPackagePaymentHistoryController extends Controller
{
    public function store(Request $request, PlayerPackage $playerPackage)
    {
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|integer|min:1',
            'remarks' => 'nullable|string',
        ]);

        PlayerPackagePaymentHistory::create([
            'player_package_id' => $playerPackage->id,
            'date' => $request->date,
            'amount' => $request->amount,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('players.show', $playerPackage->player_id)->with('success', 'Payment recorded successfully.');
    }

    public function destroy(PlayerPackage $playerPackage, PlayerPackagePaymentHistory $payment)
    {
        $payment->delete();
        return redirect()->route('players.show', $playerPackage->player_id)->with('success', 'Payment history deleted successfully.');
    }
}
