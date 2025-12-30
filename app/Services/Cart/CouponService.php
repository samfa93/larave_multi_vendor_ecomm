<?php

namespace App\Services\Cart;

use App\Models\Coupon;
use Illuminate\Support\Facades\Session;

class CouponService
{
    /**
     * Validate and apply coupon
     */
    public function validateCoupon(string $code, float $cartTotal): array
    {
        $coupon = Coupon::where('code', $code)
            ->where('status', 'active')
            ->first();

        if (!$coupon) {
            throw new \Exception('Invalid coupon code');
        }

        // Check expiry date
        if ($coupon->end_date && now()->gt($coupon->end_date)) {
            throw new \Exception('This coupon has expired');
        }

        // Check start date
        if ($coupon->start_date && now()->lt($coupon->start_date)) {
            throw new \Exception('This coupon is not yet active');
        }

        // Check minimum purchase amount
        if ($coupon->min_purchase_amount && $cartTotal < $coupon->min_purchase_amount) {
            throw new \Exception('Minimum purchase amount of ' . $coupon->min_purchase_amount . ' required');
        }

        // Check usage limit
        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            throw new \Exception('This coupon has reached its usage limit');
        }

        // Calculate discount
        $discount = 0;
        if ($coupon->discount_type === 'percentage') {
            $discount = ($cartTotal * $coupon->discount) / 100;
            if ($coupon->max_discount_amount && $discount > $coupon->max_discount_amount) {
                $discount = $coupon->max_discount_amount;
            }
        } else {
            $discount = $coupon->discount;
        }

        return [
            'coupon_id' => $coupon->id,
            'code' => $coupon->code,
            'discount' => $discount,
            'discount_type' => $coupon->discount_type,
        ];
    }

    /**
     * Apply coupon (for API - store in cache or database)
     */
    public function applyCoupon(int $userId, string $code, float $cartTotal): array
    {
        $couponData = $this->validateCoupon($code, $cartTotal);
        
        // Store coupon in cache for this user
        cache()->put("user_coupon_{$userId}", $couponData, now()->addHours(24));
        
        return $couponData;
    }

    /**
     * Remove coupon
     */
    public function removeCoupon(int $userId): void
    {
        cache()->forget("user_coupon_{$userId}");
    }

    /**
     * Get applied coupon for user
     */
    public function getAppliedCoupon(int $userId): ?array
    {
        return cache()->get("user_coupon_{$userId}");
    }

    /**
     * Increment coupon usage
     */
    public function incrementUsage(int $couponId): void
    {
        $coupon = Coupon::find($couponId);
        if ($coupon) {
            $coupon->increment('used_count');
        }
    }
}
