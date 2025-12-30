<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductResource;
use App\Services\Product\ProductService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponseTrait;

    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * List products with filters
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'category' => $request->category,
            'min_price' => $request->min_price,
            'max_price' => $request->max_price,
            'brands' => $request->brands ? (is_array($request->brands) ? $request->brands : explode(',', $request->brands)) : null,
            'tags' => $request->tags ? (is_array($request->tags) ? $request->tags : explode(',', $request->tags)) : null,
            'search' => $request->search,
            'store_id' => $request->store_id,
        ];

        $products = $this->productService->getProducts($filters, $request->per_page ?? 20);

        return $this->paginatedResponse(
            $products->setCollection(
                $products->getCollection()->map(fn($product) => new ProductResource($product))
            )
        );
    }

    /**
     * Get single product
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $product = $this->productService->getProductBySlug($slug);
            return $this->successResponse(new ProductResource($product));
        } catch (\Exception $e) {
            return $this->notFoundResponse('Product not found');
        }
    }

    /**
     * Get featured products
     */
    public function featuredProducts(Request $request): JsonResponse
    {
        $limit = $request->limit ?? 10;
        $products = $this->productService->getFeaturedProducts($limit);
        
        return $this->successResponse(ProductResource::collection($products));
    }

    /**
     * Get flash sale products
     */
    public function flashSales(): JsonResponse
    {
        $products = $this->productService->getFlashSaleProducts();
        
        return $this->successResponse(ProductResource::collection($products));
    }

    /**
     * Get brands
     */
    public function brands(Request $request): JsonResponse
    {
        $limit = $request->limit ?? 20;
        $brands = $this->productService->getBrands($limit);
        
        return $this->successResponse($brands->map(function ($brand) {
            return [
                'id' => $brand->id,
                'name' => $brand->name,
                'slug' => $brand->slug,
                'logo' => $brand->logo ? asset($brand->logo) : null,
                'products_count' => $brand->products_count,
            ];
        }));
    }
}
