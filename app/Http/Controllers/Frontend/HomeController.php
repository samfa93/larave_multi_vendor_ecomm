<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CustomPage;
use App\Models\FlashSale;
use App\Models\HeroBanner;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PopularCategory;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductSection;
use App\Models\Slider;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;

class HomeController extends Controller
{
    function index(): View
    {
        $featuredCategories = Category::withCount('products')->whereIsFeatured(true)->take(15)->get();
        $sliders = Slider::whereIsActive(true)->get();
        $heroBanner = HeroBanner::first();
        $popularCategoriesIds = PopularCategory::first()?->categories ?? [];
        $popularCategories = Category::whereIn('id', $popularCategoriesIds)->get();
        $popularProducts = $this->productsByCategory($popularCategoriesIds);
        $flashSale = FlashSale::first();
        $flashSaleProducts = Product::withAvg('reviews', 'rating')->whereIn('id', $flashSale?->products ?? [])->get();
        $productSections = ProductSection::first();

        $productSectionsIds = [
            $productSections?->category_one,
            $productSections?->category_two,
            $productSections?->category_three
        ];


        $hotProducts = Product::with('primaryImage')->withAvg('reviews', 'rating')->whereIsHot(true)->latest()->take(4)->get();
        $newProducts = Product::with('primaryImage')->withAvg('reviews', 'rating')->whereIsNew(true)->latest()->take(4)->get();
        $featuredProducts = Product::with('primaryImage')->withAvg('reviews', 'rating')->whereIsFeatured(true)->latest()->take(4)->get();
        $topRatedProducts = Product::with('primaryImage')->whereHas('reviews')->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'desc')->take(4)->get();

        $productSectionsProducts = $this->productsByCategory($productSectionsIds, false);

        return view('frontend.home.index', compact(
            'featuredCategories',
            'sliders',
            'heroBanner',
            'popularCategories',
            'popularProducts',
            'flashSale',
            'flashSaleProducts',
            'productSectionsProducts',
            'hotProducts',
            'newProducts',
            'featuredProducts',
            'topRatedProducts'
        ));
    }

    function productsByCategory(array $categoryIds, $featured = true, $limit = 12)
    {
        $results = [];

        foreach ($categoryIds as $categoryId) {
            $category = Category::find($categoryId);
            if ($category) {
                $ids = [$category->id];
                $ids = array_merge($ids, $category->allChildrenIds());
                if ($featured)
                    $products = Product::withAvg('reviews', 'rating')->whereHas('categories', function ($query) use ($ids) {
                        $query->whereIn('categories.id', $ids);
                    })->whereIsFeatured(true)->take(12)->get();
                else {
                    $products = Product::withAvg('reviews', 'rating')->whereHas('categories', function ($query) use ($ids) {
                        $query->whereIn('categories.id', $ids);
                    })->latest()->take($limit)->get();
                }


                $results[$categoryId] = $products;
            }
        }


        return $results;
    }

    function storeReview(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'review' => ['required', 'string', 'max: 500'],
        ]);

        $productPurchasedByUser = Order::where('user_id', user()->id)->whereHas('orderProducts', function ($query) use ($product) {
            $query->where('product_id', $product->id);
        })->exists();

        if (!$productPurchasedByUser) {
            throw ValidationException::withMessages([
                'review' => 'You have not purchased this product'
            ]);
        }
        if (ProductReview::where('product_id', $product->id)->where('user_id', user()->id)->exists()) {
            throw ValidationException::withMessages([
                'review' => 'You have already reviewed this product'
            ]);
        }

        $review = new ProductReview();
        $review->product_id = $product->id;
        $review->user_id = user()->id;
        $review->rating = $request->rating;
        $review->review = $request->review;
        $review->save();

        AlertService::created('Product Review Added Successfully');

        return response()->json(['status' => 'success', 'message' => 'Product Review Added Successfully']);
    }

    function customPage(string $slug): View
    {
        $page = CustomPage::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('frontend.pages.custom-page', compact('page'));
    }

    function flashSales(): View
    {
        $flashSale = FlashSale::first();
        $flashSaleProducts = Product::withAvg('reviews', 'rating')->whereIn('id', $flashSale?->products ?? [])->paginate(20);
        return view('frontend.pages.flash-sale', compact('flashSale', 'flashSaleProducts'));
    }
}
