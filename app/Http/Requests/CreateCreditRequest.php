<?php

namespace App\Http\Requests;

use App\Enums\Credit\Currency;
use App\Enums\Credit\InterestCalculationPeriod;
use App\Enums\Credit\ProductKey;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

class CreateCreditRequest extends FormRequest
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
            'credit_ref' => 'required|string',
            'borrower_name' => 'required|string',
            'product_key' => [
                'required',
                Rule::in(ProductKey::getKeys())
            ],
            'currency' => [
                'required',
                Rule::in(Currency::getKeys())
            ],
            'life_time' => 'required|int|min:1|max:365',
            'initial_principal' => 'required|int|min:1|max:10000000000',
            'interest_calculation_period' => [
                'required',
                Rule::in(InterestCalculationPeriod::getKeys())
            ],
            'interest_before_due' => 'required|decimal:0,6|min:0|max:1',
            'interest_after_due' => 'required|decimal:0,6|min:0|max:1',
            'grace_period' => 'required|int|min:0|max:365',
            'credited_at' => 'sometimes|date_format:Y-m-d H:i:s'
        ];
    }
}
