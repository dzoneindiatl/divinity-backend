<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Couriers;
use App\Models\EmailTemplate;
use App\Mail\orderSuccessEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmailHelper
{



    public static function getEmailTemplate($slug, $order, $ordermain)
    {

        $orderDetails = '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;">
                            <tr>
                                <th>Product Image</th>
                                <th>Product Name</th>
                                <th>Qty</th>
                                <th>Total Price</th>
                                <th>Combination</th>
                            </tr>';

        $combination = json_decode($order->combination, true);
        $combinationText = collect($combination)->map(function ($v, $k) {
            return ucfirst($k) . ': ' . $v;
        })->implode(', ');

        $valueIds = [];

        foreach ($combination as $variantName => $variantValue) {
            $variantId = DB::table('variants')
                ->where('name', $variantName)
                ->value('id');

            if ($variantId) {
                $valueId = DB::table('variant_values')
                    ->where('variant_id', $variantId)
                    ->where('name', $variantValue)
                    ->value('id');

                if ($valueId) {
                    $valueIds[] = $valueId;
                }
            }
        }

        // Find image matching any of the variant values
        $productimage = DB::table('product_graphics')
            ->where('product_id', $order->product_id)
            ->whereIn('variant_id', $valueIds)
            ->orderBy('id', 'Asc')
            ->first();
        $track = Couriers::where('id', $order->courier_id)->first();
        $orderDetails .= '<tr>
                                <td><img src=" ' . config('constant.PRODUCT_IMAGE_URL') . '/' . $productimage->graphic . ' " style="max-width: 85px; max-height: 120px;"></td>
                                <td>' . $order->product->name . '</td>
                                <td>' . $order->qty  . '</td>
                                <td>' . $order->total . '</td>
                               <td>' .  $combinationText . '</td>
                            </tr>';
        $orderDetails .= '</table>';
        $trackDetail = "";
        if (!empty($track->tracking_url) && !empty($order->awb_number)) {
            $trackDetail = '<div><p>Track Your Item <br><strong>Traking Url ' . $track->tracking_url . '</strong><br><strong> Traking Number ' . $order->awb_number . ' </strong></p></div>';
        }


        $user  = User::where('id', $ordermain->user_id)->first();
        $data = [
            'CUSTOMER_NAME' => $user->name,
            'ORDER_ID'      => $ordermain->order_number,
            'ORDER_DETAILS' => $orderDetails,
            'ORDER_STATUS' => $slug->name,
            'TRAKING_ORDER' => $trackDetail,
        ];
        $template = EmailHelper::getProcessedTemplate($slug, $data);

        if (!empty($user->email)) {
            Mail::to($user->email)->queue(new orderSuccessEmail($template['subject'], $template['body']));
        }
    }

    public static function getProcessedTemplate($slug, $data = [])
    {
        $newSlug = is_string($slug) ? $slug : $slug->slug;
        $template = EmailTemplate::where('slug', $newSlug)->first();
        if (!$template) {
            return ['subject' => 'No Subject', 'body' => 'Template not found'];
        }

        $search  = array_map(fn($k) => '{' . $k . '}', array_keys($data));
        $replace = array_values($data);

        $subject = str_replace($search, $replace, $template->subject);
        $body    = str_replace($search, $replace, $template->body);

        return ['subject' => $subject, 'body' => $body];
    }
}