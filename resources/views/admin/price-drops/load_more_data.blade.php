@if($results->isNotEmpty())
@forelse($results as $result)
<tr class="list-data-row" data-total-count="{{$totalResults}}">

    <td>{{ ucfirst($result->assign_type) ?? "N/A" }}</td>
    <td>
        {{ !empty($result->drop_type) ? ucfirst($result->drop_type) : "" }}
    </td>
    <td>
        {{ !empty($result->gain_type) ? ucfirst($result->gain_type) : "" }}
    </td>
    <td>
        {{ $result->amount ?? "N/A" }}

    </td>
    <td>
        {{ date('d-m-Y h:i A', strtotime($result->start_date)) }}
    </td>
    <td>
        {{ date('d-m-Y h:i A', strtotime($result->end_date)) }}
    </td>
    <td>
        {{ date('d-m-Y',strtotime($result->created_at)) }}
    </td>

    <td>
        <div class="hstack gap-2 flex-wrap"> -->

            <a href="{{route('admin-price-drops.edit',base64_encode($result->id))}}" class="btn btn-info"><i
                    class="ri-edit-line"></i></a>

            <form method="GET" action="{{route('admin-price-drops.delete',base64_encode($result->id))}}">
                @csrf
                <input name="_method" type="hidden" value="DELETE">
                <button type="submit" class="btn btn-danger" id="confirm-button"><i
                        class="ri-delete-bin-5-line"></i></button>
            </form>

            <form method="POST"
                action="{{route('admin-price-drops.delete',base64_encode($result->id))}}">
                @csrf
                <input name="_method" type="hidden" value="GET">
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