<?php

namespace App\Http\Controllers\Api\V1\Wishlist;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductResource;
use App\Models\Wishlist;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get wishlist items
     */
    public function index(Request $request): JsonResponse
    {
        $wishlistItems = Wishlist::with('product.primaryImage', 'product.store')
            ->where('user_id', $request->user()->id)
            ->get();

        return $this->successResponse([
            'items' => $wishlistItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => new ProductResource($item->product),
                    'added_at' => $item->created_at?->toISOString(),
                ];
            }),
            'count' => $wishlistItems->count(),
        ]);
    }

    /**
     * Add to wishlist
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        try {
            // Check if already in wishlist
            $existing = Wishlist::where('user_id', $request->user()->id)
                ->where('product_id', $validated['product_id'])
                ->first();

            if ($existing) {
                return $this->errorResponse('Product already in wishlist', null, 400);
            }

            $wishlist = Wishlist::create([
                'user_id' => $request->user()->id,
                'product_id' => $validated['product_id'],
            ]);

            return $this->createdResponse([
                'id' => $wishlist->id,
                'product_id' => $wishlist->product_id,
            ], 'Product added to wishlist');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to add to wishlist: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Remove from wishlist
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $wishlist = Wishlist::where('user_id', $request->user()->id)
                ->findOrFail($id);
            $wishlist->delete();

            return $this->successResponse(null, 'Product removed from wishlist');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to remove from wishlist: ' . $e->getMessage(), null, 500);
        }
    }
}
