<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\GameType;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('gameType')->get();
        return view('skatepark.packages.index', compact('packages'));
    }

    public function create()
    {
        $gameTypes = GameType::all();
        return view('skatepark.packages.create', compact('gameTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'game_type_id' => 'required|exists:game_types,id',
            'time_per_day' => 'required|integer|min:1',
            'no_of_days' => 'required|integer|min:1',
            'amount' => 'required|integer|min:0',
        ]);

        Package::create($request->only(['game_type_id', 'time_per_day', 'no_of_days', 'amount']));

        return redirect()->route('packages.index')->with('success', 'Package created successfully.');
    }

    public function edit(Package $package)
    {
        $gameTypes = GameType::all();
        return view('skatepark.packages.edit', compact('package', 'gameTypes'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'game_type_id' => 'required|exists:game_types,id',
            'time_per_day' => 'required|integer|min:1',
            'no_of_days' => 'required|integer|min:1',
            'amount' => 'required|integer|min:0',
        ]);

        $package->update($request->only(['game_type_id', 'time_per_day', 'no_of_days', 'amount']));

        return redirect()->route('packages.index')->with('success', 'Package updated successfully.');
    }

    public function destroy(Package $package)
    {
        if ($package->playerPackages()->exists()) {
            return back()->with('error', 'Cannot delete Package because players have already subscribed to it.');
        }
        $package->delete();
        return redirect()->route('packages.index')->with('success', 'Package deleted successfully.');
    }
}
