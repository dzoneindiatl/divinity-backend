@extends('admin.layout.master')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
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
                <li class="breadcrumb-item active" aria-current="page">Edit Option Value</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="col-xl-6">
    <div class="card custom-card">
        <div class="card-header">
            <div class="card-title">
                Edit Option Value
            </div>
        </div>
        <form action="{{ route('admin-product-options-values-update',['token' => encrypt($productOptionValue->id)]) }}" method="post" id="editProductOptionValuesForm">
            @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card-body p-0">
                                <div class="mb-3 select2-error">
                                    <label for="product_option_id" class="form-label"><span class="text-danger">* </span>Product Option</label>
                                    <select class="js-example-placeholder-single js-states form-control" name="product_option_id"
                                        id="product_option_id">
                                        <option value="">None</option>
                                        @forelse ($productOptions as $productOption)
                                        <option value="{{ $productOption->id }}" {{ $productOptionValue->product_option_id == $productOption->id ? 'selected' : '' }}>{{ $productOption->name }}</option>
                                        @empty
                                        <option value="" selected>No Data found</option>
                                        @endforelse
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label"><span class="text-danger">* </span>Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Enter Name" value="{{ $productOptionValue->name }}">
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Internal Select-2.js -->
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/js/form-validation.js') }}"></script>
{{-- <script src="{{ asset('assets/js/custom/product-values.js') }}"></script> --}}
@endpush