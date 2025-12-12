<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    function index(Request $request) : View
    {
        $orders = Order::where('store_id', user()->store->id)
            ->whereIn('payment_status', ['paid', 'pending'])
            ->when($request->filled('status'), function($query) use ($request) {
                return $query->where('order_status', $request->status);
            })
            ->latest()->paginate(30);
        return view('vendor-dashboard.order.index', compact('orders'));
    }

    function show(Order $order) : View
    {
        return view('vendor-dashboard.order.show', compact('order'));
    }

    function update(Request $request, Order $order) : RedirectResponse
    {
        $request->validate([
            'order_status' => ['required', 'in:pending,processing,packed,shipped,in_transit,out_for_delivery,delivered,canceled']
        ]);

        $order->order_status = $request->order_status;
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
