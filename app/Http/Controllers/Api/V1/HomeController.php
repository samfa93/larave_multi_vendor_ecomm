<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductResource;
use App\Models\Category;
use App\Models\Contact;
use App\Models\FlashSale;
use App\Models\Newsletter;
use App\Models\Product;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get home page data
     */
    public function index(): JsonResponse
    {
        $featuredCategories = Category::withCount('products')
            ->where('is_featured', true)
            ->take(15)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'icon' => $category->icon ? asset($category->icon) : null,
                    'image' => $category->image ? asset($category->image) : null,
                    'products_count' => $category->products_count,
                ];
            });

        $flashSale = FlashSale::first();
        $flashSaleProducts = [];
        
        if ($flashSale) {
            $flashSaleProducts = Product::withAvg('reviews', 'rating')
                ->with('primaryImage')
                ->whereIn('id', $flashSale->products ?? [])
                ->where('status', 'active')
                ->where('approved_status', 'approved')
                ->get()
                ->map(fn($p) => new ProductResource($p));
        }

        $hotProducts = Product::with('primaryImage')
            ->withAvg('reviews', 'rating')
            ->where('is_hot', true)
            ->where('status', 'active')
            ->where('approved_status', 'approved')
            ->latest()
            ->take(4)
            ->get()
            ->map(fn($p) => new ProductResource($p));

        $newProducts = Product::with('primaryImage')
            ->withAvg('reviews', 'rating')
            ->where('is_new', true)
            ->where('status', 'active')
            ->where('approved_status', 'approved')
            ->latest()
            ->take(4)
            ->get()
            ->map(fn($p) => new ProductResource($p));

        $featuredProducts = Product::with('primaryImage')
            ->withAvg('reviews', 'rating')
            ->where('is_featured', true)
            ->where('status', 'active')
            ->where('approved_status', 'approved')
            ->latest()
            ->take(4)
            ->get()
            ->map(fn($p) => new ProductResource($p));

        return $this->successResponse([
            'featured_categories' => $featuredCategories,
            'flash_sale' => $flashSale ? [
                'id' => $flashSale->id,
                'title' => $flashSale->title,
                'end_date' => $flashSale->end_date,
                'products' => $flashSaleProducts,
            ] : null,
            'hot_products' => $hotProducts,
            'new_products' => $newProducts,
            'featured_products' => $featuredProducts,
        ]);
    }

    /**
     * Submit contact form
     */
    public function contact(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        try {
            Contact::create($validated);
            return $this->successResponse(null, 'Your message has been sent successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send message: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Subscribe to newsletter
     */
    public function subscribeNewsletter(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:newsletters,email'],
        ]);

        try {
            Newsletter::create(['email' => $validated['email']]);
            return $this->successResponse(null, 'Successfully subscribed to newsletter');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to subscribe: ' . $e->getMessage(), null, 500);
        }
    }
}
