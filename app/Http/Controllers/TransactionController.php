<?php

namespace App\Http\Controllers;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use App\Models\Account;
use App\Models\Day;
use App\Models\Transaction;
use App\Models\PlayRecord;
use App\Models\PlayRecordExtra;
use App\Models\PlayerPackagePaymentHistory;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\DateFilterTrait;
class TransactionController extends Controller
{
    use DateFilterTrait;

    public function index(Request $request)
    {
        $query = Transaction::query();
        $dayQuery = Day::query();
        $filter_account_id = null;

        // Apply date filters using the trait
        [$query, $filter_date_range] = $this->applyDateFilters($query, $request);
        [$dayQuery, $day_filter_date_range] = $this->applyDateFilters($dayQuery, $request);

        // Handle account filter
        if ($request->filled('account_id')) {
            $filter_account_id = $request->account_id;
            $query->where('to_account_id', $filter_account_id);
            $dayQuery->whereHas('transactions', function ($q) use ($filter_account_id) {
                $q->where('to_account_id', $filter_account_id);
            });
        }
        $total_amount = $query->clone()->sum('amount');
        $day_total_amount = $total_amount;
        $days = $dayQuery->orderBy('date_ad', 'desc')
            ->with([
                'transactions' => function ($q) use ($filter_account_id) {
                    if ($filter_account_id) {
                        $q->where('to_account_id', $filter_account_id);
                    }
                },
                'transactions.toAccount'
            ])
            ->paginate(50)
            ->withQueryString();

        // Compare each day's total with the corresponding date's total play records amount & play records extras amounts
        $skateparkTotals = [];
        $dateAds = $days->pluck('date_ad')->unique()->filter()->values();

        if ($dateAds->isNotEmpty()) {
            $minDate = $dateAds->min();
            $maxDate = $dateAds->max();

            $fromDt = Carbon::parse($minDate)->startOfDay();
            $toDt   = Carbon::parse($maxDate)->endOfDay();

            // 1. Play records normal player revenue by date
            $playRecordsByDate = PlayRecord::whereNotNull('end_time')
                ->whereBetween('end_time', [$fromDt, $toDt])
                ->where('player_type', 'normal')
                ->selectRaw('DATE(end_time) as date, SUM(amount) as total_amount')
                ->groupBy(DB::raw('DATE(end_time)'))
                ->pluck('total_amount', 'date');

            // 2. Play record extras linked to play records
            $sessionExtrasByDate = PlayRecordExtra::whereNotNull('play_record_id')
                ->where('status', 'Paid')
                ->whereHas('playRecord', function ($q) use ($fromDt, $toDt) {
                    $q->whereNotNull('end_time')->whereBetween('end_time', [$fromDt, $toDt]);
                })
                ->join('play_records', 'play_record_extras.play_record_id', '=', 'play_records.id')
                ->selectRaw('DATE(play_records.end_time) as date, SUM(play_record_extras.total_amount) as total_amount')
                ->groupBy(DB::raw('DATE(play_records.end_time)'))
                ->pluck('total_amount', 'date');

            // 3. Standalone direct extra sales
            $directExtrasByDate = PlayRecordExtra::whereNull('play_record_id')
                ->where('status', 'Paid')
                ->whereBetween('created_at', [$fromDt, $toDt])
                ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total_amount')
                ->groupBy(DB::raw('DATE(created_at)'))
                ->pluck('total_amount', 'date');

            // 4. Package payment histories
            $packagePaymentsByDate = PlayerPackagePaymentHistory::whereBetween('date', [$fromDt, $toDt])
                ->selectRaw('DATE(date) as date, SUM(amount) as total_amount')
                ->groupBy(DB::raw('DATE(date)'))
                ->pluck('total_amount', 'date');

            foreach ($dateAds as $date) {
                $dateKey = Carbon::parse($date)->format('Y-m-d');
                $pr  = floatval($playRecordsByDate[$dateKey] ?? 0);
                $se  = floatval($sessionExtrasByDate[$dateKey] ?? 0);
                $de  = floatval($directExtrasByDate[$dateKey] ?? 0);
                $pkg = floatval($packagePaymentsByDate[$dateKey] ?? 0);

                $skateparkTotals[$dateKey] = $pr + $se + $de + $pkg;
            }
        }

        // Total transactions recorded for each specific date
        $dateTxnTotals = Transaction::whereIn('date_ad', $dateAds)
            ->selectRaw('DATE(date_ad) as date, SUM(amount) as total_amount')
            ->groupBy(DB::raw('DATE(date_ad)'))
            ->pluck('total_amount', 'date');

        $dateDaySums = [];
        foreach ($days as $d) {
            $dateKey = Carbon::parse($d->date_ad)->format('Y-m-d');
            $dateDaySums[$dateKey] = ($dateDaySums[$dateKey] ?? 0) + floatval($d->total);
        }

        foreach ($days as $day) {
            $dateKey   = Carbon::parse($day->date_ad)->format('Y-m-d');
            $expected  = floatval($skateparkTotals[$dateKey] ?? 0);
            $dateTotal = floatval($dateTxnTotals[$dateKey] ?? ($dateDaySums[$dateKey] ?? ($day->total ?: $day->transactions->sum('amount'))));

            // Only show yellow background IF the total transaction amount of that specific date is LESS than the total play records of that date
            $isLessThanPlayRecords = ($expected > 0) && ($dateTotal < ($expected - 0.01));

            $day->expected_skatepark_total = $expected;
            $day->date_total_transactions  = $dateTotal;
            $day->has_skatepark_discrepancy = $isLessThanPlayRecords;
        }

        $transactions = $query->orderBy('date_ad', 'desc')->with('toAccount')->paginate(50)->withQueryString();
        $accounts = Account::all();

        return view('transactions.index', compact('days', 'day_total_amount', 'total_amount', 'transactions', 'accounts', 'filter_date_range', 'filter_account_id'));
    }

