@if($results->isNotEmpty())
<?php
$sno = 1;
$validity = (int) Config("Referral.validity")
?>
@forelse($results as $result)
<tr class="list-data-row items-inner" data-total-count="{{$totalResults}}" data-id="{{$result->id}}">
    <td>{{$sno++}}</td>
    <td>{{ '₹'.$result->sender_amount ?? 'NA'}}</td>
    <td>{{ '₹'.$result->receiver_amount ?? "N/A" }}</td>
    <td>{{date('d-m-Y',strtotime($result->start_date)) ?? 'NA'}}</td>
    <td>{{date('d-m-Y',strtotime($result->end_date)) ?? 'NA'}}</td>
    <td>{{ $result->validity.' Days' ?? "N/A" }}</td>
    <td>
        <div class="form-check form-switch">
            <input class="form-check-input toggle-status" data-id="{{ $result->id }}" type="checkbox" role="switch" id="flexSwitchCheckChecked" {{ $result->status ? 'checked' : '' }}>
        </div> 
    </td>
    <td>
        <div class="hstack gap-2 flex-wrap">
            <form method="GET" action="{{route('admin-referral_setting.delete',base64_encode($result->id))}}">
                @csrf

                <!-- referral_setting.delete -->
                <input name="_method" type="hidden" value="DELETE">
                <button type="submit" class="btn btn-danger" id="confirm-button"><i class="ri-delete-bin-5-line"></i></button>
            </form>
            <form method="GET" action="{{route('admin-referral_setting.edit',base64_encode($result->id))}}">
                @csrf
                <button type="submit" class="btn btn-primary"><i class="ri-edit-2-line"></i></button>
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