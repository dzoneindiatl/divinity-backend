@if($results->isNotEmpty())
<?php
$sno = 1;
$validity = (int) Config("Referral.validity")
?>
@forelse($results as $result)
<tr class="list-data-row items-inner" data-total-count="{{$totalResults}}" data-id="{{$result->id}}">
    <td>{{$sno++}}</td>
    <td>{{ date('d-m-Y',strtotime($result->created_at)) }}</td>
    <td>{{ $result->to_name ?? "N/A" }}</td>
    <td>{{ $result->to_code ?? "N/A" }}</td>
    <td>{{ '₹'.$result->referral_to_amount ?? "N/A" }}</td>
    <td>{{ $result->by_code ?? "N/A" }}</td>
    <td>{{ '₹'.$result->referral_by_amount ?? "N/A" }}</td>
    <td>{{ '₹'.$result->by_wallet_avl_balance ?? "N/A" }}</td>
    <td>₹{{Config("Referral.sender")}} / ₹{{Config("Referral.receiver")}}</td>
    <?php
    $todayTimestamp = time();
    $createdAtTimestamp = strtotime($result->to_created_at);
    $validityTimestamp = strtotime("+$validity days", $createdAtTimestamp);
    $isActive = $todayTimestamp <= $validityTimestamp;
    ?>
    <td>{{ date('d-m-Y',strtotime("+$validity days", strtotime($result->to_created_at)))}}</td>
    <td>{{ $isActive ? 'Active' : 'Expired' }}</td>
    <td>
        <div class="hstack gap-2 flex-wrap">
            <form method="GET" action="{{route('admin-referral_histories.delete',base64_encode($result->id))}}">
                @csrf
                <input name="_method" type="hidden" value="DELETE">
                <button type="submit" class="btn btn-danger" id="confirm-button"><i class="ri-delete-bin-5-line"></i></button>
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