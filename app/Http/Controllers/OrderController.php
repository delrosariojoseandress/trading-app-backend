<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\MatchingEngine;

class OrderController extends Controller
{
    public function place(Request $request, MatchingEngine $engine)
    {
        $request->validate([
            'type' => 'required|in:buy,sell',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric'
        ]);

        $order = Order::create([
            'user_id' => auth()->id(),
            'type' => $request->type,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'filled' => 0,
            'status' => 'open'
        ]);

        // 🔥 send to matching engine
        $engine->process($order);

        return response()->json([
            'message' => 'Order placed',
            'order' => $order
        ]);
    }

    public function myOrders()
    {
        return Order::where('user_id', auth()->id())
            ->orderByDesc('id')
            ->get();
    }

    public function cancel($id)
    {
        $order = Order::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        if ($order->status === 'filled') {
            return response()->json(['message' => 'Cannot cancel filled order'], 400);
        }

        $order->status = 'cancelled';
        $order->save();

        return response()->json(['message' => 'Order cancelled']);
    }
}