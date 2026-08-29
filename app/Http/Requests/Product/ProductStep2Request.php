<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductStep2Request extends FormRequest
{
     public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'variant' => 'nullable|array',
            'variant.*' => 'nullable|exists:variants,id|distinct',
            'variant_values' => 'nullable|array',
            'variant_values.*' => 'required_with:variant.*|array|min:1',
            'variant_values.*.*' => 'exists:variant_values,id',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Product ID is missing.',
            'variant.*.distinct' => 'Duplicate variant selected.',
            'variant_values.*.required_with' => 'Please select values for each selected variant.',
            'variant_values.*.*.exists' => 'One or more selected variant values are invalid.',
        ];
    }
}
