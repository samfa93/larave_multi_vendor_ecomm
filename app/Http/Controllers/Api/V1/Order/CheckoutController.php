<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ShippingRule;
use App\Services\Cart\CouponService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    use ApiResponseTrait;

    protected CouponService $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    /**
     * Get available shipping methods for address
     */
    public function shippingMethods(int $addressId): JsonResponse
    {
        try {
            $shippingRules = ShippingRule::where('status', 'active')->get();

            return $this->successResponse($shippingRules->map(function ($rule) {
                return [
                    'id' => $rule->id,
                    'name' => $rule->name,
                    'type' => $rule->type,
                    'charge' => $rule->charge,
                    'min_amount' => $rule->min_amount,
                ];
            }));
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch shipping methods: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Set billing and shipping info
     */
    public function setBillingInfo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shipping_address_id' => ['required', 'exists:addresses,id'],
            'billing_address_id' => ['required', 'exists:addresses,id'],
            'shipping_method_id' => ['required', 'exists:shipping_rules,id'],
        ]);

        try {
            // Store billing info in cache for this user
            cache()->put(
                "billing_info_{$request->user()->id}",
                $validated,
                now()->addHours(24)
            );

            return $this->successResponse($validated, 'Billing information saved');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to save billing info: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Get checkout summary
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            
            // Get cart items
            $cartItems = Cart::with('product.store', 'variant')
                ->where('user_id', $userId)
                ->get();

            if ($cartItems->isEmpty()) {
                return $this->errorResponse('Cart is empty', null, 400);
            }

            // Calculate subtotal
            $subtotal = 0;
            foreach ($cartItems as $item) {
                if ($item->variant) {
                    $price = $item->variant->special_price > 0 ? $item->variant->special_price : $item->variant->price;
                } else {
                    $price = $item->product->special_price > 0 ? $item->product->special_price : $item->product->price;
                }
                $subtotal += $price * $item->quantity;
            }

            // Get billing info
            $billingInfo = cache()->get("billing_info_{$userId}");
            
            $shippingCharge = 0;
            if ($billingInfo && isset($billingInfo['shipping_method_id'])) {
                $shippingRule = ShippingRule::find($billingInfo['shipping_method_id']);
                $shippingCharge = $shippingRule ? $shippingRule->charge : 0;
            }

            // Get coupon discount
            $discount = 0;
            $couponData = $this->couponService->getAppliedCoupon($userId);
            if ($couponData) {
                $discount = $couponData['discount'];
            }

            $total = $subtotal + $shippingCharge - $discount;

            return $this->successResponse([
                'subtotal' => $subtotal,
                'shipping_charge' => $shippingCharge,
                'discount' => $discount,
                'total' => $total,
                'items_count' => $cartItems->count(),
                'coupon' => $couponData,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get checkout summary: ' . $e->getMessage(), null, 500);
        }
    }
}
