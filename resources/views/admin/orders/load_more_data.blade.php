@if ($results->isNotEmpty())
    @forelse($results as $result)
        <?php
            $SubTotal = '0';
            $statuss = getOrderStatuss();
            $cancelRequest = orderCancellationRequest($result->id);
            $refundRequest = orderRefundRequest($result->id);
        ?>
        <tr class="list-data-row" data-total-count="{{ $totalResults }}">
            <td><input type="checkbox" class="order-checkbox" value="{{ $result->id }}" name="order[{{ $result->id }}]" onclick="event.stopPropagation();">
                {{ !empty($result->order_number) ? ucfirst($result->order_number) : '' }} 
            </td>
            <td> {{ isset($result->user) ? ucwords($result->user->name) : 'N/A' }} </td>
            <td> {{ !empty($result->total) ? ucfirst($result->total) : '' }} </td>
            <td> {{ !empty($result->payment_status) ? ucfirst($result->payment_status) : '' }} </td>
            <td> {{ !empty($result->items) ? $result->items->count() : '' }}
                {{ $result->items->count() > 1 ? 'items' : 'item' }} 
            </td>
            <td> {{ ucwords($result->payment_method) ?? 'N/A' }} </td>

            <td>
                <?php
                    if(!empty($orderItemStatusCountArr)){
                        foreach($orderItemStatusCountArr as $key => $value){
                            if($result->id==$key){
                                foreach($value as $status => $count){
                                echo "<span class='badge bg-primary'>". $status." (".$count.")</span></br>";
                                }
                            }
                        }
                    } else {
                        echo "N/A";
                    }
                ?>
            </td>

            <td> {{ date('d-M-Y', strtotime($result->created_at)) }} </td>
            
            <!-- <td align="center"> <b>{{ strtoupper($result->status) }}</b>
                @if (!empty($result->status) && $result->status == 'shipped')  
                <select class="form-control basicstatus" data-order="{{ $result->id }}"> 
                    @foreach ($basic_status as $key => $value)
                        <option value="{{ $key }}" {{ ($result->status==$key) ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach            
                </select>
                @endif 
                @if (!empty($result->status) && $result->status != 'received' && $result->status != 'delivered' && $result->status != 'cancelled')
                <select class="form-control shippedstatus" data-order="{{ $result->id }}">   
                    <option value="">Select Status</option>
                    @foreach ($shipped_status as $key => $value)
                        <option value="{{ $key }}" {{ ($result->status==$key) ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach 
                </select>
                @endif 
                @if (!empty($result->status) && $result->status == 'delivered' || $result->status == 'refunded request')
                <select class="form-control exchangedstatus" data-order="{{ $result->id }}">  
                    <option value="">Select Status</option>
                    @foreach ($exchanged_status as $key => $value)
                        <option value="{{ $key }}" {{ ($result->status==$key) ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach           
                </select>
                @endif
            </td> -->
            <td>
                @php
                $odrStatus = 'pending';
                if(!is_numeric($result->status)){
                    $odrStatus = $result->status;
                }
                if(!empty($getStatusAccrdingtoOrderstatusForAdmin[$odrStatus]) && $odrStatus!='delivered') {
                @endphp
                <select class="form-control statusChange" data-order="{{ $result->id }}">  
                    <option value="">Select Status</option>
                    @foreach ($getStatusAccrdingtoOrderstatusForAdmin[$odrStatus] as $key => $value)
                        <option value="{{ $key }}" <?php if($odrStatus===$key) { echo 'selected'; } else { ''; } ?> >{{ $value }}</option>
                    @endforeach           
                </select>
                @php
                } else {
                    echo  $odrStatus;
                }
                @endphp
            </td>

            <td>
                @can('generate_invoice_order')
                @php  if(in_array($result->status,array('shipped','in-transit','out-for-delivery','delivered','return-requested','return-accepted','refund-pending','refunded','cancelled','cancelled_by_customer'))) { @endphp
                    <a href="{{ route('admin-orders.generate.invoice', $result->id) }}" class="btn btn-info"
                        target="_blank" title="Generate Invoice"><i class="bi bi-printer"></i></a>
                 @php } else  { @endphp
                    <a href="javascript:void(0)" class="btn btn-info" onclick="alert('Order Invoice is not generated Yet, After Shipped You can generate invoice.')"><i class="bi bi-printer"></i></a>
                @php } @endphp
                @endcan
            </td>
            <td>
                <div class="hstack gap-2 flex-wrap">
                    @can('view_order')
                        <a href="{{ route('admin-orders.view', base64_encode($result->id)) }}" class="btn btn-info"><i
                            class="ri-eye-line"></i></a>
                    @endcan
                    <!-- <a href="{{route('admin-orders.edit',base64_encode($result->id))}}" class="btn btn-info"><i class="ri-edit-line"></i></a> -->
                </div>
            </td>
        </tr>
    @empty
    @endforelse
@else
    <tr class="noresults-row">
        <td colspan="6" style="text-align: center;">No results found.</td>
    </tr>
@endif


