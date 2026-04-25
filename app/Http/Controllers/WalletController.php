<?php

namespace App\Http\Controllers;

use App\Models\Wallet;

class WalletController extends Controller
{
    public function myWallet()
    {
        return Wallet::firstOrCreate(
            ['user_id' => auth()->id()],
            ['balance' => 0]
        );
    }

    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $wallet = Wallet::firstOrCreate(['user_id' => auth()->id()]);

        $wallet->balance += $request->amount;
        $wallet->save();

        return response()->json([
            'message' => 'Deposit successful',
            'balance' => $wallet->balance
        ]);
    }
}