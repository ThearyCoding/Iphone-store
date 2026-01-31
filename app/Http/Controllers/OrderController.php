<?php

namespace App\Http\Controllers;
use App\Models\Order;


class OrderController extends Controller
{
    public function myOrders()
    {
        $orders = Order::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->withCount('items')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function myOrderShow(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load(['items.product.specs']);

        return view('orders.show', compact('order'));
    }
}
