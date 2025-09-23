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

    //いるかいらないかコーチに確認中
    public function messages(): array
    {
        return [
            'name.required' => 'お名前を入力してください',
            'name.max' => 'お名前は20文字以内で入力してください',
            'image_path.mimes' => 'プロフィール画像はjpegまたはpng形式でアップロードしてください',
            'postal_code.regex' => '郵便番号は「123-4567」の形式で入力してください',
            'address.max' => '住所は255文字以内で入力してください',
        ];
    }

}
