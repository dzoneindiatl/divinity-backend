@if($results->isNotEmpty())
@forelse($results as $result)
<tr class="list-data-row" data-total-count="{{$totalResults}}">

    <td>{{ $result->name ?? "N/A" }}</td>
    <td>
        {{ !empty($result->currency_code) ? $result->currency_code : "" }}
    </td>
    <td>
        {{ !empty($result->symbol) ? $result->symbol : "" }}
    </td>
    <td>
        {{ $result->amount ?? "N/A" }}
        @if($result->is_default == 1)
            <a href="javascript:void(0)" class="default-currency-icon" data-currency-id="{{ $result->id }}">
                <i class="ri-star-fill"></i>
            </a>
        @else
            <a href="{{route('admin-currencies.makeDefault',$result->id)}}" class="default-currency-icon" id="mark-default" data-currency-id="{{ $result->id }}">
                <i class="ri-star-line"></i>
            </a>
        @endif
    </td>
    <td>
        {{ date('Y-m-d',strtotime($result->created_at)) }}
    </td>
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
            <a href='{{route("admin-currencies.status",array($result->id,0))}}' class="btn btn-danger"
                id="deactivate-button"><i class="ri-close-line"></i></a>
            @else
            <a href='{{route("admin-currencies.status",array($result->id,1))}}' class="btn btn-success"
                id="activate-button"><i class="ri-check-line"></i></a>
            @endif

            {{-- <a href="{{route('admin-currencies.show',base64_encode($result->id))}}" class="btn btn-info"><i
                    class="ri-eye-line"></i></a> --}}

            <a href="{{route('admin-currencies.edit',base64_encode($result->id))}}" class="btn btn-info"><i
                    class="ri-edit-line"></i></a>

            <form method="GET" action="{{route('admin-currencies.delete',base64_encode($result->id))}}">
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