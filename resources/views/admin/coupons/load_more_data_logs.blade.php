@if ($results->isNotEmpty())
    @forelse($results as $result)
        @php
            $couponUses = $result?->couponUses?->count() ?? 0;
        @endphp
        <tr class="list-data-row" data-total-count="{{ $totalResults }}">
            <td>{{ $result->name ?? 'N/A' }}</td>
            <td>
                {{ !empty($result->coupon_code) ? ucfirst($result->coupon_code) : 'N/A' }}
                {!! $couponUses
                    ? "<span style='cursor:pointer' class='couponUses text-danger' title='Coupon Uses' data-bs-toggle='tooltip' data-cid='$result->id'> ($couponUses)</span>"
                    : " <span title='Coupon Uses' class='text-danger' data-bs-toggle='tooltip'>(0)</span>" !!}
            </td>
            <td>
                @if ($result->discount_type == 'percentage')
                    {{ $result->discount_value . '%' ?? 'N/A' }}
                @else
                    {{ '₹' . $result->discount_value ?? 'N/A' }}
                @endif
            </td>

            {{-- ----------User Type---------------------- --}}
            <td>
                {{ $result->available_coupons ?? 'N/A' }}
            </td>
            <td>
                {{ $result->per_user_avalibity ?? 'N/A' }}
            </td>
            <td>
                {{ $result->user_type ? ucfirst($result->user_type) . ' User' : 'N/A' }}
            </td>
            {{-- ----------User Type---------------------- --}}
            <td>
                @if (!empty($result->start_date) || !empty($result->end_date))
                    From: {{ !empty($result->start_date) ? date('Y-m-d', strtotime($result->start_date)) : '-' }}
                    <br> To: {{ !empty($result->end_date) ? date('Y-m-d', strtotime($result->end_date)) : '-' }}
                @else
                    -
                @endif
            </td>
            <td>
                @if ($result->is_active == 1)
                    <span class="badge bg-success">Activated</span>
                @else
                    <span class="badge bg-danger">Deactivated</span>
                @endif
            </td>
            <td>
                <?php  echo $result->ip;  ?>
            </td>
            <td>
                <?php  $userDeatis = getUserDetail($result->updated_by);
                echo   $userDeatis['name'] ." </br> ".  $userDeatis['email'] ." </br> ".   $result->updated_at;  ?>
            </td>
        </tr>
    @endforeach
@else
    <tr class="noresults-row">
        <td colspan="12" style="text-align: center;">No results found.</td>
    </tr>
@endif
