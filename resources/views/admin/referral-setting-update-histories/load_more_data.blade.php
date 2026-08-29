@if($results->isNotEmpty())
@php $i = $offset +1;	 @endphp
@forelse($results as $result)
<tr class="list-data-row" data-total-count="{{$totalResults}}">
	<td>{{ $i++ }}</td>
    <td>
        @if (!empty($result->receiver_amount))
        {{isset($result->receiver_amount)? $result->receiver_amount:''}}
        @endif
    </td>
	
	<td>
        @if (!empty($result->sender_amount))
        {{isset($result->sender_amount)? $result->sender_amount:''}}
        @endif
    </td>
	
	<td>
        @if (!empty($result->ip))
        {{isset($result->ip)? $result->ip:''}}
        @endif
    </td>
	
	<td>
        @if (!empty($result->getUserCreated->name))
        {{isset($result->getUserCreated->name)? $result->getUserCreated->name:''}}
        @endif
		@php echo  " / "; @endphp
		@if (!empty($result->getUserCreated->email))
        {{isset($result->getUserCreated->email)? $result->getUserCreated->email:''}}
        @endif
    </td>
	
	<td>
        @if (!empty($result->created_at))
        {{isset($result->created_at)? $result->created_at:''}}
        @endif
    </td>
	
	<td>
        @if (!empty($result->getUserUpdated->name))
        {{isset($result->getUserUpdated->name)? $result->getUserUpdated->name:''}}
        @endif
		@php echo  " / "; @endphp
		@if (!empty($result->getUserUpdated->email))
        {{isset($result->getUserUpdated->email)? $result->getUserUpdated->email:''}}
        @endif
		
		</br></br>
		<!--<a href="{{ route('admin-referral-setting-update-histories.index2',$result->id) }}" target="_blank" class="btn btn-primary">-->
		<a data-bs-toggle="modal" data-bs-target="#referalPopup" data-id="<?php echo $result->id; ?>" class="referalPopupBtn" style="background:blue;color:#fff;font-size:14px;padding:3px;cursor:pointer">
			Referal Update Count:
			<span class="totalDataCount"><?php if(!is_null($result->getUpdatedRow)) { echo $result->getUpdatedRow->count(); } else { echo '0'; }  ?></span> 
		</a> 
    </td>
	
	<td>
        @if (!empty($result->updated_at))
        {{isset($result->updated_at)? $result->updated_at:''}}
        @endif
	</td>
	<td>
        <label class="switch">
            <input type="checkbox" id="referal_status" class="referal_status"  data-value="{{isset($result->status) && $result->status==1 ? '1' :'0'}}" data-id="{{isset($result->id)? $result->id:'0'}}"  <?php if($result->status==1){ echo "checked"; } ?> >
            <span class="slider round"></span>
        </label>
    </td>
	
	
	<!--<td>
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
    </td>-->
</tr>


@empty
@endforelse
@else
<tr class="noresults-row">
    <td colspan="7" style="text-align: center;">No results found.</td>
</tr>
@endif
