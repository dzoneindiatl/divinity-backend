@if($results->isNotEmpty())
@forelse($results as $result)
<tr class="list-data-row" data-total-count="{{$totalResults}}">
    <td>{{ $result->id ?? "N/A" }}</td>
    <td>
        {{ $result->title ?? "N/A" }}
    </td>
    <td>
        {{ $result->key ?? "N/A" }}
    </td>
    <td>{!! $result->value ?? "N/A" !!}</td>
   

    <td>
        <div class="hstack gap-2 flex-wrap">
           
            
            <a href="{{route('admin-settings.edit',base64_encode($result->id))}}" class="btn btn-info"><i
                    class="ri-edit-line"></i></a>
            
            <form method="GET" action="{{route('admin-settings.delete',base64_encode($result->id))}}">
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