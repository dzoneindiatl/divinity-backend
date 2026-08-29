<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\User;
use App\Models\CouponAssign;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\orderSuccessEmail;
use App\Helpers\EmailHelper;
use Illuminate\Support\Facades\Auth;


class CouponService
{
    public function store(array $data,$ip=null)
    {
        return DB::transaction(function () use ($data,$ip) {
            // Check if it's update or create
            $coupon = isset($data['coupon_id']) && !empty($data['coupon_id'])
                ? Coupon::findOrFail($data['coupon_id']) // update
                : new Coupon(); // create

            // Fill common fields
            $coupon->name              = $data['name'];
            $coupon->coupon_code       = $data['coupon_code'];
            $coupon->coupon_type       = $data['coupon_type'];
            $coupon->user_type         = $data['user_type'] ?? null;
            $coupon->discount_type     = $data['discount_type'];
            $coupon->discount_value    = $data['discount_value'];
            $coupon->max_discount      = $data['max_discount'] ?? null;
            $coupon->min_discount      = $data['min_discount'] ?? null;
            $coupon->start_date        = $data['start_date'] ?? null;
            $coupon->end_date          = $data['end_date'] ?? null;
            $coupon->is_unlimited      = $data['is_unlimited'] ?? 0;
            $coupon->per_user_avalibity = $data['per_user_avalibity'] ?? null;
            $coupon->available_coupons = $data['available_coupons'] ?? null;
            $coupon->min_cart_value    = $data['min_cart_value'] ?? null;
            $coupon->category_id       = $data['category'] ?? null;
            $coupon->is_active         = $data['is_active'] ?? 1;
            $coupon->description       = $data['description'] ?? null;

            $coupon->save();

            // Coupon edit Log 
                $couponNew = new Coupon();
                $couponNew->name              = $data['name'];
                $couponNew->coupon_code       = $data['coupon_code'];
                $couponNew->coupon_type       = $data['coupon_type'];
                $couponNew->user_type         = $data['user_type'] ?? null;
                $couponNew->discount_type     = $data['discount_type'];
                $couponNew->discount_value    = $data['discount_value'];
                $couponNew->max_discount      = $data['max_discount'] ?? null;
                $coupon->min_discount         = $data['min_discount'] ?? null;
                $couponNew->start_date        = $data['start_date'] ?? null;
                $couponNew->end_date          = $data['end_date'] ?? null;
                $couponNew->is_unlimited      = $data['is_unlimited'] ?? 0;
                $couponNew->per_user_avalibity = $data['per_user_avalibity'] ?? null;
                $couponNew->available_coupons = $data['available_coupons'] ?? null;
                $couponNew->min_cart_value    = $data['min_cart_value'] ?? null;
                $couponNew->category_id       = $data['category'] ?? null;
                $couponNew->is_active         = $data['is_active'] ?? 1;
                $couponNew->description       = $data['description'] ?? null;
                $couponNew->ip                = $ip;
                $couponNew->updated_by        = Auth::user()->id;
                $couponNew->updated_coupon_id = $coupon->id;
                $couponNew->save();
            // Coupon edit Log 

            // Attach Subcategories (store as JSON in DB field)
            if (!empty($data['sub_category'])) {
                $coupon->sub_categories = json_encode($data['sub_category']);
                $coupon->save();
            }
            if (!empty($data['child_category'])) {
                $coupon->child_category = json_encode($data['child_category']);
                $coupon->save();
            }

            // Attach Customer IDs (for private coupons)
            if ($coupon->coupon_type === 'private' && !empty($data['customer_name'])) {
                $users = User::whereIn('id', $data['customer_name'])->pluck('name', 'email')->toArray();                
                if (!empty($users)) {
                    $coupon->customers()->sync($data['customer_name']);
                    $couponDescription = !empty($coupon->description) ? strip_tags($coupon->description) : 'New coupon created for you!';

                    collect($users)->chunk(100)->each(function ($batch) use ($coupon, $couponDescription) {

                        foreach ($batch as $email => $name) {
                            //continue;
                            $data = [
                                'CUSTOMER_NAME' => $name,
                                'COUPON_TITLE' => $coupon->name,
                                'COUPON_CODE' => $coupon->coupon_code,
                                'COUPON_START_DATE' => !empty($coupon->start_date) ? date('d F, Y', strtotime($coupon->start_date)) : '-',
                                'EXPIRY_DATE' => !empty($coupon->end_date) ? date('d F, Y', strtotime($coupon->end_date)) : 'Never Expired',
                                'OFFER_DESCRIPTION' => $couponDescription,
                                'SHOP_URL' => env('WEBSITE_URL'),
                                'COMPANY_NAME' => 'VASVI',
                                'SUPPORT_EMAIL' => 'support@vasvi.in',
                            ];
                            $template = EmailHelper::getProcessedTemplate('coupon-assigned', $data);
                            $subject = (empty($template['subject']) || $template['subject'] == 'No Subject') ? 'Coupon Code: ' . $coupon->coupon_code : $template['subject'];
                            $body = $template['body'] ?? 'Template not found';
                            if ($body == 'Template not found') {
                                $body = 'Get discount for your next order. Apply This Coupon Code: ' . $coupon->coupon_code;
                            }
                            //$email = 'dzone.developers@gmail.com';
                            Mail::to($email)->queue(new orderSuccessEmail($subject, $body));
                        }
                    });
                }
            }else{
                $coupon->customers()->sync([]);
            }

            return true;
        });
    }
}
