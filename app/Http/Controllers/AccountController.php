<?php

namespace App\Http\Controllers;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use App\Models\Account;
use App\Models\Expense;
use App\Models\ExtraIncome;
use App\Models\Transaction;
use DateTime;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function dashboard(Request $request){
        $filter_date_range = '';
        
        // Handle custom date range filter
        if ($request->filled('date_range_filter') && !$request->filled('date_filter')) {
            $filter_date_range = $request->date_range_filter;
        }
        // Handle predefined date filters (This Month, Last Month, This Week, Last Week)
        if ($request->filled('date_filter')) {
            $today = LaravelNepaliDate::from(now())->toNepaliDate();
            [$currentYear, $currentMonth, $currentDay] = explode('-', $today);

            switch ($request->date_filter) {
                case 'this_month':
                    $startDateBS = "$currentYear-$currentMonth-01";
                    $endDateBS = "$currentYear-$currentMonth-32";
                    $filter_date_range = "$startDateBS - $endDateBS";
                    break;
                case 'last_month':
                    $lastYear = $currentYear;
                    $lastMonth = $currentMonth-1;
                    if($lastMonth<1){
                        $lastMonth = 12;
                        $lastYear -= 1;
                    }
                    $startDateBS = "$lastYear-$lastMonth-01";
                    $endDateBS = "$lastYear-$lastMonth-32";
                    $filter_date_range = "$startDateBS - $endDateBS";
                    break;
                case 'this_week':
                    $startDateBS = LaravelNepaliDate::from(now()->startOfWeek())->toNepaliDate();
                    $endDateBS = LaravelNepaliDate::from(now()->endOfWeek())->toNepaliDate();
                    $filter_date_range = "$startDateBS - $endDateBS";
                    break;
                case 'last_week':
                    $lastWeek = now()->subWeek();
                    $startDateBS = LaravelNepaliDate::from($lastWeek->startOfWeek())->toNepaliDate();
                    $endDateBS = LaravelNepaliDate::from($lastWeek->endOfWeek())->toNepaliDate();
                    $filter_date_range = "$startDateBS - $endDateBS";
                    break;
            }
        }
        $total_balance = Account::sum('balance');
        $total_amount = Transaction::sum('amount');
        $extra_income = ExtraIncome::sum('amount');
        $expenses = Expense::sum('amount');
        if($request->filled('date_range_filter') || $request->filled('date_filter')){
            [$startDateBS, $endDateBS] = explode(' - ', $filter_date_range);
            $startDateAD = new DateTime(LaravelNepaliDate::from($startDateBS)->toEnglishDate());
            $endDateAD = new DateTime(LaravelNepaliDate::from($endDateBS)->toEnglishDate());
            $total_balance = Account::sum('balance');
            $total_amount = Transaction::whereBetween('date_ad', [$startDateAD->format('Y-m-d'), $endDateAD->format('Y-m-d')])->sum('amount');
            $extra_income = ExtraIncome::whereBetween('date_ad', [$startDateAD->format('Y-m-d'), $endDateAD->format('Y-m-d')])->sum('amount');
            $expenses = Expense::whereBetween('date_ad', [$startDateAD->format('Y-m-d'), $endDateAD->format('Y-m-d')])->sum('amount');
        }

        return view('dashboard',compact('filter_date_range','total_balance','total_amount','extra_income','expenses'));
    }
    public function index()
    {
        $accounts = Account::all();
        $total_amount = Account::sum('balance');
        return view('accounts.index', ['total_amount' => $total_amount, 'accounts' => $accounts]);
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'required|integer',
        ]);

        Account::create($request->all());
        return redirect()->route('accounts.index')->with('success', 'Account created successfully.');
    }

    public function edit(Account $account)
    {
        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_default_cash_account' => 'boolean',
            'is_default_online_account' => 'boolean',
            'balance' => 'required|integer',
        ]);

        $account->update($request->all());
        return redirect()->route('accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        $account->delete();
        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully.');
    }
}