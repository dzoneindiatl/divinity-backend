<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class ProductStep3Request extends FormRequest
{
    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
   public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'country_origin' => 'nullable|integer',
            'sku' => 'required|string|max:100|unique:products,sku,' . $this->product_id,
            'hsn' => 'nullable|string|max:100',
            'bar_code' => 'nullable|string|max:100',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'specification' => 'nullable|string',
            'product_details' => 'nullable|string',
            'others' => 'nullable|string',
            'wash_care' => 'nullable|string',
            'weight' => 'nullable|string|max:50',
            'weight_type' => 'nullable|string|max:50',
            'categorys_id' => 'nullable|integer',
            'subcategory_id' => 'nullable|integer',
            'buying_price' => 'nullable|numeric',
            'discount_type' => 'nullable|in:flat,percentage',
            'discount' => 'nullable|numeric',
            'selling_price' => 'required|numeric',
            'qty' => 'required|numeric',
            'max_selling_units' => 'nullable|numeric',
            'min_selling_units' => 'nullable|numeric',

            'variant_name' => 'nullable|array',
            'variant_name.*' => 'string',

            'variant_id' => 'nullable|array',
            'variant_id.*' => 'integer',

            'variant_sku' => 'nullable|array',
            'variant_sku.*' => 'string',

            'variant_price' => 'nullable|array',
            'variant_price.*' => 'nullable|numeric',

            'variant_sale_price' => 'nullable|array',
            'variant_sale_price.*' =>  'nullable|numeric',

            'variant_qty' => 'nullable|array',
            'variant_qty.*' =>  'nullable|numeric',

            'variant_discount_type' => 'nullable|array',
            'variant_discount_type.*' => 'nullable|string|in:flat,percentage',

            'variant_discount' => 'nullable|array',
            'variant_discount.*' => 'nullable|numeric',

            'product_crop_images' => 'nullable|array',
            'product_crop_images.*' => 'nullable|array',
            'product_crop_images.*.*' => 'nullable',

            'variant_video' => 'nullable|array',
            'variant_video.*' => 'nullable|file|mimes:mp4,mov,avi|max:1024',

            'front_image' => 'nullable|array',
            'back_image' => 'nullable|array',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }

}
