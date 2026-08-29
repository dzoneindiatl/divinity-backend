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
    <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ ucfirst($action) }} About Us Section</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="" method="post" enctype="multipart/form-data" id="createBlogForm">
            @csrf
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Create Section
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
                                                    value="{{isset($blog->title) ? $blog->title: old('title')}}"
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
                                                    <option value="1" {{ (isset($blog) && $blog->is_active=='1' ? 'selected' : '') }}>Active</option>
                                                    <option value="0" {{ (isset($blog) && $blog->is_active=='0' ? 'selected' : '') }}>Inactive</option>
                                                </select>
                                                @if ($errors->has('is_active'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('is_active') }}
                                                </div>
                                                @endif
                                            
                                            </div>

                                            <div class="col-xl-12">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" cols="30" rows="5">{!! isset($blog->description) ? $blog->description: old('description') !!}</textarea>
                                                @if ($errors->has('description'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('description') }}
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
    CKEDITOR.replace(<?php echo 'description'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;
</script>

@endpush