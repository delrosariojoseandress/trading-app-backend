<?php

namespace App\Http\Controllers;

use App\Models\Trade;

class TradeController extends Controller
{
    public function myTrades()
    {
        return Trade::whereHas('buyOrder', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->orWhereHas('sellOrder', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->orderByDesc('id')
            ->get();
    }
}