<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransactionRequest extends FormRequest
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
            'transaction_key' => 'required|string',
            'sequence' => 'sometimes|integer',
            'items' => 'required|array',
            'items.*' => 'required|array:account_id,amount_subunit,currency',
            'items.*.account_id' => 'required|uuid',
            'items.*.amount_subunit' => 'required|integer',
            'items.*.currency' => 'required|string|max:3',
        ];
    }
}
