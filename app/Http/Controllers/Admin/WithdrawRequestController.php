<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreWallet;
use App\Models\StoreWithdrawalRequest;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class WithdrawRequestController extends Controller implements HasMiddleware
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
        $withdrawRequests = StoreWithdrawalRequest::paginate(30);
        return view('admin.withdraw-request.index', compact('withdrawRequests'));
    }

    /**
     * Display the specified resource.
     */
    public function show(StoreWithdrawalRequest $withdraw_request)
    {
        return view('admin.withdraw-request.show', compact('withdraw_request'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(StoreWithdrawalRequest $withdraw_request, Request $request)
    {
        $withdraw_request->status = $request->status;
        $withdraw_request->save();

        if ($withdraw_request->status == 'paid') {
            $storeWallet = StoreWallet::whereStoreId($withdraw_request->store_id)->first();
            $storeWallet->balance -= $withdraw_request->amount;
            $storeWallet->save();
        }

        AlertService::updated();

        return redirect()->route('admin.withdraw-requests.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
