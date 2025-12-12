<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SliderStoreRequest;
use App\Http\Requests\Admin\SliderUpdateRequest;
use App\Models\Slider;
use App\Services\AlertService;
use App\Traits\FileUploadTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SliderController extends Controller implements HasMiddleware
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
        $sliders = Slider::paginate(30);
        return view('admin.hero.slider.index', compact('sliders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.hero.slider.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SliderStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $image = $this->uploadFile($request->file('image'));

        $data['image'] = $image;
        $data['is_active'] = $request->has('status') ? 1 : 0;


        Slider::create($data);
        AlertService::created();
        return redirect()->route('admin.sliders.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Slider $slider): View
    {
        return view('admin.hero.slider.edit', compact('slider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SliderUpdateRequest $request, Slider $slider): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $image = $this->uploadFile($request->file('image'));
            $data['image'] = $image;
        }
        $data['is_active'] = $request->has('status') ? 1 : 0;
        $slider->update($data);

        AlertService::updated();
        return redirect()->route('admin.sliders.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slider $slider): JsonResponse
    {
        $slider->delete();
        AlertService::deleted();
        return response()->json(['status' => 'success', 'message' => 'Deleted Successfully']);
    }
}
