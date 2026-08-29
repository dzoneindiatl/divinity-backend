@extends('admin.layout.master')

@push('styles')
    @dd('1')
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
                    <li class="breadcrumb-item active" aria-current="page">Create Product</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->
    <div class="row">
        <div class="col-xl-12">
            <form action="{{ route('admin-product-update', ['token' => $product->id]) }}" method="post"
                enctype="multipart/form-data" id="createProductForm">
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
                                <div class="col-xxl-6 col-xl-12 col-lg-12 col-md-6">
                                    <div class="card custom-card shadow-none mb-0 border-0">
                                        <div class="card-body p-0">
                                            <div class="row gy-3">
                                                <div class="col-xl-12">
                                                    <label for="product-name-add" class="form-label"><span
                                                            class="text-danger">* </span>Product Name</label>
                                                    <input type="text" class="form-control" id="name" name="name"
                                                        placeholder="Enter Name" onkeyup="editDisplaySlug($(this))"
                                                        value="{{ $product->name }}">
                                                    <h6 class="edit-product-slug mt-2"></h6>
                                                </div>

                                                <div class="col-xl-12">
                                                    {{-- <div id="sub_categegory_select"></div> --}}
                                                    <label for="sub_category_id" class="form-label">Sab Category</label>
                                                    <select class="js-example-placeholder-single js-states form-control"
                                                        name="sub_category_id" id="sub_category_id"
                                                        data-action="{{ route('admin-product-child-category-list') }}">
                                                        <option value="" selected>None</option>
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-6 col-xl-12 col-lg-12 col-md-6">
                                    <div class="card custom-card shadow-none mb-0 border-0">
                                        <div class="card-body p-0">
                                            <div class="row gy-4">
                                                <div class="col-xl-12 select2-error">
                                                    <label for="category_id" class="form-label"><span class="text-danger">*
                                                        </span>Category</label>
                                                    <select class="js-example-placeholder-single js-states form-control"
                                                        name="category_id" id="category_id"
                                                        data-action="{{ route('admin-product-sub-category-list') }}">
                                                        <option value="" selected>None</option>
                                                        @forelse ($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}
                                                            </option>
                                                        @empty
                                                            <option value="" selected>No Data found</option>
                                                        @endforelse
                                                    </select>
                                                </div>
                                                <div class="col-xl-12">
                                                    <label for="product-size-add" class="form-label">Child Category</label>
                                                    <select class="js-example-placeholder-single js-states form-control"
                                                        name="child_category_id" id="child_category_id">
                                                        <option value="" selected>None</option>
                                                    </select>
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
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Variants
                        </div>
                    </div>
                    <div class="card-body add-products p-0">
                        <div class="p-4">
                            <div class="row gx-5">
                                <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-6">
                                    <div class="card custom-card shadow-none mb-0 border-0">
                                        <div class="card-body p-0">
                                            <div class="row gy-3">
                                                @foreach ($productOptions as $productOption)
                                                    <div class="col-xl-6 select2-error">
                                                        <label for="product_option_value_id" class="form-label"><span
                                                                class="text-danger">*
                                                            </span>{{ $productOption->name }}</label>
                                                        <select
                                                            class="{{ $productOption->id == 1 ? '' : 'js-example-placeholder-single' }} js-states form-control"
                                                            name="product_option_value_id[]" id="product_option_value_id"
                                                            {{ $productOption->id == 1 ? '' : 'multiple' }}>
                                                            <option value="" selected>None</option>
                                                            @forelse ($productOption->productOptionValues as $productOptionValuesitem)
                                                                <option value="{{ $productOptionValuesitem->id }}">
                                                                    {{ $productOptionValuesitem->name }}</option>
                                                            @empty
                                                                <option value="" selected>No Data found</option>
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                @endforeach
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
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Details
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-xl-6">
                                <label for="description" class="form-label"><span class="text-danger">*
                                    </span>Short Description</label>
                                <textarea class="form-control" name="short_description" id="short_description" cols="30" rows="5"></textarea>
                            </div>
                            <div class="col-xl-6">
                                <label for="description" class="form-label"><span class="text-danger">*
                                    </span>Description</label>
                                <textarea class="form-control" name="description" id="description" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Medias
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            <div class="col-xl-6">
                                <label for="front_image" class="form-label"><span class="text-danger">*
                                    </span>Front
                                    Image</label>
                                <input type="file" class="form-control" id="front_image" name="front_image">
                            </div>
                            <div class="col-xl-6">
                                <label for="back_image" class="form-label"><span class="text-danger">*
                                    </span>Back Image</label>
                                <input type="file" class="form-control" id="back_image" name="back_image">
                            </div>
                        </div>
                        <div class="row gy-4">
                            <div class="col-xl-6">
                                <label for="control-label" class="form-label"><span class="text-danger">* </span>Product
                                    Images</label>
                                <input type="file" name="product_images[]" id="product_images" multiple
                                    class="form-control">
                            </div>
                            <div class="col-xl-6">
                                <label for="control-label" class="form-label"><span class="text-danger">* </span>Product
                                    Videos</label>
                                <input type="file" name="product_videos[]" id="product_videos" multiple
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Advance SEO
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="col-xl-12">
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" class="form-control" id="meta_title" name="meta_title"
                                placeholder="Meta TItle">
                        </div>
                        <div class="col-xl-12">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea class="form-control" name="meta_description" id="meta_description" cols="30" rows="5"></textarea>
                        </div>
                        <div class="col-xl-12">
                            <label for="meta_keywords" class="form-label">Meta Keywords</label>
                            <input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
                                placeholder="Meta Keywords">
                        </div>
                        <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Submit</button>
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
    <script src="{{ asset('assets/js/form-validation.js') }}"></script>
@endpush
