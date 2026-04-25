<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Trade;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class MatchingEngine
{
    public function process(Order $order)
    {
        if ($order->type === 'buy') {
            $this->matchBuy($order);
        } else {
            $this->matchSell($order);
        }
    }

    private function matchBuy(Order $buyOrder)
    {
        $sells = Order::where('type', 'sell')
            ->where('price', '<=', $buyOrder->price)
            ->whereIn('status', ['open', 'partial'])
            ->orderBy('price', 'asc')
            ->get();

        foreach ($sells as $sellOrder) {

            if ($buyOrder->filled >= $buyOrder->quantity) break;

            $this->execute($buyOrder, $sellOrder);
        }
    }

    private function matchSell(Order $sellOrder)
    {
        $buys = Order::where('type', 'buy')
            ->where('price', '>=', $sellOrder->price)
            ->whereIn('status', ['open', 'partial'])
            ->orderBy('price', 'desc')
            ->get();

        foreach ($buys as $buyOrder) {

            if ($sellOrder->filled >= $sellOrder->quantity) break;

            $this->execute($buyOrder, $sellOrder);
        }
    }

    private function execute(Order $buy, Order $sell)
    {
        DB::transaction(function () use ($buy, $sell) {

            $buyRemaining = $buy->quantity - $buy->filled;
            $sellRemaining = $sell->quantity - $sell->filled;

            $qty = min($buyRemaining, $sellRemaining);
            $price = $sell->price;

            if ($qty <= 0) return;

            // Create Trade
            Trade::create([
                'buy_order_id' => $buy->id,
                'sell_order_id' => $sell->id,
                'price' => $price,
                'quantity' => $qty,
                'fee' => $qty * $price * 0.001
            ]);

            // Update orders
            $buy->filled += $qty;
            $sell->filled += $qty;

            $buy->status = $buy->filled == $buy->quantity ? 'filled' : 'partial';
            $sell->status = $sell->filled == $sell->quantity ? 'filled' : 'partial';

            $buy->save();
            $sell->save();

            // Wallet updates
            $this->updateWallet($buy->user_id, $sell->user_id, $qty, $price);
        });
    }

    private function updateWallet($buyerId, $sellerId, $qty, $price)
    {
        $total = $qty * $price;

        Wallet::where('user_id', $buyerId)
            ->decrement('balance', $total);

        Wallet::where('user_id', $sellerId)
            ->increment('balance', $total);
    }
}