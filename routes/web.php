<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AccountTransactionController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExtraIncomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

Route::resource('accounts', AccountController::class);
Route::resource('transactions', TransactionController::class);
Route::post('transactions/{transaction}/post', [TransactionController::class, 'post'])->name('transactions.post');
Route::resource('account_transactions', AccountTransactionController::class);
Route::post('account_transactions/{accountTransaction}/post', [AccountTransactionController::class, 'post'])->name('account_transactions.post');
Route::resource('expenses', ExpenseController::class);
Route::post('expenses/{expense}/post', [ExpenseController::class, 'post'])->name('expenses.post');
Route::resource('extra_incomes', ExtraIncomeController::class);
Route::post('extra_incomes/{extraIncome}/post', [ExtraIncomeController::class, 'post'])->name('extra_incomes.post');