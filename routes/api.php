<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TradeController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', function (Request $request) {
        return $request->user();
    });
});

Route::middleware(['auth:sanctum', 'role:admin'])->get('/admin', function () {
    return "Admin only area";
});

Route::middleware('auth')->group(function () {

    // Orders
    Route::post('/order', [OrderController::class, 'place']);
    Route::get('/orders', [OrderController::class, 'myOrders']);
    Route::delete('/order/{id}', [OrderController::class, 'cancel']);

    // Wallet
    Route::get('/wallet', [WalletController::class, 'myWallet']);
    Route::post('/wallet/deposit', [WalletController::class, 'deposit']);

    // Trades
    Route::get('/trades', [TradeController::class, 'myTrades']);
});