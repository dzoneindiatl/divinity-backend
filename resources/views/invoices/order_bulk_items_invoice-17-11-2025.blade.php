<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="favicon.png" type="image/x-icon">
    <link rel="icon" href="favicon.png" type="image/x-icon">
    <title>Tax Invoice</title>
</head>

<body
    style="background-color: #fbfbfb; padding: 0; margin: 0; font-family: 'Nunito Sans', sans-serif; font-weight: 400;">

    @if (!empty($itemsData))
    @php
        $itemCount = 0;
    @endphp
    @foreach ($itemsData as $checkout)
    @php
        $order = $checkout?->order;
        $itemCount++;
    @endphp
    <table width="100%" border="0" cellspacing="0" cellpadding="0"
        style="background-color:#ffffff; padding: 20px 20px 20px 20px; width: 100%; max-width: 1116px; margin: 0 auto; border: 1px solid #eee;">
        <tr>
            <td style="font-size: 24px; font-weight: 700; padding-bottom: 15px;text-align: center;">Tax Invoice</td>
        </tr>
        <tr>
            <td>
                <table width="100%">
                    <tr>
                        <td>
                            <p
                                style="font-size: 14px; margin-bottom: 5px; margin-top: 5px;font-family: 'Nunito Sans', sans-serif;">
                                <b>Invoice Number:</b> {{ $supplySetting->invoice_number }}/{{ $order->id }}
                            </p>
                            <p
                                style="font-size: 14px; margin-bottom: 5px; margin-top: 5px;font-family: 'Nunito Sans', sans-serif;">
                                <b>Order Number:</b>
                                {{ !empty($supplySetting->order_prefix) ? $supplySetting->order_prefix . '/' : '' }}{{ $order->order_number }}
                            </p>
                            <p
                                style="font-size: 14px; margin-bottom: 5px; margin-top: 5px;font-family: 'Nunito Sans', sans-serif;">
                                <b>Nature of Transaction:</b> {{ $order->payment_method }}
                            </p>
                            <p style="font-size: 14px; margin-bottom: 0px; margin-top: 5px;"><b>Place of Supply :</b>
                                {{ $supplySetting->city->name }}, {{ $supplySetting->state->name }},
                                {{ $supplySetting->country->name }}</p>
                        </td>
                        <td align="right">
                            <!-- <p style="font-size: 14px; margin-bottom: 5px; margin-top: 5px;font-family: 'Nunito Sans', sans-serif;"><b>PacketID:</b> {{ $supplySetting->packet_id }}{{ $order->id }}   </p> -->
                            <p
                                style="font-size: 14px; margin-bottom: 5px; margin-top: 5px;font-family: 'Nunito Sans', sans-serif;">
                                <b>Invoice Date: </b> {{ $order?->created_at?->format('d M, Y h:i a') }}
                            </p>
                            <p
                                style="font-size: 14px; margin-bottom: 5px; margin-top: 5px;font-family: 'Nunito Sans', sans-serif;">
                                <b>Order Date:</b> {{ $order?->created_at?->format('d M, Y h:i a') }}
                            </p>
                            <p
                                style="font-size: 14px; margin-bottom: 5px; margin-top: 5px;font-family: 'Nunito Sans', sans-serif;">
                                <b>Nature of Supply:</b> {{ $supplySetting->nature_spilly }}
                            </p>
                            <p
                                style="font-size: 14px; margin-bottom: 5px; margin-top: 5px;font-family: 'Nunito Sans', sans-serif;">
                                <b>GST No:</b> {{ !empty($GSTIN) ? $GSTIN :''  }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <hr>
            </td>
        </tr>
        @php
            $bAddress = json_decode($order->billing_address);
            $sAddress = json_decode($order->shipping_address);
        @endphp
        <tr>
            <td>
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p style="margin-bottom: 10px;font-size: 14px;font-family: 'Nunito Sans', sans-serif;">
                                <b>Bill to / Ship to:</b>
                            </p>
                            <p style="margin-bottom: 0;margin-top: 0;font-family: 'Nunito Sans', sans-serif;">
                                {{ $bAddress->billing_customer_name }}</p>
                            <p
                                style="margin-bottom: 0;margin-top: 0;font-size: 14px;font-family: 'Nunito Sans', sans-serif;">
                                {{ $bAddress->billing_address }},
                                {{ $bAddress->billing_city }},
                                {{ $bAddress->billing_state }} - {{ $bAddress->billing_pincode }} ,
                                {{ $bAddress->billing_country }}</p>
                            <p
                                style="margin-bottom: 0;margin-top: 0;font-size: 14px;font-family: 'Nunito Sans', sans-serif;">
                                Phone No: {{ $bAddress->billing_phone }}</p>
                        </td>
                        <td width="50%">
                            <p><b>Customer Type:</b> Reregistered </p>
                        </td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td>
                            <p><b>Bill From: </b></p>
                            <p style="margin-bottom: 0;margin-top: 0;font-family: 'Nunito Sans', sans-serif;">
                                {{ $supplySetting->website_name }}</p>
                            <p
                                style="margin-bottom: 0;margin-top: 0;font-size: 14px;font-family: 'Nunito Sans', sans-serif;">
                                Address: {{ $supplySetting->address }},
                                {{ $supplySetting->city->name }}, {{ $supplySetting->state->name }} -
                                {{ $supplySetting->pincode }} , {{ $supplySetting->country->name }}</p>
                            <p
                                style="margin-bottom: 0;margin-top: 0;font-size: 14px;font-family: 'Nunito Sans', sans-serif;">
                                Phone No: {{ $supplySetting->phone_number }}</p>

                        </td>
                        <td>
                            <p><b>Ship From: </b></p>
                            <p style="margin-bottom: 0;margin-top: 0;font-family: 'Nunito Sans', sans-serif;">
                                {{ $supplySetting->website_name }}</p>
                            <p
                                style="margin-bottom: 0;margin-top: 0;font-size: 14px;font-family: 'Nunito Sans', sans-serif;">
                                Address: {{ $supplySetting->address }},
                                {{ $supplySetting->city->name }}, {{ $supplySetting->state->name }} -
                                {{ $supplySetting->pincode }} , {{ $supplySetting->country->name }}</p>
                            <p
                                style="margin-bottom: 0;margin-top: 0;font-size: 14px;font-family: 'Nunito Sans', sans-serif;">
                                Phone No: {{ $supplySetting->phone_number }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <hr>
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%" style="padding: 0px; margin:0px; width:100%">
                    <tr>
                        <th align="left" style="font-size: 12px;font-family: 'Nunito Sans', sans-serif;">Qty </th>
                        <th align="left" style="font-size: 12px;font-family: 'Nunito Sans', sans-serif;">MRP
                        </th>
                        <th align="left" style="font-size: 14px;font-family: 'Nunito Sans', sans-serif;">Discount
                        </th>
                        <th align="left" style="font-size: 12px;font-family: 'Nunito Sans', sans-serif;">Discount
                            Coupon </th>
                        <th align="left" style="font-size: 12px;font-family: 'Nunito Sans', sans-serif;">Taxable
                            Amount</th>
                        <th align="left" style="font-size: 12px;font-family: 'Nunito Sans', sans-serif;">SGST</th>
                        <th align="left" style="font-size: 12px;font-family: 'Nunito Sans', sans-serif;">CGST</th>
                        <th align="left" style="font-size: 12px;font-family: 'Nunito Sans', sans-serif;">IGST</th>
                        <th align="right" style="font-size: 12px;font-family: 'Nunito Sans', sans-serif;">
                            Gross Total
                            Amount</th>
                    </tr>
                    @php
                        $taxPrice = 0;
                        $cgstTaxTotal = 0;
                        $igstTaxTotal = 0;
                        $sgstTaxTotal = 0;
                        $discount = 0;
                        $mrpPrice = 0;
                        $taxableAmount = 0;
                        $mrpPriceQty = 0;
                        $totalcoup = 0;
                        $finaltotalamount = 0;
                        $totalvarintprice = 0;
                        $totaldiscountvarint = 0;
                    @endphp
                   
                            @php

                                $prsku = !empty($checkout['product']['sku']) ? $checkout['product']['sku'] : '';
                                $prhsn = !empty($checkout['product']['hsn']) ? $checkout['product']['hsn'] : '';
                                $varitions = getProductVariantSku($checkout['product_variant_combination_id']);
                                $textpreset = !empty($checkout['itemTax']['tax_val'])
                                    ? $checkout['itemTax']['tax_val']
                                    : '';
                                $varitionsku = !empty($varitions) ? $varitions->sku : '';
                                $varitionprice = !empty($varitions) ? $varitions->price : '';
                                $totalvarintprice += $varitionprice * $checkout['qty'];
                                $varitionselling_price = !empty($varitions) ? $varitions->selling_price : '';
                                $discount = ($varitionprice - $varitionselling_price) * $checkout['qty'];
                                $prname = !empty($checkout['product']['name']) ? $checkout['product']['name'] : '';
                                $totaldiscountvarint += ($varitionprice - $varitionselling_price) * $checkout['qty'];
                                $cgstTax = 0;
                                $igstTax = 0;
                                $sgstTax = 0;
                                $taxPrice += $checkout['tax_amount'] ?? 0;
                                $totalcoup += $checkout['discount_amount'] ?? 0;
                                $checkoutTax = $checkout['tax_amount'] ?? 0;
                                $checkoutvarition = !empty($checkout['combination'])
                                    ? json_decode($checkout['combination'])
                                    : '';
                                $shippingState = strtolower($sAddress->shipping_state ?? '');
                                $supplyState = strtolower($supplySetting->state->name ?? '');

                                if ($supplyState == $shippingState) {
                                    $cgstTaxPrice = $checkoutTax / 2;
                                    $sgstTaxPrice = $checkoutTax / 2;
                                    $igstTaxPrice = 0.0;
                                    $cgstTaxTotal += $cgstTaxPrice;
                                    $sgstTaxTotal += $sgstTaxPrice;
                                    $cgstTax += $cgstTaxPrice;
                                    $sgstTax += $sgstTaxPrice;
                                    $igstTax = 0.0;
                                } else {
                                    // Inter-state: Apply IGST
                                    $cgstTaxPrice = 0.0;
                                    $igstTaxPrice = $checkoutTax;
                                    $igstTaxTotal += $igstTaxPrice;
                                    $cgstTax = 0.0;
                                    $igstTax += $igstTaxPrice;
                                    $sgstTaxPrice = 0.0;
                                    $sgstTax = 0.0;
                                    $cgstTaxTotal = 0.0;
                                    $sgstTaxTotal = 0.0;
                                }
                                $mrpPrice += $checkout['mrp'] * $checkout['qty'];
                                //  $mrpPriceQty = $checkout['mrp'] * $checkout['qty'];
                                $mrpPriceQty = $checkout['mrp'];
                                $taxableAmount += $checkout['sub_total'];
                                $finaltotalamount += $checkout['total'] ?? 0;
                            @endphp
                            <tr>
                                <td colspan="9">
                                    <hr>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="9">
                                    <p
                                        style="margin-top: 0; font-size: 13px; margin-bottom: 0;font-family: 'Nunito Sans', sans-serif;">

                                        {{ $prname }},
                                        @foreach ($checkoutvarition as $key => $value)
                                            {{ ucfirst($key) }}: {{ $value }} ,
                                        @endforeach
                                    </p>
                                    <p style="margin-top: 0; font-size: 13px;font-family: 'Nunito Sans', sans-serif;">
                                        SKU:
                                        {{ $prsku . '_' . $varitionsku }},
                                        Tax: {{ $textpreset }} % , HSN:
                                        {{ $prhsn }}
                                    </p>

                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 11px;font-family: 'Nunito Sans', sans-serif;">
                                    {{ $checkout['qty'] ?? '' }}</td>

                                <td style="font-size: 11px;font-family: 'Nunito Sans', sans-serif;">Rs
                                    {{ number_format($varitionprice ?? 0, 2) }}</td>

                                <td style="font-size: 11px;font-family: 'Nunito Sans', sans-serif;">Rs
                                    {{ number_format($discount ?? 0, 2) }}</td>

                                <td style="font-size: 11px;font-family: 'Nunito Sans', sans-serif;">Rs
                                    {{ number_format($checkout['discount_amount'] ?? 0, 2) }}</td>

                                <td style="font-size: 11px;font-family: 'Nunito Sans', sans-serif;">Rs
                                    {{ number_format($checkout['sub_total'] ?? 0, 2) }}</td>

                                <td style="font-size: 11px;font-family: 'Nunito Sans', sans-serif;">Rs
                                    {{ number_format($cgstTax ?? 0, 2) }}</td>

                                <td style="font-size: 11px;font-family: 'Nunito Sans', sans-serif;">Rs
                                    {{ number_format($sgstTax ?? 0, 2) }}</td>

                                <td style="font-size: 11px;font-family: 'Nunito Sans', sans-serif;">Rs
                                    {{ number_format($igstTax ?? 0, 2) }}</td>

                                <td align="right" style="font-size: 11px;font-family: 'Nunito Sans', sans-serif;">Rs
                                    {{ number_format($checkout['total'] ?? 0, 2) }}</td>
                            </tr>
                       




                    <tr>
                        <td colspan="9">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 11px;font-family: 'Nunito Sans', sans-serif;"><b>G.Total</b></td>
                        <td style="font-size: 11px;font-family: 'Nunito Sans', sans-serif;"> <b>Rs
                                {{ number_format($totalvarintprice ?? 0, 2) }}</b></td>
                        <td style="font-size: 11px;font-family: 'Nunito Sans', sans-serif;"> <b>Rs
                                {{ number_format($totaldiscountvarint ?? 0, 2) }}</b></td>

                        <td style="font-size: 11px;font-family: 'Nunito Sans', sans-serif;"><b>Rs
                                {{ number_format($totalcoup) }}</b></td>
                        <td style="font-size: 11px;font-family: 'Nunito Sans', sans-serif;"><b>Rs
                                {{ number_format($taxableAmount ?? 0, 2) }}</b></td>
                        <td style="font-size: 11px;font-family: 'Nunito Sans', sans-serif;"><b>Rs
                                {{ number_format($cgstTaxTotal ?? 0, 2) }}</b></td>
                        <td style="font-size: 11px;font-family: 'Nunito Sans', sans-serif;"><b>Rs
                                {{ number_format($sgstTaxTotal ?? 0, 2) }}</b></td>
                        <td style="font-size: 11px;font-family: 'Nunito Sans', sans-serif;"><b>Rs
                                {{ number_format($igstTaxTotal ?? 0, 2) }}</b></td>
                        <td style="font-size: 11px;font-family: 'Nunito Sans', sans-serif;" align="right"><b>Rs
                                {{ number_format($finaltotalamount ?? 0, 2) }}</b></td>
                    </tr>
                    <tr>
                        <td colspan="9">
                            <hr>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%">
                    <tr>
                        <td style="vertical-align: text-top;">
                            <p style="margin-bottom: 10px;font-family: 'Nunito Sans', sans-serif;">
                                <b>{{ $supplySetting->website_name }} </b>
                            </p>
                            <p style="font-size: 14px;font-family: 'Nunito Sans', sans-serif;"><b>Name</b>:
                                {{ $supplySetting->name }}</p>
                            <p style="font-size: 14px;font-family: 'Nunito Sans', sans-serif;"><b>Designation</b>:
                                {{ $supplySetting->designation }}</p>
                        </td>
                        <td align="left" colspan="6">
                            <!--<p style="font-size: 14px;font-family: 'Nunito Sans', sans-serif;"><b>Signature:</b>{{ $supplySetting->signature }}</p>-->

                        </td>
                        <td align="right" colspan="6">
                            <!--<div class="txt_sine" style="padding: 10px;">{{ $supplySetting->signature }}</div>-->
                            <div class="txt_sine" style="padding: 10px;">

                                @if (!empty($supplySetting->signature))
                                    <img height="70" width="70"
                                        src="{{ Config('constant.SIGNATURE_IMAGE_URL') . $supplySetting->signature ? Config('constant.SIGNATURE_IMAGE_URL') . $supplySetting->signature : '' }}" />
                                @endif
                            </div>
                            <p style="font-size: 14px;font-family: 'Nunito Sans', sans-serif;">Authorized Signatory
                            </p>
                        </td>
                        <!-- <td align="right"><img src="{{ $supplySetting->scanner_image }}"></td> -->
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-bottom: 0;font-family: 'Nunito Sans', sans-serif;"><b>DECLARATION</b></p>
                <p style="margin-top: 0; margin-bottom: 0;font-size: 14px;font-family: 'Nunito Sans', sans-serif;">
                    {{ $supplySetting->note }}
                </p>
            </td>
        </tr>
        <tr>
            <td>
                <hr>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 10px; font-size: 14px;font-family: 'Nunito Sans', sans-serif;"><b>Reg Address:
                        {{ $supplySetting->address }}, {{ $supplySetting->pincode }},
                        {{ $supplySetting->city->name }}, {{ $supplySetting->state->name }},
                        {{ $supplySetting->country->name }}</b></p>
            </td>
        </tr>
    </table>
    <div class="page-break"></div>
    <table width="100%">
        <tr>

        </tr>
        <tr style="vertical-align: top;">
            <td>
                <p style="margin-bottom: 10px;font-size: 14px;font-family: 'Nunito Sans', sans-serif;">
                    <b>Ship To:</b>
                </p>

                <p style="margin-bottom: 0;margin-top: 0;font-family: 'Nunito Sans', sans-serif;">
                    {{ $bAddress->billing_customer_name }}</p>
                <p style="margin-bottom: 0;margin-top: 0;font-size: 14px;font-family: 'Nunito Sans', sans-serif;">
                    {{ $bAddress->billing_address }},
                    {{ $bAddress->billing_city }},
                    {{ $bAddress->billing_state }} - {{ $bAddress->billing_pincode }} ,
                    {{ $bAddress->billing_country }}</p>
                <p style="margin-bottom: 0;margin-top: 0;font-size: 14px;font-family: 'Nunito Sans', sans-serif;">
                    Phone No: {{ $bAddress->billing_phone }}</p>
            </td>
            <td width="50%">
                <p><b>From: </b></p>
                <p style="margin-bottom: 0;margin-top: 0;font-family: 'Nunito Sans', sans-serif;">
                    {{ $supplySetting->website_name }}</p>
                <p style="margin-bottom: 0;margin-top: 0;font-size: 14px;font-family: 'Nunito Sans', sans-serif;">
                    Address: {{ $supplySetting->address }},
                    {{ $supplySetting->city->name }}, {{ $supplySetting->state->name }} -
                    {{ $supplySetting->pincode }} , {{ $supplySetting->country->name }}</p>
                <p style="margin-bottom: 0;margin-top: 0;font-size: 14px;font-family: 'Nunito Sans', sans-serif;">
                    Phone No: {{ $supplySetting->phone_number }}</p>
            </td>

        </tr>
    </table>
    @if(count($itemsData) > $itemCount)
    <div class="page-break"></div>
    @endif    

    @endforeach
    @endif

    <style>
        .page-break {
            page-break-after: always;
            break-after: page;
        }
    </style>
</body>

</html>
