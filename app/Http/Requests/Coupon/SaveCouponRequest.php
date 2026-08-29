<?php

namespace App\Http\Requests\Coupon;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveCouponRequest extends FormRequest
{
    public function rules(): array
    {
        $couponId = $this->input('coupon_id');

        return [
            'name'              => 'required|string|max:255',
            'coupon_code' => [
                'required',
                'string',
                'max:255',
                $couponId
                    ? Rule::unique('coupons', 'coupon_code')->ignore($couponId)
                    : Rule::unique('coupons', 'coupon_code'),
            ],
            'coupon_type'       => 'required|in:public,private',
            'user_type'         => 'required_if:coupon_type,public',
            'customer_name'     => 'required_if:coupon_type,private|array',
            'customer_name.*'   => 'exists:users,id',
            'discount_type'     => 'required|in:flat,percentage',
            'discount_value'    => 'required|numeric|min:0',
            'max_discount'      => 'nullable|numeric|min:0',
            'min_discount'      => 'nullable|numeric|min:0',

            'start_date'        => 'nullable|date|after_or_equal:today',
            'end_date'          => 'nullable|date|after_or_equal:start_date',

            //'is_unlimited'      => 'nullable|required|in:0,1',
            'available_coupons' => 'required_if:is_unlimited,0|nullable|integer|min:1',
            'min_cart_value'    => 'nullable|numeric|min:0',

            'category'          => 'nullable',
            'sub_category'      => 'nullable|array',
            'sub_category.*'    => 'nullable',

            //'is_active'         => 'nullable|required|in:0,1',

            'description'       => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Coupon name is required.',
            'coupon_code.required' => 'Coupon code is required.',
            'coupon_code.unique' => 'Coupon code must be unique.',
            'available_coupons.required_if' => 'Please provide number of available coupons.',
            'user_type.required_if' => 'Please select a user type.',
            'customer_name.required_if' => 'Please select at least one customer.',
        ];
    }
}
