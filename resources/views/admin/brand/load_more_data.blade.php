@if($results->isNotEmpty())
@forelse($results as $result)
<tr class="list-data-row" data-total-count="{{$totalResults}}">
    <td>{{ $result->name ?? "N/A" }}</td>
    <td>  
        @if($result->is_primary == 1)
           <a href='{{route("admin-brand.primarystatus",array($result->id,0))}}' class="btn btn-success" id="primary-deactivate-button"><i class="ri-check-line"></i></a>
        @else
           <a href='{{route("admin-brand.primarystatus",array($result->id,1))}}' class="btn btn-danger" id="primary-activate-button"><i class="ri-close-line"></i></a>
        @endif
    </td>
    <td>{{ date('Y-m-d',strtotime($result->created_at)) }}</td>
    <td>
        @if($result->is_active == 1)
        <span class="badge bg-success">Activated</span>
        @else
        <span class="badge bg-danger">Deactivated</span>
        @endif
    </td>

    <td>
        <div class="hstack gap-2 flex-wrap">
            @can('view_brand')
                @if($result->is_active == 1)
                <a href='{{route("admin-brand.status",array($result->id,0))}}' class="btn btn-danger"
                    id="deactivate-button"><i class="ri-close-line"></i></a>
                @else
                <a href='{{route("admin-brand.status",array($result->id,1))}}' class="btn btn-success"
                    id="activate-button"><i class="ri-check-line"></i></a>
                @endif
            @endcan

            @can('edit_brand')
                <a href="{{route('admin-brand.edit',base64_encode($result->id))}}" class="btn btn-info"><i
                    class="ri-edit-line"></i></a>

            @endcan
           
            @can('delete_brand')
                <form method="GET" action="{{route('admin-brand.delete',base64_encode($result->id))}}">
                    @csrf
                    <input name="_method" type="hidden" value="DELETE">
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