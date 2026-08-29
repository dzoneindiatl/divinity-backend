@if ($results->isNotEmpty())
    @php
        $statuss = getOrderStatuss();
    @endphp
   @forelse($results as $result)
        @
         @php
            $orderStatus = array();
            
            if (property_exists($result, 'items')) {
                $orderItems = $result->status; 
            }           
            if(!empty($orderItems)){
                foreach($orderItems as $item){
                    $statusName = $statuss[$item->order_status_id]['name'];
                    $orderStatus[$statusName][] = $statusName;
                }
            } else {
                $result->status = 'N/A';
            }
            //prx($orderStatus,0);
        @endphp
        
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
                @if(!empty($orderStatus))
                    @foreach($orderStatus as $key => $status)
                        <span class="badge bg-primary">{{ $key }} ({{ count($status) }})</span>
                    @endforeach
                @else
                    N/A
                @endif
            </td>
            <td> {{ date('d-M-Y', strtotime($result->created_at)) }} </td>
            <td align="center"> <b>{{ strtoupper($result->status) }}</b>
                @if (empty($result->status == 'shipped'))  
                <select class="form-control basicstatus" data-order="{{ $result->id }}"> 
                    @foreach ($basic_status as $key => $value)
                        <option value="{{ $key }}" {{ ($result->status==$key) ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach            
                </select>
                @endif 
                @if ($result->status != 'received' && $result->status != 'delivered' && $result->status != 'cancelled')
                <select class="form-control shippedstatus" data-order="{{ $result->id }}">   
                    <option value="">Select Status</option>
                    @foreach ($shipped_status as $key => $value)
                        <option value="{{ $key }}" {{ ($result->status==$key) ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach 
                </select>
                @endif 
                @if ($result->status == 'delivered' || $result->status == 'refunded request')
                <select class="form-control exchangedstatus" data-order="{{ $result->id }}">  
                    <option value="">Select Status</option>
                    @foreach ($exchanged_status as $key => $value)
                        <option value="{{ $key }}" {{ ($result->status==$key) ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach           
                </select>
                @endif
            </td>

            <td>
                @can('generate_invoice_order')
                    <a href="{{ route('admin-orders.generate.invoice', $result->id) }}" class="btn btn-info"
                        target="_blank" title="Generate Invoice"><i class="bi bi-printer"></i></a>
                @endcan
            </td>
            <td>
                <div class="hstack gap-2 flex-wrap">
                    @can('view_order')
                        <a href="{{ route('admin-orders.view', base64_encode($result->id)) }}" class="btn btn-info"><i
                            class="ri-eye-line"></i></a>
                    @endcan
                    <a href="{{route('admin-orders.edit',base64_encode($result->id))}}" class="btn btn-info"><i class="ri-edit-line"></i></a>
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
