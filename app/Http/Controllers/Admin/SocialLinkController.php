<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use App\Services\AlertService;
use App\Traits\FileUploadTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SocialLinkController extends Controller implements HasMiddleware
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
        $socialLinks = SocialLink::paginate(30);
        return view('admin.social-link.index', compact('socialLinks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.social-link.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'icon' => ['required', 'mimes:png,jpg,jpeg,svg', 'max:1048'],
            'url' => ['required', 'url'],
            'status' => ['sometimes', 'boolean'],
        ]);

        $validatedData['icon'] = $this->uploadFile($request->file('icon'));
        $validatedData['status'] = $request->has('status') ? 1 : 0;
        SocialLink::create($validatedData);

        AlertService::created();

        return to_route('admin.social-links.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SocialLink $social_link): View
    {
        return view('admin.social-link.edit', compact('social_link'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SocialLink $social_link): RedirectResponse
    {

        $validatedData = $request->validate([
            'icon' => ['nullable', 'mimes:png,jpg,jpeg,svg', 'max:1048'],
            'url' => ['required', 'url'],
            'status' => ['sometimes', 'boolean'],
        ]);

        if ($request->hasFile('icon')) {
            $validatedData['icon'] = $this->uploadFile($request->file('icon'));
        }

        $validatedData['status'] = $request->has('status') ? 1 : 0;

        $social_link->update($validatedData);

        AlertService::updated();

        return to_route('admin.social-links.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SocialLink $social_link): JsonResponse
    {
        $social_link->delete();
        return response()->json(['status' => 'success', 'message' => 'Deleted Successfully']);
    }
}
