@if($results->isNotEmpty())
@forelse($results as $result)
<tr class="list-data-row items-inner" data-total-count="{{$totalResults}}" data-id = "{{$result->id}}">
    <td>{{ $result->weight ?? "N/A" }}</td>
    <td>{{ $result->amount ?? "N/A" }}</td>
    <td>{{ date('Y-m-d',strtotime($result->created_at)) }}</td>


    <td>
        <div class="hstack gap-2 flex-wrap">

            <a href="{{route('admin-shipping-costs.edit',base64_encode($result->id))}}" class="btn btn-info"><i
                    class="ri-edit-line"></i></a>

            <form method="GET" action="{{route('admin-shipping-costs.delete',base64_encode($result->id))}}">
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