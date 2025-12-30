<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductResource;
use App\Models\Store;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    use ApiResponseTrait;

    /**
     * List vendors
     */
    public function index(Request $request): JsonResponse
    {
        $vendors = Store::with('seller:id,name')
            ->where('status', 'active')
            ->paginate(20);

        return $this->paginatedResponse(
            $vendors->setCollection(
                $vendors->getCollection()->map(function ($store) {
                    return [
                        'id' => $store->id,
                        'name' => $store->name,
                        'slug' => $store->slug,
                        'logo' => $store->logo ? asset($store->logo) : null,
                        'banner' => $store->banner ? asset($store->banner) : null,
                        'description' => $store->description,
                        'email' => $store->email,
                        'phone' => $store->phone,
                        'address' => $store->address,
                        'owner' => [
                            'name' => $store->seller->name ?? null,
                        ],
                    ];
                })
            )
        );
    }

    /**
     * Get vendor details
     */
    public function show(int $id): JsonResponse
    {
        try {
            $store = Store::with('seller:id,name')
                ->where('status', 'active')
                ->findOrFail($id);

            return $this->successResponse([
                'id' => $store->id,
                'name' => $store->name,
                'slug' => $store->slug,
                'logo' => $store->logo ? asset($store->logo) : null,
                'banner' => $store->banner ? asset($store->banner) : null,
                'description' => $store->description,
                'email' => $store->email,
                'phone' => $store->phone,
                'address' => $store->address,
                'facebook' => $store->facebook,
                'twitter' => $store->twitter,
                'instagram' => $store->instagram,
                'owner' => [
                    'name' => $store->seller->name ?? null,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->notFoundResponse('Vendor not found');
        }
    }

    /**
     * Get vendor products
     */
    public function products(Request $request, int $id): JsonResponse
    {
        try {
            $store = Store::where('status', 'active')->findOrFail($id);

            $products = \App\Models\Product::with(['primaryImage', 'store'])
                ->withAvg('reviews', 'rating')
                ->where('store_id', $id)
                ->where('status', 'active')
                ->where('approved_status', 'approved')
                ->paginate(20);

            return $this->paginatedResponse(
                $products->setCollection(
                    $products->getCollection()->map(fn($product) => new ProductResource($product))
                )
            );
        } catch (\Exception $e) {
            return $this->notFoundResponse('Vendor not found');
        }
    }
}
