@foreach($all_orders as $order)
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('Site.title') ?? '' }} Order Invoices</title>
    <!-- Add any necessary CSS styles here -->
</head>

<body>
    <h1>{{ config('Site.title') ?? '' }} Order Invoice</h1>
    <p>Order ID: {{ $order->order_number }}</p>
    <p>Customer Name: {{ $order->user_name ?? '' }}</p>
    <p>Email: {{ $order->user_email ?? '' }}</p>
    <p>Address: {{ $order->address->address_line_1 ?? '' }},
        {{ $order->address->address_line_2 ?? '' }}<br />
        {{ $order->address->landmark ?? '' }}, {{ $order->address->city ?? '' }},
        {{ $order->address->state ?? '' }},{{ $order->address->country ?? '' }}
        {{ $order->address->postal_code ?? '' }}
    </p>
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
            <?php $subtotal = 0; ?>
            @if(!empty($order->items))
            @foreach($order->items as $item)
            <tr>

                <td>{{ $item->product->name ?? '' }}</td>
                <td>{{ $item['qty'] ?? '' }}</td>
                <td>{{ $order['currency']['symbol'] ?? '' }}{{ number_format($item['sub_total'] ?? 0, 2) }}</td>
                @if(!empty($item['tax']))
                <td>
                    @foreach($item['tax'] as $tax)
                    <p>{{ $tax['tax_name'] ?? '' }} - {{ $order['currency']['symbol'] ?? '' }}{{ number_format($tax['tax_price'] ?? 0, 2) }}</p>
                    <br>

                    @endforeach
                    @php
                    $totalTaxes = array_reduce($item['tax'], function($carry, $item) {
                    return $carry + $item['tax_price'];
                    }, 0);
                    @endphp
                    <p>Total Taxes - {{ $order['currency']['symbol'] ?? '' }}{{ number_format($totalTaxes, 2) }}</p>
                </td>
                @else
                <td>-</td>
                @endif
                <td>
                    @if(!empty($item['delivery']))
                    {{ $order['currency']['symbol'] ?? '' }}{{ number_format($item['delivery'] ?? 0, 2) }}
                    @else
                    -
                    @endif
                </td>
                <td>
                    @if(!empty($item['coupon_discount']))
                    {{ $item['coupon_name'] ?? '' }} - {{ $order['currency']['symbol'] ?? '' }}{{ number_format($item['coupon_discount'] ?? 0, 2) }}
                    @else
                    -
                    @endif
                </td>
                <td>{{ $order['currency']['symbol'] ?? '' }}{{ number_format($item['total'] ?? 0, 2) }}</td>
            </tr>
            <?php
            // if(isset($item['total'])){
            //     $subtotal += number_format($numericValue ?? 0,2);
            // }
            ?>
            @endforeach
            @endif
        </tbody>
    </table>

    <!-- <p>Sub Total: {{ $order['currency']['symbol'] ?? '' }}{{ number_format($subtotal ?? 0, 2) }}</p> -->

    @if(!empty($checkoutData['tax']))
    @foreach($checkoutData['tax'] as $tax)
    <p>{{ $tax['tax_name'] ?? '' }} - {{ $order['currency']['symbol'] ?? '' }}{{ number_format($tax['tax_price'] ?? 0, 2) }}</p>
    <br>
    @endforeach
    @endif
    @if(!empty($checkoutData['delivery']))
    <p>Delivery: {{ $order['currency']['symbol'] ?? '' }}{{ number_format($checkoutData['delivery'] ?? 0, 2) }}</p>
    @endif
    @if(!empty($checkoutData['coupon_name']))
    <p>Coupon ({{ $checkoutData['coupon_name'] }}) : {{ $order['currency']['symbol'] ?? '' }}{{ number_format($checkoutData['coupon_discount'] ?? 0, 2) }}</p>
    @endif
    <p>Total Price: {{ $order['currency']['symbol'] ?? '' }}{{ number_format($order['total'] ?? 0, 2) }}</p>
</body>

</html>
@endforeach