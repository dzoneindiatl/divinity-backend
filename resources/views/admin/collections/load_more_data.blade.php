@if($results->isNotEmpty())
@forelse($results as $result)
<tr class="list-data-row items-inner" data-total-count="{{$totalResults}}" data-id = "{{$result->id}}">
    <td>
        @if (!empty($result->image))
        <img height="50" width="50" src="{{asset(  Config('constant.COLLECTION_IMAGE_URL').$result->image )}}" />
        @endif
    </td>

    <td>{{ $result->title ?? "N/A" }}</td>
    <td>{{ $result->total_product ?? 0 }} </td>
    <td>{{ date('Y-m-d',strtotime($result->created_at)) }}</td>
    <td>
        <div class="hstack gap-2 flex-wrap">
            @can('edit_collection')
                <a href="{{route('admin-collections.edit',base64_encode($result->id))}}" class="btn btn-info"><i
                    class="ri-edit-line"></i></a>
            @endcan
            @can('delete_collection')
                <form method="GET" action="{{route('admin-collections.destroy',base64_encode($result->id))}}">
                    @csrf
                    <input name="_method" type="hidden" value="DELETE">
                    <button type="submit" class="btn btn-danger" id="confirm-button"><i
                            class="ri-delete-bin-5-line"></i></button>
                </form>
            @endcan

            @can('edit_collection')
                <div class="dropdown dropdown-inline">
                    <a href="javascript:;" class="btn btn-light"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-list-check"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                        <ul class="nav nav-hoverable flex-column">
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="{{route('admin-sub-category.index',['endesid'=>base64_encode($result->id)])}}">
                                    <span class="nav-text">Sub Categories</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script type="text/javascript">
new Sortable(powerwidgets, {
		animation: 150,
		ghostClass: 'sortable-ghost',
		onEnd: function (evt) {
			var counter  = 1;
			var requestData	=	[];
			$(".items-inner").each(function(){
				requestData.push({"id":$(this).attr("data-id"),"order":counter});
				counter++;
			});

			$.ajax({
				url:'{{ Route("admin-category.updateCategoryOrder") }}',
				type:'POST',
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data:{"requestData":requestData},
				success:function(response){
					
				}
			});	
		},
	});
</script>