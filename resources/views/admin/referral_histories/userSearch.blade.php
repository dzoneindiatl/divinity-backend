@if($users->isNotEmpty())
@foreach($users as $key => $user)
    <div class="col-md-12">
        <!--begin::Input-->
        <div class="form-group my-7" onclick=window.open("{{route('admin-referral_histories.treeView',base64_encode($user->id))}}",'_blank')>
            <div class="mws-form-item text-left">
                <h6>{{$user->name ?? ''}} ({{$user->email ?? ''}})</h6>
            </div>
        </div>
        <!--end::Input-->
    </div>

    <hr>
@endforeach
@else
<div class="col-md-12 ">
        <!--begin::Input-->
        <div class="form-group my-7">
            <div class="mws-form-item">

                <span class="mr-3">No Users Found</span>


            </div>
        </div>
        <!--end::Input-->
    </div>
@endif
