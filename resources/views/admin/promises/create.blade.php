@extends('admin.layout.master')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('public/assets/libs/dropzone/dropzone.css') }}">
<link href="{{ asset('public/assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('public/assets/js/ckeditor/ckeditor.js') }}"></script>

@endpush

@section('content')
<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <a class="btn btn-dark" href="{{ route('admin-promises.index') }}">List</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ ucfirst($action) }} Promise</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="" method="post" enctype="multipart/form-data" id="createPromisesForm">
            @csrf
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Create Promises
                    </div>
                </div>
                <div class="card-body add-products p-0">
                    <div class="p-4">
                        <div class="row gx-5">
                            <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12">
                                <div class="card custom-card shadow-none mb-0 border-0">
                                    <div class="card-body p-0">
                                        <div class="row gy-3">

                                            <div class="col-xl-4">
                                                <label for="name" class="form-label"><span class="text-danger">*
                                                    </span>Title</label>
                                                <input type="text"
                                                    class="form-control @error('title') is-invalid @enderror" id="title"
                                                    name="title"
                                                    value="{{isset($Promise->title) ? $Promise->title: old('title')}}"
                                                    placeholder="Title">
                                                @if ($errors->has('title'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('title') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-4">
                                                <label for="media" class="form-label">Media</label>
                                                <input type="file" class="form-control @error('media') is-invalid @enderror" id="media" name="media" />
                                                @if ($errors->has('media'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('media') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-4">
                                                <label for="is_active" class="form-label">Active</label>
                                                <select class="js-example-placeholder-single form-control @error('is_active') is-invalid @enderror" name="is_active" id="is_active">
                                                    <option value="1" {{ (isset($Promise) && $Promise->is_active=='1' ? 'selected' : '') }}>Active</option>
                                                    <option value="0" {{ (isset($Promise) && $Promise->is_active=='0' ? 'selected' : '') }}>Inactive</option>
                                                </select>
                                                @if ($errors->has('is_active'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('is_active') }}
                                                </div>
                                                @endif
                                            
                                            </div>

                                            <div class="col-xl-4">
                                                <input type="hidden" name="show_home" value="0">
                                                 <input class="form-check-input" name="show_home" type="checkbox" value="1" {{ old('show_home', $Promise->show_home ?? 0) == 1 ? 'checked' : '' }} id="flexCheckDefault"> <label class="form-check-label" for="flexCheckDefault"> Show On Home</label>
                                            
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="short_description" class="form-label">Short Description</label>
                                                <textarea class="form-control @error('short_description') is-invalid @enderror" name="short_description" id="short_description" cols="30" rows="5">{!! isset($Promise->short_description) ? $Promise->short_description: old('short_description') !!}</textarea>
                                                @if ($errors->has('short_description'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('short_description') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="long_description" class="form-label">Long Description</label>
                                                <textarea class="form-control @error('long_description') is-invalid @enderror" name="long_description" id="long_description" cols="30" rows="5">{!! isset($Promise->long_description) ? $Promise->long_description: old('long_description') !!}</textarea>
                                                @if ($errors->has('long_description'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('long_description') }}
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
<script src="{{ asset('public/assets/plugin/tagify/tagify.min.js') }}"></script>

<!-- Internal Select-2.js -->
<script src="{{ asset('public/assets/js/select2.js') }}"></script>
<script src="{{ asset('public/assets/libs/dropzone/dropzone-min.js') }}"></script>
<script src="{{ asset('public/assets/js/custom/product.js') }}"></script>
<script src="{{ asset('public/assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('public/assets/js/repeater.js')}}"></script>
<script>
    $(document).ready(function() {
        
    });
</script>


<script>
    CKEDITOR.replace(<?php echo 'short_description'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;

    CKEDITOR.replace(<?php echo 'long_description'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;
</script>

@endpush