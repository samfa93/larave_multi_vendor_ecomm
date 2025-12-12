<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShippingRuleStoreRequest;
use App\Models\ShippingRule;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ShippingRuleController extends Controller implements HasMiddleware
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
        $shippingRules = ShippingRule::all();
        return view('admin.shipping-rule.index', compact('shippingRules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.shipping-rule.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShippingRuleStoreRequest $request): RedirectResponse
    {
        ShippingRule::create($request->validated());
        AlertService::created();
        return redirect()->route('admin.shipping-rules.index');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShippingRule $shippingRule): View
    {
        return view('admin.shipping-rule.edit', compact('shippingRule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShippingRuleStoreRequest $request, ShippingRule $shippingRule): RedirectResponse
    {
        $shippingRule->update($request->validated());
        AlertService::updated();
        return redirect()->route('admin.shipping-rules.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShippingRule $shippingRule): JsonResponse
    {
        $shippingRule->delete();
        AlertService::deleted();
        return response()->json(['status' => 'success', 'message' => 'Deleted Successfully']);
    }
}
