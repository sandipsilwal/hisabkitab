<?php

namespace App\Http\Controllers;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use App\Models\Account;
use App\Models\AccountTransaction;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = AccountTransaction::query();
        $filter_date_range = '';
        $filter_account_id = null;
        if ($request->filled('date_range_filter')) {
            $filter_date_range = $request->date_range_filter; // e.g., "2082-03-17 - 2082-03-25"
            // Split the date range string into start and end dates
            [$startDateBS, $endDateBS] = explode(' - ', $filter_date_range);
            // Convert BS dates to AD dates
            $startDateAD = new DateTime(LaravelNepaliDate::from($startDateBS)->toEnglishDate());
            $endDateAD = new DateTime(LaravelNepaliDate::from($endDateBS)->toEnglishDate());
            $query->whereBetween('date_ad', [$startDateAD->format('Y-m-d'), $endDateAD->format('Y-m-d')]);
        }
        if ($request->filled('account_id')) {
            $filter_account_id = $request->account_id;
            $query->where(function ($q) use ($request, $filter_account_id) {
                $q->where('from_account_id', $filter_account_id)
                  ->orWhere('to_account_id', $filter_account_id);
            });
        }
        $accountTransactions = $query->orderBy('date_ad','desc')->with(['fromAccount', 'toAccount'])->paginate(50);
        $accounts = Account::all();
        return view('account_transactions.index', compact('accountTransactions', 'accounts','filter_date_range','filter_account_id'));
    }

    public function create()
    {
        $accounts = Account::all();
        return view('account_transactions.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_bs' => 'required',
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id|different:from_account_id',
            'amount' => 'required|integer|min:1',
            'remarks' => 'nullable|string',
        ],[
            'to_account_id.different' => 'Accounts should be different',
        ]);
        $date_ad = new DateTime(LaravelNepaliDate::from($request->date_bs)->toEnglishDate());
        DB::beginTransaction();
        try {
            $transaction = AccountTransaction::create([
                'date_bs' => $request->date_bs,
                'date_ad' => $date_ad,
                'from_account_id' => $request->from_account_id,
                'to_account_id' => $request->to_account_id,
                'amount' => $request->amount,
                'remarks' => $request->remarks,
                'is_posted' => false,
            ]);

            $fromAccount = Account::find($request->from_account_id);
            $toAccount = Account::find($request->to_account_id);
            $fromAccount->balance -= $request->amount;
            $toAccount->balance += $request->amount;
            $fromAccount->save();
            $toAccount->save();
            DB::commit();
            return redirect()->route('account_transactions.index')->with('success', 'Account transactions created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return back()->with('error', 'Failed to create account transactions.');
        }
    }

    public function edit(AccountTransaction $accountTransaction)
    {
        if ($accountTransaction->is_posted) {
            return back()->with('error', 'Posted account transactions cannot be edited.');
        }
        $accounts = Account::all();
        return view('account_transactions.edit', compact('accountTransaction', 'accounts'));
    }

    public function update(Request $request, AccountTransaction $accountTransaction)
    {
        if ($accountTransaction->is_posted) {
            return back()->with('error', 'Posted account transactions cannot be edited.');
        }

        $request->validate([
            'date_bs' => 'required',
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id|different:from_account_id',
            'amount' => 'required|integer|min:1',
            'remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $oldAmount = $accountTransaction->amount;
            $oldFromAccountId = $accountTransaction->from_account_id;
            $oldToAccountId = $accountTransaction->to_account_id;
            $date_ad = new DateTime(LaravelNepaliDate::from($request->date_bs)->toEnglishDate());

            $accountTransaction->update(array_merge($request->only(['date_bs', 'from_account_id', 'to_account_id', 'amount', 'remarks']), [
                'date_ad' => $date_ad
            ]));
            if ($oldFromAccountId != $request->from_account_id || $oldToAccountId != $request->to_account_id) {
                $oldFromAccount = Account::find($oldFromAccountId);
                $oldToAccount = Account::find($oldToAccountId);
                $oldFromAccount->balance += $oldAmount;
                $oldToAccount->balance -= $oldAmount;
                $oldFromAccount->save();
                $oldToAccount->save();
            } else {
                $fromAccount = Account::find($request->from_account_id);
                $toAccount = Account::find($request->to_account_id);
                $fromAccount->balance += $oldAmount;
                $toAccount->balance -= $oldAmount;
                $fromAccount->save();
                $toAccount->save();
            }

            $newFromAccount = Account::find($request->from_account_id);
            $newToAccount = Account::find($request->to_account_id);
            $newFromAccount->balance -= $request->amount;
            $newToAccount->balance += $request->amount;
            $newFromAccount->save();
            $newToAccount->save();

            DB::commit();
            return redirect()->route('account_transactions.index')->with('success', 'Account transaction updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update account transaction.');
        }
    }

    public function post(AccountTransaction $accountTransaction)
    {
        if ($accountTransaction->is_posted) {
            return back()->with('error', 'Account transaction already posted.');
        }

        $accountTransaction->is_posted = true;
        $accountTransaction->save();
        return redirect()->route('account_transactions.index')->with('success', 'Account transaction posted successfully.');
    }

    public function destroy(AccountTransaction $accountTransaction)
    {
        if ($accountTransaction->is_posted) {
            return back()->with('error', 'Posted account transactions cannot be deleted.');
        }

        DB::beginTransaction();
        try {
            $fromAccount = Account::find($accountTransaction->from_account_id);
            $toAccount = Account::find($accountTransaction->to_account_id);
            $fromAccount->balance += $accountTransaction->amount;
            $toAccount->balance -= $accountTransaction->amount;
            $fromAccount->save();
            $toAccount->save();

            $accountTransaction->delete();
            DB::commit();
            return redirect()->route('account_transactions.index')->with('success', 'Account transaction deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete account transaction.');
        }
    }
}