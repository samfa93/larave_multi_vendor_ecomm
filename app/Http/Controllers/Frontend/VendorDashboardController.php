<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class VendorDashboardController extends Controller
{
    function index() : View
    {
        $pendingOrders = Order::where('store_id', user()->store->id)->where('order_status', 'pending')->count();
        $completedOrders = Order::where('store_id', user()->store->id)->where('order_status', 'delivered')->count();
        $totalOrders = Order::where('store_id', user()->store->id)->count();
        $canceledOrders = Order::where('store_id', user()->store->id)->where('order_status', 'canceled')->count();
        $totalProducts = Product::where('store_id', user()->store->id)->count();
        $totalDigitalProducts = Product::where('store_id', user()->store->id)->where('product_type', 'digital')->count();
        $totalPhysicalProducts = Product::where('store_id', user()->store->id)->where('product_type', 'physical')->count();
        return view('vendor-dashboard.dashboard.index', compact(
            'pendingOrders',
            'completedOrders',
            'totalOrders',
            'canceledOrders',
            'totalProducts',
            'totalDigitalProducts',
            'totalPhysicalProducts'
        ));
    }
}
