<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    /**
     * List all categories
     */
    public function index(): JsonResponse
    {
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->withCount('products')
            ->get();

        return $this->successResponse($categories->map(function ($category) {
            return $this->formatCategory($category);
        }));
    }

    /**
     * Get products by category
     */
    public function products(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $categoryIds = array_merge([$category->id], $category->allChildrenIds());
        
        $products = \App\Models\Product::with(['primaryImage', 'store'])
            ->withAvg('reviews', 'rating')
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })
            ->where('status', 'active')
            ->where('approved_status', 'approved')
            ->paginate(20);

        return $this->paginatedResponse(
            $products->setCollection(
                $products->getCollection()->map(fn($product) => new \App\Http\Resources\Product\ProductResource($product))
            )
        );
    }

    /**
     * Format category data
     */
    protected function formatCategory($category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'icon' => $category->icon ? asset($category->icon) : null,
            'image' => $category->image ? asset($category->image) : null,
            'is_featured' => (bool) $category->is_featured,
            'products_count' => $category->products_count ?? 0,
            'children' => $category->children->map(function ($child) {
                return $this->formatCategory($child);
            }),
        ];
    }
}
