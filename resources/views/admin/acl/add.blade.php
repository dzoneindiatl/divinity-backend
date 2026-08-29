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
                <li class="breadcrumb-item active" aria-current="page">Create Acl</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="{{ route('admin-acl.store') }}" method="post" enctype="multipart/form-data"
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
                                                <label for="parent_id">Select Parent</label><span class="text-danger"> </span>
                                                <select class="form-control" name="parent_id" value="{{old('parent_id')}}">
                                                    <option value="{{old('parent_id')}}">Select Parent </option>
                                                    @foreach($parent_list as $list)
                                                    <option value="{{$list->id}}">{{$list->title}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-xl-6">
                                                <label for="title">Title </label><span class="text-danger"> * </span>
                                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{old('title')}}">
                                                @if ($errors->has('title'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('title') }}
                                                </div>
                                                @endif
                                            </div>
                                            <div class="col-xl-6">
                                                <label for="path">Path </label><span class="text-danger"> * </span>
                                                <input type="text" name="path" class="form-control @error('path') is-invalid @enderror" value="{{old('path')}}">
                                                <span>Without Plugin URL: javascript::void();</span>
                                                @if ($errors->has('path'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('path') }}
                                                </div>
                                                @endif
                                            </div>
                                            <div class="col-xl-6">
                                                <label for="module_order">Order </label><span class="text-danger"> * </span>
                                                <input type="text" name="module_order" class="form-control @error('module_order') is-invalid @enderror" value="{{old('module_order')}}">
                                                @if ($errors->has('module_order'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('module_order') }}
                                                </div>
                                                @endif
                                            </div>
                                            <div class="col-xl-12">
                                                <label for="icon">Icon </label><span class="text-danger"> </span>
                                                <textarea name="icon" class="form-control">{{old('icon')}} </textarea>
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