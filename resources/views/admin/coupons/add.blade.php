@extends('admin.layout.master')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.css') }}">
<link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>

@endpush

@section('content')
<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <a class="btn btn-dark" href="{{ route('admin-coupons.index') }}">List</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Coupon</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="{{ route('admin-coupons.save') }}" method="post" enctype="multipart/form-data"
            id="createCouponForm">
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
                                                <label for="coupon_code" class="form-label"><span class="text-danger">
                                                    </span>Coupon Code</label>
                                                <input type="text"
                                                    class="form-control @error('coupon_code') is-invalid @enderror" id="coupon_code"
                                                    name="coupon_code"
                                                    value="{{isset($userDetails->coupon_code) ? $userDetails->coupon_code: old('coupon_code')}}"
                                                    placeholder="Coupon Code">
                                                @if ($errors->has('coupon_code'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('coupon_code') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="coupon_type" class="form-label"><span class="text-danger">*
                                                    </span>Coupon Type</label>
                                                <select class="js-example-placeholder-single form-control @error('coupon_type') is-invalid @enderror"
                                                    name="coupon_type" id="coupon_type">
                                                    <option value="" selected>Select Coupon Type</option>
                                                    <option value="flat"
                                                        {{(!empty($userDetails->coupon_type) && $userDetails->coupon_type == 'flat') ? 'selected' : '' }}>
                                                        Flat</option>
                                                    <option value="percentage"
                                                        {{(!empty($userDetails->coupon_type) && $userDetails->coupon_type == 'percentage') ? 'selected' : '' }}>
                                                        Percentage</option>
                                                </select>
                                                @if ($errors->has('coupon_type'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('coupon_type') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="amount" class="form-label"><span class="text-danger">*
                                                    </span>Amount</label>
                                                <input type="text"
                                                    class="form-control @error('amount') is-invalid @enderror" id="amount"
                                                    name="amount"
                                                    value="{{isset($userDetails->amount) ? $userDetails->amount: old('amount')}}"
                                                    placeholder="Amount">
                                                @if ($errors->has('amount'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('amount') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="start_date" class="form-label"><span class="text-danger">* </span>Start Date</label>
                                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{isset($userDetails->start_date) ? $userDetails->start_date: old('start_date')}}" placeholder="Start Date">
                                                @if ($errors->has('start_date'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('start_date') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-xl-6">
                                                <label for="end_date" class="form-label"><span class="text-danger">* </span>End Date</label>
                                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{isset($userDetails->end_date) ? $userDetails->end_date: old('end_date')}}" placeholder="End Date">
                                                @if ($errors->has('end_date'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('end_date') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="min_amount" class="form-label"><span class="text-danger">
                                                    </span>Minimum Amount</label>
                                                <input type="text"
                                                    class="form-control @error('min_amount') is-invalid @enderror" id="min_amount"
                                                    name="min_amount"
                                                    value="{{isset($userDetails->min_amount) ? $userDetails->min_amount: old('min_amount')}}"
                                                    placeholder="Minimum Amount">
                                                @if ($errors->has('min_amount'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('min_amount') }}
                                                </div>
                                                @endif
                                            </div>
                                            {{-- <div class="col-xl-6">
                                                <label for="max_amount" class="form-label"><span class="text-danger">
                                                    </span>Maximum Amount</label>
                                                <input type="text"
                                                    class="form-control @error('max_amount') is-invalid @enderror" id="max_amount"
                                                    name="max_amount"
                                                    value="{{isset($userDetails->max_amount) ? $userDetails->max_amount: old('max_amount')}}"
                                                    placeholder="Maximum Amount">
                                                @if ($errors->has('max_amount'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('max_amount') }}
                                                </div>
                                                @endif
                                            </div> --}}

                                            <div class="col-xl-12 mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control @error('title') is-invalid @enderror" name="description" id="description" cols="30" rows="5">{!! isset($category->description) ? $category->description: old('description') !!}</textarea>
                                                @if ($errors->has('description'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('description') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-12 mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="is_assign" value="1" id="isAssign">
                                                    <label class="form-check-label" for="is_assign">
                                                        Do you want to assign this coupon for particular
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Hidden select box initially -->
                                            <div id="assignCouponField" style="display: none;">
                                                <div class="col-xl-6">
                                                    <label for="assign_type" class="form-label">Assign To</label>
                                                    <select class="form-select" name="assign_type" id="assign_type">
                                                        <option value="">Select Reference</option>
                                                        <option value="category"
                                                        {{(!empty($userDetails->coupon_type) && $userDetails->coupon_type == 'category') ? 'selected' : '' }}>
                                                        Category</option>
                                                        <option value="product"
                                                        {{(!empty($userDetails->coupon_type) && $userDetails->coupon_type == 'product') ? 'selected' : '' }}>
                                                        Product</option>
                                                        <option value="user"
                                                        {{(!empty($userDetails->coupon_type) && $userDetails->coupon_type == 'user') ? 'selected' : '' }}>
                                                        User</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div id="categoryDropdown" style="display: none;">
                                                <div class="col-xl-6">
                                                    <label for="reference" class="form-label">Categories</label>
                                                    <select class="js-example-placeholder-single js-states form-control" multiple="multiple" name="categoryData[]">
                                                        @forelse ($categories as $category)
                                                        <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                                        @empty
                                                        <option value="" selected>No Data found</option>
                                                        @endforelse
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-6" id="userDropdown" style="display: none;">
                                                <label for="reference" class="form-label">Users</label>
                                                <select class="js-example-placeholder-single js-states form-control" multiple="multiple" name="userData[]">
                                                    @forelse ($users as $user)
                                                    <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                                                    @empty
                                                    <option value="" selected>No Data found</option>
                                                    @endforelse
                                                </select>
                                            </div>
                                            <div class="col-xl-6" id="productDropdown" style="display: none;">
                                                <label for="reference" class="form-label">Products</label>
                                                <select class="js-example-placeholder-single js-states form-control" multiple="multiple" name="productData[]">
                                                    @forelse ($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                    @empty
                                                    <option value="" selected>No Data found</option>
                                                    @endforelse
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
    $(document).ready(function() {
        // Listen for changes in the checkbox state
        $('#isAssign').change(function() {
            if ($(this).is(':checked')) {
                // If checkbox is checked, show the select box field
                $('#assignCouponField').show();
            } else {
                // If checkbox is unchecked, hide the select box field
                $('#assignCouponField').hide();

                $('#assignCouponField select').val('').trigger('change');
                $('#categoryDropdown select').val('').trigger('change');
                $('#productDropdown select').val('').trigger('change');
                $('#userDropdown select').val('').trigger('change');
            }
        });

        $('#assign_type').change(function() {
            var selectedValue = $(this).val();
            if (selectedValue === 'category') {
                // If assign_type is "category", show the categoryDropdown
                $('#productDropdown').hide();
                $('#userDropdown').hide();
                $('#categoryDropdown').show();

                $('#userDropdown select').val('').trigger('change');
                $('#productDropdown select').val('').trigger('change');
            } else if(selectedValue === 'product') {
                // If assign_type is not "category", hide the categoryDropdown
                $('#categoryDropdown').hide();
                $('#userDropdown').hide();
                $('#productDropdown').show();

                $('#categoryDropdown select').val('').trigger('change');
                $('#userDropdown select').val('').trigger('change');
            } else if(selectedValue === 'user') {
                $('#categoryDropdown').hide();
                $('#productDropdown').hide();
                $('#userDropdown').show();

                $('#categoryDropdown select').val('').trigger('change');
                $('#productDropdown select').val('').trigger('change');
            } else{
                $('#categoryDropdown').hide();
                $('#productDropdown').hide();
                $('#userDropdown').hide();

                $('#categoryDropdown select').val('').trigger('change');
                $('#productDropdown select').val('').trigger('change');
                $('#userDropdown select').val('').trigger('change');
            }
        });
    });
</script>


<script>
    CKEDITOR.replace(<?php echo 'description'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;
</script>

@endpush