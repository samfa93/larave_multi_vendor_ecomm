<?php

namespace App\Services;

use App\Models\Address;
use App\Models\AdminComission;
use App\Models\AdminCommission;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\StoreWallet;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class OrderService
{
    static function storeOrder(string $paymentId, float $paidAmount, string $paymentMethod, string $currency, float $currencyRate, string $paymentStatus, ?int $userId = null)
    {
        $userId = $userId ?? user()->id;
        
        $cartItems = Cart::with('product.store')
            ->where('user_id', $userId)
            ->get()
            ->groupBy(function ($cartItem) {
                return $cartItem->product->store_id;
            });

        $groupProductByStore = $cartItems->map(function ($items, $storeId) {
            $store = $items->first()->product->store;

            return [
                'store' => $store,
                'items' => $items
            ];
        });

        // Get billing info from session (web) or cache (API)
        $billingInfo = Session::get('billing_info') ?? Cache::get("billing_info_{$userId}");
        
        if (!$billingInfo) {
            throw new \Exception('Billing information not found');
        }

        $shippingAddressId = $billingInfo['shipping_address_id'];
        $billingAddressId = $billingInfo['billing_address_id'];

        $billingAddress = Address::find($billingAddressId);
        $shippingAddress = Address::find($shippingAddressId);

        // Get coupon from session (web) or cache (API)
        $couponData = Session::get('coupon') ?? Cache::get("user_coupon_{$userId}");

        foreach ($groupProductByStore as $store) {

            /** Store Order */
            $order = new Order();
            $order->user_id = $userId;
            $order->store_id = $store['store']->id;
            $order->transaction_id = $paymentId;
            $order->customer_email = user() ? user()->email : $billingAddress->email;
            $order->customer_first_name = user() ? user()->name : $billingAddress->name;
            $order->billing_info = $billingAddress;
            $order->shipping_info = $shippingAddress;
            $order->shipping_charge = getShippingCharge();
            
            if ($couponData) {
                $order->has_coupon = true;
                $order->coupon = $couponData['code'];
                $order->discount = $couponData['discount'];
            }
            
            $order->total = $paidAmount;
            $order->payment_method = $paymentMethod;
            $order->currency = $currency;
            $order->currency_rate = $currencyRate;
            $order->order_status = 'pending';
            $order->payment_status = $paymentStatus;
            $order->save();

            // store admin commission
            $adminCommission = new AdminCommission();
            $adminCommission->order_id = $order->id;
            $adminCommission->commission_rate = config('settings.admin_commission');
            $adminCommission->commission_amount = $order->total * ($adminCommission->commission_rate / 100);
            $adminCommission->save();

            // insert update balance

            if(StoreWallet::where('store_id', $store['store']->id)->exists()) {
                $storeWallet = StoreWallet::where('store_id', $store['store']->id)->first();
            }else {
                $storeWallet = new StoreWallet();
            }

            $storeWallet->store_id = $store['store']->id;
            $storeWallet->balance = $storeWallet->balance + ($order->total - $adminCommission->commission_amount);
            $storeWallet->save();


            /** Store Ordered Products */
            foreach ($store['items'] as $item) {
                $orderProduct = new OrderProduct();
                $orderProduct->order_id = $order->id;
                $orderProduct->product_id = $item->product_id;
                $orderProduct->product_name = $item->name;
                if ($item->variant) {
                    $orderProduct->unit_price = $item?->variant?->special_price > 0 ? $item->variant->special_price : $item->variant->price;
                } else {
                    $orderProduct->unit_price = $item->product->price;
                }
                $orderProduct->variant = $item->variant;
                $orderProduct->quantity = $item->quantity;
                $orderProduct->save();
            }

        }

        self::clearCart($userId);

    }

    private static function clearCart(?int $userId = null) : void
    {
        $userId = $userId ?? user()->id;
        
        Cart::where('user_id', $userId)->delete();
        
        // Clear both session and cache
        Session::forget('billing_info');
        Session::forget('coupon');
        Cache::forget("billing_info_{$userId}");
        Cache::forget("user_coupon_{$userId}");
    }
}
