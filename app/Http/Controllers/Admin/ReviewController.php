<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    function index() : View
    {
        $reviews = ProductReview::paginate(30);
        return view('admin.review.index', compact('reviews'));
    }

    function destroy(ProductReview $review) : JsonResponse
    {
        $review->delete();
        return response()->json(['status' => 'success', 'message' => 'Deleted Successfully']);
    }
}
