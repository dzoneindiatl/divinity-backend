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
                <li class="breadcrumb-item active" aria-current="page">Edit Cms</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="{{ route('admin-cms-manager.update',base64_encode($cmsDetails->id)) }}" method="post" enctype="multipart/form-data"
            id="createCmsForm">
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
                                                <label for="page_name" class="form-label"><span class="text-danger">* </span>Page Name</label>
                                                <input type="text" class="form-control @error('page_name') is-invalid @enderror" id="page_name" name="page_name" value="{{isset($cmsDetails->page_name) ? $cmsDetails->page_name: old('page_name')}}" placeholder="Page Name">
                                                @if ($errors->has('page_name'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('page_name') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="title" class="form-label"><span class="text-danger">* </span>Page Title</label>
                                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{isset($cmsDetails->title) ? $cmsDetails->title: old('title')}}" placeholder="Page Title">
                                                @if ($errors->has('title'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('title') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-12 mt-3">
                                                <label for="body" class="form-label">Description</label>
                                                <textarea class="form-control @error('title') is-invalid @enderror" name="body" id="body" cols="30" rows="5">{!! isset($cmsDetails->body) ? $cmsDetails->body: old('body') !!}</textarea>
                                                @if ($errors->has('body'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('body') }}
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

<script>
    CKEDITOR.replace(<?php echo 'body'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;
</script>
@endpush