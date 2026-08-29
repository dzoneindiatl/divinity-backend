@extends('admin.layout.master')

@push('styles')
    <link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
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
                    <li class="breadcrumb-item active" aria-current="page">Create Category</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->

    <div class="row">
        <div class="col-xl-12">
            <form action="{{ route('admin-category.store') }}" method="post" id="categoryForm"
                enctype="multipart/form-data">
                @csrf
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Create Category
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card-body p-0">
                                    <div class="mb-3">
                                        <label for="name" class="form-label"><span class="text-danger">*
                                            </span>Name</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" placeholder="Enter Name"
                                            onkeyup="displaySlug($(this))">
                                        {{-- <h6 class="category-slug mt-2"></h6> --}}
                                        @if ($errors->has('name'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('name') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 mb-3 SlugBox" style="display: none">
                                <label for="category" class="form-label"><span class="text-danger">*
                                    </span>Slug</label>
                                <input type="text" class="form-control category-slug" disabled value="">
                            </div>
                            <div class="col-xl-6 mb-3">
                                <label for="image" class="form-label"><span class="text-danger">
                                    </span>Banner Image</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                    id="image" name="image">
                                @if ($errors->has('image'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('image') }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-xl-6 mb-3">
                                <label for="thumbnail_image" class="form-label"><span class="text-danger">
                                    </span>Thumbnail Image</label>
                                <input type="file" class="form-control @error('thumbnail_image') is-invalid @enderror"
                                    id="thumbnail_image" name="thumbnail_image">
                                @if ($errors->has('thumbnail_image'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('thumbnail_image') }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-xl-6 mb-3">
                                <label for="thumbnail_image" class="form-label"><span class="text-danger">
                                    </span>Thumbnail Image Width</label>
                                <input type="text" class="form-control @error('width') is-invalid @enderror"
                                    id="width" name="width">
                                @if ($errors->has('width'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('width') }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-xl-6 mb-3">
                                <label for="thumbnail_image" class="form-label"><span class="text-danger">
                                    </span>Thumbnail Image Height</label>
                                <input type="text" class="form-control @error('height') is-invalid @enderror"
                                    id="height" name="height">
                                @if ($errors->has('height'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('height') }}
                                    </div>
                                @endif
                            </div>

                            <div class="col-xl-6 mb-3">
                                <label for="video" class="form-label"><span class="text-danger"> </span>Video</label>
                                <input type="file" class="form-control @error('video') is-invalid @enderror"
                                    id="video" name="video">
                                @if (!empty($category->video))
                                    <video height="70" controls>
                                        <source src="{{ isset($category->video) ? $category->video : '' }}" type="video/mp4">
                                    </video>
                                @endif
                                @if ($errors->has('video'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('video') }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-xl-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('title') is-invalid @enderror" name="description" id="description"
                                    cols="30" rows="5">{!! isset($category->description) ? $category->description : old('description') !!}</textarea>
                                @if ($errors->has('description'))
                                    <div class=" invalid-feedback">
                                        {{ $errors->first('description') }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-xl-6 mb-3">
                                <label for="meta_title" class="form-label">Meta Title</label>
                                <input type="text" class="form-control" id="meta_title" name="meta_title"
                                    placeholder="Meta TItle">
                            </div>
                            <div class="col-xl-6 mb-3">
                                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
                                    placeholder="Meta Keywords">
                            </div>
                            <div class="col-xl-6 mb-3">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea class="form-control" name="meta_description" id="meta_description" cols="30" rows="5"></textarea>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Variants
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 select2-error">
                                <label for="category_id" class="form-label"><span class="text-danger">
                                    </span>Variants</label>
                                <select class="js-example-placeholder-single js-states form-control" multiple="multiple"
                                    name="variantsData[]" id="variantsSelect">
                                    <!-- <option value="" selected>None</option> -->
                                    @forelse ($variants as $variant)
                                        <option value="{{ $variant->id }}">{{ $variant->name }}</option>
                                    @empty
                                        <option value="" selected>No Data found</option>
                                    @endforelse
                                </select>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Specifications
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 select2-error">
                                <label for="category_id" class="form-label"><span class="text-danger">
                                    </span>Specifications</label>
                                <select class="js-example-placeholder-single js-states form-control" multiple="multiple"
                                    name="specificationsData[]" id="specificationsSelect">
                                    <!-- <option value="" selected>None</option> -->
                                    @forelse ($specifications as $specification)
                                        <option value="{{ $specification->id }}">{{ $specification->name }}</option>
                                    @empty
                                        <option value="" selected>No Data found</option>
                                    @endforelse
                                </select>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Taxes
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 select2-error">
                                <label for="category_id" class="form-label"><span class="text-danger">
                                    </span>Taxes</label>
                                <select class="js-example-placeholder-single js-states form-control" multiple="multiple"
                                    name="taxesData[]" id="taxesSelect">
                                    @forelse ($taxes as $tax)
                                        <option value="{{ $tax->id }}">{{ $tax->name }}</option>
                                    @empty
                                        <option value="" selected>No Data found</option>
                                    @endforelse
                                </select>

                            </div>

                            <div id="taxCountFields" style="display: none;">
                                <!-- Text fields for tax counts will be appended here dynamically -->
                            </div>
                        </div>
                    </div>
                </div>




                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Category SEO
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 mb-3">
                                <label for="seo_data" class="form-label">SEO Data</label>
                                <textarea class="form-control @error('title') is-invalid @enderror" name="seo_data" id="seo_data" cols="30"
                                    rows="5">{!! isset($category->seo_data) ? $category->seo_data : old('seo_data') !!}</textarea>
                                @if ($errors->has('seo_data'))
                                    <div class=" invalid-feedback">
                                        {{ $errors->first('seo_data') }}
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
    </div>
@endsection

@push('scripts')
    <!-- Select2 Cdn -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugin/tagify/tagify.min.js') }}"></script>
    <!-- Internal Select-2.js -->
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>
    <script src="{{ asset('assets/js/form-validation.js') }}"></script>
    <script src="{{ asset('assets/js/custom/category.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Add an event listener to the taxesSelect dropdown
            $('#taxesSelect').on('change', function() {
                // Clear existing count text fields
                $('#taxCountFields').empty();

                // Get selected tax values
                var selectedTaxes = $(this).val();

                // Check if any taxes are selected
                if (selectedTaxes && selectedTaxes.length > 0) {
                    // Display the tax count fields div
                    $('#taxCountFields').show();

                    // Create a text field for each selected tax
                    selectedTaxes.forEach(function(taxId) {
                        var taxFieldName = 'tax_counts[' + taxId + ']';
                        var taxName = $('#taxesSelect option[value="' + taxId + '"]')
                    .text(); // Get tax name
                        var label = taxName + ' Value'; // Create label based on tax name

                        // Create the label and input field
                        var inputField = '<div class="mb-3"><label for="' + taxFieldName +
                            '" class="form-label">' +
                            label + '</label><input type="text" class="form-control" id="' +
                            taxFieldName + '" name="' + taxFieldName +
                            '" placeholder="Enter Value"></div>';
                        $('#taxCountFields').append(inputField);
                    });
                } else {
                    // Hide the tax count fields div if no taxes are selected
                    $('#taxCountFields').hide();
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
