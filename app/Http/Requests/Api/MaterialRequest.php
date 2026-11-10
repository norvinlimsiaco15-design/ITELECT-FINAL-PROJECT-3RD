<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $materialId = $this->route('material')?->id;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', Rule::unique('services', 'slug')->ignore($materialId)],
            'excerpt' => ['nullable', 'string'],
            'unit' => ['required', 'string', 'max:50'],
            'quantity' => [$this->isMethod('post') ? 'required' : 'prohibited', 'numeric', 'min:0'],
            'min_stock_level' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'boolean'],
        ];
    }
}
