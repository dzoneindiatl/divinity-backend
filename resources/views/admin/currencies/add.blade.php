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
    <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Currency</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="{{ route('admin-currencies.save') }}" method="post" enctype="multipart/form-data"
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
                                                <label for="currency_code" class="form-label"><span class="text-danger">
                                                    </span>Currency Code</label>
                                                <input type="text"
                                                    class="form-control @error('currency_code') is-invalid @enderror" id="currency_code"
                                                    name="currency_code"
                                                    value="{{isset($userDetails->currency_code) ? $userDetails->currency_code: old('currency_code')}}"
                                                    placeholder="Currency Code">
                                                @if ($errors->has('currency_code'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('currency_code') }}
                                                </div>
                                                @endif
                                            </div>


                                            <div class="col-xl-6">
                                                <label for="symbol" class="form-label"><span class="text-danger">
                                                    </span>Symbol</label>
                                                <input type="text"
                                                    class="form-control @error('symbol') is-invalid @enderror" id="symbol"
                                                    name="symbol"
                                                    value="{{isset($userDetails->symbol) ? $userDetails->symbol: old('symbol')}}"
                                                    placeholder="Symbol">
                                                @if ($errors->has('symbol'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('symbol') }}
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

@endpush