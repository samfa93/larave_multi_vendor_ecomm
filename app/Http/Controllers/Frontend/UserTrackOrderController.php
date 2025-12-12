<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserTrackOrderController extends Controller
{
    function index(Request $request) : View
    {
        $orderId = $request->input('order-id');
        $order = null;

        if(!empty($orderId)) {
            $order = Order::where('id', $orderId)->first();
        }

        return view('frontend.dashboard.track-order.index', compact('order'));
    }

}
