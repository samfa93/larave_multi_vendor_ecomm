<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserOrderController extends Controller
{
    //
    function index() : View
    {
        $orders = Order::where('user_id', user()->id)->latest()->paginate(30);
        return view('frontend.dashboard.order.index', compact('orders'));
    }

    function show(Order $order) : View
    {
        return view('frontend.dashboard.order.show', compact('order'));
    }
}
