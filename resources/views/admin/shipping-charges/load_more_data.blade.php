@if($results->isNotEmpty())
@forelse($results as $result)
<tr class="list-data-row items-inner" data-total-count="{{$totalResults}}" data-id = "{{$result->id}}">
<?php  $shippingzone = DB::table('shipping_zone')->select('*')->where('id', $result->shipping_zone_id)->orderBy('id', 'Desc')->first();  
$stateCount = DB::table('shipping_charges')->select(DB::raw('LENGTH(state_id) - LENGTH(REPLACE(state_id, ",", "")) + 1 AS state_count'))->where('id', $result->id)->pluck('state_count')->first();
 // Assuming $result->state_id is a comma-separated string like "1,2,3"
$stateIds = explode(',', $result->state_id);
// Fetch state names based on the IDs
$stateNames = DB::table('states')->whereIn('id', $stateIds)->pluck('name')->toArray();

// Ensure $stateNames is an array
$stateNamesString = is_array($stateNames) ? implode(', ', $stateNames) : '';
?>
    <td>{{ $result->country->name ?? "N/A" }}</td>
    <td>{{ $shippingzone->name ?? "N/A" }}</td>
    <td data-bs-toggle="tooltip" title="{{ $stateNamesString }}">{{$stateCount ?? 0}}</td>
    <td>{{ $result->shipping_method ?? "N/A" }}</td>
    
    <td>{{ date('d-m-Y',strtotime($result->created_at)) }}</td>
    <td>
        @if($result->is_active == 1)
        <span class="badge bg-success">Activated</span>
        @else
        <span class="badge bg-danger">Deactivated</span>
        @endif
    </td>

    <td>
        <div class="hstack gap-2 flex-wrap">
            @if($result->is_active == 1)
            <a href='{{route("admin-shipping-charges.status",array($result->id,0))}}' class="btn btn-danger"
                id="deactivate-button"><i class="ri-close-line"></i></a>
            @else
            <a href='{{route("admin-shipping-charges.status",array($result->id,1))}}' class="btn btn-success"
                id="activate-button"><i class="ri-check-line"></i></a>
            @endif

            <!-- <a href="{{route('admin-shipping-charges.show',base64_encode($result->id))}}" class="btn btn-info"><i
                    class="ri-eye-line"></i></a> -->

            <!-- <a href="{{route('admin-shipping-charges.edit',base64_encode($result->id))}}" class="btn btn-info"><i
                    class="ri-edit-line"></i></a> -->

            <form method="GET" action="{{route('admin-shipping-charges.delete',base64_encode($result->id))}}">
                @csrf
                <input name="_method" type="hidden" value="DELETE">
                <button type="submit" class="btn btn-danger" id="confirm-button"><i
                        class="ri-delete-bin-5-line"></i></button>
            </form>

            
        </div>
    </td>
</tr>
@empty
@endforelse
@else
<tr class="noresults-row">
    <td colspan="7" style="text-align: center;">No results found.</td>
</tr>
@endif