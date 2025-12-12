<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreWallet;
use App\Models\StoreWithdrawalRequest;
use App\Models\StoreWithdrawMethod;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreWithdrawRequestController extends Controller
{
    function index(): View
    {
        $withdrawRequests = StoreWithdrawalRequest::whereStoreId(user()->store->id)->get();
        $currentBalance = user()->store?->wallet?->balance;
        $pendingBalance = StoreWithdrawalRequest::whereStoreId(user()->store->id)->whereStatus('pending')->sum('amount');
        $totalWithdraw = StoreWithdrawalRequest::whereStoreId(user()->store->id)->whereStatus('paid')->sum('amount');
        return view('vendor-dashboard.withdraw-request.index', compact('withdrawRequests', 'currentBalance', 'pendingBalance', 'totalWithdraw'));
    }

    function create(): View
    {
        $currentBalance = user()->store?->wallet?->balance;
        $pendingBalance = StoreWithdrawalRequest::whereStoreId(user()->store->id)->whereStatus('pending')->sum('amount');
        $totalWithdraw = StoreWithdrawalRequest::whereStoreId(user()->store->id)->whereStatus('paid')->sum('amount');
        $withdrawMethods = StoreWithdrawMethod::with('withdrawMethod')->whereStoreId(user()->store->id)->get();
        return view('vendor-dashboard.withdraw-request.create', compact('withdrawMethods', 'currentBalance', 'pendingBalance', 'totalWithdraw'));
    }

    function store(Request $request): RedirectResponse
    {
        $request->validate([
            'method' => ['required', 'exists:store_withdraw_methods,id'],
            'amount' => ['required', 'numeric'],
        ]);

        $method = StoreWithdrawMethod::with('withdrawMethod')->find($request->method);
        $wallet = user()->store->wallet->balance;
        $requestedAmount = $request->amount;

        if (StoreWithdrawalRequest::whereStoreId(user()->store->id)->whereStatus('pending')->exists()) {
            AlertService::error('You have a pending withdraw request. You cannot create another until it is processed by admin.');
            return back();
        }

        if ($wallet < $requestedAmount) {
            AlertService::error('Insufficient balance.');
            return back();
        }
        if ($requestedAmount < $method->withdrawMethod->minimum_amount) {
            AlertService::error('Minimum withdraw amount is ' . $method->withdrawMethod->minimum_amount);
            return back();
        }
        if ($requestedAmount > $method->withdrawMethod->maximum_amount) {
            AlertService::error('Maximum withdraw amount is ' . $method->withdrawMethod->maximum_amount);
            return back();
        }

        $withdrawRequest = new StoreWithdrawalRequest();
        $withdrawRequest->store_id = user()->store->id;
        $withdrawRequest->amount = $requestedAmount;
        $withdrawRequest->payment_method = $method->withdrawMethod->name;
        $withdrawRequest->payment_details = $method->description;
        $withdrawRequest->status = 'pending';
        $withdrawRequest->save();

        AlertService::created('Withdraw request created successfully.');
        return redirect()->route('vendor.withdraw-requests.index');
    }

    function destroy(StoreWithdrawalRequest $withdraw_request): JsonResponse
    {
        abort_if($withdraw_request->store_id !== user()->store->id, 404);

        $withdraw_request->delete();
        AlertService::deleted();
        return response()->json(['status' => 'success', 'message' => 'Deleted Successfully']);
    }
}
