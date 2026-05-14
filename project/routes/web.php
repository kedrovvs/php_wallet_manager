<?php

use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::post('/wallet/create', [WalletController::class, 'create']);
    Route::post('/wallet/fund', [WalletController::class, 'fund']);
    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw']);
    Route::post('/wallet/hold', [WalletController::class, 'hold']);
    Route::post('/wallet/hold/cancel', [WalletController::class, 'cancelHold']);
    Route::get('/wallet/balance', [WalletController::class, 'balance']);
});
