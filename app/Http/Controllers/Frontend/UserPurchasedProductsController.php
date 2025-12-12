<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Cache\Events\RetrievingKey;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserPurchasedProductsController extends Controller
{
    function index() : View
    {
        $digitalProducts = OrderProduct::whereHas('order', function ($query) {
            $query->where('user_id', user()->id);
        })->whereHas('product', function($query) {
            $query->where('product_type', 'digital');
        })->get();

        return view('frontend.dashboard.purchased-product.index', compact('digitalProducts'));
    }

    function show(int $id) : View
    {
        $orderProduct = OrderProduct::findOrFail($id);
        $product = Product::findOrFail($orderProduct->product_id);
        return view('frontend.dashboard.purchased-product.show', compact('product'));
    }

    function download(int $productId, int $fileId) : BinaryFileResponse
    {
        $product = Product::findOrFail($productId);
        $file = $product->files()->findOrFail($fileId);

        return response()->download(Storage::disk('local')->path($file->path));

    }
}

