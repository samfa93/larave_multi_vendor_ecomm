<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FlashSaleStoreRequest extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sale_start' => 'required|date',
            'sale_end' => 'required|date',
            'products' => 'required|array',
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
