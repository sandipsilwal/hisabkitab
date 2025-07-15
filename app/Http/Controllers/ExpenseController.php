<?php

namespace App\Http\Controllers;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use App\Models\Account;
use App\Models\Expense;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\DateFilterTrait;

class ExpenseController extends Controller
{
    use DateFilterTrait;
    public function index(Request $request)
    {
        $query = Expense::query();
        $filter_account_id = null;
        
        [$query, $filter_date_range] = $this->applyDateFilters($query, $request);
        
        if ($request->filled('account_id')) {
            $filter_account_id = $request->account_id;
            $query->where(function ($q) use ($request, $filter_account_id) {
                $q->where('from_account_id', $filter_account_id);
            });
        }
        $total_amount = $query->clone()->sum('amount');
        $expenses = $query->orderBy('date_ad','desc')->with('fromAccount')->paginate(20);
        $accounts = Account::all();
        return view('expenses.index', compact('total_amount', 'expenses', 'accounts','filter_account_id','filter_date_range'));
    }

    public function create()
    {
        $accounts = Account::all();
        return view('expenses.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_bs' => 'required|date',
            'title' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
            'from_account_id' => 'required|exists:accounts,id',
            'remarks' => 'nullable|string',
        ]);

        $date_ad = new DateTime(LaravelNepaliDate::from($request->date_bs)->toEnglishDate());
        DB::beginTransaction();
        try {
                $expense = Expense::create([
                    'date_bs' => $request->date_bs,
                    'date_ad' => $date_ad,
                    'title' => $request->title,
                    'amount' => $request->amount,
                    'from_account_id' => $request->from_account_id,
                    'remarks' => $request->remarks,
                    'is_posted' => false,
                ]);

                $account = Account::find($request->from_account_id);
                $account->balance -= $request->amount;
                $account->save();
            DB::commit();
            return redirect()->route('expenses.index')->with('success', 'Expenses created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create expenses.');
        }
    }

    public function edit(Expense $expense)
    {
        if ($expense->is_posted) {
            return back()->with('error', 'Posted expenses cannot be edited.');
        }
        $accounts = Account::all();
        return view('expenses.edit', compact('expense', 'accounts'));
    }

    public function update(Request $request, Expense $expense)
    {
        if ($expense->is_posted) {
            return back()->with('error', 'Posted expenses cannot be edited.');
        }

        $request->validate([
            'date_bs' => 'required',
            'title' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
            'from_account_id' => 'required|exists:accounts,id',
            'remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $oldAmount = $expense->amount;
            $oldAccountId = $expense->from_account_id;
            $date_ad = new DateTime(LaravelNepaliDate::from($request->date_bs)->toEnglishDate());

            $expense->update(array_merge($request->only(['date_bs', 'title', 'amount', 'from_account_id', 'remarks']), [
                'date_ad' => $date_ad
            ]));
            if ($oldAccountId != $request->from_account_id) {
                $oldAccount = Account::find($oldAccountId);
                $oldAccount->balance += $oldAmount;
                $oldAccount->save();
            } else {
                $account = Account::find($request->from_account_id);
                $account->balance += $oldAmount;
                $account->save();
            }

            $newAccount = Account::find($request->from_account_id);
            $newAccount->balance -= $request->amount;
            $newAccount->save();

            DB::commit();
            return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update expense.');
        }
    }

    public function post(Expense $expense)
    {
        if ($expense->is_posted) {
            return back()->with('error', 'Expense already posted.');
        }

        $expense->is_posted = true;
        $expense->save();
        return redirect()->route('expenses.index')->with('success', 'Expense posted successfully.');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->is_posted) {
            return back()->with('error', 'Posted expenses cannot be deleted.');
        }

        DB::beginTransaction();
        try {
            $account = Account::find($expense->from_account_id);
            $account->balance += $expense->amount;
            $account->save();

            $expense->delete();
            DB::commit();
            return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete expense.');
        }
    }
}