@extends('admin.layout.master')

@push('styles')
<link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
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
                <li class="breadcrumb-item"><a href="{{  route('admin-footer-category.index')}}">Footer Categories</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Footer Subcategory</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->

<div class="card custom-card">
    <div class="card-header">
        <div class="card-title">
            Edit Footer Subcategory
        </div>
    </div>
    <form action="" method="post" id="designationForm" autocomplete="off" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-xl-6">
                    <div class="card-body p-0">
                        <div class="mb-3">
                            <label for="name" class="form-label"><span class="text-danger">* </span>Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" placeholder="Enter Name" value="{{ $modell->name ?? '' }}">
                            @if ($errors->has('name'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('name') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card-body p-0">
                        <div class="mb-3">
                            <label for="url" class="form-label"><span class="text-danger">* </span>URL</label>
                            <input type="text" class="form-control @error('url') is-invalid @enderror" id="url"
                                name="url" placeholder="Enter URL" value="{{ $modell->url ?? '' }}">
                            @if ($errors->has('url'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('url') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-6">
                    <div class="card-body p-0">
                        <div class="mb-3">
                            <label for="order_number" class="form-label"><span class="text-danger"> </span>Order Number</label>
                            <input type="text" class="form-control @error('order_number') is-invalid @enderror" id="order_number"
                                name="order_number" placeholder="Enter Order Number" value="{{ $modell->order_number ?? '' }}">
                            @if ($errors->has('order_number'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('order_number') }}
                            </div>
                            @endif
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

@endsection
@push('scripts')
<script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>
<!-- <script src="{{ asset('assets/js/form-validation.js') }}"></script> -->
<script src="{{ asset('assets/plugin/tagify/tagify.min.js') }}"></script>
<script src="{{ asset('assets/js/custom/designation.js') }}"></script>

@endpush