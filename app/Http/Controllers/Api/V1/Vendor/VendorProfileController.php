<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorProfileController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get vendor profile
     */
    public function show(Request $request): JsonResponse
    {
        $store = $request->user()->store;

        if (!$store) {
            return $this->errorResponse('Store not found', null, 404);
        }

        return $this->successResponse([
            'id' => $store->id,
            'name' => $store->name,
            'slug' => $store->slug,
            'logo' => $store->logo ? asset($store->logo) : null,
            'banner' => $store->banner ? asset($store->banner) : null,
            'description' => $store->description,
            'email' => $store->email,
            'phone' => $store->phone,
            'address' => $store->address,
            'status' => $store->status,
        ]);
    }

    /**
     * Update vendor profile
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],
            'address' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        try {
            $store = $request->user()->store;

            if (!$store) {
                return $this->errorResponse('Store not found', null, 404);
            }

            $store->update($validated);

            return $this->successResponse([
                'id' => $store->id,
                'name' => $store->name,
                'email' => $store->email,
                'phone' => $store->phone,
                'address' => $store->address,
                'description' => $store->description,
            ], 'Profile updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update profile: ' . $e->getMessage(), null, 500);
        }
    }
}
