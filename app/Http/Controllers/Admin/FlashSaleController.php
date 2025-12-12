<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FlashSaleStoreRequest;
use App\Models\FlashSale;
use App\Models\Product;
use App\Services\AlertService;
use Illuminate\Console\View\Components\Alert;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FlashSaleController extends Controller implements HasMiddleware
{
    static function Middleware(): array
    {
        return [
            new Middleware('permission:Ecommerce Management')
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $flashSale = FlashSale::first();
        $products = Product::whereIn('id', $flashSale?->products ?? [])->get();
        return view('admin.flash-sale.index', compact('flashSale', 'products'));
    }

    function getProducts(Request $request): JsonResponse
    {
        $products = Product::where('name', 'LIKE', '%' . $request->q . '%')->paginate(20);

        $requests = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'text' => $product->name,
                'image' => asset($product->primaryImage->path)
            ];
        });

        return response()->json([
            'results' => $requests,
            'pagination' => [
                'more' => $products->hasMorePages()
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FlashSaleStoreRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('status') ? 1 : 0;
        FlashSale::updateOrCreate(
            ['id' => 1],
            $data
        );

        AlertService::updated();
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
