<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AccountTransactionController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExtraIncomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameTypeController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\DefaultTimeController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\PlayerPackageController;
use App\Http\Controllers\PlayerPackagePaymentHistoryController;
use App\Http\Controllers\CurrentSessionController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('lang/{locale}', function ($locale) {
        if (in_array($locale, ['en', 'ne'])) {
            session(['locale' => $locale]);
        }
        return redirect()->back();
    })->name('lang.switch');

    Route::get('/', [TransactionController::class, 'index']);
    Route::get('dashboard', [AccountController::class, 'dashboard'])->name('dashboard');
    Route::resource('accounts', AccountController::class);
    Route::resource('transactions', TransactionController::class);
    Route::post('transactions/{transaction}/post', [TransactionController::class, 'post'])->name('transactions.post');
    Route::resource('account_transactions', AccountTransactionController::class);
    Route::post('account_transactions/{accountTransaction}/post', [AccountTransactionController::class, 'post'])->name('account_transactions.post');
    Route::resource('expenses', ExpenseController::class);
    Route::post('expenses/{expense}/post', [ExpenseController::class, 'post'])->name('expenses.post');
    Route::resource('extra_incomes', ExtraIncomeController::class);
    Route::post('extra_incomes/{extraIncome}/post', [ExtraIncomeController::class, 'post'])->name('extra_incomes.post');

    Route::prefix('skatepark')->group(function () {
        // Masters CRUD
        Route::resource('game_types', GameTypeController::class);
        Route::resource('payment_types', PaymentTypeController::class);
        Route::resource('default_times', DefaultTimeController::class);
        Route::resource('rates', RateController::class);
        Route::resource('tokens', TokenController::class);
        Route::resource('packages', PackageController::class);
        
        // Players & Packages CRUD
        Route::resource('players', PlayerController::class);
        Route::resource('player_packages', PlayerPackageController::class);
        Route::resource('player_packages.payments', PlayerPackagePaymentHistoryController::class);

        // Current Session Command Center
        Route::get('current-session', [CurrentSessionController::class, 'index'])->name('skatepark.current-session');
        
        // AJAX Endpoints
        Route::get('api/session-data', [CurrentSessionController::class, 'getSessionData'])->name('skatepark.api.session-data');
        Route::post('api/play-records', [CurrentSessionController::class, 'storePlayRecord'])->name('skatepark.api.store-play-record');
        Route::post('api/play-records/{playRecord}/start', [CurrentSessionController::class, 'startPlayRecord'])->name('skatepark.api.start-play-record');
        Route::post('api/play-records/{playRecord}/stop', [CurrentSessionController::class, 'stopPlayRecord'])->name('skatepark.api.stop-play-record');
        Route::post('api/play-records/{playRecord}/update', [CurrentSessionController::class, 'updatePlayRecord'])->name('skatepark.api.update-play-record');
        Route::post('api/play-records/{playRecord}/delete', [CurrentSessionController::class, 'deletePlayRecord'])->name('skatepark.api.delete-play-record');
        Route::get('api/rates/lookup', [CurrentSessionController::class, 'lookupRate'])->name('skatepark.api.lookup-rate');
        Route::get('api/tts', [CurrentSessionController::class, 'tts'])->name('skatepark.api.tts');

        // Reports
        Route::get('reports', [CurrentSessionController::class, 'report'])->name('skatepark.reports');
    });
});