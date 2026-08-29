@if($results->isNotEmpty())
<?php
$sno = 1;
?>
@forelse($results as $result)
<tr class="list-data-row items-inner" data-total-count="{{$totalResults}}" data-id="{{$result->id}}">
    <td>{{$sno++}}</td>
    <td>{{ date('d-m-Y',strtotime($result->created_at)) }}</td>
    <td>{{ $result->to_name ?? "N/A" }}</td>
    <td>{{$result->to_code ?? "N/A"}}</td>
    <td>{{'₹'.$result->referral_to_amount ?? 'NA'}}</td>
    <td>{{'₹'.$result->to_wallet_avl_balance}}</td>
    <td>{{$result->by_code}}</td>
    <td>{{'₹'.$result->referral_by_amount ?? 'NA'}}</td>
    <td>{{'₹'.$result->referral_by_amount ?? 'NA'}}/₹{{$result->referral_to_amount ?? 'NA'}}</td>
    <!-- <td>
    <?php
// Ensure $result->validity is a valid number and not empty
$validity = is_numeric($result->validity) ? 'P' . (int)$result->validity . 'D' : 'P0D';  // Default to 0 days if not valid

$date = new DateTime($result->created_at);
$date->add(new DateInterval($validity));
echo $date->format('d-m-Y');
?>
    </td>
    <td>
        <?php
        $today = new DateTime();
        if ($date < $today) {
            echo 'Expired';
        } else {
            echo 'Active';
        }
        ?>
    </td> -->

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