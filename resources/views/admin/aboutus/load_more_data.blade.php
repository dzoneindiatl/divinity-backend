@if($results->isNotEmpty())
@forelse($results as $result)
<tr class="list-data-row" data-total-count="{{$totalResults}}">

    <td>{{ $result->title ?? "N/A" }}</td>
    <td>
        <img src="{{ Config('constant.BANNER_IMAGE_URL').$result->media }}" alt="" height="80" width="100" />
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
            @can('edit_about_us_manage')
                @if($result->is_active == 1)
                <a href='{{route("admin-aboutus.status",array($result->id,0))}}' class="btn btn-danger"
                    id="deactivate-button"><i class="ri-close-line"></i></a>
                @else
                <a href='{{route("admin-aboutus.status",array($result->id,1))}}' class="btn btn-success"
                    id="activate-button"><i class="ri-check-line"></i></a>
                @endif
                


                {{-- <a href="{{route('admin-aboutus.show',base64_encode($result->id))}}" class="btn btn-info"><i
                        class="ri-eye-line"></i></a> --}}

                <a href="{{route('admin-aboutus.edit',base64_encode($result->id))}}" class="btn btn-info"><i
                        class="ri-edit-line"></i></a>
            @endcan

            @can('delete_about_us_manage')
            
            <form method="GET" action="{{route('admin-aboutus.delete',base64_encode($result->id))}}">
                @csrf
                <input name="_method" type="hidden" value="DELETE">
                <button type="submit" class="btn btn-danger" id="confirm-button"><i class="ri-delete-bin-5-line"></i></button>
            </form>
            @endcan

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