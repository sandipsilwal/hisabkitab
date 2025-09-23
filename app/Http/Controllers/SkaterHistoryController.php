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
        try {
            // Update playing skaters whose session has expired to over_time
            SkaterHistory::where('status', 'playing')
                ->whereNotNull('start_time')
                ->whereRaw('DATE_ADD(start_time, INTERVAL session_minutes MINUTE) < NOW()')
                ->update(['status' => 'over_time']);

            return view('current_session', ['partialView' => $this->getPartialView()]);
        } catch (\Exception $e) {
            Log::error('Error in index method: ' . $e->getMessage());
            return view('current_session', ['partialView' => $this->getPartialView()])
                ->with('error', 'An error occurred while loading the session.');
        }
    }

    public function getPartialView()
    {
        $activeSkaters = SkaterHistory::where('status', 'active')->with('skater')->get();
        $playingSkaters = SkaterHistory::where('status', 'playing')->with('skater')->get();
        $overTimeSkaters = SkaterHistory::where('status', 'over_time')->with('skater')->get();


        // dd($activeSkaters,$playingSkaters,$overTimeSkaters);
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

            return redirect()->route('skaters.index');
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
            if (!$skaterHistory->end_time) {
                $skaterHistory->update(['end_time' => now(), 'status' => 'completed']);
                return response()->json(['success' => true, 'partialView' => $this->getPartialView()]);
            }
            return response()->json(['success' => false, 'message' => 'Cannot stop timer'], 400);
        } catch (\Exception $e) {
            Log::error('Error stopping skater timer: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to stop timer'], 500);
        }
    }
    public function overTime(Request $request, $id)
    {
        try {
            $skaterHistory = SkaterHistory::findOrFail($id);
            if (!$skaterHistory->end_time) {
                $skaterHistory->update(['status' => 'over_time']);
                return response()->json(['success' => true, 'partialView' => $this->getPartialView()]);
            }
            return response()->json(['success' => false, 'message' => 'Overtime issue'], 400);
        } catch (\Exception $e) {
            Log::error('Error stopping skater timer: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function complete(Request $request, $id)
    {
            return response()->json(['success' => true, 'partialView' => $this->getPartialView()]);
    }
}