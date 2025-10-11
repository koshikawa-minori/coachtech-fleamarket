<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
        $rules = [
            'building' => ['nullable', 'string', 'max:255'],
        ];

        if (!$this->user()->profile ||
            !$this->user()->profile->postal_code ||
            !$this->user()->profile->address
        ) {
            $rules['postal_code'] = ['required', 'regex:/^\d{3}-\d{4}$/'];
            $rules['address'] = ['required', 'string', 'max:255'];
        } else {
            $rules['postal_code'] = ['nullable', 'regex:/^\d{3}-\d{4}$/'];
            $rules['address'] = ['nullable', 'string', 'max:255'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return[
        'postal_code.required' => '郵便番号を入力してください',
        'postal_code.regex' => '郵便番号は「123-4567」の形式で入力してください',
        'address.required' => '住所を入力してください',
        'address.max' => '住所は255文字以内で入力してください',
        'building.max' => '建物名は255文字以内で入力してください',
        ];
    }
}
