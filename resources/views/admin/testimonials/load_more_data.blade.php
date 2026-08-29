@if($results->isNotEmpty())
@forelse($results as $result)
<tr class="list-data-row" data-total-count="{{$totalResults}}">
    <td>
        @if (!empty($result->image))
        <img height="50" width="50" src="{{isset($result->image)? $result->image:''}}" />
        @endif
    </td>

    <td>{{ $result->name ?? "N/A" }}</td>
    <td>
        {{ $result->rating ?? "N/A" }}
    </td>
    <td>
        <div class="hstack gap-2 flex-wrap">
            @if($result->is_active == 1)
            <a href='{{route("admin-testimonials.status",array($result->id,0))}}' class="btn btn-danger"
                id="deactivate-button"><i class="ri-close-line"></i></a>
            @else
            <a href='{{route("admin-testimonials.status",array($result->id,1))}}' class="btn btn-success"
                id="activate-button"><i class="ri-check-line"></i></a>
            @endif

            <a href="{{route('admin-testimonials.edit',base64_encode($result->id))}}" class="btn btn-info"><i
                class="ri-edit-line"></i></a>

            <form method="GET" action="{{route('admin-testimonials.delete',base64_encode($result->id))}}">
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