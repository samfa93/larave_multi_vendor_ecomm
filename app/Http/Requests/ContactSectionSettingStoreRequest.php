<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactSectionSettingStoreRequest extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'map_url' => ['nullable', 'url'],
            'title_one' => ['nullable', 'string', 'max:255'],
            'address_one' => ['nullable', 'string', 'max:255'],
            'phone_one' => ['nullable', 'string', 'max:255'],
            'email_one' => ['nullable', 'email', 'max:255'],

            'title_two' => ['nullable', 'string', 'max:255'],
            'address_two' => ['nullable', 'string', 'max:255'],
            'phone_two' => ['nullable', 'string', 'max:255'],
            'email_two' => ['nullable', 'email', 'max:255'],

            'title_three' => ['nullable', 'string', 'max:255'],
            'address_three' => ['nullable', 'string', 'max:255'],
            'phone_three' => ['nullable', 'string', 'max:255'],
            'email_three' => ['nullable', 'email', 'max:255'],
        ];
    }
}
