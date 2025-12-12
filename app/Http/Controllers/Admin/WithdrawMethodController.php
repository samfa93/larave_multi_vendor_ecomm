<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WithdrawMethodStoreRequest;
use App\Models\WithdrawMethod;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class WithdrawMethodController extends Controller implements HasMiddleware
{
    static function Middleware(): array
    {
        return [
            new Middleware('permission:Withdraw Management')
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $withdrawMethods = WithdrawMethod::all();
        return view('admin.withdraw-method.index', compact('withdrawMethods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.withdraw-method.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WithdrawMethodStoreRequest $request): RedirectResponse
    {
        WithdrawMethod::create($request->validated());
        AlertService::created();
        return redirect()->route('admin.withdraw-methods.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WithdrawMethod $withdrawMethod): View
    {
        return view('admin.withdraw-method.edit', compact('withdrawMethod'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WithdrawMethodStoreRequest $request, WithdrawMethod $withdrawMethod): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $withdrawMethod->update($data);
        AlertService::updated();
        return redirect()->route('admin.withdraw-methods.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WithdrawMethod $withdrawMethod): JsonResponse
    {
        $withdrawMethod->delete();
        AlertService::deleted();
        return response()->json(['status' => 'success', 'message' => 'Deleted Successfully']);
    }
}
