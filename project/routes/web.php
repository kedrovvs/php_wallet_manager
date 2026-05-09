<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WalletController;

Route::get('/', [WalletController::class, 'index'])->name('wallets.index');
Route::get('/wallets/create', [WalletController::class, 'create'])->name('wallets.create');
Route::post('/wallets', [WalletController::class, 'store'])->name('wallets.store');
Route::get('/wallets/{wallet}', [WalletController::class, 'show'])->name('wallets.show');
Route::post('/wallets/{wallet}/add-money', [WalletController::class, 'addMoney'])->name('wallets.add-money');
Route::post('/wallets/{wallet}/hold', [WalletController::class, 'holdMoney'])->name('wallets.hold');
Route::post('/holds/{hold}/cancel', [WalletController::class, 'cancelHold'])->name('holds.cancel');
