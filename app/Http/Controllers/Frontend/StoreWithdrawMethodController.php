<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreWithdrawMethod;
use App\Models\WithdrawMethod;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreWithdrawMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $withdrawMethods = StoreWithdrawMethod::with('withdrawMethod')->whereStoreId(user()->store->id)->get();
        return view('vendor-dashboard.withdraw-method.index', compact('withdrawMethods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $withdrawMethods = WithdrawMethod::whereIsActive(true)->get();
        return view('vendor-dashboard.withdraw-method.create', compact('withdrawMethods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) : RedirectResponse
    {
        $request->validate([
            'gateway' => ['required', 'integer', 'exists:withdraw_methods,id'],
            'description' => ['required', 'string', 'max:600']
        ]);

        $withdrawMethod = new StoreWithdrawMethod();
        $withdrawMethod->withdraw_method_id = $request->gateway;
        $withdrawMethod->description = $request->description;
        $withdrawMethod->store_id = user()->store->id;
        $withdrawMethod->save();

        AlertService::created();

        return to_route('vendor.withdraw-methods.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StoreWithdrawMethod $withdraw_method) : View
    {
        abort_if($withdraw_method->store_id !== user()->store->id, 404);
        $withdrawMethods = WithdrawMethod::whereIsActive(true)->get();
        return view('vendor-dashboard.withdraw-method.edit', compact('withdraw_method', 'withdrawMethods'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StoreWithdrawMethod $withdraw_method) : RedirectResponse
    {
        abort_if($withdraw_method->store_id !== user()->store->id, 404);

        $request->validate([
            'gateway' => ['required', 'integer', 'exists:withdraw_methods,id'],
            'description' => ['required', 'string', 'max:600']
        ]);

        $withdrawMethod = $withdraw_method;
        $withdrawMethod->withdraw_method_id = $request->gateway;
        $withdrawMethod->description = $request->description;
        $withdrawMethod->store_id = user()->store->id;
        $withdrawMethod->save();

        AlertService::updated();

        return to_route('vendor.withdraw-methods.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StoreWithdrawMethod $withdraw_method) : JsonResponse
    {
        abort_if($withdraw_method->store_id !== user()->store->id, 404);

        $withdraw_method->delete();
        AlertService::deleted();
        return response()->json(['status' => 'success', 'message' => 'Deleted Successfully']);
    }
}
