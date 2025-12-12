<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactSectionSettingStoreRequest;
use App\Models\ContactSectionSetting;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ContactSectionSettingController extends Controller implements HasMiddleware
{

    static function Middleware(): array
    {
        return [
            new Middleware('permission:Contact Management')
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $section = ContactSectionSetting::first();
        return view('admin.contact.contact-setting.index', compact('section'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(ContactSectionSettingStoreRequest $request): RedirectResponse
    {
        ContactSectionSetting::updateOrCreate(
            ['id' => 1], // Assuming there's only one settings record
            $request->validated()
        );

        AlertService::updated();
        return back();
    }
}
