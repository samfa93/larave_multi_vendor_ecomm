<?php

namespace App\Services\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProductService
{
    /**
     * Get products with filters
     */
    public function getProducts(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Product::query();
        
        $query->with(['images' => function ($q) {
            $q->limit(2);
        }, 'store:id,name,logo', 'primaryVariant']);

        $query->withAvg('reviews', 'rating');

        // Apply filters
        $this->applyFilters($query, $filters);

        $query->where('status', 'active');
        $query->where('approved_status', 'approved');
        $query->orderBy('id', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Get single product by slug
     */
    public function getProductBySlug(string $slug): Product
    {
        return Product::with([
            'images',
            'store',
            'categories',
            'brand',
            'tags',
            'variants.attributeValues.attribute',
            'reviews.user'
        ])
            ->withAvg('reviews', 'rating')
            ->where('slug', $slug)
            ->where('status', 'active')
            ->where('approved_status', 'approved')
            ->firstOrFail();
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters(Builder $query, array $filters): void
    {
        // Category filter
        if (!empty($filters['category'])) {
            $category = Category::where('slug', $filters['category'])->first();
            if ($category) {
                $categoryIds = array_merge([$category->id], $category->allChildrenIds());
                $query->whereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('categories.id', $categoryIds);
                });
            }
        }

        // Price range filter
        if (!empty($filters['min_price']) && !empty($filters['max_price'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereHas('variants', function ($v) use ($filters) {
                    $v->where(function ($v2) use ($filters) {
                        $v2->where(function ($v3) use ($filters) {
                            $v3->whereNotNull('special_price')
                                ->whereBetween('special_price', [$filters['min_price'], $filters['max_price']]);
                        })->orWhere(function ($v3) use ($filters) {
                            $v3->whereNull('special_price')
                                ->whereBetween('price', [$filters['min_price'], $filters['max_price']]);
                        });
                    });
                })->orWhere(function ($q2) use ($filters) {
                    $q2->whereDoesntHave('variants')
                        ->where(function ($q3) use ($filters) {
                            $q3->whereNotNull('special_price')
                                ->whereBetween('special_price', [$filters['min_price'], $filters['max_price']]);
                        })->orWhere(function ($q3) use ($filters) {
                            $q3->whereNull('special_price')
                                ->whereBetween('price', [$filters['min_price'], $filters['max_price']]);
                        });
                });
            });
        }

        // Brand filter
        if (!empty($filters['brands'])) {
            $query->whereIn('brand_id', $filters['brands']);
        }

        // Tags filter
        if (!empty($filters['tags'])) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->whereIn('tags.id', $filters['tags']);
            });
        }

        // Search filter
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Store filter
        if (!empty($filters['store_id'])) {
            $query->where('store_id', $filters['store_id']);
        }
    }

    /**
     * Get featured products
     */
    public function getFeaturedProducts(int $limit = 10)
    {
        return Product::with('primaryImage')
            ->withAvg('reviews', 'rating')
            ->where('is_featured', true)
            ->where('status', 'active')
            ->where('approved_status', 'approved')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get flash sale products
     */
    public function getFlashSaleProducts()
    {
        $flashSale = \App\Models\FlashSale::first();
        
        if (!$flashSale) {
            return collect();
        }

        return Product::withAvg('reviews', 'rating')
            ->with('primaryImage')
            ->whereIn('id', $flashSale->products ?? [])
            ->where('status', 'active')
            ->where('approved_status', 'approved')
            ->get();
    }

    /**
     * Get brands
     */
    public function getBrands(int $limit = 20)
    {
        return Brand::withCount('products')
            ->whereHas('products', function ($query) {
                $query->where('status', 'active')
                    ->where('approved_status', 'approved');
            })
            ->take($limit)
            ->get();
    }
}
