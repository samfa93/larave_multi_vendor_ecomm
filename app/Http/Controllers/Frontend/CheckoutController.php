<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ShippingRule;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpKernel\DataCollector\RouterDataCollector;

class CheckoutController extends Controller
{
    //
    function index() : View | RedirectResponse
    {
        if(cartTotal() == 0) {
            AlertService::error('Your cart is empty please add some products.');
            return redirect()->route('products.index');
        }

        $cartItems = Cart::with('product.store')
        ->where('user_id', user()->id)
        ->get()
        ->groupBy(function($cartItem) {
            return $cartItem->product->store_id;
        });

        $groupedCartItems = $cartItems->map(function($items, $storeId) {
            $store = $items->first()->product->store;

            return [
                'store' => $store,
                'items' => $items
            ];

        });

        $shippingMethods = ShippingRule::all();
        return view('frontend.pages.checkout', compact('shippingMethods', 'groupedCartItems'));
    }

    function shippingMethod(int $id) : JsonResponse
    {
        $shippingMethod = ShippingRule::findOrFail($id);
        $shippingCharge = $shippingMethod->charge;
        $total = (cartTotal() + $shippingCharge) - cartDiscount();
        return response()->json(['charge' => $shippingCharge, 'total' => round($total, 3)]);
    }


    function billingInfo(Request $request) {
        abort_if(!$request->has('billing_address_id') || !$request->has('shipping_method_id'), 422);

        Session::put('billing_info', [
            'billing_address_id' => $request->billing_address_id,
            'shipping_address_id' => $request->shipping_address_id,
            'shipping_method_id' => $request->shipping_method_id
        ]);

        return response()->json(['status' => 'success', 'redirect_url' => route('payment.index')]);
    }
}
