<?php

namespace App\Http\Controllers;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use App\Models\Account;
use App\Models\ExtraIncome;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExtraIncomeController extends Controller
{
    public function index(Request $request)
    {
        $query = ExtraIncome::query();
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
                $q->where('to_account_id', $filter_account_id);
            });
        }
        $extraIncomes = $query->orderBy('date_ad','desc')->with('toAccount')->get();
        $accounts = Account::all();
        return view('extra_incomes.index', compact('extraIncomes', 'accounts', 'filter_date_range', 'filter_account_id'));
    }

    public function create()
    {
        $accounts = Account::all();
        return view('extra_incomes.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_bs' => 'required',
            'title' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
            'to_account_id' => 'required|exists:accounts,id',
            'remarks' => 'nullable|string',
        ]);

        $date_ad = new DateTime(LaravelNepaliDate::from($request->date_bs)->toEnglishDate());
        DB::beginTransaction();
        try {
            $income = ExtraIncome::create([
                'date_bs' => $request->date_bs,
                'date_ad' => $date_ad,
                'title' => $request->title,
                'amount' => $request->amount,
                'to_account_id' => $request->to_account_id,
                'remarks' => $request->remarks,
                'is_posted' => false,
            ]);

            $account = Account::find($request->to_account_id);
            $account->balance += $request->amount;
            $account->save();
            DB::commit();
            return redirect()->route('extra_incomes.index')->with('success', 'Extra incomes created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create extra incomes.');
        }
    }

    public function edit(ExtraIncome $extraIncome)
    {
        if ($extraIncome->is_posted) {
            return back()->with('error', 'Posted extra incomes cannot be edited.');
        }
        $accounts = Account::all();
        return view('extra_incomes.edit', compact('extraIncome', 'accounts'));
    }

    public function update(Request $request, ExtraIncome $extraIncome)
    {
        if ($extraIncome->is_posted) {
            return back()->with('error', 'Posted extra incomes cannot be edited.');
        }

        $request->validate([
            'date_bs' => 'required',
            'title' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
            'to_account_id' => 'required|exists:accounts,id',
            'remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $oldAmount = $extraIncome->amount;
            $oldAccountId = $extraIncome->to_account_id;
            $date_ad = new DateTime(LaravelNepaliDate::from($request->date_bs)->toEnglishDate());

            $extraIncome->update(array_merge($request->only(['date_bs', 'title', 'to_account_id', 'amount', 'remarks']), [
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
            return redirect()->route('extra_incomes.index')->with('success', 'Extra income updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update extra income.');
        }
    }

    public function post(ExtraIncome $extraIncome)
    {
        if ($extraIncome->is_posted) {
            return back()->with('error', 'Extra income already posted.');
        }

        $extraIncome->is_posted = true;
        $extraIncome->save();
        return redirect()->route('extra_incomes.index')->with('success', 'Extra income posted successfully.');
    }

    public function destroy(ExtraIncome $extraIncome)
    {
        if ($extraIncome->is_posted) {
            return back()->with('error', 'Posted extra incomes cannot be deleted.');
        }

        DB::beginTransaction();
        try {
            $account = Account::find($extraIncome->to_account_id);
            $account->balance -= $extraIncome->amount;
            $account->save();

            $extraIncome->delete();
            DB::commit();
            return redirect()->route('extra_incomes.index')->with('success', 'Extra income deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete extra income.');
        }
    }
}