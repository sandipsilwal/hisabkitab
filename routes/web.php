<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AccountTransactionController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExtraIncomeController;
use App\Http\Controllers\SkaterController;
use App\Http\Controllers\SkaterHistoryController;
use Illuminate\Support\Facades\Route;



Route::get('/', [AccountController::class,'dashboard'])->name('dashboard');
Route::resource('accounts', AccountController::class);
Route::resource('transactions', TransactionController::class);
Route::post('transactions/{transaction}/post', [TransactionController::class, 'post'])->name('transactions.post');
Route::resource('account_transactions', AccountTransactionController::class);
Route::post('account_transactions/{accountTransaction}/post', [AccountTransactionController::class, 'post'])->name('account_transactions.post');
Route::resource('expenses', ExpenseController::class);
Route::post('expenses/{expense}/post', [ExpenseController::class, 'post'])->name('expenses.post');
Route::resource('extra_incomes', ExtraIncomeController::class);
Route::post('extra_incomes/{extraIncome}/post', [ExtraIncomeController::class, 'post'])->name('extra_incomes.post');

Route::get('/skater-timer', function () {
    return view('skater_timer');
})->name('skater_timer');

Route::get('/skaters', [SkaterHistoryController::class, 'index'])->name('skaters.index');
Route::post('/skaters', [SkaterHistoryController::class, 'store'])->name('skaters.store');
Route::post('/skaters/{id}/start', [SkaterHistoryController::class, 'start'])->name('skaters.start');
Route::post('/skaters/{id}/stop', [SkaterHistoryController::class, 'stop'])->name('skaters.stop');
Route::post('/skaters/{id}/complete', [SkaterHistoryController::class, 'complete'])->name('skaters.complete');
Route::post('/skaters/{id}/overTime', [SkaterHistoryController::class, 'overTime'])->name('skaters.overTime');