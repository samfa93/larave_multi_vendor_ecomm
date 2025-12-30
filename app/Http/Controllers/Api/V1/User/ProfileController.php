<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Traits\ApiResponseTrait;
use App\Traits\FileUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use ApiResponseTrait, FileUploadTrait;

    /**
     * Get user profile
     */
    public function show(Request $request): JsonResponse
    {
        return $this->successResponse(new UserResource($request->user()));
    }

    /**
     * Update user profile
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $request->user()->id],
        ]);

        try {
            $user = $request->user();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->save();

            return $this->successResponse(
                new UserResource($user),
                'Profile updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update profile: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $user = $request->user();
            $user->password = Hash::make($validated['password']);
            $user->save();

            return $this->successResponse(null, 'Password updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update password: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Update avatar
     */
    public function updateAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        try {
            $user = $request->user();
            $filepath = $this->uploadFile($request->file('avatar'), $user->avatar);
            
            if ($filepath) {
                $user->avatar = $filepath;
                $user->save();
            }

            return $this->successResponse(
                new UserResource($user),
                'Avatar updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update avatar: ' . $e->getMessage(), null, 500);
        }
    }
}
