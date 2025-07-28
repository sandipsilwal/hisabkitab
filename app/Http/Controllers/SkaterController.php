<?php

namespace App\Http\Controllers;

use App\Models\Skater;
use App\Models\SkaterHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SkaterController extends Controller
{
    public function index()
    {
        $activeSkaters = SkaterHistory::where('status', 'active')->get();
        $completedSkaters = SkaterHistory::where('status', 'completed')->get();
        return response()->json([
            'active' => $activeSkaters,
            'completed' => $completedSkaters
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'session_minutes' => 'required|integer|min:1'
            ]);
            $maxMembership = Skater::orderBy('membership_no', 'desc')->first();
            $skater = Skater::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'age' => $request->age,
                'gender' => $request->gender,
                'shoes_size' => $request->shoes_size,
                'membership_no' => $maxMembership->membership_no+1
            ]);
            $skaterHistory = SkaterHistory::create([
                'skater_id' => $skater->id,
                'session_minutes' => $request->session_minutes,
                'no_of_skaters' => $request->no_of_skaters,
                'amount' => $request->amount,
                'payment_status' => $request->payment_status,
                'status' => 'active'
            ]);

            return response()->json(['success' => true, 'skater' => $skater]);
        } catch (\Exception $e) {
            Log::error('Error adding skater: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to add skater'], 500);
        }
    }

    public function start(Request $request, $id)
    {
        try {
            $skater = Skater::findOrFail($id);
            if ($skater->status === 'active' && !$skater->start_time) {
                $skater->update(['start_time' => now()]);
                return response()->json(['success' => true, 'skater' => $skater]);
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
            $skater = Skater::findOrFail($id);
            if ($skater->status === 'completed' && !$skater->end_time) {
                $skater->update(['end_time' => now()]);
                return response()->json(['success' => true, 'skater' => $skater]);
            }
            return response()->json(['success' => false, 'message' => 'Cannot stop timer'], 400);
        } catch (\Exception $e) {
            Log::error('Error stopping skater timer: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to stop timer'], 500);
        }
    }
}