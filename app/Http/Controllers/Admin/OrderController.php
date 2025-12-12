<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class OrderController extends Controller implements HasMiddleware
{
    static function Middleware(): array
    {
        return [
            new Middleware('permission:Order Management')
        ];
    }
    
    function index(Request $request): View
    {
        $orders = Order::when($request->filled('status'), function ($query) use ($request) {
            return $query->where('order_status', $request->status);
        })->latest()->paginate(30);
        return view('admin.order.index', compact('orders'));
    }

    function show(Order $order): View
    {
        return view('admin.order.show', compact('order'));
    }

    function update(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'order_status' => ['required', Rule::in(array_keys(config('order_status')))],
            'payment_status' => ['nullable', Rule::in(['pending', 'paid'])],
        ]);

        $order->order_status = $request->order_status;

        if ($order->payment_method === 'Cash on Delivery' && $request->filled('payment_status')) {
            $order->payment_status = $request->payment_status;
        }

        $order->save();

        $orderStatusHistory = new OrderStatusHistory();
        $orderStatusHistory->order_id = $order->id;
        $orderStatusHistory->status = $request->order_status;
        $orderStatusHistory->comment = config('order_status')[$request->order_status];
        $orderStatusHistory->save();
        AlertService::updated();

        return back();
    }
}
