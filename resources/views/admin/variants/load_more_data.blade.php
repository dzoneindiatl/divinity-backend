@if($results->isNotEmpty())
@forelse($results as $result)
<tr class="list-data-row" data-total-count="{{$totalResults}}">
    <td>{{ $result->name ?? "N/A" }}</td>
    <td>
        @if($result->type == 1)
        Only Round
        @elseif($result->type == 2)
        Only Box
        @elseif($result->type == 3)
        Round with image
        @elseif($result->type == 4)
        Round with color
        @elseif($result->type == 5)
        Box with color
        @elseif($result->type == 6)
        Box with Images
        @elseif($result->type == 7)
        Only Rectangle
        @endif
    </td>                                        
    <td>{{ $result->created_at }}</td>
    <td>
        @if($result->is_active == 1)
        <span class="badge bg-success">Activated</span>
        @else
        <span class="badge bg-danger">Deactivated</span>
        @endif
    </td>
    <td>
        <div class="hstack gap-2 flex-wrap">
            @can('edit_variant')
                @if($result->is_active == 1)

                <a title="Click To Deactivate" href='{{route("admin-".$model.".status",array($result->id,1))}}'
                    class="btn btn-danger" id="deactivate-button"><i class="ri-close-line"></i>

                </a>
                @else
                <a title="Click To Activate" href='{{route("admin-".$model.".status",array($result->id,0))}}'
                    class="btn btn-success" id="activate-button"><i class="ri-check-line"></i>

                </a>
                @endif


                <a href="{{route('admin-'.$model.'.edit',base64_encode($result->id))}}" class="btn btn-info"><i
                        class="ri-edit-line"></i></a>
            @endcan
            @can('delete_variant')
                <form method="POST" action="{{route('admin-'.$model.'.delete',['endepid'=>base64_encode($result->id)])}}">
                    @csrf
                    <input name="_method" type="hidden" value="GET">
                    <button type="submit" class="btn btn-danger" id="confirm-button"><i
                            class="ri-delete-bin-5-line"></i></button>
                </form>
            @endcan
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