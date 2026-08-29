@extends('admin.layout.master')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.css') }}">
    <link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />

    <script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
    <style>
        .home-checkbox {
            display: flex;
            gap: 10px;
            align-items: center;
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create Banner</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->
    <div class="row">
        <div class="col-xl-12">
            <form action="{{ route('admin-Banner.save') }}" method="post" enctype="multipart/form-data"
                id="createBannerForm">
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
                                                    <label for="type" class="form-label"><span class="text-danger">*
                                                        </span>Banner Type</label>
                                                    <select class="form-control @error('type') is-invalid @enderror"
                                                        name="type" id="type">
                                                        <option value="" selected>Select Type</option>
                                                        <option value="full_image"
                                                            {{ !empty($userDetails->type) && $userDetails->type == 'full_image' ? 'selected' : '' }}>
                                                            Full Image</option>
                                                        <option value="left_image_right_content"
                                                            {{ !empty($userDetails->type) && $userDetails->type == 'left_image_right_content' ? 'selected' : '' }}>
                                                            Left Image Right Content</option>
                                                        <option value="right_image_left_content"
                                                            {{ !empty($userDetails->type) && $userDetails->type == 'right_image_left_content' ? 'selected' : '' }}>
                                                            Right Image Left Content</option>
                                                        <option value="video"
                                                            {{ !empty($userDetails->type) && $userDetails->type == 'video' ? 'selected' : '' }}>
                                                            Video</option>
                                                        <option value="youtube"
                                                            {{ !empty($userDetails->type) && $userDetails->type == 'youtube' ? 'selected' : '' }}>
                                                            youtube</option>
                                                        <option value="vimeo"
                                                            {{ !empty($userDetails->type) && $userDetails->type == 'vimeo' ? 'selected' : '' }}>
                                                            Vimeo</option>
                                                    </select>
                                                    @if ($errors->has('type'))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('type') }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-xl-6 select-home-checkbox">
                                                    <div class="home-checkbox">
                                                        <label for="Show on ">Show on Home Page Slider</label>
                                                        <input type="checkbox" value="1"name="show_on_home_slider">
                                                    </div>
                                                    <div class="home-checkbox">
                                                        <label for="Show on ">Show on Home Page Banner</label>
                                                        <input type="checkbox"value="1" name="show_on_home_banner">
                                                    </div>
                                                    <div class="home-checkbox">
                                                        <label for="Show on ">Show on Home Page Offer Banner</label>
                                                        <input type="checkbox" value="1 "
                                                            name="show_on_home_offer_banner">
                                                    </div>
                                                    <div class="home-checkbox">
                                                        <label for="Show on ">Shop Button Show</label>
                                                        <input type="checkbox" value="1 " name="button_active">
                                                    </div>
                                                    <div class="home-checkbox">
                                                        <label for="Show on ">Video Auto Play</label>
                                                        <input type="checkbox" value="1 " name="video_auto_play">
                                                    </div>


                                                </div>

                                            </div>
                                            <div class="row gy-3 mt-2" id="imageField">

                                                <div class="col-xl-6">
                                                    <label for="image" class="form-label"><span class="text-danger">
                                                        </span>Banner Image</label>
                                                    <input type="file"
                                                        class="form-control @error('image') is-invalid @enderror"
                                                        id="image" name="image">
                                                    @if (!empty($userDetails->image))
                                                        <img height="50" width="50"
                                                            src="{{ isset($userDetails->image) ? $userDetails->image : '' }}" />
                                                    @endif
                                                    @if ($errors->has('image'))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('image') }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-xl-6">
                                                    <label for="url" class="form-label"><span class="text-danger">
                                                        </span>URL</label>
                                                    <input type="text"
                                                        class="form-control @error('url') is-invalid @enderror"
                                                        id="urlfdf" name="urlfdf"
                                                        value="{{ isset($userDetails->url) ? $userDetails->url : old('url') }}"
                                                        placeholder="Video URL">
                                                    @if ($errors->has('url'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('url') }}
                                                        </div>
                                                    @endif
                                                </div>

                                            </div>
                                            <div class="row gy-3 mt-2" id="videoFields">
                                                <div class="col-xl-6">
                                                    <label for="image" class="form-label"><span class="text-danger">
                                                        </span>Video Image</label>
                                                    <input type="file"
                                                        class="form-control @error('th_image') is-invalid @enderror"
                                                        id="th_image" name="th_image">
                                                    @if (!empty($userDetails->th_image))
                                                        <img height="50" width="50"
                                                            src="{{ isset($userDetails->th_image) ? $userDetails->th_image : '' }}" />
                                                    @endif
                                                    @if ($errors->has('th_image'))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('th_image') }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-xl-6" id="innerVideoSection">
                                                    <label for="video" class="form-label"><span class="text-danger"> *
                                                        </span>Video</label>
                                                    <input type="file"
                                                        class="form-control @error('video') is-invalid @enderror"
                                                        id="video" name="video">
                                                    @if (!empty($userDetails->video))
                                                        <img height="50" width="50"
                                                            src="{{ isset($userDetails->video) ? $userDetails->video : '' }}" />
                                                    @endif
                                                    @if ($errors->has('video'))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('video') }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-xl-6">
                                                    <label for="url" class="form-label"><span class="text-danger">
                                                        </span>Video URL</label>
                                                    <input type="text"
                                                        class="form-control @error('url') is-invalid @enderror"
                                                        id="url" name="url"
                                                        value="{{ isset($userDetails->url) ? $userDetails->url : old('url') }}"
                                                        placeholder="Video URL">
                                                    @if ($errors->has('url'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('url') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row gy-3 mt-2" id="heightWidth">
                                                <div class="col-xl-6">
                                                    <label for="height" class="form-label"><span class="text-danger">
                                                        </span>Height</label>
                                                    <input type="text"
                                                        class="form-control @error('height') is-invalid @enderror"
                                                        id="height" name="height"
                                                        value="{{ isset($userDetails->height) ? $userDetails->height : old('height') }}"
                                                        placeholder="Height">
                                                    @if ($errors->has('height'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('height') }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-xl-6">
                                                    <label for="width" class="form-label"><span class="text-danger">
                                                        </span>Width</label>
                                                    <input type="text"
                                                        class="form-control @error('width') is-invalid @enderror"
                                                        id="width" name="width"
                                                        value="{{ isset($userDetails->width) ? $userDetails->width : old('width') }}"
                                                        placeholder="Width">
                                                    @if ($errors->has('width'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('width') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row gy-3 mt-2" id="descriptionField">
                                                <div class="col-xl-6">
                                                    <label for="title" class="form-label">Title</label>
                                                    <input type="text"
                                                        class="form-control @error('title') is-invalid @enderror"
                                                        name="title" id="title" value="{!! isset($userDetails->title) ? $userDetails->title : old('title') !!}">

                                                    @if ($errors->has('title'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('title') }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-xl-12">
                                                    <label for="description" class="form-label">Description</label>
                                                    <textarea class="form-control @error('title') is-invalid @enderror" name="description" id="description"
                                                        cols="30" rows="5">{!! isset($userDetails->description) ? $userDetails->description : old('description') !!}</textarea>
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
        $(document).ready(function() {
            // Initially hide all fields
            $('#imageField').hide();
            $('#heightWidth').hide();
            $('#descriptionField').hide();
            $('#videoFields').hide();
            $('#innerVideoSection').hide();

            // Attach an event listener to the "Banner Type" dropdown
            $('#type').change(function() {
                var selectedOption = $(this).val();
                if (selectedOption === 'full_image' || selectedOption === 'left_image_right_content' ||
                    selectedOption ===
                    'right_image_left_content') {
                    $('#imageField').show();
                    $('#heightWidth').show();
                    $('#descriptionField').show();
                    $('#innerVideoSection').show();
                    $('#videoFields').hide();
                } else if (selectedOption === 'video') {
                    $('#heightWidth').hide();
                    $('#descriptionField').hide();
                    $('#imageField').hide();
                    $('#videoFields').show();
                    $('#innerVideoSection').show();
                    // Add code by mohit
                } else if (selectedOption === 'youtube' || selectedOption === 'vimeo') {
                    $('#heightWidth').hide();
                    $('#descriptionField').hide();
                    $('#imageField').hide();
                    $('#videoFields').show();
                    $('#innerVideoSection').hide();
                    // End code by mohit
                } else if (selectedOption === 'aboutus') {
                    $('#imageField').show();
                    $('#heightWidth').show();
                    $('#videoFields').show();
                    $('#innerVideoSection').show();
                } else {
                    // Hide all fields if none of the above options are selected
                    $('#imageField').hide();
                    $('#heightWidth').hide();
                    $('#descriptionField').hide();
                    $('#videoFields').hide();
                    $('#innerVideoSection').hide();
                }
            });
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
