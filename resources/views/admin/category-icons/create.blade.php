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
                <li class="breadcrumb-item active" aria-current="page">{{ ucfirst($action) }} Category Section</li>
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
                    {{ ucfirst($action) }} Section
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
                                                <label for="type" class="form-label">Type Position</label>
                                                <select class="js-example-placeholder-single form-control" name="type" id="type">
                                                    <option value="category_icon" {{ (isset($blog) && $blog->type=='category_icon' ? 'selected' : '') }}>Category Icon</option>
                                                    <option value="offer" {{ (isset($blog) && $blog->type=='offer' ? 'selected' : '') }}>Offer</option>
                                                </select>                                            
                                            </div>

                                            <div class="col-xl-6">
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

                                            <div class="col-xl-6">
                                                <label for="slug" class="form-label">slug</label>
                                                <input type="text"
                                                    class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{isset($blog->slug) ? $blog->slug: old('slug')}}" placeholder="Slug">
                                                @if ($errors->has('slug'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('slug') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="offer" class="form-label">Offer</label>
                                                <input type="text"
                                                    class="form-control" id="offer" name="offer" value="{{isset($blog->offer) ? $blog->offer: old('offer')}}" placeholder="offer">
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="description" class="form-label">Description</label>
                                                <input type="text" class="form-control" id="description" name="description" value="{{isset($blog->description) ? $blog->description: old('description')}}" placeholder="description">
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="image_position" class="form-label">Image Position</label>
                                                <select class="js-example-placeholder-single form-control" name="image_position" id="image_position">
                                                    <option value="left" {{ (isset($blog) && $blog->image_position=='left' ? 'selected' : '') }}>Left</option>
                                                    <option value="right" {{ (isset($blog) && $blog->image_position=='right' ? 'selected' : '') }}>Right</option>
                                                    <option value="center" {{ (isset($blog) && $blog->image_position=='center' ? 'selected' : '') }}>Center</option>
                                                </select>                                            
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="text_position" class="form-label">Text Position</label>
                                                <select class="js-example-placeholder-single form-control" name="text_position" id="text_position">
                                                    <option value="left" {{ (isset($blog) && $blog->text_position=='left' ? 'selected' : '') }}>Left</option>
                                                    <option value="right" {{ (isset($blog) && $blog->text_position=='right' ? 'selected' : '') }}>Right</option>
                                                    <option value="center" {{ (isset($blog) && $blog->text_position=='center' ? 'selected' : '') }}>Center</option>
                                                </select>
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="media" class="form-label">Media</label>
                                                <input type="file" class="form-control @error('media') is-invalid @enderror" id="media" name="media" />
                                                @if ($errors->has('media'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('media') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="height" class="form-label">Height</label>
                                                <input type="text" class="form-control" id="height" name="height" value="{{isset($blog->height) ? $blog->height: old('height')}}" placeholder="Height">
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="width" class="form-label">Width</label>
                                                <input type="text" class="form-control" id="width" name="width" value="{{isset($blog->width) ? $blog->width: old('width')}}" placeholder="Width">
                                            </div>

                                            <div class="col-xl-6">
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

@endpush