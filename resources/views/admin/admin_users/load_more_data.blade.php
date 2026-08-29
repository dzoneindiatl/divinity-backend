<?php
$createPermission   = functionCheckPermission("UsersController@create");
$editPermission   = functionCheckPermission("UsersController@edit");
$viewPermission     = functionCheckPermission("UsersController@view");
$deletePermission   = functionCheckPermission("UsersController@delete");
$statusPermission   = functionCheckPermission("UsersController@changeStatus");

?>
@if($results->isNotEmpty())
@forelse($results as $result)
<tr class="list-data-row" data-total-count="{{$totalResults}}">
    <td>{{ date('d-m-Y', strtotime($result->created_at)) }}</td>
    <td><a href="{{route('admin-admin_users.show',base64_encode($result->id))}}">{{ $result->name ?? "N/A" }} </a><br>
    @php
        $email_subscription = \App\Models\Subscriber::where('email', $result->email)->exists();
        @endphp

        @if($email_subscription)
        <span class="badge bg-success">Subscribed</span>
        @else
        <span class="badge bg-danger">Not subscribed</span>
        @endif
</td>
     
    <td>
    {{!empty($result->addresses[0]->city->name) ? $result->addresses[0]->city->name:"NA"}} ,{{!empty($result->addresses[0]->state) ? $result->addresses[0]->state->code:"NA"}}, {{!empty($result->addresses[0]->country->name) ? $result->addresses[0]->country->name:"NA"}}
    </td>

    <td>{{!empty($result->orders) ? count($result->orders).' order':"NA"}}</td>
    <td>{{$result->referral_code}}</td>

    <td><a href="{{ route('admin-admin_users.user-referral-histories',['token' => encrypt($result->id)]) }}" class="btn btn-success" title="View Referral Histories">{{!empty($result->referralhistorys) ? count($result->referralhistorys):"0"}}</a></td>

    <td>{{ $result->role->name}}</td>
    <td><a href="{{ route('admin-admin_users.user-refunded-histories',['token' => encrypt($result->id)]) }}" class="btn btn-success" title="View Refunded Histories">{{!empty($result->refundedhistorys) ? count($result->refundedhistorys):"0"}}</a></td>

    <td>{{$result->wallet_avl_balance}}</td>

     <td>
        @if($result->is_active == 1)
        <span class="badge bg-success">Activated</span>
        @else
        <span class="badge bg-danger">Deactivated</span>
        @endif
    </td>

    <td>
        <div class="hstack gap-2 flex-wrap">
            @can('edit_user')
                @if($result->is_active == 1)
                    <a href='{{route("admin-admin_users.status",array($result->id,0))}}' class="btn btn-danger" id="deactivate-button"><i class="ri-close-line"></i></a>
                @else
                    <a href='{{route("admin-admin_users.status",array($result->id,1))}}' class="btn btn-success" id="activate-button"><i class="ri-check-line"></i></a>
                @endif
            @endcan

            @can('view_user')
                <a href="{{route('admin-admin_users.show',base64_encode($result->id))}}" class="btn btn-info"><i class="ri-eye-line"></i></a>
            @endcan
            
            @can('edit_user')
                <a href="{{route('admin-admin_users.edit',base64_encode($result->id))}}" class="btn btn-info"><i class="ri-edit-line"></i></a>
            @endcan

            @can('delete_user')                         
                <form method="GET" action="{{route('admin-admin_users.delete',base64_encode($result->id))}}">
                    @csrf
                    <input name="_method" type="hidden" value="DELETE">
                    <button type="submit" class="btn btn-danger" id="confirm-button"><i class="ri-delete-bin-5-line"></i></button>
                </form>
            @endcan
            @can('view_user')
                <a href="{{ route('admin-admin_users.user-review',['token' => encrypt($result->id)]) }}" class="btn btn-success" title="View Review">View Review</a>
            @endcan
            <!-- <a href="{{ route('admin-admin_users.user-referral-histories',['token' => encrypt($result->id)]) }}" class="btn btn-success" title="View Referral Histories">View Referral</a> -->

            <a data-bs-toggle="modal" data-bs-target="#userHistoryPopup" data-id="<?php echo $result->id; ?>" class="userHistoryPopupBtn btn btn-info" >User Login History</a>
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