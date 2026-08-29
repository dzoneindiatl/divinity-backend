@extends('admin.layout.master')

@push('styles')
<link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
@endpush
@section('content')
@include('admin.layout.response_message')
<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Plan</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->

<div class="row">
    <div class="col-xl-12">
        <form action="{{ route('admin-plans.store') }}" method="post" id="plansForm"
            enctype="multipart/form-data">
            @csrf
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Create Plan
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="name" class="form-label"><span class="text-danger">* </span>Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Enter Name">
                                    @if ($errors->has('name'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('name') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="payout_period" class="form-label"><span class="text-danger"> </span>Payout Period (in days)</label>
                                    <input type="text" class="form-control @error('payout_period') is-invalid @enderror"
                                        id="payout_period" name="payout_period" placeholder="Enter Payout Period">
                                    @if ($errors->has('payout_period'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('payout_period') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12 mt-3">
                            <label for="body" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" cols="30" rows="5">{!! old('description') !!}</textarea>
                            @if ($errors->has('description'))
                                <div class=" invalid-feedback">
                                    {{ $errors->first('description') }}
                                </div>
                            @endif
                        </div>

                        <div class="col-xl-12 mt-3">
                            <label for="body" class="form-label">Terms and Conditions</label>
                            <textarea class="form-control @error('term_conditions') is-invalid @enderror" name="term_conditions" id="term_conditions" cols="30" rows="5">{!! old('term_conditions') !!}</textarea>
                            @if ($errors->has('term_conditions'))
                                <div class=" invalid-feedback">
                                    {{ $errors->first('term_conditions') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <div class="planDetailsRow card custom-card">

                <div class="card-header">
                    <div class="card-title">
                        Plan Details
                    </div>
                </div>
                <div class="card-body">
                    @if(!empty(($planDetailsData)))
                    <?php $iterationCount = 0; ?>
                    <div id="kt_repeater_1" class="ml-7">
                        <div class="form-group row" id="kt_repeater_1">
                            <div data-repeater-list="planDetailsArr" class="col-lg-12">
                                @foreach($planDetailsData as $dataKey => $dataVal)
                                @if(!empty($dataVal['sales_from']) && !empty($dataVal['sales_to']) && !empty($dataVal['type']) && !empty($dataVal['amount']) )
                                <div data-repeater-item class="form-group row align-items-center mb-0">
                                    <div class="col-md-2 mb-3">
                                        <div class="form-group">
                                            <label for="sales_from" class="form-label">Sales From</label><span class="text-danger">
                                                 </span>
                                            <input type="number" name="sales_from"
                                                class="form-control form-control-solid form-control-lg  @error('sales_from') is-invalid @enderror"
                                                value="{{!empty($dataVal['sales_from']) ? $dataVal['sales_from'] : ''}}">

                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <div class="form-group">
                                            <label for="sales_to" class="form-label">Sales To</label><span class="text-danger">
                                                 </span>
                                            <input type="number" name="sales_to"
                                                class="form-control form-control-solid form-control-lg  @error('sales_to') is-invalid @enderror"
                                                value="{{!empty($dataVal['sales_to']) ? $dataVal['sales_to'] : ''}}">

                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group">
                                            <label for="type" class="form-label">Type</label><span class="text-danger">
                                                 </span>
                                                 <select
                                                    class=" form-control"
                                                                name="type" id="type">
                                                                <option value="" selected>Select Type</option>
                                                                <option value="flat" {{(!empty($dataVal['type']) && $dataVal['type'] == 'flat') ? 'selected' : ''}} >Flat</option>
                                                                <option value="percentage" {{(!empty($dataVal['type']) && $dataVal['type'] == 'percentage') ? 'selected' : ''}} >Percentage</option>
                                                                
                                                    </select>

                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group">
                                            <label for="amount" class="form-label">Amount</label><span class="text-danger">
                                                 </span>
                                            <input type="number" name="amount"
                                                class="form-control form-control-solid form-control-lg  @error('amount') is-invalid @enderror"
                                                value="{{!empty($dataVal['amount']) ? $dataVal['amount'] : ''}}">

                                        </div>
                                    </div>

                                    

                                    <div class="col-md-2">
                                        @if($iterationCount == 0)
                                        <a href="javascript:;" data-repeater-create=""
                                            class="btn btn-sm font-weight-bolder btn btn-primary-light btn-border-down">
                                            <i class="la la-plus"></i>Add More Details
                                        </a>
                                        <a href="javascript:;" data-repeater-delete=""
                                            class="btn btn-sm font-weight-bolder btn btn-danger-light btn-border-down"
                                            style="display:none;">
                                            <i class="la la-trash-o"></i>Delete Details
                                        </a>
                                        @else
                                        <a href="javascript:;" data-repeater-create=""
                                            class="btn btn-sm font-weight-bolder btn btn-primary-light btn-border-down"
                                            style="display:none;">
                                            <i class="la la-plus"></i>Add More Details
                                        </a>
                                        <a href="javascript:;" data-repeater-delete=""
                                            class="btn btn-sm font-weight-bolder btn btn-danger-light btn-border-down">
                                            <i class="la la-trash-o"></i>Delete Details
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                @endif


                                <?php $iterationCount++; ?>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @else
                    <div id="kt_repeater_1" class="ml-7">
                        <div class="form-group row" id="kt_repeater_1">

                            <div data-repeater-list="planDetailsArr" class="col-lg-12">
                                <div data-repeater-item class="form-group row align-items-center mb-0">
                                    <div class="col-md-2 mb-3">
                                        <div class="form-group">
                                            <label for="sales_from" class="form-label">Sales From</label><span class="text-danger">
                                                 </span>
                                            <input type="number" name="sales_from"
                                                class="form-control form-control-solid form-control-lg  @error('sales_from') is-invalid @enderror"
                                                value="{{!empty($dataVal['sales_from']) ? $dataVal['sales_from'] : ''}}">

                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <div class="form-group">
                                            <label for="sales_to" class="form-label">Sales To</label><span class="text-danger">
                                                 </span>
                                            <input type="number" name="sales_to"
                                                class="form-control form-control-solid form-control-lg  @error('sales_to') is-invalid @enderror"
                                                value="{{!empty($dataVal['sales_to']) ? $dataVal['sales_to'] : ''}}">

                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group">
                                            <label for="type" class="form-label">Type</label><span class="text-danger">
                                                 </span>
                                                 <select
                                                    class=" form-control"
                                                                name="type" id="type">
                                                                <option value="" selected>Select Type</option>
                                                                <option value="flat" {{(!empty($dataVal['type']) && $dataVal['type'] == 'flat') ? 'selected' : ''}} >Flat</option>
                                                                <option value="percentage" {{(!empty($dataVal['type']) && $dataVal['type'] == 'percentage') ? 'selected' : ''}} >Percentage</option>
                                                                
                                                    </select>

                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group">
                                            <label for="amount" class="form-label">Amount</label><span class="text-danger">
                                                 </span>
                                            <input type="number" name="amount"
                                                class="form-control form-control-solid form-control-lg  @error('amount') is-invalid @enderror"
                                                value="{{!empty($dataVal['amount']) ? $dataVal['amount'] : ''}}">

                                        </div>
                                    </div>



                                    <div class="col-md-2">
                                        <a href="javascript:;" data-repeater-create=""
                                            class="btn btn-sm font-weight-bolder btn btn-primary-light btn-border-down">
                                            <i class="la la-plus"></i>Add More Details
                                        </a>
                                        <a href="javascript:;" data-repeater-delete=""
                                            class="btn btn-sm font-weight-bolder btn btn-danger-light btn-border-down"
                                            style="display:none;">
                                            <i class="la la-trash-o"></i>Delete Details
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    @endif
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
<script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>
<script src="{{ asset('assets/js/form-validation.js') }}"></script>
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
</script>


<script>
    CKEDITOR.replace(<?php echo 'description'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;

    CKEDITOR.replace(<?php echo 'term_conditions'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;
</script>

@endpush