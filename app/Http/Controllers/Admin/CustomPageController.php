<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomPageStoreRequest;
use App\Http\Requests\Admin\CustomPageUpdateRequest;
use App\Models\CustomPage;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Pest\ArchPresets\Custom;

class CustomPageController extends Controller implements HasMiddleware
{
    static function Middleware(): array
    {
        return [
            new Middleware('permission:Page Management')
        ];
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $pages = CustomPage::paginate(30);
        return view('admin.custom-page.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.custom-page.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomPageStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = \Str::slug($data['title']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        CustomPage::create($data);

        AlertService::created();
        return redirect()->route('admin.custom-pages.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomPage $custom_page): View
    {
        return view('admin.custom-page.edit', compact('custom_page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomPageUpdateRequest $request, CustomPage $custom_page): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = \Str::slug($data['title']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $custom_page->update($data);

        AlertService::created();
        return redirect()->route('admin.custom-pages.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomPage $custom_page): JsonResponse
    {
        $custom_page->delete();

        AlertService::deleted();
        return response()->json(['status' => 'success', 'message' => 'Custom page deleted successfully']);
    }
}
