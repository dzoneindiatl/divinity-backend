@extends('admin.layout.master')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.css') }}">
<link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Partner</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="{{route('admin-partners.update',$userDetails->id)}}" method="post" enctype="multipart/form-data"
            id="editUserForm">
            @csrf
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Basic Info
                    </div>
                </div>
                <div class="card-body add-products p-0">
                    <div class="p-4">
                        <div class="row gx-5">
                            <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12">
                                <div class="card custom-card shadow-none mb-0 border-0">
                                    <div class="card-body p-0">
                                        <div class="row gy-3">
                                            <div class="col-xl-6">
                                                <label for="name" class="form-label"><span class="text-danger">*
                                                    </span>Name</label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror" id="name"
                                                    name="name"
                                                    value="{{isset($userDetails->name) ? $userDetails->name: old('name')}}"
                                                    placeholder="Name">
                                                @if ($errors->has('name'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('name') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="email" class="form-label"><span class="text-danger">*
                                                    </span>Email</label>
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror" id="email"
                                                    name="email"
                                                    value="{{isset($userDetails->email) ? $userDetails->email: old('email')}}"
                                                    placeholder="Email">
                                                @if ($errors->has('email'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('email') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="phone_number" class="form-label"><span class="text-danger">*
                                                    </span>Phone Number</label>
                                                <input type="text"
                                                    class="form-control @error('phone_number') is-invalid @enderror"
                                                    id="phone_number" name="phone_number"
                                                    value="{{isset($userDetails->phone_number) ? $userDetails->phone_number: old('phone_number')}}"
                                                    placeholder="Phone Number">
                                                @if ($errors->has('phone_number'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('phone_number') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="gender" class="form-label"><span class="text-danger">*
                                                    </span>Gender</label>
                                                <select
                                                    class="js-example-placeholder-single form-control @error('gender') is-invalid @enderror"
                                                    name="gender" id="gender">
                                                    <option value="" selected>Select Gender</option>
                                                    <option value="male"
                                                        {{(!empty($userDetails->gender) && $userDetails->gender == 'male') ? 'selected' : '' }}>
                                                        Male</option>
                                                    <option value="female"
                                                        {{(!empty($userDetails->gender) && $userDetails->gender == 'female') ? 'selected' : '' }}>
                                                        Female</option>
                                                    <option value="other"
                                                        {{(!empty($userDetails->gender) && $userDetails->gender == 'others') ? 'selected' : '' }}>
                                                        Other</option>
                                                </select>
                                                @if ($errors->has('gender'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('gender') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="image" class="form-label"><span class="text-danger">
                                                    </span>Profile Picture</label>
                                                <input type="file"
                                                    class="form-control @error('image') is-invalid @enderror" id="image"
                                                    name="image">
                                                @if (!empty($userDetails->image))
                                                <img height="50" width="50"
                                                    src="{{isset($userDetails->image)? $userDetails->image:''}}" />
                                                @endif
                                                @if ($errors->has('image'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('image') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="password" class="form-label"><span class="text-danger">
                                                    </span>Password</label>
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="password" name="password" value="{{ old('password') }}"
                                                    placeholder="Password">
                                                @if ($errors->has('password'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('password') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="confirm_password" class="form-label"><span
                                                        class="text-danger"> </span>Confirm Password</label>
                                                <input type="password"
                                                    class="form-control @error('confirm_password') is-invalid @enderror"
                                                    id="confirm_password" name="confirm_password"
                                                    value="{{old('confirm_password') }}" placeholder="Confirm Password">
                                                @if ($errors->has('confirm_password'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('confirm_password') }}
                                                </div>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="planDetailsRow card custom-card">

                <div class="card-header">
                    <div class="card-title">
                        Assign Plans
                    </div>
                </div>
                <div class="card-body row">
                    <div class="col-xl-6">
                        <label for="plan_id" class="form-label"><span class="text-danger">*
                            </span>Plan</label>
                        <select class=" js-example-placeholder-single form-control planSelect @error('plan_id') is-invalid @enderror" name="plan_id"
                            id="plan_id">
                            <option value="" selected>Select Plan</option>
                            @forelse ($plans as $plan)
                            <option value="{{ $plan->id }}"
                                {{(!empty($userDetails) && $plan->id == $userDetails->plan_id ) ? 'selected' : ''}}>
                                {{ $plan->name }}</option>
                            @empty
                            <option value="" selected>No Data found</option>
                            @endforelse
                        </select>
                        @if ($errors->has('plan_id'))
                        <div class="invalid-feedback">
                            {{ $errors->first('plan_id') }}
                        </div>
                        @endif
                    </div>
                    <div class="col-xl-6">
                        <div class="card-body p-0">
                            <div class="mb-3">
                                <label for="payout_period" class="form-label"><span class="text-danger"> </span>Payout
                                    Period (in days)</label>
                                <input type="text" class="form-control @error('payout_period') is-invalid @enderror"
                                    id="payout_period" name="payout_period"
                                    value="{{isset($userDetails->payout_period) ? $userDetails->payout_period: old('payout_period')}}"
                                    placeholder="Enter Payout Period">
                                @if ($errors->has('payout_period'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('payout_period') }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-12 planDetailsContainer">
                        @if(!empty($userDetails->plan_id))
                            @include('admin.partners.plan_details')
                        @endif
                    </div>
                </div>
                
                

            </div>

            <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>

        </form>
    </div>
</div>

@endsection

@push('scripts')
<!-- Select2 Cdn -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/plugin/tagify/tagify.min.js') }}"></script>

<!-- Internal Select-2.js -->
<script src="{{ asset('assets/js/select2.js') }}"></script>

<script src="{{ asset('assets/libs/dropzone/dropzone-min.js') }}"></script>

<script src="{{ asset('assets/js/custom/product.js') }}"></script>

{{-- <script src="{{ asset('assets/js/fileupload.js') }}"></script> --}}
<script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<!-- <script src="{{ asset('assets/js/form-validation.js') }}"></script> -->
<script src="{{ asset('assets/js/repeater.js')}}"></script>
<script>
var KTFormRepeater = function() {

    var demo1 = function() {


        $('#kt_repeater_1').repeater({
            initEmpty: false,

            defaultValues: {

            },

            show: function() {

                var $item = $(this);
                // Get the counter value (assuming you have a counter element)
                var counter = $item.parent().find('[data-repeater-item]').index($item);
                $elem = $(this).slideDown();
                $elem.find('.btn-primary-light').remove();
                $elem.find('.btn-danger-light').show();

            },

            hide: function(deleteElement) {
                $(this).slideUp(deleteElement);
            },
            isFirstItemUndeletable: true
        });


    }

    return {
        // public functions
        init: function() {
            demo1();
        }
    };
}();

jQuery(document).ready(function() {
    KTFormRepeater.init();
});


$(document).on('change', '.planSelect', function() {
    $planId = $(this).val() ?? '';
    if($planId && $planId != ''){
        let routeUrl = "{{route('admin-partners.fetchPlanDetails',':planId')}}";
        routeUrl = routeUrl.replace(':planId', $planId);

        $.ajax({
            url: routeUrl,
            method: "GET",
            success: function(response) {
                if (response && response.htmlData) {

                    $('.planDetailsContainer').html(response.htmlData);
                }
                if (response && response.payout_period) {
                    $('[name=payout_period]').val(response.payout_period);
                }
            },
            error: function(jqXHR, exception) {
                console.log("error");
            }
        });
    }else{
        $('.planDetailsContainer').html('');
    }
    
});
</script>
@endpush