<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $wishlistItems = Wishlist::with('product')->where('user_id', user()->id)->paginate(20);
        return view('frontend.pages.wishlist', compact('wishlistItems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) : JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        if (Wishlist::where('user_id', user()->id)->where('product_id', $request->product_id)->exists()) {
            $wishlist = Wishlist::where('user_id', user()->id)->where('product_id', $request->product_id)->first();
            $wishlist->delete();
            return response()->json(['status' => 'success', 'message' => 'Product removed from your wishlist', 'type' => 'remove']);
        } else {
            $wishlist = new Wishlist();
            $wishlist->user_id = user()->id;
            $wishlist->product_id = $request->product_id;
            $wishlist->save();
            return response()->json(['status' => 'success', 'message' => 'Product added to your wishlist', 'type' => 'add']);
        }

        throw ValidationException::withMessages(['error' => 'Something went wrong, please try again later']);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wishlist $wishlist) : JsonResponse
    {
        if ($wishlist->user_id != user()->id) {
            abort(403);
        }

        $wishlist->delete();
        return response()->json(['status' => 'success', 'message' => 'Product removed from your wishlist']);
    }
}
