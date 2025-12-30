<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorProductController extends Controller
{
    use ApiResponseTrait;

    /**
     * List vendor products
     */
    public function index(Request $request): JsonResponse
    {
        $store = $request->user()->store;

        if (!$store) {
            return $this->errorResponse('Store not found', null, 404);
        }

        $products = \App\Models\Product::with(['primaryImage'])
            ->withAvg('reviews', 'rating')
            ->where('store_id', $store->id)
            ->latest()
            ->paginate(20);

        return $this->paginatedResponse(
            $products->setCollection(
                $products->getCollection()->map(fn($product) => new ProductResource($product))
            )
        );
    }

    /**
     * Get single product
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $store = $request->user()->store;

        if (!$store) {
            return $this->errorResponse('Store not found', null, 404);
        }

        try {
            $product = \App\Models\Product::where('store_id', $store->id)
                ->with(['images', 'variants', 'categories', 'brand'])
                ->findOrFail($id);

            return $this->successResponse(new ProductResource($product));
        } catch (\Exception $e) {
            return $this->notFoundResponse('Product not found');
        }
    }

    /**
     * Create product (placeholder - full implementation needed)
     */
    public function store(Request $request): JsonResponse
    {
        return $this->errorResponse('Product creation via API coming soon', null, 501);
    }

    /**
     * Update product (placeholder - full implementation needed)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->errorResponse('Product update via API coming soon', null, 501);
    }

    /**
     * Delete product
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $store = $request->user()->store;

        if (!$store) {
            return $this->errorResponse('Store not found', null, 404);
        }

        try {
            $product = \App\Models\Product::where('store_id', $store->id)->findOrFail($id);
            $product->delete();

            return $this->successResponse(null, 'Product deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete product: ' . $e->getMessage(), null, 500);
        }
    }
}
