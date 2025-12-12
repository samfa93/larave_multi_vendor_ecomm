<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ShippingRuleStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:minimum_order_amount,flat_amount'],
            'minimum_amount' => ['required_if:type,minimum_order_amount', 'numeric', 'nullable'],
            'charge' => ['required', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
