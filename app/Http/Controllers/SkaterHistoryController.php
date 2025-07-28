<?php

namespace App\Http\Controllers;

use App\Models\Skater;
use App\Models\SkaterHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SkaterHistoryController extends Controller
{
    public function index()
    {
        return view('current_session', ['partialView' => $this->getPartialView()]);
    }

    public function getPartialView()
    {
        $activeSkaters = SkaterHistory::where('status', 'active')->with('skater')->get();
        $playingSkaters = SkaterHistory::where('status', 'playing')->with('skater')->get();
        $overTimeSkaters = SkaterHistory::where('status', 'playing')
            ->whereRaw("TIMESTAMPADD(MINUTE, session_minutes, start_time) < ?", [Carbon::now()])
            ->with('skater')
            ->get();
        $partialView = view('partial_current_session', compact('activeSkaters', 'playingSkaters', 'overTimeSkaters'))->render();
        return $partialView;
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'session_minutes' => 'required|integer|min:1',
                'amount' => 'required|integer',
                'no_of_skaters' => 'required|integer|min:1',
                'payment_method' => 'required|in:online,cash,unpaid',
            ]);
            $maxMembership = Skater::orderBy('membership_no', 'desc')->first();
            $skater = Skater::create([
                'name' => $request->name,
                'membership_no' => $maxMembership ? $maxMembership->membership_no + 1 : 1
            ]);
            $skaterHistory = SkaterHistory::create([
                'skater_id' => $skater->id,
                'session_minutes' => $request->session_minutes,
                'no_of_skaters' => $request->no_of_skaters,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'status' => 'active'
            ]);

            return response()->json(['success' => true, 'partialView' => $this->getPartialView()]);
        } catch (\Exception $e) {
            Log::error('Error adding skater: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to add skater'], 500);
        }
    }

    public function start(Request $request, $id)
    {
        try {
            $skaterHistory = SkaterHistory::findOrFail($id);
            if ($skaterHistory->status === 'active' && !$skaterHistory->start_time) {
                $skaterHistory->update(['start_time' => now(), 'status' => 'playing']);
                return response()->json(['success' => true, 'partialView' => $this->getPartialView()]);
            }
            return response()->json(['success' => false, 'message' => 'Cannot start timer'], 400);
        } catch (\Exception $e) {
            Log::error('Error starting skater timer: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to start timer'], 500);
        }
    }

    public function stop(Request $request, $id)
    {
        try {
            $skaterHistory = SkaterHistory::findOrFail($id);
            if ($skaterHistory->status === 'playing' && !$skaterHistory->end_time) {
                $skaterHistory->update(['end_time' => now(), 'status' => 'completed']);
                return response()->json(['success' => true, 'partialView' => $this->getPartialView()]);
            }
            return response()->json(['success' => false, 'message' => 'Cannot stop timer'], 400);
        } catch (\Exception $e) {
            Log::error('Error stopping skater timer: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to stop timer'], 500);
        }
    }

    public function complete(Request $request, $id)
    {
            return response()->json(['success' => true, 'partialView' => $this->getPartialView()]);
    }
}