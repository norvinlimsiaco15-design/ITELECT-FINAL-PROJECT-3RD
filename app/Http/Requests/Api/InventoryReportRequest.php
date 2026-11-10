<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class InventoryReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'report_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'submit' => ['nullable', 'boolean'],
        ];
    }
}
