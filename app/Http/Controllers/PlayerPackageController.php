<?php

namespace App\Http\Controllers;

use App\Models\PlayerPackage;
use App\Models\Package;
use App\Models\Player;
use App\Models\PlayRecord;
use Carbon\Carbon;
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
            'package_id' => 'required|exists:packages,id',
            'total_amount' => 'required|integer|min:0',
            'package_status' => 'required|in:not played,started,completed,inactive',
            'remarks' => 'nullable|string',
        ]);

        $playerPackage->update($request->only(['package_id', 'total_amount', 'package_status', 'remarks']));

        return redirect()->route('players.show', $playerPackage->player_id)->with('success', 'Player package updated successfully.');
    }

    public function destroy(Request $request, PlayerPackage $playerPackage)
    {
        $playerId = $playerPackage->player_id;
        
        if ($playerPackage->payments()->exists() || $playerPackage->playRecords()->exists()) {
            if ($request->input('delete_password') !== date('dmY')) {
                return redirect()->route('players.show', $playerId)->with('error', 'Cannot delete player package because it has associated payments or play sessions. Provide the correct password to force delete.');
            }
            
            $playerPackage->payments()->delete();
            $playerPackage->playRecords()->delete();
        }
        
        $playerPackage->delete();

        return redirect()->route('players.show', $playerId)->with('success', 'Player package removed successfully.');
    }

    /**
     * Return JSON history of a specific purchased player package and its play records.
     */
    public function history(PlayerPackage $playerPackage)
    {
        $playerPackage->load([
            'player',
            'package.gameType',
            'playRecords.token',
            'playRecords.gameType',
        ]);

        return response()->json([
            'success' => true,
            'player' => [
                'id' => $playerPackage->player->id,
                'name' => $playerPackage->player->name,
                'contact' => $playerPackage->player->contact,
            ],
            'packages' => [$playerPackage->toHistoryArray()],
        ]);
    }

    /**
     * Mark a player package as inactive so it no longer appears in the active package play list.
     */
    public function deactivate(PlayerPackage $playerPackage)
    {
        $playerPackage->update(['package_status' => 'inactive']);

        return response()->json([
            'success' => true,
            'message' => __('Player package marked as inactive.'),
        ]);
    }

    /**
     * Update start and end date-time for a package play record and recalculate duration/package status.
     */
    public function updatePlayRecordTimes(Request $request, PlayRecord $playRecord)
    {
        if ($playRecord->player_type !== 'package' || !$playRecord->player_package_id) {
            return response()->json([
                'success' => false,
                'message' => __('Only package play records can be updated here.'),
            ], 422);
        }

        $validated = $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
        ]);

        $startTime = Carbon::parse($validated['start_time']);
        $endTime = !empty($validated['end_time']) ? Carbon::parse($validated['end_time']) : null;
        $actualMinutes = $endTime ? PlayRecord::calculateDurationMinutes($startTime, $endTime) : null;

        $playRecord->update([
            'start_time' => $startTime,
            'end_time' => $endTime,
            'actual_time' => $actualMinutes,
        ]);

        $playerPackage = $playRecord->playerPackage()->with(['package', 'playRecords'])->first();
        if ($playerPackage && $playerPackage->package_status !== 'inactive') {
            $totalPlayedMinutes = $playerPackage->playRecords
                ->whereNotNull('end_time')
                ->sum(fn ($rec) => $rec->resolvedActualMinutes() ?? 0);
            $allowedMinutes = ($playerPackage->package?->time_per_day ?? 0) * ($playerPackage->package?->no_of_days ?? 0);
            $hasStarted = $playerPackage->playRecords->contains(fn ($rec) => $rec->start_time !== null);

            if ($allowedMinutes > 0 && $totalPlayedMinutes >= $allowedMinutes) {
                $newStatus = 'completed';
            } elseif ($hasStarted) {
                $newStatus = 'started';
            } else {
                $newStatus = 'not played';
            }

            if ($playerPackage->package_status !== $newStatus) {
                $playerPackage->update(['package_status' => $newStatus]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => __('Play record time updated successfully.'),
        ]);
    }
}
