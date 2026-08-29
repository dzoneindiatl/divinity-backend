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

                <div class="d-flex align-items-center showDetailPageToggle">
                    <label class="toggle-switch">
                        <input type="checkbox" class="showDetailPageToggleInput"
                            {{ $result->show_on_detail ? 'checked' : '' }} data-id="{{ $result->id }}">
                        <span class="slider"></span>
                    </label>
                    <span
                        class="toggle-label showDetailPageToggleLabel">{{ $result->show_on_detail ? 'Yes' : 'No' }}</span>
                </div>
            </td>

            <td>
                <div class="hstack gap-2 flex-wrap">
                    @if ($result->is_active == 1)
                        <a href='{{ route('admin-coupons.status', [$result->id, 0]) }}' class="btn btn-danger"
                            id="deactivate-button"><i class="ri-close-line"></i></a>
                    @else
                        <a href='{{ route('admin-coupons.status', [$result->id, 1]) }}' class="btn btn-success"
                            id="activate-button"><i class="ri-check-line"></i></a>
                    @endif


                    <a href="{{ route('admin-coupons.create', ['coupon_id' => base64_encode($result->id)]) }}"
                        class="btn btn-info"><i class="ri-edit-line"></i></a>
                        <a href="{{ route('admin-coupons.couponlog', ['couponId' => $result->id]) }}"
                        class="btn btn-info"><i class="ri-edit-line">Edit Logs</i></a>

                        <form method="POST" action="{{ route('admin-coupons.destroy', base64_encode($result->id)) }}" >
                        @csrf
                        <input name="_method" type="hidden" value="DELETE">
                        <button type="submit" class="btn btn-danger" id="confirm-button"><i
                                class="ri-delete-bin-5-line"></i></button>
                    </form>

                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr class="noresults-row">
        <td colspan="12" style="text-align: center;">No results found.</td>
    </tr>
@endif
