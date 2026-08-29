@extends('admin.layout.master')

@section('content')
<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Coupon</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header End -->

<div class="row">
    <div class="col-xl-12">
        <!-- Coupon Creation Form -->
        <form action="{{ route('admin-coupons.store') }}" method="post" enctype="multipart/form-data" id="createCouponForm">
            @csrf
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Basic Info</div>
                </div>
                <div class="card-body add-products p-0">
                    <div class="p-4">
                        <div class="row gx-5">
                            <div class="col-12">
                                <div class="card custom-card shadow-none mb-0 border-0">
                                    <div class="card-body p-0">
                                        <div class="row gy-3">
                                            <!-- Name -->

                                            <input type="hidden"  name="coupon_id"  value="{{ isset($coupon->id) ? $coupon->id : '' }}">

                                              
                                            <div class="col-xl-3">
                                                <label for="name" class="form-label"><span class="text-danger">*</span>Name</label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" required value="{{ isset($coupon->name) ? $coupon->name : old('name') }}" placeholder="Name">
                                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <!-- Coupon Code -->
                                            <div class="col-xl-3">
                                                <label for="coupon_code" class="form-label"><span class="text-danger">*</span>Coupon Code</label>
                                                <input type="text" class="form-control @error('coupon_code') is-invalid @enderror" id="coupon_code" name="coupon_code" required value="{{ isset($coupon->coupon_code) ? $coupon->coupon_code : old('coupon_code') }}" placeholder="Coupon Code">
                                                @error('coupon_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <!-- Coupon Type -->
                                            <div class="col-xl-3">
                                                <label for="coupon_type" class="form-label">Coupon Type</label>
                                                <select class="select2 form-control @error('coupon_type') is-invalid @enderror" name="coupon_type" id="coupon_type" required>
                                                    <option value="private" {{ (!empty($coupon->coupon_type) && $coupon->coupon_type == 'private') ? 'selected' : '' }}>Private</option>
                                                    <option value="public" {{ (!empty($coupon->coupon_type) && $coupon->coupon_type == 'public') ? 'selected' : '' }}>Public</option>
                                                </select>
                                                @error('coupon_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <!-- User Type -->
                                            <div class="col-xl-3 user_type" style="display: none;">
                                                <label for="user_type" class="form-label">User Type</label>
                                                <select class="select2 form-control @error('user_type') is-invalid @enderror" name="user_type" id="user_type" required>
                                                    <option value="" selected>Select User Type</option>
                                                    <option value="all" {{ (!empty($coupon->user_type) && $coupon->user_type == 'all') ? 'selected' : '' }}>All Users</option>
                                                    <option value="existing" {{ (!empty($coupon->user_type) && $coupon->user_type == 'existing') ? 'selected' : '' }}>Existing Users</option>
                                                    <option value="new" {{ (!empty($coupon->user_type) && $coupon->user_type == 'new') ? 'selected' : '' }}>New Users</option>
                                                </select>
                                                @error('user_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <!-- Customer Name (Multi Select) -->
                                            <div class="col-xl-3 customer_name">
                                                <label for="customer_name" class="form-label"><span class="text-danger">*</span>Customer Name</label>
                                                <select required class="select2 form-control @error('customer_name') is-invalid @enderror" name="customer_name[]" id="customer_name" multiple required>
                                                    @foreach($users as $user)
                                                         <option value="{{ $user->id }}"
                                                            @if(!empty($coupon->customers) && $coupon->customers->contains($user->id)) selected @endif>
                                                            {{ $user->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                           

                                            <!-- Discount Type -->
                                            <div class="col-xl-3">
                                                <label for="discount_type" class="form-label">Discount Type</label>
                                                <select class="select2 form-control @error('discount_type') is-invalid @enderror" name="discount_type" id="discount_type" required>
                                                    <option value="flat" {{ (!empty($coupon->discount_type) && $coupon->discount_type == 'flat') ? 'selected' : '' }}>Flat</option>
                                                    <option value="percentage" {{ (!empty($coupon->discount_type) && $coupon->discount_type == 'percentage') ? 'selected' : '' }}>Percentage</option>
                                                </select>
                                                @error('discount_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <!-- Discount Value -->
                                            <div class="col-xl-3">
                                                <label for="discount_value" class="form-label"><span class="text-danger">*</span>Discount Value</label>
                                                <input type="text" class="form-control @error('discount_value') is-invalid @enderror" id="discount_value" name="discount_value" required value="{{ isset($coupon->discount_value) ? $coupon->discount_value : old('discount_value') }}" placeholder="Discount Value">
                                                @error('discount_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <!-- Max & Min Discount -->
                                            <div class="col-xl-3">
                                                <label for="max_discount" class="form-label">Maximum Discount</label>
                                                <input type="text" class="form-control @error('max_discount') is-invalid @enderror" id="max_discount" name="max_discount" value="{{ isset($coupon->max_discount) ? $coupon->max_discount : old('max_discount') }}" placeholder="Maximum Amount">
                                                @error('max_discount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-xl-3">
                                                <label for="min_discount" class="form-label">Minimum Discount</label>
                                                <input type="text" class="form-control @error('min_discount') is-invalid @enderror" id="min_discount" name="min_discount" value="{{ isset($coupon->min_discount) ? $coupon->min_discount : old('min_discount') }}" placeholder="Minimum Amount">
                                                @error('min_discount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <!-- Start & End Dates -->
                                            <div class="col-xl-3">
                                                <label for="start_date" class="form-label">Valid From</label>
                                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ isset($coupon->start_date) ? $coupon->start_date : old('start_date') }}">
                                                @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-xl-3">
                                                <label for="end_date" class="form-label">Valid To</label>
                                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ isset($coupon->end_date) ? date('Y-m-d', strtotime($coupon->end_date)) : old('end_date') }}">
                                                @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <!-- Is Unlimited -->
                                            <div class="col-xl-3">
                                                <label for="is_unlimited" class="form-label">Is Unlimited</label>
                                                <select class="select2 form-control @error('is_unlimited') is-invalid @enderror" name="is_unlimited" id="is_unlimited">
                                                    <option value="1" {{ (!empty($coupon->is_unlimited) && $coupon->is_unlimited == 1) ? 'selected' : '' }}>Yes</option>
                                                     <option value="0" {{ (!empty($coupon->is_unlimited) && $coupon->is_unlimited == 0) ? 'selected' : '' }}>No</option>
                                                </select>
                                                @error('is_unlimited') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <!-- Available Coupons -->
                                            <div class="col-xl-3 available_coupons" style="display: {{ (isset($coupon->description) && $coupon->is_unlimited ? 'none' : 'block') }};">
                                                <label for="available_coupons" class="form-label"><span class="text-danger">*</span>Available Coupons</label>
                                                <input type="text" class="form-control @error('available_coupons') is-invalid @enderror" id="available_coupons" name="available_coupons" required value="{{ isset($coupon->available_coupons) ? $coupon->available_coupons : old('available_coupons') }}">
                                                @error('available_coupons') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <!-- Min Cart Value -->
                                            <div class="col-xl-3">
                                                <label for="min_cart_value" class="form-label">Minimum Cart Value</label>
                                                <input type="text" class="form-control @error('min_cart_value') is-invalid @enderror" id="min_cart_value" name="min_cart_value" value="{{ isset($coupon->min_cart_value) ? $coupon->min_cart_value : old('min_cart_value') }}">
                                                @error('min_cart_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <!-- Category -->
                                            <div class="col-xl-3">
                                                <label for="category" class="form-label">Category</label>
                                                <select class="select2 form-control @error('category') is-invalid @enderror" name="category" id="category">
                                                    <option value="">select Category</option>
                                                    @foreach($categories as $key => $value)
                                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <!-- Sub Category -->
                                            <div class="col-xl-3">
                                                <label for="sub_category" class="form-label">Sub Category</label>
                                                <select class="select2 form-control @error('sub_category') is-invalid @enderror" name="sub_category[]" id="sub_category" multiple></select>
                                                @error('sub_category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <!-- Status -->
                                            <div class="col-xl-3">
                                                <label for="is_active" class="form-label">Status</label>
                                                <select class="select2 form-control @error('is_active') is-invalid @enderror" name="is_active" id="is_active">
                                                    <option value="1" {{ (!empty($coupon->is_active) && $coupon->is_active) ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ (!empty($coupon->is_active) && !$coupon->is_active) ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                                @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <!-- Description -->
                                            <div class="col-xl-12 mb-3">
                                                <label for="description" class="form-label">Terms & Conditions</label>
                                                <textarea required  class="form-control @error('description') is-invalid @enderror" name="description" id="description" cols="30" rows="5">{!! isset($coupon->description) ? $coupon->description : old('description') !!}</textarea>
                                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('assets/js/custom/coupon.js') }}"></script>
<script>
    $('#createCouponForm').validate();
    var subCategoryRoute = "{{ route('admin-coupons.getSubCategoryList') }}";
    CKEDITOR.replace('description', {
        filebrowserUploadUrl: '{{ url('base/uploder') }}',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;
</script>
@endpush