<?php

namespace App\Http\Controllers\Api\V1\Cart;

use App\Http\Controllers\Controller;
use App\Services\Cart\CartService;
use App\Services\Cart\CouponService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    use ApiResponseTrait;

    protected CouponService $couponService;
    protected CartService $cartService;

    public function __construct(CouponService $couponService, CartService $cartService)
    {
        $this->couponService = $couponService;
        $this->cartService = $cartService;
    }

    /**
     * Apply coupon
     */
    public function apply(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        try {
            $totals = $this->cartService->calculateCartTotal($request->user()->id);
            $couponData = $this->couponService->applyCoupon(
                $request->user()->id,
                $request->code,
                $totals['subtotal']
            );

            return $this->successResponse($couponData, 'Coupon applied successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Remove coupon
     */
    public function remove(Request $request): JsonResponse
    {
        try {
            $this->couponService->removeCoupon($request->user()->id);
            return $this->successResponse(null, 'Coupon removed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to remove coupon: ' . $e->getMessage(), null, 500);
        }
    }
}
