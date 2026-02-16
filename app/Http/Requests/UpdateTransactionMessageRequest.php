<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\TransactionMessage;



class UpdateTransactionMessageRequest extends FormRequest
{

    protected $errorBag = 'edit';
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
            'edit_message' => ['required', 'string', 'max:400'],
        ];
    }

    public function messages(): array
    {
        return [
            'edit_message.required' => '本文を入力してください',
            'edit_message.max' => '本文は400文字以内で入力してください',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $messageId = (int) $this->route('messageId');
        $message = TransactionMessage::findOrFail($messageId);

        $response = redirect()
            ->route('transaction.show', [
                'transactionId' => $message->transaction_id,
                'edit_message_id' => $messageId,
            ])
            ->withInput()
            ->withErrors($validator, $this->errorBag);

        throw new ValidationException($validator, $response);
    }
}
