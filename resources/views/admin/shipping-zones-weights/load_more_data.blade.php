@if($results->isNotEmpty())
@forelse($results as $result)
<tr class="list-data-row items-inner" data-total-count="{{$totalResults}}" data-id = "{{$result->id}}">
    <td>{{ $result->weight_min }} - {{ $result->weight_max }} {{ $result->weight_type }}</td>
    <td>Rs. {{ $result->amount ?? "N/A" }}</td>

    <td>{{ date('d-m-Y',strtotime($result->created_at)) }}</td>
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
            <a href='{{route("admin-shipping-zones-weights.status",array($result->id,0))}}' class="btn btn-danger"
                id="deactivate-button"><i class="ri-close-line"></i></a>
            @else
            <a href='{{route("admin-shipping-zones-weights.status",array($result->id,1))}}' class="btn btn-success"
                id="activate-button"><i class="ri-check-line"></i></a>
            @endif

            <a href="{{route('admin-shipping-zones-weights.edit',base64_encode($result->id))}}" class="btn btn-info"><i
                    class="ri-edit-line"></i></a>

            <form method="GET" action="{{route('admin-shipping-zones-weights.delete',base64_encode($result->id))}}">
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