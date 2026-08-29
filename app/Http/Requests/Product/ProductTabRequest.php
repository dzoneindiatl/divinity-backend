<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductTabRequest extends FormRequest
{
  

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku')
            ],
            'description' => 'required|string',
            'specification' => 'required|string',
            'buying_price' => 'required|numeric',
            // 'selling_price' => 'required|numeric',
            'qty' => 'required|integer',
            'product_type' => 'required|in:1,2', // adjust if product types differ
            'main_category_id' => 'required|exists:categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'sku.required' => 'SKU is required.',
            'sku.unique' => 'This SKU already exists. Please choose another one.',
            'description.required' => 'Product Description is required.',
            'specification.required' => 'Product Specification is required.',
            'buying_price.required' => 'MRP is required.',
            // 'selling_price.required' => 'Selling Price is required.',
            'qty.required' => 'Quantity is required.',
            'product_type.required' => 'Product Type is required.',
            'main_category_id.required' => 'Main Family / Category is required.',
            'main_category_id.exists' => 'Selected category does not exist.',
        ];
    }
}
