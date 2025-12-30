<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar ? asset($this->avatar) : null,
            'user_type' => $this->user_type,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at?->toISOString(),
            'store' => $this->when($this->user_type === 'vendor' && $this->store, function () {
                return [
                    'id' => $this->store->id,
                    'name' => $this->store->name,
                    'slug' => $this->store->slug,
                    'logo' => $this->store->logo ? asset($this->store->logo) : null,
                ];
            }),
        ];
    }
}
