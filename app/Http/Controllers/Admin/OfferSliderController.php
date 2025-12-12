<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OfferSliderStoreRequest;
use App\Models\OfferSlider;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class OfferSliderController extends Controller implements HasMiddleware
{

    static function Middleware(): array
    {
        return [
            new Middleware('permission:Section Management')
        ];
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $offers = OfferSlider::paginate(30);
        return view('admin.offer-slider.index', compact('offers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.offer-slider.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OfferSliderStoreRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $validatedData['is_active'] = $request->has('is_active') ? 1 : 0;
        OfferSlider::create($validatedData);

        AlertService::created();
        return to_route('admin.offer-sliders.index');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OfferSlider $offer_slider): View
    {
        return view('admin.offer-slider.edit', compact('offer_slider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OfferSliderStoreRequest $request, OfferSlider $offer_slider): RedirectResponse
    {

        $validatedData = $request->validated();
        $validatedData['is_active'] = $request->has('is_active') ? 1 : 0;
        $offer_slider->update($validatedData);

        AlertService::updated();

        return to_route('admin.offer-sliders.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OfferSlider $offer_slider): JsonResponse
    {
        $offer_slider->delete();

        return response()->json(['status' => 'success', 'message' => 'Deleted Successfully']);
    }
}
