<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponseTrait;

    /**
     * List user orders
     */
    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with('store:id,name,logo')
            ->latest()
            ->paginate(20);

        return $this->paginatedResponse(
            $orders->setCollection(
                $orders->getCollection()->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->id,
                        'transaction_id' => $order->transaction_id,
                        'total' => $order->total,
                        'currency' => $order->currency,
                        'order_status' => $order->order_status,
                        'payment_status' => $order->payment_status,
                        'payment_method' => $order->payment_method,
                        'store' => [
                            'id' => $order->store->id,
                            'name' => $order->store->name,
                            'logo' => $order->store->logo ? asset($order->store->logo) : null,
                        ],
                        'created_at' => $order->created_at?->toISOString(),
                    ];
                })
            )
        );
    }

    /**
     * Get order details
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $order = Order::where('user_id', $request->user()->id)
                ->with(['store:id,name,logo,email,phone', 'orderProducts.product'])
                ->findOrFail($id);

            return $this->successResponse([
                'id' => $order->id,
                'order_number' => $order->id,
                'transaction_id' => $order->transaction_id,
                'customer_email' => $order->customer_email,
                'customer_first_name' => $order->customer_first_name,
                'billing_info' => $order->billing_info,
                'shipping_info' => $order->shipping_info,
                'shipping_charge' => $order->shipping_charge,
                'has_coupon' => (bool) $order->has_coupon,
                'coupon' => $order->coupon,
                'discount' => $order->discount,
                'total' => $order->total,
                'currency' => $order->currency,
                'currency_rate' => $order->currency_rate,
                'order_status' => $order->order_status,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'store' => [
                    'id' => $order->store->id,
                    'name' => $order->store->name,
                    'logo' => $order->store->logo ? asset($order->store->logo) : null,
                    'email' => $order->store->email,
                    'phone' => $order->store->phone,
                ],
                'products' => $order->orderProducts->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'unit_price' => $item->unit_price,
                        'quantity' => $item->quantity,
                        'total' => $item->unit_price * $item->quantity,
                        'variant' => $item->variant,
                        'thumbnail' => $item->product && $item->product->thumbnail 
                            ? asset($item->product->thumbnail) 
                            : null,
                    ];
                }),
                'created_at' => $order->created_at?->toISOString(),
                'updated_at' => $order->updated_at?->toISOString(),
            ]);
        } catch (\Exception $e) {
            return $this->notFoundResponse('Order not found');
        }
    }

    /**
     * Track order
     */
    public function track(Request $request, int $id): JsonResponse
    {
        try {
            $order = Order::where('user_id', $request->user()->id)
                ->with('statusHistory')
                ->findOrFail($id);

            return $this->successResponse([
                'order_id' => $order->id,
                'current_status' => $order->order_status,
                'payment_status' => $order->payment_status,
                'history' => $order->statusHistory->map(function ($history) {
                    return [
                        'status' => $history->status,
                        'note' => $history->note,
                        'created_at' => $history->created_at?->toISOString(),
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            return $this->notFoundResponse('Order not found');
        }
    }
}
