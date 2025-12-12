<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OurFeatureStoreRequest;
use App\Http\Requests\Admin\OurFeatureUpdateRequest;
use App\Models\OurFeature;
use App\Services\AlertService;
use App\Traits\FileUploadTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class OurFeatureController extends Controller implements HasMiddleware
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
        $features = OurFeature::paginate(30);
        return view('admin.our-feature.index', compact('features'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.our-feature.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OurFeatureStoreRequest $request) : RedirectResponse
    {
        $data = $request->validated();
        $data['icon'] = $this->uploadFile($request->file('icon'));
        $data['status'] = $request->has('status') ? 1 : 0;
        OurFeature::create($data);

        AlertService::created();

        return to_route('admin.our-features.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OurFeature $our_feature): View
    {
        return view('admin.our-feature.edit', compact('our_feature'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OurFeatureUpdateRequest $request, OurFeature $our_feature) : RedirectResponse
    {
        $data = $request->validated();
        if($request->hasFile('icon')) {
            $data['icon'] = $this->uploadFile($request->file('icon'));
        }

        $data['status'] = $request->has('status') ? 1 : 0;
        $our_feature->update($data);

        AlertService::updated();

        return to_route('admin.our-features.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OurFeature $our_feature) : JsonResponse
    {
        $this->deleteFile($our_feature->icon);
        $our_feature->delete();

        AlertService::deleted();

        return response()->json(['status' => 'success', 'message' => 'Feature deleted successfully']);
    }
}
