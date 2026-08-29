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
                <li class="breadcrumb-item active" aria-current="page">{{ ucfirst($pageHeading) }} Slider</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        {{ ucfirst($pageHeading) }} Slider
                    </div>
                </div>
                <div class="card-body add-products p-0">
                    <div class="p-4">
                        <div class="row gx-5">
                            <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12">
                                <div class="card custom-card shadow-none mb-0 border-0">
                                    <div class="card-body p-0">
                                        <div class="row gy-3">

                                            @if(isset($details->media_type))
                                            <div class="col-xl-12">
                                                @if($details->media_type=='image' || $details->media_type=='banner')
                                                    <img src="{{ Config('constant.BANNER_IMAGE_URL') . $details->media_url }}" height="100" width="100" />
                                                @else
                                                    <video width="100" height="80" controls>
                                                        <source src="{{ Config('constant.BANNER_IMAGE_URL') . $details->media_url }}" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                @endif
                                            </div>
                                            @endif

                                            <div class="col-xl-3">
                                                <label for="media_type" class="form-label">Media Type</label>
                                                <select class="js-example-placeholder-single form-control @error('media_type') is-invalid @enderror"
                                                    name="media_type" id="media_type">
                                                    <option value="">Select</option>
                                                    <option value="image" {{isset($details->media_type) && $details->media_type=='image' ? 'selected' : ''}}>Image</option>
                                                    <option value="video" {{isset($details->media_type) && $details->media_type=='video' ? 'selected' : ''}}>Video</option>
                                                    <option value="banner" {{isset($details->media_type) && $details->media_type=='banner' ? 'selected' : ''}}>Banner</option>
                                                    <option value="others" {{isset($details->media_type) && $details->media_type=='others' ? 'selected' : ''}}>Others</option>
                                                </select>
                                                @if ($errors->has('media_type'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('media_type') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-3">
                                                <label for="media_url" class="form-label"><span class="text-danger">*</span> Media</label>
                                                <input type="text" class="form-control @error ('media_url') is-invalid @enderror" id="media_url" name="media_url" placeholder="Media Url" style="display: none;">
                                                <input type="file" class="form-control media @error('media') is-invalid @enderror" id="media" name="media" placeholder="media" multiple>
                                                <span class="text-hint media">Note: You can upload a max file size of 2 MB</span>
                                                @if ($errors->has('media'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('media') }}
                                                </div>
                                                @endif
                                                @if ($errors->has('media_url'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('media_url') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-3">
                                                <label for="redirection_url" class="form-label">Redirection Url</label>
                                                <input type="text" class="form-control @error('redirection_url') is-invalid @enderror" id="redirection_url" name="redirection_url" value="{{isset($details->redirection_url) ? $details->redirection_url: old('redirection_url')}}" placeholder="Redirection Url">
                                                @if ($errors->has('redirection_url'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('redirection_url') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-3">
                                                <label for="is_active_shop_btn" class="form-label">Shop Button</label>
                                                <select class="js-example-placeholder-single" name="is_active_shop_btn" id="is_active_shop_btn">
                                                    <option value="1"
                                                        {{(!empty($details->is_active_shop_btn) && $details->is_active_shop_btn) ? 'selected' : '' }}>
                                                        Active</option>
                                                    <option value="0"
                                                        {{(!empty($details->is_active_shop_btn) && !$details->is_active_shop_btn) ? 'selected' : '' }}>
                                                        Inactive</option>
                                                </select>
                                                @if ($errors->has('is_active_shop_btn'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('is_active_shop_btn') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-4 position">
                                                <label for="position" class="form-label"><span class="text-danger">*
                                                    </span>Text Position</label>
                                                <select class="js-example-placeholder-single form-control @error('position') is-invalid @enderror" name="position" id="position">
                                                    <option value="">Select</option>
                                                    <option value="left" {{isset($details->position) && $details->position=='left' ? 'selected' : ''}}>Left</option>
                                                    <option value="right" {{isset($details->position) && $details->position=='right' ? 'selected' : ''}}>Right</option>
                                                    <option value="center" {{isset($details->position) && $details->position=='center' ? 'selected' : ''}}>Center</option>
                                                </select>
                                                @if ($errors->has('position'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('position') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-4 position">
                                                <label for="title" class="form-label">Title</label>
                                                <input type="text"
                                                    class="form-control @error('title') is-invalid @enderror" id="title"
                                                    name="title"
                                                    value="{{isset($details->title) ? $details->title: old('title')}}"
                                                    placeholder="Title">
                                                @if ($errors->has('title'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('title') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-4">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="js-example-placeholder-single form-control @error('is_active') is-invalid @enderror"
                                                    name="is_active" id="is_active">
                                                    <option value="1"
                                                        {{(!empty($details->is_active) && $details->is_active) ? 'selected' : '' }}>
                                                        Active</option>
                                                    <option value="0"
                                                        {{(!empty($details->is_active) && !$details->is_active) ? 'selected' : '' }}>
                                                        Inactive</option>
                                                </select>
                                                @if ($errors->has('is_active'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('is_active') }}
                                                </div>
                                                @endif
                                            </div>
                                            
                                            <div class="col-xl-3">
                                                <label for="height" class="form-label">Height</label>
                                                <input type="text" class="form-control @error('height') is-invalid @enderror" id="height" name="height" value="{{isset($details->height) ? $details->height: 800}}" placeholder="Height">
                                                <span class="text-hint">Note: Default height is 800, You can update from here</span>
                                                @if ($errors->has('height'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('height') }}
                                                </div>
                                                @endif
                                            </div>
                                            
                                            <div class="col-xl-3">
                                                <label for="width" class="form-label">Width</label>
                                                <input type="text" class="form-control @error('width') is-invalid @enderror" id="width" name="width" value="{{isset($details->width) ? $details->width: 1890}}" placeholder="Width">
                                                <span class="text-hint">Note: Default width is 1890, You can update from here</span>
                                                @if ($errors->has('width'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('width') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-12 position">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{isset($details->description) ? $details->description: old('description')}}</textarea>
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

{{-- <script src="{{ asset('public/assets/js/fileupload.js') }}"></script> --}}
<script src="{{ asset('public/assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<!-- <script src="{{ asset('public/assets/js/form-validation.js') }}"></script> -->
<script src="{{ asset('public/assets/js/repeater.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#media_type').change(function(){
            if($(this).val() != 'image'){
                $('.position').hide();
            } else {
                $('.position').show();
            } 
            
            
            if($(this).val() == 'others'){
                $('.media').hide();
                $('#media_url').show();
            } else {
                $('.media').show();
                $('#media_url').hide();
            }
        })
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