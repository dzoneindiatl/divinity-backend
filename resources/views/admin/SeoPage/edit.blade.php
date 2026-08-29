@extends('admin.layout.master')

@push('styles')
<link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
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
                <li class="breadcrumb-item active" aria-current="page">Edit Seo Page</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->

<div class="row">
    <div class="col-xl-12">
        <form action="{{route('admin-'.$model.'.update',base64_encode($doc->id))}}" method="post" id="categoryForm"
            enctype="multipart/form-data">
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
                                                <label for="page_id" class="form-label"><span class="text-danger">* </span>Page ID</label>
                                                <input type="text" class="form-control @error('page_id') is-invalid @enderror" id="page_id" name="page_id" value="{{isset($doc->page_id) ? $doc->page_id: old('page_id')}}" placeholder="Page ID">
                                                @if ($errors->has('page_id'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('page_id') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-xl-6">
                                                <label for="page_name" class="form-label"><span class="text-danger">* </span>Page Name</label>
                                                <input type="text" class="form-control @error('page_name') is-invalid @enderror" id="page_name" name="page_name" value="{{isset($doc->page_name) ? $doc->page_name: old('page_name')}}" placeholder="Page Name">
                                                @if ($errors->has('page_name'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('page_name') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="title" class="form-label"><span class="text-danger">* </span>Title</label>
                                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{isset($doc->title) ? $doc->title: old('title')}}" placeholder="Title">
                                                @if ($errors->has('title'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('title') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-12 mt-3">
                                                <label for="meta_description" class="form-label">Meta Description</label>
                                                <textarea class="form-control @error('meta_description') is-invalid @enderror" name="meta_description" id="meta_description" cols="30" rows="5">{!! isset($doc->meta_description) ? $doc->meta_description: old('meta_description') !!}</textarea>
                                                @if ($errors->has('meta_description'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('meta_description') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-xl-6 mt-3">
                                                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                                <textarea class="form-control @error('meta_keywords') is-invalid @enderror" name="meta_keywords" id="meta_keywords" cols="30" rows="5">{!! isset($doc->meta_keywords) ? $doc->meta_keywords: old('meta_keywords') !!}</textarea>
                                                @if ($errors->has('meta_keywords'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('meta_keywords') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-xl-6 mt-3">
                                                <label for="twitter_card" class="form-label">Twitter Card</label>
                                                <textarea class="form-control @error('twitter_card') is-invalid @enderror" name="twitter_card" id="twitter_card" cols="30" rows="5">{!! isset($doc->twitter_card) ? $doc->twitter_card: old('twitter_card') !!}</textarea>
                                                @if ($errors->has('twitter_card'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('twitter_card') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-xl-6 mt-3">
                                                <label for="twitter_site" class="form-label">Twitter Site</label>
                                                <textarea class="form-control @error('twitter_site') is-invalid @enderror" name="twitter_site" id="twitter_site" cols="30" rows="5">{!! isset($doc->twitter_site) ? $doc->twitter_site: old('twitter_site') !!}</textarea>
                                                @if ($errors->has('twitter_site'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('twitter_site') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-xl-6 mt-3">
                                                <label for="twitter_description" class="form-label">Twitter Description</label>
                                                <textarea class="form-control @error('twitter_description') is-invalid @enderror" name="twitter_description" id="twitter_description" cols="30" rows="5">{!! isset($doc->twitter_description) ? $doc->twitter_description: old('twitter_description') !!}</textarea>
                                                @if ($errors->has('twitter_description'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('twitter_description') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-xl-6 mt-3">
                                                <label for="og_url" class="form-label">Og Url</label>
                                                <textarea class="form-control @error('og_url') is-invalid @enderror" name="og_url" id="og_url" cols="30" rows="5">{!! isset($doc->og_url) ? $doc->og_url: old('og_url') !!}</textarea>
                                                @if ($errors->has('og_url'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('og_url') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-xl-6 mt-3">
                                                <label for="og_type" class="form-label">Og Type</label>
                                                <textarea class="form-control @error('og_type') is-invalid @enderror" name="og_type" id="og_type" cols="30" rows="5">{!! isset($doc->og_type) ? $doc->og_type: old('og_type') !!}</textarea>
                                                @if ($errors->has('og_type'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('og_type') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-xl-6 mt-3">
                                                <label for="og_title" class="form-label">Og Title</label>
                                                <textarea class="form-control @error('og_title') is-invalid @enderror" name="og_title" id="og_title" cols="30" rows="5">{!! isset($doc->og_title) ? $doc->og_title: old('og_title') !!}</textarea>
                                                @if ($errors->has('og_title'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('og_title') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-xl-6 mt-3">
                                                <label for="og_description" class="form-label">Og Description</label>
                                                <textarea class="form-control @error('og_description') is-invalid @enderror" name="og_description" id="og_description" cols="30" rows="5">{!! isset($doc->og_description) ? $doc->og_description: old('og_description') !!}</textarea>
                                                @if ($errors->has('og_description'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('og_description') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-xl-6">
                                                <label for="chronological_tag" class="form-label"><span class="text-danger"> </span>Chronological Tag</label>
                                                <input type="text" class="form-control @error('chronological_tag') is-invalid @enderror" id="chronological_tag" name="chronological_tag" value="{{isset($doc->chronological_tag) ? $doc->chronological_tag: old('chronological_tag')}}" placeholder="Chronological Tag">
                                                @if ($errors->has('chronological_tag'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('chronological_tag') }}
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

{{-- <script src="{{ asset('assets/js/fileupload.js') }}"></script> --}}
<script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<!-- <script src="{{ asset('assets/js/form-validation.js') }}"></script> -->

<script>
    CKEDITOR.replace(<?php echo 'meta_description'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;
</script>

<script>
    CKEDITOR.replace(<?php echo 'meta_keywords'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;
</script>
<script>
    CKEDITOR.replace(<?php echo 'twitter_card'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;
</script>
<script>
    CKEDITOR.replace(<?php echo 'twitter_site'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;
</script>
<script>
    CKEDITOR.replace(<?php echo 'twitter_description'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;
</script>
<script>
    CKEDITOR.replace(<?php echo 'og_url'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;
</script>
<script>
    CKEDITOR.replace(<?php echo 'og_type'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;
</script>
<script>
    CKEDITOR.replace(<?php echo 'og_title'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;
</script>
<script>
    CKEDITOR.replace(<?php echo 'og_description'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;
</script>

@endpush