<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HeroBannerStoreRequest;
use App\Models\HeroBanner;
use App\Services\AlertService;
use App\Traits\FileUploadTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class HeroBannerController extends Controller implements HasMiddleware
{
    use FileUploadTrait;

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
        $heroBanner = HeroBanner::first();
        return view('admin.hero.banner.index', compact('heroBanner'));
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
    public function store(HeroBannerStoreRequest $request)
    {
        $data = $request->validated();
        $heroBanner = HeroBanner::first();
        if ($request->hasFile('banner_one')) {
            $bannerOne = $this->uploadFile($request->file('banner_one'), $heroBanner?->banner_one);
            $data['banner_one'] = $bannerOne;
        }
        if ($request->hasFile('banner_two')) {
            $bannerTwo = $this->uploadFile($request->file('banner_two'), $heroBanner?->banner_two);
            $data['banner_two'] = $bannerTwo;
        }

        HeroBanner::updateOrCreate(
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
