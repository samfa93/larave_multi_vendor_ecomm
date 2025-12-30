<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get product reviews
     */
    public function index(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        
        $reviews = ProductReview::with('user:id,name,avatar')
            ->where('product_id', $id)
            ->where('status', 'active')
            ->latest()
            ->paginate(20);

        return $this->paginatedResponse(
            $reviews->setCollection(
                $reviews->getCollection()->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'user' => [
                            'name' => $review->user->name,
                            'avatar' => $review->user->avatar ? asset($review->user->avatar) : null,
                        ],
                        'created_at' => $review->created_at?->toISOString(),
                    ];
                })
            )
        );
    }

    /**
     * Submit product review
     */
    public function store(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        try {
            $product = Product::findOrFail($id);

            // Check if user has already reviewed this product
            $existingReview = ProductReview::where('product_id', $id)
                ->where('user_id', $request->user()->id)
                ->first();

            if ($existingReview) {
                return $this->errorResponse('You have already reviewed this product', null, 400);
            }

            $review = ProductReview::create([
                'product_id' => $id,
                'user_id' => $request->user()->id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
                'status' => 'pending',
            ]);

            return $this->createdResponse([
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'status' => $review->status,
            ], 'Review submitted successfully and pending approval');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to submit review: ' . $e->getMessage(), null, 500);
        }
    }
}
