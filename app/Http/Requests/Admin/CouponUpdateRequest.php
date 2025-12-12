<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CouponUpdateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255', 'unique:coupons,code,' . $this->route('coupon')->id],
            'value' => ['required', 'numeric'],
            'is_percent' => ['required', 'boolean'],
            'minimum_spend' => ['required', 'numeric'],
            'maximum_spend' => ['required', 'numeric'],
            'usage_limit_per_coupon' => ['required', 'numeric'],
            'usage_limit_per_customer' => ['required', 'numeric'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
