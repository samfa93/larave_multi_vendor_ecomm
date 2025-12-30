<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class CartService
{
    /**
     * Get user's cart items
     */
    public function getCartItems(int $userId): Collection
    {
        return Cart::with(['product.primaryImage', 'product.store', 'variant'])
            ->where('user_id', $userId)
            ->get();
    }

    /**
     * Add item to cart
     */
    public function addToCart(int $userId, array $data): Cart
    {
        $product = Product::findOrFail($data['product_id']);
        $variant = null;

        if (isset($data['variant_id'])) {
            $variant = ProductVariant::findOrFail($data['variant_id']);
        }

        // Check if item already exists in cart
        $existingCart = Cart::where('user_id', $userId)
            ->where('product_id', $data['product_id'])
            ->where('variant_id', $data['variant_id'] ?? null)
            ->first();

        if ($existingCart) {
            // Update quantity
            $existingCart->quantity += $data['quantity'];
            $existingCart->save();
            return $existingCart;
        }

        // Create new cart item
        $cart = new Cart();
        $cart->user_id = $userId;
        $cart->product_id = $data['product_id'];
        $cart->variant_id = $data['variant_id'] ?? null;
        $cart->quantity = $data['quantity'];
        $cart->name = $product->name;
        $cart->save();

        return $cart->load(['product.primaryImage', 'product.store', 'variant']);
    }

    /**
     * Update cart item quantity
     */
    public function updateCartItem(int $userId, int $cartId, int $quantity): Cart
    {
        $cart = Cart::where('user_id', $userId)->findOrFail($cartId);
        $cart->quantity = $quantity;
        $cart->save();

        return $cart->load(['product.primaryImage', 'product.store', 'variant']);
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(int $userId, int $cartId): void
    {
        Cart::where('user_id', $userId)->where('id', $cartId)->delete();
    }

    /**
     * Clear entire cart
     */
    public function clearCart(int $userId): void
    {
        Cart::where('user_id', $userId)->delete();
    }

    /**
     * Calculate cart totals
     */
    public function calculateCartTotal(int $userId): array
    {
        $cartItems = $this->getCartItems($userId);
        $subtotal = 0;

        foreach ($cartItems as $item) {
            if ($item->variant) {
                $price = $item->variant->special_price > 0 ? $item->variant->special_price : $item->variant->price;
            } else {
                $price = $item->product->special_price > 0 ? $item->product->special_price : $item->product->price;
            }
            $subtotal += $price * $item->quantity;
        }

        return [
            'subtotal' => $subtotal,
            'items_count' => $cartItems->count(),
        ];
    }

    /**
     * Check product stock availability
     */
    public function checkStock(Product $product, ?int $variantId, int $quantity): bool
    {
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            return $variant && $variant->stock >= $quantity;
        }

        return $product->stock >= $quantity;
    }
}
