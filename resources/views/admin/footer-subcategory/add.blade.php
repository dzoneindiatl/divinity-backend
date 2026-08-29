@extends('admin.layout.master')

@push('styles')
<link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
@endpush
@section('content')
@include('admin.layout.response_message')
<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <a class="btn btn-dark" href="{{ route('admin-footer-category.index') }}">Back</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{  route('admin-footer-category.index')}}">Footer Categories</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ ucfirst($action) }} Footer Subcategory</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->

<div class="card custom-card">
    <div class="card-header">
        <div class="card-title">
            {{ ucfirst($action) }} Footer Subcategory In {{ ucfirst($departmentDetails->name) }}
        </div>
    </div>
    <form action="{{route('admin-'.$model.'.'.$action,['endesid'=>base64_encode($dep_id)])}}" method="post" id="designationForm" autocomplete="off" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-xl-4">
                    <div class="card-body p-0">
                        <div class="mb-3">
                            <label for="type" class="form-label"><span class="text-danger">* </span>Type</label>
                            <select class="js-example-placeholder-single form-control @error('type') is-invalid @enderror" name="type" id="type">
                                <option value="">Select</option>
                                <option value="page" {{ (isset($modell) && $modell->type=='page' ? 'selected' : '') }}>Page</option>
                                <option value="url" {{ (isset($modell) && $modell->type=='url' ? 'selected' : '') }}>Url</option>
                            </select>
                            @if ($errors->has('type'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('type') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 common">
                    <div class="card-body p-0">
                        <div class="mb-3">
                            <label for="title" class="form-label"><span class="text-danger">* </span>Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" placeholder="Enter Title" value="{{ (isset($modell) ? $modell->title : old('title')) }}">
                            @if ($errors->has('title'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('title') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 common">
                    <div class="card-body p-0">
                        <div class="mb-3">
                            <label for="slug" class="form-label"><span class="text-danger"></span>Slug</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" placeholder="Enter Slug" value="{{ (isset($modell) ? $modell->slug : old('slug')) }}" readonly>
                            @if ($errors->has('slug'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('slug') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- <div class="col-xl-6 common">
                    <div class="card-body p-0">
                        <div class="mb-3">
                            <label for="order_number" class="form-label"><span class="text-danger"> </span>Order Number</label>
                            <input type="text" class="form-control @error('order_number') is-invalid @enderror" id="order_number"
                                name="order_number" placeholder="Enter Order Number" value="{{ (isset($modell) ? $modell->order_number : old('order_number')) }}">
                            @if ($errors->has('order_number'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('order_number') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div> -->
                <div class="col-xl-6 page-section">
                    <div class="card-body p-0">
                        <div class="mb-3">
                            <label for="description" class="form-label"><span class="text-danger">* </span>Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ (isset($modell) ? $modell->description : old('description')) }}</textarea>
                            @if ($errors->has('description'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('description') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 page-section">
                    <div class="card-body p-0">
                        <div class="mb-3">
                            <label for="meta_description" class="form-label"><span class="text-danger"> </span>Meta Description</label>
                            <textarea class="form-control @error('meta_description') is-invalid @enderror" id="meta_description" name="meta_description">{{ (isset($modell) ? $modell->meta_description : old('meta_description')) }}</textarea>
                            @if ($errors->has('meta_description'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('meta_description') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 page-section">
                    <div class="card-body p-0">
                        <div class="mb-3">
                            <label for="meta_key" class="form-label"><span class="text-danger"> </span>Meta Key</label>
                            <input type="text" class="form-control @error('meta_key') is-invalid @enderror" id="meta_key" name="meta_key" placeholder="Enter Meta Key" value="{{ (isset($modell) ? $modell->meta_key : old('meta_key')) }}">
                            @if ($errors->has('meta_key'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('meta_key') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 page-section">
                    <div class="card-body p-0">
                        <div class="mb-3">
                            <label for="meta_title" class="form-label"><span class="text-danger"> </span>Meta Title</label>
                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror" id="meta_title" name="meta_title" placeholder="Enter Meta Title" value="{{ (isset($modell) ? $modell->meta_title : old('meta_title')) }}">
                            @if ($errors->has('meta_title'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('meta_title') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 page-section">  
                    <div class="card-body p-0">
                        <div class="mb-3">
                            <label for="meta_url" class="form-label"><span class="text-danger"> </span>Meta URL</label>
                            <input type="text" class="form-control @error('meta_url') is-invalid @enderror" id="meta_url"
                                name="meta_url" placeholder="Enter Meta URL" value="{{ (isset($modell) ? $modell->meta_url : old('meta_url')) }}">
                            @if ($errors->has('meta_url'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('meta_url') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 page-section">
                    <div class="card-body p-0">
                        <div class="mb-3">
                            <label for="banner_type" class="form-label"><span class="text-danger"> </span>Banner Type</label>
                            <select class="js-example-placeholder-single form-control @error('banner_type') is-invalid @enderror" name="banner_type" id="banner_type">
                                <option value="">Select</option>
                                <option value="image" {{ (isset($modell) && $modell->banner_type=='image' ? 'selected' : '') }}>Image</option>
                                <option value="video" {{ (isset($modell) && $modell->banner_type=='video' ? 'selected' : '') }}>Video</option>
                            </select>
                            @if ($errors->has('banner_type'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('banner_type') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 page-section banner-media">
                    @if(isset($modell))
                        @if($modell->banner_type == 'image')
                            <img src="{{ str_replace(['//public', '/public'], '', Config('constant.BANNER_IMAGE_URL').$modell->banner_media) }}" width="100" height="100" alt="" />
                        @elseif($modell->banner_type == 'video')
                            <video width="100" height="80" controls>
                                <source src="{{ str_replace(['//public', '/public'], '', Config('constant.BANNER_IMAGE_URL').$modell->banner_media) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @endif
                    @endif
                    <div class="card-body p-0">
                        <div class="mb-3">
                            <label for="banner_media" class="form-label"><span class="text-danger"> </span>Banner Media</label>
                            <input type="file" class="form-control @error('banner_media') is-invalid @enderror" id="banner_media"
                                name="banner_media">
                            @if ($errors->has('banner_media'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('banner_media') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 url-section">  
                    <div class="card-body p-0">
                        <div class="mb-3">
                            <label for="url" class="form-label"><span class="text-danger"> </span>URL</label>
                            <input type="text" class="form-control @error('url') is-invalid @enderror" id="url"
                                name="url" placeholder="Enter URL" value="{{ (isset($modell) ? $modell->url : old('url')) }}">
                            @if ($errors->has('url'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('url') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 common">
                    <div class="card-body p-0">
                        <div class="mb-3">
                            <label for="is_active" class="form-label">Active</label>
                            <select class="js-example-placeholder-single form-control @error('is_active') is-invalid @enderror" name="is_active" id="is_active">
                                <option value="1" {{ (isset($modell) && $modell->is_active=='1' ? 'selected' : '') }}>Active</option>
                                <option value="0" {{ (isset($modell) && $modell->is_active=='0' ? 'selected' : '') }}>Inactive</option>
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
        <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/plugin/tagify/tagify.min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>
<script src="{{ asset('assets/js/custom/designation.js') }}"></script>

<script>
    $(document).ready(function() {
        var type = "{{ (isset($modell) ? $modell->type : '') }}";
        if(type == 'page'){
            $('.page-section').show();
            $('.url-section').hide();
        } else if(type == 'url'){
            $('.page-section').hide();
            $('.url-section').show();
        }
        $('#type').change(function(){
            if($(this).val() == 'page'){
                $('.page-section').show();
                $('.url-section').hide();
            } else {
                $('.page-section').hide();
                $('.url-section').show();
            }
            banner-media
        })
    });
</script>


<script>
    CKEDITOR.replace(<?php echo 'description'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;

    CKEDITOR.replace(<?php echo 'meta_description'; ?>, {
        filebrowserUploadUrl: '<?php echo route('admin-editor-upload'); ?>',
        filebrowserUploadMethod: 'form'
    });
    CKEDITOR.config.allowedContent = true;
</script>

@endpush
