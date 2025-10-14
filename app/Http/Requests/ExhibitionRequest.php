<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'file', 'mimes:jpeg,png'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'condition' => ['required', 'integer', 'in:1,2,3,4'],
            'price' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
        {
            return [
                'name.required' => '商品名を入力してください',
                'name.max' => '商品名は255文字以内で入力してください',
                'description.required' => '商品説明を入力してください',
                'description.max' => '商品説明は255文字以内で入力してください',
                'brand.max' => 'ブランド名は255文字以内で入力してください',
                'image.required' => '商品画像を選択してください',
                'image.mimes' => '商品画像はjpegまたはpng形式でアップロードしてください',
                'category_ids.required' => 'カテゴリーを1つ以上選択してください',
                'category_ids.array' => 'カテゴリーの形式が正しくありません',
                'category_ids.min' => 'カテゴリーを1つ以上選択してください',
                'category_ids.*.integer' => 'カテゴリーの指定が正しくありません',
                'category_ids.*.exists' => '存在しないカテゴリーが含まれています',
                'condition.required' => '商品の状態を選択してください',
                'condition.integer' => '商品の状態の選択が正しくありません',
                'condition.in' => '商品の状態の選択が正しくありません',
                'price.required' => '価格を入力してください',
                'price.integer' => '価格は整数で入力してください',
                'price.min' => '価格は0円以上で入力してください',

            ];
        }


}
