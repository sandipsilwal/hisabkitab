<?php

namespace App\Http\Controllers;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use App\Models\Account;
use App\Models\Transaction;
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
        $filter_account_id = null;

        // Apply date filters using the trait
        [$query, $filter_date_range] = $this->applyDateFilters($query, $request);

        // Handle account filter
        if ($request->filled('account_id')) {
            $filter_account_id = $request->account_id;
            $query->where('to_account_id', $request->account_id);
        }
        $total_amount = $query->clone()->sum('amount');
        $transactions = $query->orderBy('date_ad', 'desc')->with('toAccount')->paginate(50);
        $accounts = Account::all();

        return view('transactions.index', compact('total_amount', 'transactions', 'accounts', 'filter_date_range', 'filter_account_id'));
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
            'transactions.*.amount' => 'required|integer|min:1',
            'transactions.*.remarks' => 'nullable|string',
        ]);
        $date_ad = new DateTime(LaravelNepaliDate::from($request->date_bs)->toEnglishDate());
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
                ]);

                $account = Account::find($transactionData['to_account_id']);
                $account->balance += $transactionData['amount'];
                $account->save();
            }
            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transactions created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
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
            'amount' => 'required|integer|min:1',
            'remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $oldAmount = $transaction->amount;
            $oldAccountId = $transaction->to_account_id;
            $date_ad = new DateTime(LaravelNepaliDate::from($request->date_bs)->toEnglishDate());

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

        DB::beginTransaction();
        try {
            $account = Account::find($transaction->to_account_id);
            $account->balance -= $transaction->amount;
            $account->save();

            $transaction->delete();
            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete transaction.');
        }
    }
}