<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/wallet', [WalletController::class, 'showWallet']);
    Route::get('/wallet/fund', [WalletController::class, 'showFund']);
    Route::get('/wallet/withdraw', [WalletController::class, 'showWithdraw']);
    Route::get('/wallet/holds', [WalletController::class, 'showHolds']);

    Route::post('/wallet/create', [WalletController::class, 'create']);
    Route::post('/wallet/fund', [WalletController::class, 'fund']);
    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw']);
    Route::post('/wallet/hold', [WalletController::class, 'hold']);
    Route::post('/wallet/hold/cancel', [WalletController::class, 'cancelHold']);
    Route::get('/wallet/balance', [WalletController::class, 'balance'])->name('wallet.balance');
});
