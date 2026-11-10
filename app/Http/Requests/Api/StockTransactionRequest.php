<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StockTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_id' => ['required', 'exists:services,id'],
            'type' => ['required', 'in:in,out,adjustment'],
            'adjustment_direction' => ['required_if:type,adjustment', 'nullable', 'in:increase,decrease'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'transaction_date' => ['required', 'date'],
            'party_name' => ['required_if:type,in,out', 'nullable', 'string', 'max:255'],
            'party_details' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.gt' => 'Quantity must be greater than zero.',
            'party_name.required_if' => 'Enter the supplier for stock-in, or the customer for stock-out.',
            'adjustment_direction.required_if' => 'Choose whether the adjustment increases or decreases stock.',
        ];
    }
}
