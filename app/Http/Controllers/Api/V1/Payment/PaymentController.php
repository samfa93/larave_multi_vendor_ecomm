<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Services\Cart\CouponService;
use App\Services\OrderService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Razorpay\Api\Api as RazorpayApi;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    protected CouponService $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    /**
     * Create PayPal payment
     */
    public function createPaypalPayment(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $total = $this->calculateTotal($userId);

            if ($total == 0) {
                return $this->errorResponse('Cart is empty', null, 400);
            }

            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $token = $provider->getAccessToken();
            $provider->setAccessToken($token);

            $order = $provider->createOrder([
                "intent" => "CAPTURE",
                "purchase_units" => [[
                    "amount" => [
                        "currency_code" => config('settings.currency_code', 'USD'),
                        "value" => number_format($total, 2, '.', '')
                    ]
                ]],
                "application_context" => [
                    "return_url" => route('api.v1.payment.paypal.success'),
                    "cancel_url" => route('api.v1.payment.paypal.cancel')
                ]
            ]);

            if (isset($order['id'])) {
                return $this->successResponse([
                    'order_id' => $order['id'],
                    'approval_url' => $order['links'][1]['href'] ?? null,
                ]);
            }

            return $this->errorResponse('Failed to create PayPal order', null, 500);
        } catch (\Exception $e) {
            return $this->errorResponse('PayPal payment failed: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Create Stripe checkout session
     */
    public function createStripePayment(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $total = $this->calculateTotal($userId);

            if ($total == 0) {
                return $this->errorResponse('Cart is empty', null, 400);
            }

            Stripe::setApiKey(config('settings.stripe_secret'));

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower(config('settings.currency_code', 'USD')),
                        'product_data' => [
                            'name' => 'Order Payment',
                        ],
                        'unit_amount' => $total * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('api.v1.payment.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('api.v1.payment.stripe.cancel'),
            ]);

            return $this->successResponse([
                'session_id' => $session->id,
                'checkout_url' => $session->url,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Stripe payment failed: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Create Razorpay order
     */
    public function createRazorpayPayment(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $total = $this->calculateTotal($userId);

            if ($total == 0) {
                return $this->errorResponse('Cart is empty', null, 400);
            }

            $api = new RazorpayApi(
                config('settings.razorpay_key'),
                config('settings.razorpay_secret')
            );

            $order = $api->order->create([
                'receipt' => 'order_' . time(),
                'amount' => $total * 100,
                'currency' => config('settings.currency_code', 'USD'),
            ]);

            return $this->successResponse([
                'order_id' => $order->id,
                'amount' => $total,
                'currency' => config('settings.currency_code', 'USD'),
                'key' => config('settings.razorpay_key'),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Razorpay payment failed: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Process COD payment
     */
    public function codPayment(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $total = $this->calculateTotal($userId);

            if ($total == 0) {
                return $this->errorResponse('Cart is empty', null, 400);
            }

            // Get billing info
            $billingInfo = cache()->get("billing_info_{$userId}");
            if (!$billingInfo) {
                return $this->errorResponse('Billing information not found', null, 400);
            }

            // Create order using OrderService
            $transactionId = 'COD-' . time() . '-' . $userId;
            
            OrderService::storeOrder(
                $transactionId,
                $total,
                'cod',
                config('settings.currency_code', 'USD'),
                config('settings.currency_rate', 1),
                'pending'
            );

            // Clear billing info and coupon from cache
            cache()->forget("billing_info_{$userId}");
            $this->couponService->removeCoupon($userId);

            return $this->successResponse([
                'transaction_id' => $transactionId,
                'message' => 'Order placed successfully',
            ], 'Order created successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('COD payment failed: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Calculate order total
     */
    protected function calculateTotal(int $userId): float
    {
        $cartItems = Cart::with('product', 'variant')
            ->where('user_id', $userId)
            ->get();

        $subtotal = 0;
        foreach ($cartItems as $item) {
            if ($item->variant) {
                $price = $item->variant->special_price > 0 ? $item->variant->special_price : $item->variant->price;
            } else {
                $price = $item->product->special_price > 0 ? $item->product->special_price : $item->product->price;
            }
            $subtotal += $price * $item->quantity;
        }

        // Add shipping
        $billingInfo = cache()->get("billing_info_{$userId}");
        $shippingCharge = 0;
        if ($billingInfo && isset($billingInfo['shipping_method_id'])) {
            $shippingRule = \App\Models\ShippingRule::find($billingInfo['shipping_method_id']);
            $shippingCharge = $shippingRule ? $shippingRule->charge : 0;
        }

        // Subtract discount
        $discount = 0;
        $couponData = $this->couponService->getAppliedCoupon($userId);
        if ($couponData) {
            $discount = $couponData['discount'];
        }

        return $subtotal + $shippingCharge - $discount;
    }

    /**
     * Webhook handlers (placeholders - implement based on your needs)
     */
    public function paypalWebhook(Request $request): JsonResponse
    {
        // Implement PayPal webhook handling
        return $this->successResponse(null);
    }

    public function stripeWebhook(Request $request): JsonResponse
    {
        // Implement Stripe webhook handling
        return $this->successResponse(null);
    }

    public function razorpayWebhook(Request $request): JsonResponse
    {
        // Implement Razorpay webhook handling
        return $this->successResponse(null);
    }
}
