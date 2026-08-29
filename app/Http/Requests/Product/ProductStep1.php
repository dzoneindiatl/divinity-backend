<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductStep1 extends FormRequest
{
       public function authorize(): bool
    {
        return true; // Adjust if using authorization logic
    }

    public function rules(): array
    {
        return [
            'product_type'          => 'required|in:1,2',
            'main_category_id'      => 'required|exists:categories,id',
            'main_sub_category_id'  => 'nullable|exists:categories,id',
            'main_child_cate_id'    => 'nullable|exists:categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'product_type.required' => 'Product type is required.',
            'product_type.in'       => 'Invalid product type selected.',
            'main_category_id.required' => 'Main category is required.',
            'main_category_id.exists'   => 'Selected category does not exist.',
            'main_sub_category_id.exists' => 'Selected subcategory does not exist.',
            'main_child_cate_id.exists'  => 'Selected child category does not exist.',
        ];
    }
}