    public function create()
    {
        $accounts = Account::all();
        $defaultCashAccount = Account::where('is_default_cash_account', true)->first();
        $defaultOnlineAccount = Account::where('is_default_online_account', true)->first();
        return view('transactions.create', compact('accounts', 'defaultCashAccount', 'defaultOnlineAccount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_bs' => 'required',
            'transactions' => 'required|array',
            'transactions.*.to_account_id' => 'required|exists:accounts,id',
            // 'transactions.*.amount' => 'required|integer|min:1',
            'transactions.*.remarks' => 'nullable|string',
        ]);
        $date_ad = new DateTime(LaravelNepaliDate::from($request->date_bs)->toEnglishDate());
        $day = Day::create([
            'date_bs' => $request->date_bs,
            'total' => $request->total,
            'date_ad' => $date_ad,
        ]);
        DB::beginTransaction();
        try {
            foreach ($request->transactions as $transactionData) {
                $transaction = Transaction::create([
                    'date_bs' => $request->date_bs,
                    'date_ad' => $date_ad,
                    'to_account_id' => $transactionData['to_account_id'],
                    'amount' => $transactionData['amount'],
                    'remarks' => $transactionData['remarks'],
                    'is_posted' => false,
                    'day_id' => $day->id,
                ]);

                $account = Account::find($transactionData['to_account_id']);
                $account->balance += $transactionData['amount'];
                $account->save();
            }
            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Transactions created successfully.')
                ]);
            }

            return redirect()->route('transactions.index')->with('success', 'Transactions created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to create transactions: ') . $e->getMessage()
                ], 500);
            }
            dd($e);
            return back()->with('error', 'Failed to create transactions.');
        }
    }

    public function edit(Transaction $transaction)
    {
        if ($transaction->is_posted) {
            return back()->with('error', 'Posted transactions cannot be edited.');
        }
        $accounts = Account::all();
        return view('transactions.edit', compact('transaction', 'accounts'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->is_posted) {
            return back()->with('error', 'Posted transactions cannot be edited.');
        }

        $request->validate([
            'date_bs' => 'required',
            'to_account_id' => 'required|exists:accounts,id',
            // 'amount' => 'required|integer|min:1',
            'remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $oldAmount = $transaction->amount;
            $oldAccountId = $transaction->to_account_id;
            $date_ad = new DateTime(LaravelNepaliDate::from($request->date_bs)->toEnglishDate());
            $day = Day::find($transaction->day_id);
            $day->total = $day->total - $oldAmount + $request->amount;
            $day->update();
            $transaction->update(array_merge($request->only(['date_bs', 'to_account_id', 'amount', 'remarks']), [
                'date_ad' => $date_ad
            ]));

            if ($oldAccountId != $request->to_account_id) {
                $oldAccount = Account::find($oldAccountId);
                $oldAccount->balance -= $oldAmount;
                $oldAccount->save();
            } else {
                $account = Account::find($request->to_account_id);
                $account->balance -= $oldAmount;
                $account->save();
            }

            $newAccount = Account::find($request->to_account_id);
            $newAccount->balance += $request->amount;
            $newAccount->save();

            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update transaction.');
        }
    }

    public function post(Transaction $transaction)
    {
        if ($transaction->is_posted) {
            return back()->with('error', 'Transaction already posted.');
        }

        $transaction->is_posted = true;
        $transaction->save();
        return redirect()->route('transactions.index')->with('success', 'Transaction posted successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->is_posted) {
            return back()->with('error', 'Posted transactions cannot be deleted.');
        }
        $day_id = $transaction->day_id;
        $day=Day::find($day_id);
        $amount = $transaction->amount;
        DB::beginTransaction();
        try {
            $account = Account::find($transaction->to_account_id);
            $account->balance -= $amount;
            $account->save();

            $transaction->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete transaction.');
        }
        if(!count($day->transactions)){
            $day->delete();
        }else{
            $day->total= $day->total-$amount;
            $day->update();
        }
        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
    }
}