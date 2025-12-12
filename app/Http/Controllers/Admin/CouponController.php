<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CouponStoreRequest;
use App\Http\Requests\Admin\CouponUpdateRequest;
use App\Models\Coupon;
use App\Services\AlertService;
use Illuminate\Console\View\Components\Alert;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CouponController extends Controller implements HasMiddleware
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
        $coupons = Coupon::paginate(25);
        return view('admin.coupon.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.coupon.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CouponStoreRequest $request) : RedirectResponse
    {
        $data = $request->validated();

        Coupon::create($data);

        AlertService::created();

        return redirect()->route('admin.coupons.index');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon) : View
    {
        return view('admin.coupon.edit', compact('coupon'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CouponUpdateRequest $request, Coupon $coupon)
    {
        $data = $request->validated();

        $coupon->update($data);

        AlertService::updated();

        return redirect()->route('admin.coupons.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon) : JsonResponse
    {
        $coupon->delete();

        return response()->json(['status' => 'success', 'message' => 'Deleted Successfully']);
    }
}
