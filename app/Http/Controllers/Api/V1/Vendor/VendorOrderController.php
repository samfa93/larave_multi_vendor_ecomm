<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorOrderController extends Controller
{
    use ApiResponseTrait;

    /**
     * List vendor orders
     */
    public function index(Request $request): JsonResponse
    {
        $store = $request->user()->store;

        if (!$store) {
            return $this->errorResponse('Store not found', null, 404);
        }

        $orders = Order::where('store_id', $store->id)
            ->with('user:id,name,email')
            ->latest()
            ->paginate(20);

        return $this->paginatedResponse(
            $orders->setCollection(
                $orders->getCollection()->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'transaction_id' => $order->transaction_id,
                        'customer' => [
                            'name' => $order->customer_first_name,
                            'email' => $order->customer_email,
                        ],
                        'total' => $order->total,
                        'order_status' => $order->order_status,
                        'payment_status' => $order->payment_status,
                        'payment_method' => $order->payment_method,
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
        $store = $request->user()->store;

        if (!$store) {
            return $this->errorResponse('Store not found', null, 404);
        }

        try {
            $order = Order::where('store_id', $store->id)
                ->with(['orderProducts.product'])
                ->findOrFail($id);

            return $this->successResponse([
                'id' => $order->id,
                'transaction_id' => $order->transaction_id,
                'customer_first_name' => $order->customer_first_name,
                'customer_email' => $order->customer_email,
                'billing_info' => $order->billing_info,
                'shipping_info' => $order->shipping_info,
                'shipping_charge' => $order->shipping_charge,
                'discount' => $order->discount,
                'total' => $order->total,
                'order_status' => $order->order_status,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'products' => $order->orderProducts->map(function ($item) {
                    return [
                        'product_name' => $item->product_name,
                        'unit_price' => $item->unit_price,
                        'quantity' => $item->quantity,
                        'total' => $item->unit_price * $item->quantity,
                        'variant' => $item->variant,
                    ];
                }),
                'created_at' => $order->created_at?->toISOString(),
            ]);
        } catch (\Exception $e) {
            return $this->notFoundResponse('Order not found');
        }
    }

    /**
     * Update order status
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'order_status' => ['required', 'in:pending,processing,shipped,delivered,cancelled'],
        ]);

        $store = $request->user()->store;

        if (!$store) {
            return $this->errorResponse('Store not found', null, 404);
        }

        try {
            $order = Order::where('store_id', $store->id)->findOrFail($id);
            $order->order_status = $validated['order_status'];
            $order->save();

            return $this->successResponse([
                'id' => $order->id,
                'order_status' => $order->order_status,
            ], 'Order status updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update order: ' . $e->getMessage(), null, 500);
        }
    }
}
