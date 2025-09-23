<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:20'],
            'image_path' => ['nullable', 'image', 'mimes:jpeg,png'],
            'postal_code' => ['nullable', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
