<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    use ApiResponseTrait;

    /**
     * List user addresses
     */
    public function index(Request $request): JsonResponse
    {
        $addresses = Address::where('user_id', $request->user()->id)->get();
        
        return $this->successResponse($addresses->map(function ($address) {
            return [
                'id' => $address->id,
                'name' => $address->name,
                'phone' => $address->phone,
                'email' => $address->email,
                'address_line_1' => $address->address_line_1,
                'address_line_2' => $address->address_line_2,
                'city' => $address->city,
                'state' => $address->state,
                'zip_code' => $address->zip_code,
                'country' => $address->country,
                'is_default' => (bool) $address->is_default,
            ];
        }));
    }

    /**
     * Create address
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email'],
            'address_line_1' => ['required', 'string'],
            'address_line_2' => ['nullable', 'string'],
            'city' => ['required', 'string'],
            'state' => ['required', 'string'],
            'zip_code' => ['required', 'string'],
            'country' => ['required', 'string'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        try {
            // If this is set as default, remove default from other addresses
            if ($validated['is_default'] ?? false) {
                Address::where('user_id', $request->user()->id)
                    ->update(['is_default' => false]);
            }

            $address = Address::create([
                'user_id' => $request->user()->id,
                ...$validated
            ]);

            return $this->createdResponse([
                'id' => $address->id,
                'name' => $address->name,
                'phone' => $address->phone,
                'email' => $address->email,
                'address_line_1' => $address->address_line_1,
                'address_line_2' => $address->address_line_2,
                'city' => $address->city,
                'state' => $address->state,
                'zip_code' => $address->zip_code,
                'country' => $address->country,
                'is_default' => (bool) $address->is_default,
            ], 'Address created successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create address: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Get single address
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $address = Address::where('user_id', $request->user()->id)->findOrFail($id);
        
        return $this->successResponse([
            'id' => $address->id,
            'name' => $address->name,
            'phone' => $address->phone,
            'email' => $address->email,
            'address_line_1' => $address->address_line_1,
            'address_line_2' => $address->address_line_2,
            'city' => $address->city,
            'state' => $address->state,
            'zip_code' => $address->zip_code,
            'country' => $address->country,
            'is_default' => (bool) $address->is_default,
        ]);
    }

    /**
     * Update address
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email'],
            'address_line_1' => ['required', 'string'],
            'address_line_2' => ['nullable', 'string'],
            'city' => ['required', 'string'],
            'state' => ['required', 'string'],
            'zip_code' => ['required', 'string'],
            'country' => ['required', 'string'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        try {
            $address = Address::where('user_id', $request->user()->id)->findOrFail($id);

            // If this is set as default, remove default from other addresses
            if ($validated['is_default'] ?? false) {
                Address::where('user_id', $request->user()->id)
                    ->where('id', '!=', $id)
                    ->update(['is_default' => false]);
            }

            $address->update($validated);

            return $this->successResponse([
                'id' => $address->id,
                'name' => $address->name,
                'phone' => $address->phone,
                'email' => $address->email,
                'address_line_1' => $address->address_line_1,
                'address_line_2' => $address->address_line_2,
                'city' => $address->city,
                'state' => $address->state,
                'zip_code' => $address->zip_code,
                'country' => $address->country,
                'is_default' => (bool) $address->is_default,
            ], 'Address updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update address: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Delete address
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $address = Address::where('user_id', $request->user()->id)->findOrFail($id);
            $address->delete();

            return $this->successResponse(null, 'Address deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete address: ' . $e->getMessage(), null, 500);
        }
    }
}
