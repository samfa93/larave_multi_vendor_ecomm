<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\AddressStoreRequest;
use App\Models\Address;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $addresses = Address::whereUserId(auth()->id())->get();
        return view('frontend.dashboard.address.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('frontend.dashboard.address.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddressStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = user()->id;

        if ($data['is_default'] == 1) {
            Address::where('user_id', user()->id)->update(['is_default' => 0]);
        }

        Address::create($data);
        AlertService::created();
        return to_route('address.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Address $address): View
    {
        return view('frontend.dashboard.address.edit', compact('address'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AddressStoreRequest $request, Address $address): RedirectResponse
    {
        abort_if($address->user_id !== user()->id, 403);

        $data = $request->validated();

        if ($data['is_default'] == 1) {
            Address::where('user_id', user()->id)->update(['is_default' => 0]);
        }
        
        $address->update($data);
        AlertService::updated();
        return to_route('address.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address): JsonResponse
    {
        abort_if($address->user_id !== user()->id, 403);

        $address->delete();
        AlertService::deleted();
        return response()->json(['status' => 'success', 'message' => 'Address deleted successfully']);
    }
}
