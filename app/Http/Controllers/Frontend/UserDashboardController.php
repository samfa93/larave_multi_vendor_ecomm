<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\Wishlist;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    //
    function index() : View
    {
        $totalOrders = Order::where('user_id', user()->id)->count();
        $totalCanceledOrders = Order::where('user_id', user()->id)->where('order_status', 'canceled')->count();
        $totalPendingOrders = Order::where('user_id', user()->id)->where('order_status', 'pending')->count();
        $totalReviews = ProductReview::where('user_id', user()->id)->count();
        $totalAddresses = Address::where('user_id', user()->id)->count();
        $totalWishlists = Wishlist::where('user_id', user()->id)->count();
        return view('frontend.dashboard.main.index', compact(
            'totalOrders',
            'totalCanceledOrders',
            'totalPendingOrders',
            'totalReviews',
            'totalAddresses',
            'totalWishlists'
        ));
    }


    function reviews() : View
    {
        $reviews = ProductReview::with('product:id,name,slug')->where('user_id', user()->id)->paginate(20);
        return view('frontend.dashboard.review.index', compact('reviews'));
    }
}
