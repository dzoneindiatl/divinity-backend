@if($results->isNotEmpty())
@forelse($results as $result)
<tr class="list-data-row" data-total-count="{{$totalResults}}">
    <td>{{ $result->name ?? "N/A" }}</td>
    <td>{{ date(config("Reading.date_format"),strtotime($result->created_at)) }}</td>
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

            <a title="Click To Deactivate" href='{{route("admin-".$model.".status",array($result->id,1))}}'
                class="btn btn-danger" id="deactivate-button"><i class="ri-close-line"></i>

            </a>
            @else
            <a title="Click To Activate" href='{{route("admin-".$model.".status",array($result->id,0))}}'
                class="btn btn-success" id="activate-button"><i class="ri-check-line"></i>

            </a>
            @endif


            <a href="{{route('admin-specifications.edit',base64_encode($result->id))}}" class="btn btn-info"><i
                    class="ri-edit-line"></i></a>
            <form method="POST" action="{{route('admin-'.$model.'.delete',base64_encode($result->id))}}">
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