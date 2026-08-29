<?php 

namespace App\Services;
use App\Mail\SendMail;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;
 
Class MailService 
{
    public function OrderConfirmed($userData,$orderItem){
 
        $email = $userData->email;
        $name = $userData->name;
        $subject = "Order Confirmed";
        \Log::info("-----order Item----",[$orderItem]);
        \Log::info('------userData----',[$userData]);
        $template = EmailTemplate::where('name', 'Order Confirmed')->first();
        if (!$template) {
            \Log::error('Order Confirmed email template not found');
            return false;
        }
        $orderDetails = '
                <table width="100%" cellpadding="8" cellspacing="0"
                    style="border-collapse:collapse; border:1px solid #ddd;">
 
                    <thead>
                        <tr>
                            <th style="border:1px solid #ddd; text-align:left;">
                                Product
                            </th>
 
                            <th style="border:1px solid #ddd; text-align:center;">
                                Qty
                            </th>
 
                            <th style="border:1px solid #ddd; text-align:right;">
                                Price
                            </th>
 
                            <th style="border:1px solid #ddd; text-align:right;">
                                Total
                            </th>
                        </tr>
                    </thead>
 
                    <tbody>
            ';
            foreach ($orderItem as $item)
            {
                $productName = $item->product->name; 
                $qty = $item->qty ?? 1;
                $price = $item->selling_price ?? 0;
                $total = $item->total ?? ($qty * $price);
 
                $orderDetails .= '
                    <tr>
                        <td style="border:1px solid #ddd;">
                            ' . e($productName) . '
                        </td>
 
                        <td style="border:1px solid #ddd; text-align:center;">
                            ' . $qty . '
                        </td>
 
                        <td style="border:1px solid #ddd; text-align:right;">
                            ₹' . number_format($price, 2) . '
                        </td>
 
                        <td style="border:1px solid #ddd; text-align:right;">
                            ₹' . number_format($total, 2) . '
                        </td>
                    </tr>
                ';
            }
            $orderDetails .= '
                    </tbody>
                </table>
            ';
            $body = $template->body;
            $body = strtr($body, [
            '{ORDER_STATUS}'  => 'Confirmed',
 
            '{CUSTOMER_NAME}' => $name,
 
            '{ORDER_ID}'      => $orderItem->first()->order_id ?? '',
 
            '{ORDER_DETAILS}' => $orderDetails,
        ]);
        $this->sendmail($email,$subject,$body);
    }
 
    public function orderProcessing($userData,$orderItem){
        $email = $userData->email;
        $name = $userData->name;
       
        \Log::info("-----order Item----",[$orderItem]);
        \Log::info('------userData----',[$userData]);
        $template = EmailTemplate::where('name', 'Order Processing')->first();
        if (!$template) {
            \Log::error('Order Processing email template not found');
            return false;
        }
        $orderDetails = '<table width="100%" border="1" cellspacing="0" cellpadding="8">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>';
 
                    foreach ($orderItem as $item) {
 
                        $orderDetails .= '
                            <tr>
                                <td>'.$item->product->name.'</td>
                                <td>'.$item->qty.'</td>
                                <td>₹'.$item->selling_price.'</td>
                            </tr>';
                    }
 
                    $orderDetails .= '
                        </tbody>
                    </table>';
        $subject = $template->subject;
        $body = $template->body;
        $body = strtr($body, [
                '{ORDER_STATUS}'  => 'Order Processing',
                '{CUSTOMER_NAME}' => $name,
                '{ORDER_ID}'      => $item->order->order_number,
                '{ORDER_DETAILS}' => $orderDetails,
            ]);
        $this->sendmail($email,$subject,$body);
    }
 
    public function orderShipped($userData,$orderItem){
        $email = $userData->email;
        $name = $userData->name;
       
        $template = EmailTemplate::where('name', 'Order Shipped')->first();
        if (!$template) {
            \Log::error('Order Shipped email template not found');
            return false;
        }
        \Log::info("-----order Item----",[$orderItem]);
        \Log::info('------userData----',[$userData]);
        $orderDetails = '<table width="100%" border="1" cellspacing="0" cellpadding="8">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>';
 
                    foreach ($orderItem as $item) {
 
                        $orderDetails .= '
                            <tr>
                                <td>'.$item->product->name.'</td>
                                <td>'.$item->qty.'</td>
                                <td>₹'.$item->selling_price.'</td>
                            </tr>';
                    }
 
                    $orderDetails .= '
                        </tbody>
                    </table>';
                 $subject = $template->subject;
        $body = $template->body;
        $body = strtr($body, [
                '{ORDER_STATUS}'  => 'Order Shipped',
                '{CUSTOMER_NAME}' => $name,
                '{ORDER_ID}'      => $item->order->order_number,
                '{ORDER_DETAILS}' => $orderDetails,
                '{COURIER_NAME}' => $item->order->courier->name,
                '{TRACKING_NUMBER}'=> $item->order->awb_number,
                '{TRACKING_URL}' =>$item->order->tracking_url,
            ]);
           
           $this->sendmail($email,$subject,$body);  
    }

    public function orderInTransit($userData,$orderItem){
        $email = $userData->email;
        $name = $userData->name;
       
        $template = EmailTemplate::where('name', 'Order In Transit')->first();
        if (!$template) {
            \Log::error('Order In Transit email template not found');
            return false;
        }
        $subject = $template->subject; 
        $body = $template->body; 
        $orderDetail = '<table style="width:100%; border-collapse:collapse; margin:20px 0; font-family:Arial,sans-serif;">

                    <thead>
                        <tr>
                            <th style="padding:12px 10px; text-align:left; background:#f8f8f8; border-bottom:1px solid #ddd;">
                                Product
                            </th>

                            <th style="padding:12px 10px; text-align:center; background:#f8f8f8; border-bottom:1px solid #ddd;">
                                Qty
                            </th>

                            <th style="padding:12px 10px; text-align:right; background:#f8f8f8; border-bottom:1px solid #ddd;">
                                Price
                            </th>

                            <th style="padding:12px 10px; text-align:right; background:#f8f8f8; border-bottom:1px solid #ddd;">
                                Total
                            </th>
                        </tr>
                    </thead>';
            foreach($orderItem as $item){
                    $qty = $item->qty ?? 1;
                    $price = $item->selling_price ?? 0;
                    $total = $item->total ?? ($qty * $price);
                    $productName = $item->product->name ; 
                    $courierName = $item->order->courier->name; 
                    $orderDetail .= '
                    <tr>
            <td style="padding:12px 10px; border-bottom:1px solid #eee;">
                <strong>' . e($productName) . '</strong>
            </td>

            <td style="padding:12px 10px; text-align:center; border-bottom:1px solid #eee;">
                ' . e($item->qty) . '
            </td>

            <td style="padding:12px 10px; text-align:right; border-bottom:1px solid #eee;">
                ₹' . number_format($item->selling_price, 2) . '
            </td>

            <td style="padding:12px 10px; text-align:right; border-bottom:1px solid #eee;">
                ₹' . number_format($total, 2) . '
            </td>

        </tr>
                ';  
            }        
            $orderDetail .= '</tbody></table>';
              $body = strtr($body, [
                '{ORDER_STATUS}'  => 'Order Processing',
                '{CUSTOMER_NAME}' => $name,
                '{ORDER_ID}'      => $item->order->order_number,
                '{ORDER_DETAILS}' => $orderDetail,
                '{COURIER_NAME}'  => $courierName,
                '{TRACKING_NUMBER}'=> $item->order->awb_number,
            ]);
            $this->sendmail($email,$subject,$body);  
    }

    public function orderOutForDelivery($userData,$orderItem){
        $template = EmailTemplate::where('name', 'Order Out for Delivery')->first();
        if (!$template) {
            \Log::error('Order Out for Delivery email template not found');
            return false;
        }
        
        $email = $userData->email; 
        $subject = $template->subject; 
        $body = $template->body; 

        $orderDetail = '<table style="width:100%; border-collapse:collapse; margin:20px 0; font-family:Arial,sans-serif;">

                    <thead>
                        <tr>

                            <th style="
                                padding:12px 10px;
                                text-align:left;
                                background:#f8f8f8;
                                border-bottom:1px solid #ddd;
                            ">
                                Product
                            </th>

                            <th style="
                                padding:12px 10px;
                                text-align:center;
                                background:#f8f8f8;
                                border-bottom:1px solid #ddd;
                            ">
                                Qty
                            </th>

                            <th style="
                                padding:12px 10px;
                                text-align:right;
                                background:#f8f8f8;
                                border-bottom:1px solid #ddd;
                            ">
                                Price
                            </th>

                            <th style="
                                padding:12px 10px;
                                text-align:right;
                                background:#f8f8f8;
                                border-bottom:1px solid #ddd;
                            ">
                                Total
                            </th>

                        </tr>
                    </thead>

                    <tbody>';


        foreach ($orderItem as $item) {

            $quantity = $item->qty ?? 1;
            $price = $item->selling_price ?? 0;
            $orderId = $item->order->id; 
            $itemTotal = $quantity * $price;

            $productName = $item->product->name ?? 'Product';
            
            $orderDetail .= '
                    <tr>

                        <td style="
                            padding:12px 10px;
                            border-bottom:1px solid #eee;
                        ">
                            <strong>' . e($productName) . '</strong>
                        </td>

                        <td style="
                            padding:12px 10px;
                            text-align:center;
                            border-bottom:1px solid #eee;
                        ">
                            ' . e($quantity) . '
                        </td>

                        <td style="
                            padding:12px 10px;
                            text-align:right;
                            border-bottom:1px solid #eee;
                        ">
                            ₹' . number_format($price, 2) . '
                        </td>

                        <td style="
                            padding:12px 10px;
                            text-align:right;
                            border-bottom:1px solid #eee;
                        ">
                            ₹' . number_format($itemTotal, 2) . '
                        </td>

                    </tr>
                ';
        }
        $orderDetail .='</tbody></table>'; 
        $orderStatus = "Out For Delivery"; 
        $body = str_replace(
            [   
                '{ORDER_ID}',
                '{ORDER_STATUS}',
                '{CUSTOMER_NAME}',
                '{ORDER_DETAILS}',
            ],
            [   
                $orderId,
                $orderStatus,
                $userData->name,
                $orderDetail,
            ],
            $body
        );

        $this->sendmail($email,$subject,$body);  
    }

    public function orderDelivered($userData, $orderItem)
    {
        $email = $userData->email; 
        $customerName = $userData->name; 
        $template = EmailTemplate::where('name','Order Delivered')->first(); 

        $subject = $template->subject; 
        $body = $template->body; 

        $orderDetail = '
            <table style="width:100%; border-collapse:collapse; margin:20px 0; font-family:Arial,sans-serif;">

                <thead>
                    <tr>
                        <th style="
                            padding:12px 10px;
                            text-align:left;
                            background:#f8f8f8;
                            border-bottom:1px solid #ddd;
                        ">
                            Product
                        </th>

                        <th style="
                            padding:12px 10px;
                            text-align:center;
                            background:#f8f8f8;
                            border-bottom:1px solid #ddd;
                        ">
                            Qty
                        </th>

                        <th style="
                            padding:12px 10px;
                            text-align:right;
                            background:#f8f8f8;
                            border-bottom:1px solid #ddd;
                        ">
                            Price
                        </th>

                        <th style="
                            padding:12px 10px;
                            text-align:right;
                            background:#f8f8f8;
                            border-bottom:1px solid #ddd;
                        ">
                            Total
                        </th>
                    </tr>
                </thead>

                <tbody>
        ';

        foreach ($orderItem as $item) {

            $quantity = $item->qty ?? 1;
            $price = $item->selling_price ?? 0;
            $itemTotal = $quantity * $price;
            $trackingNumber = $item->order->awb_number; 
            info("order data------------",[
                'order_id' => $item->order?->id,
                'trackingNumber'=>$item->order->awb_number,
                'delivery_partner_name' => $item->order?->delivery_partner_name,
                'courier' => $item->order?->courier,
            ]);
            $courierName = $item->order->courier->name ?? "Delhivery"; 
            $productName = $item->product->name ?? 'Product';

            $orderDetail .= '
                    <tr>

                        <td style="
                            padding:12px 10px;
                            border-bottom:1px solid #eee;
                        ">
                            <strong>' . e($productName) . '</strong>
                        </td>

                        <td style="
                            padding:12px 10px;
                            text-align:center;
                            border-bottom:1px solid #eee;
                        ">
                            ' . e($quantity) . '
                        </td>

                        <td style="
                            padding:12px 10px;
                            text-align:right;
                            border-bottom:1px solid #eee;
                        ">
                            ₹' . number_format($price, 2) . '
                        </td>

                        <td style="
                            padding:12px 10px;
                            text-align:right;
                            border-bottom:1px solid #eee;
                        ">
                            ₹' . number_format($itemTotal, 2) . '
                        </td>

                    </tr>
            ';
        }

        $orderDetail .= '
                </tbody>
            </table>
        ';
        $orderId = $orderItem->first()->order_id ?? '';

        $body = str_replace(
            [
                '{CUSTOMER_NAME}',
                '{ORDER_ID}',
                '{ORDER_DETAILS}',
                '{TRACKING_NUMBER}',
                '{COURIER_NAME}'
            ],
            [
                $customerName,
                $orderId,
                $orderDetail,
                $trackingNumber,
                $courierName
            ],
            $body
        );
        $this->sendmail($email,$subject,$body); 
    }

    public function orderReturnAccepted(){

    }
    public function sendmail($email,$subject,$body){
        Mail::to($email)->send(new Sendmail($subject,$body));
 
    }
}