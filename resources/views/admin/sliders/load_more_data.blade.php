@if($results->isNotEmpty())
@forelse($results as $result)
<tr class="list-data-row" data-total-count="{{$totalResults}}">

    <td>{{ ucfirst($result->media_type) ?? "N/A" }}</td>
    <td>
        @if($result->media_type != 'image')
            <video width="100" height="80" controls>
                <source src="{{ $result->media_url }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        @else
            @if(!empty($result->media_url))
                <img src="{{ Config('constant.BANNER_IMAGE_URL') . $result->media_url }}" alt="{{ $result->title }}" height="80" width="80" />
            @else
                Url not available
            @endif
        @endif
    </td>
    <td>
        {{ !empty($result->title) ? ucfirst($result->title) : "" }}
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
                <a href='{{route("admin-sliders.status",array($result->id,0))}}' class="btn btn-danger"
                id="deactivate-button"><i class="ri-close-line"></i></a>
            @else
                <a href='{{route("admin-sliders.status",array($result->id,1))}}' class="btn btn-success"
                id="activate-button"><i class="ri-check-line"></i></a>
            @endif

            {{-- <a href="{{route('admin-coupons.show',base64_encode($result->id))}}" class="btn btn-info"><i
                    class="ri-eye-line"></i></a> --}}

            <a href="{{route('admin-sliders.edit',base64_encode($result->id))}}" class="btn btn-info"><i
                    class="ri-edit-line"></i></a>

            <form method="GET" action="{{route('admin-sliders.delete',base64_encode($result->id))}}">
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
    <td colspan="8" style="text-align: center;">No results found.</td>
</tr>
@endif