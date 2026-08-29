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
                <li class="breadcrumb-item active" aria-current="page">Create Setting</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="{{ route('admin-settings.store') }}" method="post" enctype="multipart/form-data"
            id="createUserForm">
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
                                                <label for="title" class="form-label"><span class="text-danger">* </span>Title</label>
                                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{isset($setdetails->title) ? $setdetails->title: old('title')}}" placeholder="Title">
                                                @if ($errors->has('title'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('title') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="key" class="form-label"><span class="text-danger">* </span>Key</label>
                                                <input type="text" class="form-control @error('key') is-invalid @enderror" id="key" name="key" value="{{isset($setdetails->key) ? $setdetails->key: old('key')}}" placeholder="Key">
                                                @if ($errors->has('key'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('key') }}
                                                    </div>
                                                @endif
												<small>e.g., 'Site.title'</small>
                                            </div>

                                            <div class="col-xl-6">
												<lable class="form-label">Value </lable><span class="text-danger"> * </span>
												<textarea name="value" class="form-control form-control-solid form-control-lg  @error('value') is-invalid @enderror" cols="50" rows="10">{{old('value')}}</textarea>
												@if ($errors->has('value'))
												<div class=" invalid-feedback">
													{{ $errors->first('value') }}
												</div>
												@endif
                                            </div>

                                           
											<div class="col-xl-6">
                                                <label for="input_type" class="form-label"><span class="text-danger">* </span>Input Type</label>
                                                <input type="text" class="form-control @error('input_type') is-invalid @enderror" id="input_type" name="input_type" value="{{isset($setdetails->input_type) ? $setdetails->input_type: old('input_type')}}" placeholder="Input Type">
                                                @if ($errors->has('input_type'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('input_type') }}
                                                    </div>
                                                @endif
                                            </div>

											<div class="col-xl-6">
												<label for="editable">Editable<span class="text-danger"> * </span></label>
												<input checked="checked" name="editable" type="checkbox" id="editable">
												<input type="text" size="16" name="prependedInput2" id="prependedInput2" value="Editable" disabled="disabled" style="width:415px;" class="small">
											</div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
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
@endpush