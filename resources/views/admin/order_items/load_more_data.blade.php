@foreach ($results as $row)
    @php
        $variantSku = $row->product->sku;
        if(!empty($row->product_variant_combination_id)){
            $variantSkuData = getProductVariantSku($row->product_variant_combination_id);
            $variantSku = !empty($variantSkuData?->sku) ? $variantSku.'_'.$variantSkuData?->sku:'';
            
        }
    @endphp
    <tr>
        <td>
            <input type="checkbox" name="items[]" value="{{ $row->id }}" class="items-checkbox"> 
            #{{ $row->order->order_number ?? '-' }}
        </td>
        <td>{{ $variantSku ?? '-' }}</td>
        <td>{{ $row->product->name ?? '-' }}</td>
        <td>{{ $row->qty ?? $row->quantity ?? '-' }}</td>
        <td>{{ number_format($row->total ?? $row->selling_price ?? 0, 2) }}</td>
        <td>{{ $row->orderStatus->name ?? ucfirst(str_replace('-', ' ', $row->status ?? '-')) }}</td>
        <td>{{ $row->created_at?->format('d M Y, h:i A') }}</td>
        <td>
            @if (!empty($row->order_id))
                <a class="btn btn-sm btn-primary" href="{{ route('admin-orders.view', base64_encode($row->order_id)) }}">View Order</a>
            @endif
        </td>
    </tr>
@endforeach


