<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Customer Name</th>
                <th>Email</th>
                <th>Order No</th>
                <th>Order Amount</th>
                <th>Payment Method</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($couponUses))
                @foreach($couponUses as $key => $couponUse)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $couponUse?->user?->name ?? 'N/A' }}</td>
                        <td>{{ $couponUse?->user?->email ?? 'N/A'}}</td>
                        <td>{!!  $couponUse?->order?->order_number ? '<a target="_blank" href="'.route('admin-orders.view',base64_encode($couponUse?->order?->id)).'">'. $couponUse?->order?->order_number .'</a>' : 'N/A' !!}</td>
                        <td>₹{{ $couponUse?->order?->total ?? '0'}}</td>
                        <td>{{ strtoupper($couponUse?->order?->payment_method ?? 'N/A')}}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" style="text-align: center;">No results found.</td>
                </tr>
            @endif           
        </tbody>
    </table>
</div>