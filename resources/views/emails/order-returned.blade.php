@include('emails.header')
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td
            style="font-size: 20px; line-height: 23px; padding: 18px 0 8px; text-align: center; color:#000;">
            We’ve received your return
        </td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td
            style="font-size: 14px; line-height: 23px; padding: 20px 0 8px; text-align: center; color:#000;">
            You can track your order using the tracking link below or log in to 'My Account'
            to check delivery status. Your receipt will be sent in a separate email.
        </td>
    </tr>
</table>
 
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td
            style="font-size: 18px; line-height: 23px; padding: 20px 0; text-align: center; color:#000;">
            Dispatched items
        </td>
    </tr>
</table>
@foreach($order->items as $item)
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding-top: 30px;">
    <tr>
        <td style="background:#ffffff; display: flex; flex-direction: row;">
            <table width="35%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="text-align:center; background:#cecfcf;">
                    @if($item->product->product_type == 1)           
           <img src="{{ $item->product->front_image }}" alt="{{ $item->product->name }}" width="180">
            @else
           <?php  
            $productvariantcombinations = DB::table('product_variant_combinations')->select('*')->where('id', $item->product_variant_combinations_id)->orderBy('id', 'Desc')->first();
            if (!empty($productvariantcombinations))	 {
            $productimage = DB::table('product_color_images')->select('*')->where('product_id', $item->product_id)->where('color_id', $productvariantcombinations->color_variant_value_id)->orderBy('id', 'Desc')->first();            
            if (!empty($productimage))	 { ?>           
            <img src="{{ config('constant.PRODUCT_IMAGE_URL') . '/' . $productimage->image }}" alt="{{ $item->product->name }}" width="180">
           <?php } }   ?>
             
            @endif    
                    </td>
                </tr>
            </table>
            <table width="65%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="padding: 0 20px;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="font-size: 14px;">
                                    <a href="#" style="color:#000;">{{ $item->product->name }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 14px; padding-bottom: 30px;">
                                    <a href="#" style="color:#000;">₹{{ $item->total /  $item->qty }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 14px; font-weight:400;">
                                    <table width="100%" border="0" cellspacing="0"
                                        cellpadding="0">
                                        <tr>
                                            <td style="font-size: 14px;"><a href="#"
                                                    style="color:#000; line-height: 11px;">Color
                                                </a></td>
                                            <td style="font-size: 14px;"><a href="#"
                                                    style="color:#000; line-height: 11px;">{{ $item->color }}</a></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 14px;"><a href="#"
                                                    style="color:#000; line-height: 11px;">Size</a>
                                            </td>
                                            <td style="font-size: 14px;"><a href="#"
                                                    style="color:#000; line-height: 11px;">{{ $item->size }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 14px;"><a href="#"
                                                    style="color:#000; line-height: 11px;">Quantity</a>
                                            </td>
                                            <td style="font-size: 14px;"><a href="#"
                                                    style="color:#000; line-height: 11px;">{{ $item->qty }}</a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="font-size: 14px; text-align: right; padding-top: 10px;">
                                    
                                    Total ₹{{ $item->total }}</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endforeach
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding-top: 30px;">
    <tr>
        <td style="background:#ffffff; display: flex; flex-direction: row;">
            <table width="65%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="padding: 20px 20px;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr><td style="font-size: 16px;">Order number</td></tr>
                            <tr>
                                <td style=" padding-bottom: 30px;">
                                    <a href="#" style="color:#000; font-weight: 500; font-size: 18px;">{{ $order->order_number }}</a>
                                </td>
                            </tr>
                            <tr><td style="font-size: 16px;">Order date</td></tr>
                            <tr>
                                <td style=" padding-bottom: 30px;">
                                    <a href="" style="color:#000; font-weight: 500; font-size: 18px;">{{ date('d/m/Y', strtotime($order->created_at)) }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 14px; padding-bottom: 5px;">Delivery Method</td>
                            </tr>
                        </table>
                        <table>
                            <tr>
                                <td style="font-size: 14px; padding-bottom: 5px;">Standard Delivery</td>
                            </tr>
                            <tr>
                                <td style=" padding-bottom: 30px; font-size: 14px;">2-7 Days
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 14px; padding-bottom: 5px;">Mode Of Payment </td>
                            <tr>
                        </table>
                        <table>
                            <tr>
                                <td style="font-size: 14px; padding-bottom: 30px;">{{ $order->payment_method }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@include('emails.footer')