<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('Site.title') ?? '' }} Order Invoice</title>
    <!-- Add any necessary CSS styles here -->
</head>
<body>
    <div>
        <h1>{{ config('Site.title') ?? '' }} Order Invoice</h1>
        <p>Order ID: {{ $order->order_number }}</p>
        <p>Customer Name: {{ $order->user_name ?? '' }}</p>
        <p>Email: {{ $order->user_email ?? '' }}</p>
        <p>Address: {{ $order->address->address_line_1 ?? '' }},
            {{ $order->address->address_line_2 ?? '' }}<br />
            {{ $order->address->landmark ?? '' }}, {{ $order->address->city ?? '' }},
            {{ $order->address->state ?? '' }},{{ $order->address->country ?? '' }}
            {{ $order->address->postal_code ?? '' }}</p>
        <table border="1">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Sub Total</th>
                    <th>Taxes</th>
                    <th>Delivery</th>
                    <th>Coupon Discount</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
               
                @if(!empty($checkoutItemData))
                @foreach($checkoutItemData as $checkout)
                <tr>
                    <td>{{ $checkout['product']['product_name'] ?? '' }}</td>
                    <td>{{ $checkout['qty'] ?? '' }}</td>
                    <td>{{ $currency ?? '' }}{{ number_format($checkout['sub_total'] ?? 0, 2) }}</td>
                    @if(!empty($checkout['tax']))
                    <td>
                        @foreach($checkout['tax'] as $tax)
                        <p>{{ $tax['tax_name'] ?? '' }} - {{ $currency ?? '' }}{{ number_format($tax['tax_price'] ?? 0, 2) }}</p>
                        <br>
                       
                        @endforeach
                        @php
                        $totalTaxes = array_reduce($checkout['tax'], function($carry, $item) {
                            return $carry + $item['tax_price'];
                        }, 0);
                        @endphp
                        <p>Total Taxes - {{ $currency ?? '' }}{{ number_format($totalTaxes, 2) }}</p>
                    </td>
                    @else
                    <td>-</td>
                    @endif
                    <td>
                    @if(!empty($checkout['delivery']))
                    {{ $currency ?? '' }}{{ number_format($checkout['delivery'] ?? 0, 2) }}
                    @else
                    -
                    @endif
                    </td>
                    <td>
                    @if(!empty($checkout['coupon_discount']))
                    {{ $checkout['coupon_name'] ?? '' }} - {{ $currency ?? '' }}{{ number_format($checkout['coupon_discount'] ?? 0, 2) }}
                    @else
                    -
                    @endif
                    </td>
                    <td>{{ $currency ?? '' }}{{ number_format($checkout['total'] ?? 0, 2) }}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        <p>Sub Total: {{ $currency ?? '' }}{{ number_format($checkoutData['sub_total'] ?? 0, 2) }}</p>

        @if(!empty($checkoutData['tax']))
        @foreach($checkoutData['tax'] as $tax)
        <p>{{ $tax['tax_name'] ?? '' }} - {{ $currency ?? '' }}{{ number_format($tax['tax_price'] ?? 0, 2) }}</p>
        <br>
        @endforeach
        @endif
        @if(!empty($checkoutData['delivery']))
        <p>Delivery: {{ $currency ?? '' }}{{ number_format($checkoutData['delivery'] ?? 0, 2) }}</p>
        @endif
        @if(!empty($checkoutData['coupon_name']))
        <p>Coupon ({{ $checkoutData['coupon_name'] }}) : {{ $currency ?? '' }}{{ number_format($checkoutData['coupon_discount'] ?? 0, 2) }}</p>
        @endif
        <p>Total Price: {{ $currency ?? '' }}{{ number_format($checkoutData['total'] ?? 0, 2) }}</p>
    </div>
</body>
</html>
