<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\AlertService;
use App\Services\SettingService;
use App\Traits\FileUploadTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SettingController extends Controller implements HasMiddleware
{
    use FileUploadTrait;

    static function Middleware(): array
    {
        return [
            new Middleware('permission:Settings Management')
        ];
    }

    function index(): View
    {
        return view('admin.settings.sections.general-settings');
    }


    function generalSettings(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_email' => ['nullable', 'email', 'max:255'],
            'site_phone' => ['nullable', 'string', 'max:255'],
            'site_currency' => ['required', 'string', 'max:255'],
            'site_currency_icon' => ['required', 'string', 'max:255'],
        ]);

        foreach ($validatedData as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $settings = app()->make(SettingService::class);
        $settings->clearCashedSettings();

        AlertService::updated();

        return redirect()->back();
    }

    function commissionSettingsIndex(): View
    {
        return view('admin.settings.sections.commission-settings');
    }

    function commissionSettings(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'admin_commission' => ['required', 'numeric', 'max:100'],
        ]);

        foreach ($validatedData as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $settings = app()->make(SettingService::class);
        $settings->clearCashedSettings();

        AlertService::updated();

        return redirect()->back();
    }

    function siteSettingsIndex(): View
    {
        return view('admin.settings.sections.site-settings');
    }

    function siteSettings(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'site_short_description' => ['nullable', 'string', 'max:255'],
            'site_address' => ['nullable', 'string', 'max:255'],
            'site_copyright' => ['required', 'string', 'max:255'],
            'site_hours' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validatedData as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $settings = app()->make(SettingService::class);
        $settings->clearCashedSettings();

        AlertService::updated();

        return redirect()->back();
    }

    function logoSettingsIndex() : View
    {
        return view('admin.settings.sections.logo-settings');
    }

    function logoSettings(Request $request) : RedirectResponse
    {
        $request->validate([
            'site_logo' => ['nullable', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'site_favicon' => ['nullable', 'mimes:jpeg,png,jpg,gif,svg,ico', 'max:2048'],
        ]);

        $validatedData = [];

        if($request->hasFile('site_logo')) {
            $validatedData['site_logo'] = $this->uploadFile($request->file('site_logo'));
        }
        if($request->hasFile('site_favicon')) {
            $validatedData['site_favicon'] = $this->uploadFile($request->file('site_favicon'));
        }

        foreach ($validatedData as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $settings = app()->make(SettingService::class);
        $settings->clearCashedSettings();

        AlertService::updated();

        return redirect()->back();
    }
}
