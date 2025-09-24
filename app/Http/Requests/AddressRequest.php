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
        return [
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required', 'string', 'max:255'],
        ];
    }

    ////いるかいらないかコーチに確認中
    public function messages(): array
    {
        return[
        'postal_code.regex' => '郵便番号は「123-4567」の形式で入力してください',
        'address.max' => '住所は255文字以内で入力してください',
        ];
    }
}
