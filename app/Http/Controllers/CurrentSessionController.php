<?php

namespace App\Http\Controllers;

use App\Models\PlayRecord;
use App\Models\Token;
use App\Models\DefaultTime;
use App\Models\PaymentType;
use App\Models\PlayerPackage;
use App\Models\Rate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CurrentSessionController extends Controller
{
    /**
     * Display the current session dashboard.
     */
    public function index()
    {
        // Load active tokens
        $tokens = Token::where('is_active', true)->with('gameType')->orderBy('display_order')->get();
        // Load default times
        $defaultTimes = DefaultTime::orderBy('display_order')->get();
        // Load payment types
        $paymentTypes = PaymentType::all();
        // Find default payment type
        $defaultPaymentType = PaymentType::where('is_default', true)->first() ?? PaymentType::first();

        // Load active player packages (uncompleted)
        $playerPackages = PlayerPackage::where('package_status', '!=', 'completed')
            ->with(['player', 'package.gameType'])
            ->get();

        return view('skatepark.current_session.index', compact(
            'tokens',
            'defaultTimes',
            'paymentTypes',
            'defaultPaymentType',
            'playerPackages'
        ));
    }

    /**
     * Retrieve all active sessions in JSON format.
     */
    public function getSessionData()
    {
        $activeRecords = PlayRecord::whereNull('end_time')
            ->with(['token.gameType', 'playerPackage.player', 'paymentType'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $activeRecords,
            'server_time' => Carbon::now()->toIso8601String()
        ]);
    }

    /**
     * AJAX endpoint to lookup the rate of a game type & default time combination.
     */
    public function lookupRate(Request $request)
    {
        $request->validate([
            'game_type_id' => 'required|exists:game_types,id',
            'default_time_id' => 'nullable|exists:default_times,id',
        ]);

        if (!$request->default_time_id) {
            return response()->json(['success' => true, 'rate' => 0]);
        }

        $rate = Rate::where('game_type_id', $request->game_type_id)
            ->where('default_time_id', $request->default_time_id)
            ->first();

        return response()->json([
            'success' => true,
            'rate' => $rate ? $rate->rate : 0
        ]);
    }

    /**
     * AJAX endpoint to store a new play record.
     */
    public function storePlayRecord(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'player_type' => 'required|in:normal,package',
            'player_package_id' => 'nullable|exists:player_packages,id',
            'token_id' => 'required|exists:tokens,id',
            'default_time' => 'nullable|integer|min:0', // minutes (0 or null means No Limit)
            'no_of_players' => 'required|integer|min:1',
            'amount' => 'required|integer|min:0',
            'payment_type_id' => 'required|exists:payment_types,id',
        ]);

        // Double check if token is currently in use in an active session
        $tokenInUse = PlayRecord::where('token_id', $request->token_id)
            ->whereNull('end_time')
            ->exists();

        if ($tokenInUse) {
            return response()->json([
                'success' => false,
                'message' => 'This token is currently in use in an active play session!'
            ], 422);
        }

        $playRecord = PlayRecord::create([
            'name' => $request->name,
            'player_type' => $request->player_type,
            'player_package_id' => $request->player_type === 'package' ? $request->player_package_id : null,
            'token_id' => $request->token_id,
            'default_time' => $request->default_time,
            'start_time' => null, // Stored as null initially, started manually
            'end_time' => null,
            'actual_time' => null,
            'no_of_players' => $request->no_of_players,
            'amount' => $request->player_type === 'package' ? 0 : $request->amount,
            'payment_type_id' => $request->payment_type_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Play record added successfully!',
            'data' => $playRecord
        ]);
    }

    /**
     * AJAX endpoint to start the time counter for a play record.
     */
    public function startPlayRecord(PlayRecord $playRecord)
    {
        if ($playRecord->start_time) {
            return response()->json([
                'success' => false,
                'message' => 'Session has already started!'
            ], 422);
        }

        $playRecord->update([
            'start_time' => Carbon::now()
        ]);

        // If package play, mark package status as 'started' if it was 'not played'
        if ($playRecord->player_type === 'package' && $playRecord->player_package_id) {
            $playerPackage = PlayerPackage::find($playRecord->player_package_id);
            if ($playerPackage && $playerPackage->package_status === 'not played') {
                $playerPackage->update(['package_status' => 'started']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Timer started successfully!',
            'data' => $playRecord
        ]);
    }

    /**
     * AJAX endpoint to stop the play record, calculate actual time, and check package completions.
     */
    public function stopPlayRecord(Request $request, PlayRecord $playRecord)
    {
        if (!$playRecord->start_time) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot stop a session that has not started yet!'
            ], 422);
        }

        if ($playRecord->end_time) {
            return response()->json([
                'success' => false,
                'message' => 'Session is already stopped!'
            ], 422);
        }

        $endTime = Carbon::now();
        $startTime = $playRecord->start_time;

        // Calculate actual time in minutes (round up to nearest minute, minimum 1 minute if start != end)
        $diffInSeconds = $endTime->diffInSeconds($startTime);
        $actualTimeMinutes = max(1, (int) round($diffInSeconds / 60));

        $amount = $playRecord->amount;
        if ($request->has('amount')) {
            $amount = intval($request->input('amount'));
        }

        DB::beginTransaction();
        try {
            $playRecord->update([
                'end_time' => $endTime,
                'actual_time' => $actualTimeMinutes,
                'amount' => $amount
            ]);

            // If it is a package play record, accumulate total actual time and check for package completion
            if ($playRecord->player_type === 'package' && $playRecord->player_package_id) {
                $playerPackage = PlayerPackage::with('package')->find($playRecord->player_package_id);
                if ($playerPackage) {
                    // Calculate total actual time of all stopped records for this package subscription
                    $totalPlayedMinutes = PlayRecord::where('player_package_id', $playerPackage->id)
                        ->whereNotNull('end_time')
                        ->sum('actual_time');

                    $allowedMinutes = $playerPackage->package->time_per_day * $playerPackage->package->no_of_days;

                    if ($totalPlayedMinutes >= $allowedMinutes) {
                        $playerPackage->update(['package_status' => 'completed']);
                    } else {
                        $playerPackage->update(['package_status' => 'started']);
                    }
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Timer stopped successfully! Played for ' . $actualTimeMinutes . ' minutes.',
                'data' => $playRecord
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to stop the session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX endpoint to update all fields of a play record (Single Modal Edit).
     */
    public function updatePlayRecord(Request $request, PlayRecord $playRecord)
    {
        // Enforce 5-minute edit safety lock
        if ($playRecord->start_time && Carbon::parse($playRecord->start_time)->diffInSeconds(Carbon::now()) > 300) {
            return response()->json([
                'success' => false,
                'message' => 'Editing is locked! You cannot edit a session that started more than 5 minutes ago.'
            ], 422);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'token_id' => 'required|exists:tokens,id',
            'default_time' => 'nullable|integer|min:0',
            'no_of_players' => 'required|integer|min:1',
            'amount' => 'required|integer|min:0',
            'payment_type_id' => 'required|exists:payment_types,id',
            'start_time' => 'nullable|date',
        ]);

        // Verify if token is in use by another session
        $tokenInUse = PlayRecord::where('token_id', $request->token_id)
            ->whereNull('end_time')
            ->where('id', '!=', $playRecord->id)
            ->exists();

        if ($tokenInUse) {
            return response()->json([
                'success' => false,
                'message' => 'This token is currently in use in another active play session!'
            ], 422);
        }

        $startTime = $request->start_time ? Carbon::parse($request->start_time) : $playRecord->start_time;

        $playRecord->update([
            'name' => $request->name,
            'token_id' => $request->token_id,
            'default_time' => $request->default_time,
            'no_of_players' => $request->no_of_players,
            'amount' => $playRecord->player_type === 'package' ? 0 : $request->amount,
            'payment_type_id' => $request->payment_type_id,
            'start_time' => $startTime
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Play record updated successfully!',
            'data' => $playRecord->load(['token.gameType', 'paymentType'])
        ]);
    }

    /**
     * AJAX endpoint to delete a play record.
     */
    public function deletePlayRecord(PlayRecord $playRecord)
    {
        // Enforce 5-minute delete safety lock
        if ($playRecord->start_time && Carbon::parse($playRecord->start_time)->diffInSeconds(Carbon::now()) > 300) {
            return response()->json([
                'success' => false,
                'message' => 'Deletion is locked! You cannot delete a session that started more than 5 minutes ago.'
            ], 422);
        }

        $playRecord->delete();

        return response()->json([
            'success' => true,
            'message' => 'Play record deleted successfully!'
        ]);
    }

    /**
     * Reports page — show completed play records with summary and filters.
     */
    public function report(Request $request)
    {
        $from = $request->input('from', Carbon::today()->toDateString());
        $to   = $request->input('to',   Carbon::today()->toDateString());

        $fromDt = Carbon::parse($from)->startOfDay();
        $toDt   = Carbon::parse($to)->endOfDay();

        $records = PlayRecord::whereNotNull('end_time')
            ->whereBetween('end_time', [$fromDt, $toDt])
            ->with(['token.gameType', 'paymentType', 'playerPackage.player'])
            ->orderBy('end_time', 'desc')
            ->get();

        // Summary aggregates
        $totalRevenue     = $records->sum('amount');
        $totalSessions    = $records->count();
        $totalPlayers     = $records->sum('no_of_players');
        $totalMinutes     = $records->sum('actual_time');
        $normalCount      = $records->where('player_type', 'normal')->count();
        $packageCount     = $records->where('player_type', 'package')->count();
        $normalRevenue    = $records->where('player_type', 'normal')->sum('amount');

        // Revenue breakdown by payment type
        $paymentBreakdown = $records
            ->where('player_type', 'normal')
            ->groupBy(fn($r) => optional($r->paymentType)->name ?? 'Unknown')
            ->map(fn($g) => $g->sum('amount'));

        return view('skatepark.reports.index', compact(
            'records',
            'from',
            'to',
            'totalRevenue',
            'totalSessions',
            'totalPlayers',
            'totalMinutes',
            'normalCount',
            'packageCount',
            'normalRevenue',
            'paymentBreakdown'
        ));
    }

    /**
     * Server-side proxy for Google Translate TTS.
     * Fetches Nepali (or any language) audio on the server to bypass browser CORS restrictions.
     */
    public function tts(Request $request)
    {
        $text = $request->query('text', '');
        $lang = $request->query('lang', 'ne');

        if (empty(trim($text))) {
            abort(400, 'Text parameter is required.');
        }

        // Build Google Translate TTS URL (server-side call avoids CORS)
        $ttsUrl = "https://translate.google.com/translate_tts?ie=UTF-8"
                . "&q=" . urlencode($text)
                . "&tl=" . urlencode($lang)
                . "&client=tw-ob"
                . "&ttsspeed=0.8";

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                'Referer'    => 'https://translate.google.com/',
                'Accept'     => 'audio/webm,audio/ogg,audio/wav,audio/*;q=0.9,application/ogg;q=0.7,video/*;q=0.6,*/*;q=0.5',
            ])->timeout(10)->get($ttsUrl);

            if ($response->failed()) {
                abort(502, 'TTS service unavailable.');
            }

            return response($response->body(), 200)
                ->header('Content-Type', 'audio/mpeg')
                ->header('Cache-Control', 'public, max-age=3600')
                ->header('Access-Control-Allow-Origin', '*');
        } catch (\Exception $e) {
            abort(503, 'TTS fetch failed: ' . $e->getMessage());
        }
    }
}

