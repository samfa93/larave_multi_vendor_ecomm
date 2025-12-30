<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorDashboardController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get vendor dashboard stats
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $store = $user->store;

        if (!$store) {
            return $this->errorResponse('Store not found', null, 404);
        }

        $totalOrders = \App\Models\Order::where('store_id', $store->id)->count();
        $totalProducts = \App\Models\Product::where('store_id', $store->id)->count();
        $pendingOrders = \App\Models\Order::where('store_id', $store->id)
            ->where('order_status', 'pending')
            ->count();
        
        $totalRevenue = \App\Models\Order::where('store_id', $store->id)
            ->where('payment_status', 'paid')
            ->sum('total');

        return $this->successResponse([
            'total_orders' => $totalOrders,
            'total_products' => $totalProducts,
            'pending_orders' => $pendingOrders,
            'total_revenue' => $totalRevenue,
            'store' => [
                'id' => $store->id,
                'name' => $store->name,
                'status' => $store->status,
            ],
        ]);
    }
}
