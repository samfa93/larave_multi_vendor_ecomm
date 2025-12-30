<?php

namespace App\Http\Controllers\Api\V1\Cart;

use App\Http\Controllers\Controller;
use App\Http\Resources\Cart\CartResource;
use App\Services\Cart\CartService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponseTrait;

    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Get cart items
     */
    public function index(Request $request): JsonResponse
    {
        $cartItems = $this->cartService->getCartItems($request->user()->id);
        $totals = $this->cartService->calculateCartTotal($request->user()->id);

        return $this->successResponse([
            'items' => CartResource::collection($cartItems),
            'subtotal' => $totals['subtotal'],
            'items_count' => $totals['items_count'],
        ]);
    }

    /**
     * Add item to cart
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['nullable', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            // Check stock
            $product = \App\Models\Product::findOrFail($validated['product_id']);
            if (!$this->cartService->checkStock($product, $validated['variant_id'] ?? null, $validated['quantity'])) {
                return $this->errorResponse('Insufficient stock available', null, 400);
            }

            $cartItem = $this->cartService->addToCart($request->user()->id, $validated);
            
            return $this->createdResponse(
                new CartResource($cartItem),
                'Item added to cart successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to add item to cart: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Update cart item
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $cartItem = $this->cartService->updateCartItem($request->user()->id, $id, $validated['quantity']);
            
            return $this->successResponse(
                new CartResource($cartItem),
                'Cart updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update cart: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $this->cartService->removeFromCart($request->user()->id, $id);
            return $this->successResponse(null, 'Item removed from cart');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to remove item: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Clear cart
     */
    public function clear(Request $request): JsonResponse
    {
        try {
            $this->cartService->clearCart($request->user()->id);
            return $this->successResponse(null, 'Cart cleared successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to clear cart: ' . $e->getMessage(), null, 500);
        }
    }
}
