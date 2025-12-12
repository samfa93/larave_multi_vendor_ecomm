<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawMethodStoreRequest extends FormRequest
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
            'instruction' => ['required', 'string', 'max:2000'],
            'minimum_amount' => ['required', 'numeric'],
            'maximum_amount' => ['required', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
