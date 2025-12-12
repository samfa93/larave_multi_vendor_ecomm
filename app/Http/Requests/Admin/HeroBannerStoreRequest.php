<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HeroBannerStoreRequest extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'banner_one' => ['nullable', 'image', 'max:2048'],
            'banner_two' => ['nullable', 'image', 'max:2048'],
            'title_one' => ['required', 'string', 'max:255'],
            'title_two' => ['required', 'string', 'max:255'],
            'btn_url_one' => ['required', 'string', 'max:255'],
            'btn_url_two' => ['required', 'string', 'max:255'],
        ];
    }
}
