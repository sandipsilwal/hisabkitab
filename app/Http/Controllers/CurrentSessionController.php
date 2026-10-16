<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\GameType;
use App\Models\PlayRecord;
use App\Models\Token;
use App\Models\DefaultTime;
use App\Models\PaymentType;
use App\Models\PlayerPackage;
use App\Models\Rate;
use App\Traits\DateFilterTrait;
use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CurrentSessionController extends Controller
{
    use DateFilterTrait;

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
        // Find default game type
        $defaultGameType = GameType::where('is_default', true)->first();

        // Load active player packages (uncompleted)
        $playerPackages = PlayerPackage::where('package_status', '!=', 'completed')
            ->with(['player', 'package.gameType'])
            ->get();

        // Load accounts for the quick transaction modal
        $accounts = Account::all();

        // Load extras ordered by display_order ascending
        $extras = \App\Models\Extra::orderBy('display_order', 'asc')->orderBy('id', 'asc')->get();

        return view('skatepark.current_session.index', compact(
            'tokens',
            'defaultTimes',
            'paymentTypes',
            'defaultPaymentType',
            'defaultGameType',
            'playerPackages',
            'accounts',
            'extras'
        ));
    }

    /**
     * Retrieve all active sessions in JSON format.
     */
    public function getSessionData()
    {
        $activeRecords = PlayRecord::whereNull('end_time')
            ->with([
                'token.gameType', 
                'gameType', 
                'playerPackage.player', 
                'paymentType',
                'playRecordExtras.extra',
                'playRecordExtras.paymentType'
            ])
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
            'game_type_id' => 'nullable|exists:game_types,id',
            'default_time_id' => 'nullable|exists:default_times,id',
        ]);

        $gameTypeId = $request->game_type_id;
        if (!$gameTypeId) {
            $defaultGameType = GameType::where('is_default', true)->first();
            $gameTypeId = $defaultGameType?->id;
        }

        if (!$request->default_time_id || !$gameTypeId) {
            return response()->json(['success' => true, 'rate' => 0]);
        }

        $rate = Rate::where('game_type_id', $gameTypeId)
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
            'token_id' => 'nullable|exists:tokens,id',
            'game_type_id' => 'nullable|exists:game_types,id',
            'default_time' => 'nullable|integer|min:0', // minutes (0 or null means No Limit)
            'no_of_players' => 'required|integer|min:1',
            'amount' => 'required|integer|min:0',
            'payment_type_id' => 'required|exists:payment_types,id',
        ]);

        // Double check if token is currently in use in an active session
        if ($request->token_id) {
            $tokenInUse = PlayRecord::where('token_id', $request->token_id)
                ->whereNull('end_time')
                ->exists();

            if ($tokenInUse) {
                return response()->json([
                    'success' => false,
                    'message' => 'This token is currently in use in an active play session!'
                ], 422);
            }
        }

        // Determine game_type_id
        $gameTypeId = $request->game_type_id;
        if (!$gameTypeId && $request->token_id) {
            $token = Token::find($request->token_id);
            $gameTypeId = $token?->game_type_id;
        }
        if (!$gameTypeId && $request->player_type === 'package' && $request->player_package_id) {
            $pp = PlayerPackage::with('package')->find($request->player_package_id);
            $gameTypeId = $pp?->package?->game_type_id;
        }
        if (!$gameTypeId) {
            $defaultGameType = GameType::where('is_default', true)->first();
            $gameTypeId = $defaultGameType?->id;
        }

        $playRecord = PlayRecord::create([
            'name' => $request->name,
            'player_type' => $request->player_type,
            'player_package_id' => $request->player_type === 'package' ? $request->player_package_id : null,
            'token_id' => $request->token_id,
            'game_type_id' => $gameTypeId,
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
            'data' => $playRecord->load(['token.gameType', 'gameType', 'paymentType'])
        ]);
    }

    /**
     * AJAX endpoint to store a new extra for an active play record.
     */
    public function storePlayRecordExtra(Request $request, PlayRecord $playRecord)
    {
        $request->validate([
            'extra_id' => 'required|exists:extras,id',
            'quantity' => 'required|integer|min:1',
            'payment_type_id' => 'required|exists:payment_types,id',
        ]);

        $extra = \App\Models\Extra::find($request->extra_id);
        $totalAmount = $extra->amount * $request->quantity;

        $playRecordExtra = \App\Models\PlayRecordExtra::create([
            'play_record_id' => $playRecord->id,
            'extra_id' => $request->extra_id,
            'quantity' => $request->quantity,
            'total_amount' => $totalAmount,
            'payment_type_id' => $request->payment_type_id,
            'status' => 'Not Paid'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Extra added successfully!',
            'data' => $playRecordExtra->load(['extra', 'paymentType'])
        ]);
    }

    /**
     * AJAX endpoint to sell an extra directly (without play record).
     */
    public function sellExtra(Request $request)
    {
        $request->validate([
            'extra_id' => 'required|exists:extras,id',
            'quantity' => 'required|integer|min:1',
            'payment_type_id' => 'required|exists:payment_types,id',
        ]);

        $extra = \App\Models\Extra::findOrFail($request->extra_id);
        $totalAmount = $extra->amount * $request->quantity;

        $playRecordExtra = \App\Models\PlayRecordExtra::create([
            'play_record_id' => null,
            'extra_id' => $request->extra_id,
            'quantity' => $request->quantity,
            'total_amount' => $totalAmount,
            'payment_type_id' => $request->payment_type_id,
            'status' => 'Paid'
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Extra sold successfully!'),
            'data' => $playRecordExtra->load(['extra', 'paymentType'])
        ]);
    }

    /**
     * AJAX endpoint to fetch recent direct extra sales today.
     */
    public function recentExtraSales()
    {
        $sales = \App\Models\PlayRecordExtra::whereNull('play_record_id')
            ->whereDate('created_at', Carbon::today())
            ->with(['extra', 'paymentType'])
            ->orderBy('id', 'desc')
            ->take(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sales
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
            $updateData = [
                'end_time' => $endTime,
                'actual_time' => $actualTimeMinutes,
                'amount' => $amount
            ];

            if ($request->filled('payment_type_id')) {
                $updateData['payment_type_id'] = $request->input('payment_type_id');
            }

            $playRecord->update($updateData);

            if ($request->boolean('mark_extras_paid')) {
                $extraUpdate = ['status' => 'Paid'];
                if ($request->filled('extra_payment_type_id')) {
                    $extraUpdate['payment_type_id'] = $request->input('extra_payment_type_id');
                } elseif ($request->filled('payment_type_id')) {
                    $extraUpdate['payment_type_id'] = $request->input('payment_type_id');
                }
                \App\Models\PlayRecordExtra::where('play_record_id', $playRecord->id)
                    ->where('status', 'Not Paid')
                    ->update($extraUpdate);
            }

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
            'token_id' => 'nullable|exists:tokens,id',
            'game_type_id' => 'nullable|exists:game_types,id',
            'default_time' => 'nullable|integer|min:0',
            'no_of_players' => 'required|integer|min:1',
            'amount' => 'required|integer|min:0',
            'payment_type_id' => 'required|exists:payment_types,id',
            'start_time' => 'nullable|date',
        ]);

        // Verify if token is in use by another session
        if ($request->token_id) {
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
        }

        $startTime = $request->start_time ? Carbon::parse($request->start_time) : $playRecord->start_time;

        $gameTypeId = $request->game_type_id;
        if (!$gameTypeId && $request->token_id) {
            $token = Token::find($request->token_id);
            $gameTypeId = $token?->game_type_id;
        }
        if (!$gameTypeId) {
            $gameTypeId = $playRecord->game_type_id;
        }

        $playRecord->update([
            'name' => $request->name,
            'token_id' => $request->token_id ?: null,
            'game_type_id' => $gameTypeId,
            'default_time' => $request->default_time,
            'no_of_players' => $request->no_of_players,
            'amount' => $playRecord->player_type === 'package' ? 0 : $request->amount,
            'payment_type_id' => $request->payment_type_id,
            'start_time' => $startTime
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Play record updated successfully!',
            'data' => $playRecord->load(['token.gameType', 'gameType', 'paymentType'])
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
        [$from, $to, $filter_date_range] = $this->resolveNepaliDateRange($request);

        if (!$from || !$to) {
            if ($request->filled('from') && $request->filled('to')) {
                $from = $request->input('from');
                $to   = $request->input('to');
                try {
                    $fromBS = LaravelNepaliDate::from($from)->toNepaliDate();
                    $toBS   = LaravelNepaliDate::from($to)->toNepaliDate();
                    $filter_date_range = "$fromBS - $toBS";
                } catch (\Exception $e) {
                    $filter_date_range = '';
                }
            } else {
                $todayBS = LaravelNepaliDate::from(now())->toNepaliDate();
                $filter_date_range = "$todayBS - $todayBS";
                $from = Carbon::today()->toDateString();
                $to   = Carbon::today()->toDateString();
            }
        }

        $fromDt = Carbon::parse($from)->startOfDay();
        $toDt   = Carbon::parse($to)->endOfDay();

        $records = PlayRecord::whereNotNull('end_time')
            ->whereBetween('end_time', [$fromDt, $toDt])
            ->with(['token.gameType', 'gameType', 'paymentType', 'playerPackage.player', 'playRecordExtras.extra', 'playRecordExtras.paymentType'])
            ->orderBy('end_time', 'desc')
            ->get();

        // Package Payments
        $packagePayments = \App\Models\PlayerPackagePaymentHistory::with('playerPackage.player')
            ->whereBetween('date', [$fromDt, $toDt])
            ->orderBy('date', 'desc')
            ->get();
        $packagePaymentsTotal = $packagePayments->sum('amount');

        // Summary aggregates
        $totalSessions       = $records->count();
        $totalPlayers        = $records->sum('no_of_players');
        $totalMinutes        = $records->sum('actual_time');
        $normalCount         = $records->where('player_type', 'normal')->count();
        $packageCount        = $records->where('player_type', 'package')->count();
        $playRecordsEarnings = $records->where('player_type', 'normal')->sum('amount');

        // Revenue breakdown by payment type
        $paymentBreakdown = $records
            ->where('player_type', 'normal')
            ->groupBy(fn($r) => optional($r->paymentType)->name ?? 'Unknown')
            ->map(fn($g) => $g->sum('amount'));

        // Load Paid Extras to add to totals (session extras + standalone direct sales)
        $paidExtras = \App\Models\PlayRecordExtra::with(['extra', 'paymentType', 'playRecord.playerPackage.player', 'playRecord.token'])
            ->where(function($query) use ($fromDt, $toDt) {
                $query->whereHas('playRecord', function($q) use ($fromDt, $toDt) {
                    $q->whereNotNull('end_time')->whereBetween('end_time', [$fromDt, $toDt]);
                })->orWhere(function($q) use ($fromDt, $toDt) {
                    $q->whereNull('play_record_id')->whereBetween('created_at', [$fromDt, $toDt]);
                });
            })
            ->where('status', 'Paid')
            ->orderBy('id', 'desc')
            ->get();
            
        $extrasTotal   = $paidExtras->sum('total_amount');
        $normalRevenue = $playRecordsEarnings + $extrasTotal;
        $totalRevenue  = $normalRevenue + $packagePaymentsTotal;

        foreach($paidExtras as $extra) {
            $pName = optional($extra->paymentType)->name ?? 'Unknown';
            if (!isset($paymentBreakdown[$pName])) {
                $paymentBreakdown[$pName] = 0;
            }
            $paymentBreakdown[$pName] += $extra->total_amount;
        }

        return view('skatepark.reports.index', compact(
            'records',
            'packagePayments',
            'packagePaymentsTotal',
            'from',
            'to',
            'filter_date_range',
            'totalRevenue',
            'totalSessions',
            'totalPlayers',
            'totalMinutes',
            'normalCount',
            'packageCount',
            'normalRevenue',
            'playRecordsEarnings',
            'paidExtras',
            'extrasTotal',
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

    public function dailyTotal(Request $request)
    {
        $date_bs = $request->query('date_bs');
        if (!$date_bs) {
            return response()->json(['total' => 0]);
        }

        try {
            $date_ad = new \DateTime(\Anuzpandey\LaravelNepaliDate\LaravelNepaliDate::from($date_bs)->toEnglishDate());
            $dt = \Carbon\Carbon::instance($date_ad);
            $fromDt = $dt->copy()->startOfDay();
            $toDt   = $dt->copy()->endOfDay();

            $normalRevenue = PlayRecord::whereNotNull('end_time')
                ->whereBetween('end_time', [$fromDt, $toDt])
                ->where('player_type', 'normal')
                ->sum('amount');
                
            $packagePaymentsTotal = \App\Models\PlayerPackagePaymentHistory::whereBetween('date', [$fromDt, $toDt])
                ->sum('amount');

            $extrasTotal = \App\Models\PlayRecordExtra::where(function($query) use ($fromDt, $toDt) {
                    $query->whereHas('playRecord', function($q) use ($fromDt, $toDt) {
                        $q->whereNotNull('end_time')->whereBetween('end_time', [$fromDt, $toDt]);
                    })->orWhere(function($q) use ($fromDt, $toDt) {
                        $q->whereNull('play_record_id')->whereBetween('created_at', [$fromDt, $toDt]);
                    });
                })
                ->where('status', 'Paid')
                ->sum('total_amount');

            return response()->json([
                'total' => $normalRevenue + $packagePaymentsTotal + $extrasTotal
            ]);
        } catch (\Exception $e) {
            return response()->json(['total' => 0, 'error' => $e->getMessage()]);
        }
    }
}

