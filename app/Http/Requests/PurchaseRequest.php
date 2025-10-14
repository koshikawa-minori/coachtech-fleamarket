<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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

        $validValues = implode(',', [
            Order::PAYMENT_CONVENIENCE_STORE_PAYMENT,
            Order::PAYMENT_CREDIT_CARD,
        ]);

        return [
            'payment_method' => ['required', 'integer', 'in:'. $validValues],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
            'payment_method.integer' => '支払い方法の形式が不正です',
            'payment_method.in' => '支払い方法の選択が不正です',
        ];
    }

}
