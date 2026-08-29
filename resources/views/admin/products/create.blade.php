@extends('admin.layout.master')
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.css') }}">
    <link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
@endpush
<style>
    .portfolioPicContainer {
        height: 100px;
        width: 100px;
        background-color: #404040;
        position: relative;
        display: inline-block;
    }

    .bi-x-circle:hover {
        cursor: pointer;
        opacity: 0.7;
        /* Decrease opacity on hover for a visual effect */
    }

    .bi-x-circle {
        position: absolute;
        top: 5px;
        right: 5px;
        color: white;
        /* Change color to white */
        font-size: 20px;
        /* Adjust the font size as desired */
        z-index: 1;
        /* Ensure it appears above the image */
    }

    .portfolioPicContainerImg {
        height: 100%;
        width: 100%;
        object-fit: contain;
        object-position: center;
    }

    .closePortImgBtn {
        padding: 0 !important;
        height: 15px;
        width: 15px;
        font-size: 15px
    }

    .portfolioImgAciveInput {
        width: 2em !important;
        height: 1.2em !important
    }

    .portfolioImgAciveLabel {
        font-size: 12px !important
    }

    .image-checkbox {
        display: inline-block;
        margin: 10px;
    }

    .image-checkbox input[type="checkbox"] {
        display: none;
        /* Hide the default checkbox */
    }

    .image-checkbox input[type="checkbox"]+img {
        cursor: pointer;
        /* Change cursor to pointer on hover */
    }

    .image-bordered {
        border: 2px solid #6610f2;
        /* Add a black border when the checkbox is checked */
    }
</style>

@section('content')
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ !empty($type) && $type == 'create' ? 'Create' : (!empty($type) && $type == 'edit' ? 'Edit' : 'View') }}
                        Product</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        {{ !empty($type) && $type == 'create' ? 'Create' : (!empty($type) && $type == 'edit' ? 'Edit' : 'View') }}
                        Product </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <ul class="nav nav-tabs flex-column vertical-tabs-2" role="tablist">
                                <li class="nav-item" role="presentation"> <a class="nav-link active" data-bs-toggle="tab"
                                        role="tab" aria-current="page" href="#basicInformationTab" aria-selected="false"
                                        tabindex="-1">
                                        <p class="mb-1"><i class="ri-home-4-line"></i></p>
                                        <p class="mb-0 text-break">Basic Information</p>
                                    </a> </li>
                                <li class="nav-item" role="presentation"> <a class="nav-link" href="#detailsTab">
                                        <p class="mb-1"><i class="ri-phone-line"></i></p>
                                        <p class="mb-0 text-break">Details</p>
                                    </a> </li>
                                <li class="nav-item" role="presentation"> <a class="nav-link " href="#pricesTab">
                                        <p class="mb-1"><i class="ri-customer-service-line"></i></p>
                                        <p class="mb-0 text-break">Prices</p>
                                    </a> </li>
                                <li class="nav-item" role="presentation"> <a class="nav-link " href="#specificationsTab">
                                        <p class="mb-1"><i class="ri-customer-service-line"></i></p>
                                        <p class="mb-0 text-break">Specifications</p>
                                    </a> </li>
                                <li class="nav-item" role="presentation"> <a class="nav-link "
                                        href="#shippingSpecificationsTab">
                                        <p class="mb-1"><i class="ri-customer-service-line"></i></p>
                                        <p class="mb-0 text-break">Shipping Specifications</p>
                                    </a> </li>
                                <li class="nav-item" role="presentation"> <a class="nav-link " href="#mediasTab">
                                        <p class="mb-1"><i class="ri-customer-service-line"></i></p>
                                        <p class="mb-0 text-break">Medias</p>
                                    </a> </li>
                                <li class="nav-item" role="presentation"> <a class="nav-link " href="#variantsTab">
                                        <p class="mb-1"><i class="ri-customer-service-line"></i></p>
                                        <p class="mb-0 text-break">Variants</p>
                                    </a> </li>
                                <li class="nav-item" role="presentation"> <a class="nav-link " href="#advanceSeoTab">
                                        <p class="mb-1"><i class="ri-customer-service-line"></i></p>
                                        <p class="mb-0 text-break">Advance SEO </p>
                                    </a> </li>
                            </ul>
                        </div>
                        <div class="col-md-10">
                            <div class="tab-content">
                                <div class="tab-pane text-muted active show" data-next-tab="detailsTab" data-prev-tab=""
                                    id="basicInformationTab" role="tabpanel">
                                    <form id="basicInformationTabForm">
                                        <div class="row gx-5">
                                            <div class="col-xxl-6 col-xl-12 col-lg-12 col-md-6">
                                                <div class="card custom-card shadow-none mb-0 border-0">
                                                    <div class="card-body p-0">
                                                        <div class="row gy-3">
                                                            <div class="col-xl-12">
                                                                <label for="product-name-add" class="form-label"><span
                                                                        class="text-danger">* </span>Product Name</label>
                                                                <input type="text" class="form-control" id="name"
                                                                    name="name" placeholder="Enter Name"
                                                                    onkeyup="displaySlug($(this))"
                                                                    value="{{ !empty($productDetails->name) ? $productDetails->name : '' }}">
                                                                <div class="invalid-feedback fw-bold"></div>
                                                                {{-- <h6 class="product-slug mt-2"></h6> --}}
                                                            </div>
                                                            <div class="col-xl-12 SlugBox" style="display: none">
                                                                <label for="category" class="form-label"><span
                                                                        class="text-danger">*
                                                                    </span>Slug</label>
                                                                <input type="text" class="form-control category-slug"
                                                                    disabled value="">
                                                            </div>
                                                            <div class="col-xl-12 select2-error">
                                                                <label for="category_id" class="form-label"><span
                                                                        class="text-danger">*
                                                                    </span>Category</label>

                                                                <select
                                                                    class="js-example-placeholder-single js-states form-control"
                                                                    name="category_id" id="category_id"
                                                                    data-action="{{ route('admin-product-sub-category-list') }}">
                                                                    <option value="" selected>None</option>
                                                                    @forelse ($categories as $category)
                                                                        <option value="{{ $category->id }}"
                                                                            {{ !empty($productDetails->category_id) && $productDetails->category_id == $category->id ? 'selected' : '' }}>
                                                                            {{ $category->name }}
                                                                        </option>
                                                                    @empty
                                                                        <option value="" selected>No Data found
                                                                        </option>
                                                                    @endforelse
                                                                </select>
                                                                <div class="invalid-feedback fw-bold"></div>
                                                            </div>




                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xxl-6 col-xl-12 col-lg-12 col-md-6">
                                                <div class="card custom-card shadow-none mb-0 border-0">
                                                    <div class="card-body p-0">
                                                        <div class="row gy-4">

                                                            <div class="col-xl-12" style="display:none"
                                                                id="subcategory-filter">
                                                                {{-- <div id="sub_categegory_select"></div> --}}
                                                                <label for="sub_category_id" class="form-label">Sub
                                                                    Category</label>
                                                                <select
                                                                    class="js-example-placeholder-single js-states form-control"
                                                                    name="sub_category_id" id="sub_category_id"
                                                                    data-action="{{ route('admin-product-child-category-list') }}">
                                                                    <option value="" selected>None</option>
                                                                </select>
                                                                <div class="invalid-feedback fw-bold"></div>
                                                            </div>

                                                            <div class="col-xl-12" style="display:none"
                                                                id="child-category-filter">
                                                                <label for="product-size-add" class="form-label">Child
                                                                    Category</label>
                                                                <select
                                                                    class="js-example-placeholder-single js-states form-control"
                                                                    name="child_category_id" id="child_category_id">
                                                                    <option value="" selected>None</option>
                                                                </select>
                                                                <div class="invalid-feedback fw-bold"></div>
                                                            </div>

                                                            <div class="col-xl-12">
                                                                <label for="bar_code" class="form-label"><span
                                                                        class="text-danger"> </span>Bar Code</label>
                                                                <input type="text" class="form-control" id="bar_code"
                                                                    name="bar_code" placeholder="Enter Bar Code"
                                                                    value="{{ !empty($productDetails->bar_code) ? $productDetails->bar_code : '' }}">
                                                                <div class="invalid-feedback fw-bold"></div>

                                                            </div>

                                                            <div class="col-xl-12 select2-error">
                                                                <label for="brand_id" class="form-label"><span
                                                                        class="text-danger">
                                                                    </span>Brand</label>
                                                                <select
                                                                    class="js-example-placeholder-single js-states form-control"
                                                                    name="brand_id" id="brand_id">
                                                                    <option value="" selected>None</option>
                                                                    @forelse ($brands as $brand)
                                                                        <option value="{{ $brand->id }}"
                                                                            {{ !empty($productDetails->brand_id) && $productDetails->brand_id == $brand->id ? 'selected' : '' }}>
                                                                            {{ $brand->name }}
                                                                        </option>
                                                                    @empty
                                                                        <option value="" selected>No Data found
                                                                        </option>
                                                                    @endforelse
                                                                </select>
                                                                <div class="invalid-feedback fw-bold"></div>
                                                            </div>


                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div
                                            class="py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">

                                            <button type="button" class="btn btn-primary" id="actionBtn"
                                                data-action="next" data-current-tab="basicInformationTab">Next</button>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane text-muted" id="detailsTab" data-next-tab="pricesTab"
                                    data-prev-tab="basicInformationTab" role="tabpanel">
                                    <form id="detailsTabForm" class="row gx-5 gy-5">

                                        @if (!empty($productDescriptionsData))
                                            <?php $iterationCount = 0; ?>
                                            <div id="kt_repeater_1" class="ml-7">
                                                <div class="form-group row" id="kt_repeater_1">
                                                    <div data-repeater-list="productDetailsArr" class="col-lg-12">
                                                        @foreach ($productDescriptionsData as $dataKey => $dataVal)
                                                            @if (!empty($dataVal['name']) && !empty($dataVal['value']))
                                                                <div data-repeater-item
                                                                    class="form-group row align-items-center mb-0 mt-3">
                                                                    <div class="col-md-12 d-flex justify-content-end">
                                                                        @if ($iterationCount == 0)
                                                                            <a href="javascript:;" data-repeater-create=""
                                                                                class="btn btn-sm font-weight-bolder btn btn-primary-light btn-border-down">
                                                                                <i class="la la-plus"></i>Add More Detail
                                                                            </a>
                                                                        @else
                                                                            <a href="javascript:;" data-repeater-create=""
                                                                                class="btn btn-sm font-weight-bolder btn btn-primary-light btn-border-down"
                                                                                style="display:none;">
                                                                                <i class="la la-plus"></i>Add More Detail
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-md-12 mb-3">
                                                                        <div class="form-group">
                                                                            <label for="name"
                                                                                class="form-label">Name</label><span
                                                                                class="text-danger">
                                                                            </span>
                                                                            <input type="text" name="name"
                                                                                class="form-control form-control-solid form-control-lg  @error('name') is-invalid @enderror"
                                                                                value="{{ !empty($dataVal['name']) ? $dataVal['name'] : '' }}">

                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12 mb-3">
                                                                        <div class="form-group">
                                                                            <label for="value"
                                                                                class="form-label">Value</label><span
                                                                                class="text-danger">
                                                                            </span>
                                                                            <textarea class="form-control" name="value" cols="20" rows="5">{!! !empty($dataVal['value']) ? $dataVal['value'] : '' !!}</textarea>


                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-12 d-flex justify-content-end">
                                                                        @if ($iterationCount == 0)
                                                                            <a href="javascript:;" data-repeater-delete=""
                                                                                class="btn btn-sm font-weight-bolder btn btn-danger-light btn-border-down"
                                                                                style="display:none;">
                                                                                <i class="la la-trash-o"></i>Delete Detail
                                                                            </a>
                                                                        @else
                                                                            <a href="javascript:;" data-repeater-delete=""
                                                                                class="btn btn-sm font-weight-bolder btn btn-danger-light btn-border-down">
                                                                                <i class="la la-trash-o"></i>Delete Detail
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endif


                                                            <?php $iterationCount++; ?>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div id="kt_repeater_1" class="ml-7">
                                                <div class="form-group row" id="kt_repeater_1">

                                                    <div data-repeater-list="productDetailsArr" class="col-lg-12">
                                                        <div data-repeater-item
                                                            class="form-group row align-items-center mb-0">
                                                            <div class="col-md-12 d-flex justify-content-end">
                                                                <a href="javascript:;" data-repeater-create=""
                                                                    class="btn btn-sm font-weight-bolder btn btn-primary-light btn-border-down">
                                                                    <i class="la la-plus"></i>Add More Detail
                                                                </a>
                                                            </div>
                                                            <div class="col-md-12 mb-3 mt-3">
                                                                <div class="form-group">
                                                                    <label for="name"
                                                                        class="form-label">Name</label><span
                                                                        class="text-danger">
                                                                    </span>
                                                                    <input type="text" name="name"
                                                                        class="form-control form-control-solid form-control-lg variant-value @error('name') is-invalid @enderror"
                                                                        value="">

                                                                </div>
                                                            </div>

                                                            <div class="col-md-12 mb-3">
                                                                <div class="form-group">
                                                                    <label for="value"
                                                                        class="form-label">Value</label><span
                                                                        class="text-danger">
                                                                    </span>
                                                                    <textarea class="form-control" name="value" cols="20" rows="5"></textarea>

                                                                </div>
                                                            </div>




                                                            <div class="col-md-12 d-flex justify-content-end">
                                                                <a href="javascript:;" data-repeater-delete=""
                                                                    class="btn btn-sm font-weight-bolder btn btn-danger-light btn-border-down"
                                                                    style="display:none;">
                                                                    <i class="la la-trash-o"></i>Delete Detail
                                                                </a>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                        @endif

                                        <div
                                            class="py-3 border-top border-block-start-dashed d-sm-flex justify-content-between">
                                            <button type="button" class="btn btn-dark" id="actionBtn"
                                                data-action="back" data-current-tab="detailsTab"
                                                data-target-tab="basicInformationTab">Back</button>
                                            <button type="submit" class="btn btn-primary" id="actionBtn"
                                                data-action="next" data-current-tab="detailsTab">Next</button>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane text-muted" id="pricesTab" data-next-tab="specificationsTab"
                                    data-prev-tab="detailsTab" role="tabpanel">
                                    <form id="pricesTabForm" class="row gx-5">
                                        <div class="col-xxl-6 col-xl-12 col-lg-12 col-md-6">
                                            <div class="card custom-card shadow-none mb-0 border-0">
                                                <div class="card-body p-0">
                                                    <div class="row gy-3">
                                                        <div class="col-xl-12">
                                                            <label for="buying_price" class="form-label"><span
                                                                    class="text-danger">* </span>Buying Price</label>
                                                            <input type="text" class="form-control" id="buying_price"
                                                                name="buying_price"
                                                                value="{{ !empty($productDetails->buying_price) ? $productDetails->buying_price : '' }}"
                                                                placeholder="Enter Buying Price">

                                                            <div class="invalid-feedback fw-bold"></div>
                                                        </div>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-6 col-xl-12 col-lg-12 col-md-6">
                                            <div class="card custom-card shadow-none mb-0 border-0">
                                                <div class="card-body p-0">
                                                    <div class="row gy-4">
                                                        <div class="col-xl-12">
                                                            <label for="selling_price" class="form-label"><span
                                                                    class="text-danger">* </span>Selling Price</label>
                                                            <input type="text" class="form-control" id="selling_price"
                                                                name="selling_price"
                                                                value="{{ !empty($productDetails->selling_price) ? $productDetails->selling_price : '' }}"
                                                                placeholder="Enter Selling Price">
                                                            <div class="invalid-feedback fw-bold"></div>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-6 col-xl-12 col-lg-12 col-md-6 mt-3">

                                            <div class="form-check"> <input class="form-check-input"
                                                    name="is_including_taxes" type="checkbox" value="1"
                                                    id="flexCheckDefaultTaxes"
                                                    {{ !empty($productDetails->is_including_taxes) ? 'checked' : '' }}>
                                                <label class="form-label form-check-label" for="flexCheckDefaultTaxes"> Is
                                                    Including Taxes ? </label> </div>

                                        </div>
                                        <div
                                            class="py-3 border-top border-block-start-dashed d-sm-flex justify-content-between">
                                            <button type="button" class="btn btn-dark" id="actionBtn"
                                                data-action="back" data-current-tab="pricesTab"
                                                data-target-tab="detailsTab">Back</button>
                                            <button type="submit" class="btn btn-primary" id="actionBtn"
                                                data-action="next" data-current-tab="pricesTab">Next</button>
                                        </div>

                                    </form>
                                </div>
                                <div class="tab-pane text-muted" id="specificationsTab"
                                    data-next-tab="shippingSpecificationsTab" data-prev-tab="pricesTab" role="tabpanel">
                                    <form id="specificationsTabForm" class="row gx-5">
                                        <div class="invalid-feedback fw-bold specificationsTabErrorDiv"></div>
                                        <div
                                            class="py-3 border-top border-block-start-dashed d-sm-flex justify-content-between">
                                            <button type="button" class="btn btn-dark" id="actionBtn"
                                                data-action="back" data-current-tab="specificationsTab"
                                                data-target-tab="pricesTab">Back</button>
                                            <button type="submit" class="btn btn-primary" id="actionBtn"
                                                data-action="next" data-current-tab="specificationsTab">Next</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane text-muted" id="shippingSpecificationsTab" data-next-tab="mediasTab"
                                    data-prev-tab="specificationsTab" role="tabpanel">
                                    <form id="shippingSpecificationsTabForm" class="row gx-5">
                                        <div class="col-xxl-6 col-xl-12 col-lg-12 col-md-6">
                                            <div class="card custom-card shadow-none mb-0 border-0">
                                                <div class="card-body p-0">
                                                    <div class="row gy-3">
                                                        <div class="col-xl-12">
                                                            <label for="height" class="form-label"><span
                                                                    class="text-danger">
                                                                </span>Height</label>
                                                            <input type="text" class="form-control" id="height"
                                                                name="height"
                                                                value="{{ !empty($productDetails->height) ? $productDetails->height : '' }}"
                                                                placeholder="Height">
                                                            <div class="invalid-feedback fw-bold"></div>

                                                        </div>
                                                        <div class="col-xl-12">
                                                            <label for="width" class="form-label"><span
                                                                    class="text-danger">
                                                                </span>Width</label>
                                                            <input type="text" class="form-control" id="width"
                                                                name="width"
                                                                value="{{ !empty($productDetails->width) ? $productDetails->width : '' }}"
                                                                placeholder="Width">
                                                            <div class="invalid-feedback fw-bold"></div>

                                                        </div>
                                                        <div class="col-xl-12">
                                                            <label for="dc" class="form-label"><span
                                                                    class="text-danger">
                                                                </span>DC</label>
                                                            <input type="text" class="form-control" id="dc"
                                                                name="dc" placeholder="DC"
                                                                value="{{ !empty($productDetails->dc) ? $productDetails->dc : '' }}">
                                                            <div class="invalid-feedback fw-bold"></div>

                                                        </div>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-6 col-xl-12 col-lg-12 col-md-6">
                                            <div class="card custom-card shadow-none mb-0 border-0">
                                                <div class="card-body p-0">
                                                    <div class="row gy-4">
                                                        <div class="col-xl-12">
                                                            <label for="weight" class="form-label"><span
                                                                    class="text-danger">
                                                                </span>Weight</label>
                                                            <input type="text" class="form-control" id="weight"
                                                                name="weight" placeholder="Weight"
                                                                value="{{ !empty($productDetails->weight) ? $productDetails->weight : '' }}">
                                                            <div class="invalid-feedback fw-bold"></div>

                                                        </div>
                                                        <div class="col-xl-12">
                                                            <label for="length" class="form-label"><span
                                                                    class="text-danger">
                                                                </span>Length</label>
                                                            <input type="text" class="form-control" id="length"
                                                                name="length" placeholder="Length"
                                                                value="{{ !empty($productDetails->length) ? $productDetails->length : '' }}">
                                                            <div class="invalid-feedback fw-bold"></div>

                                                        </div>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="py-3 border-top border-block-start-dashed d-sm-flex justify-content-between">
                                            <button type="button" class="btn btn-dark" id="actionBtn"
                                                data-action="back" data-current-tab="shippingSpecificationsTab"
                                                data-target-tab="specificationsTab">Back</button>
                                            <button type="submit" class="btn btn-primary" id="actionBtn"
                                                data-action="next"
                                                data-current-tab="shippingSpecificationsTab">Next</button>
                                        </div>

                                    </form>
                                </div>
                                <div class="tab-pane text-muted" id="mediasTab" data-next-tab="variantsTab"
                                    data-prev-tab="shippingSpecificationsTab" role="tabpanel">
                                    <div class="row gx-5">
                                        <div id="imageDropzone" class="dropzone">
                                            <!-- <div class="text-center">Drop images here to upload</div> -->
                                        </div>



                                    </div>
                                    <div class="card border bg-transparent mt-3 loadImagesData">
                                        @if (!empty($productDetails) && $productDetails->product_images->isNotEmpty())
                                            @include('admin.products.load_images', [
                                                'getData' => $productDetails->product_images,
                                            ])
                                        @else
                                            <div class="row p-3 text-center"><small>Product Images will be shown
                                                    here.</small>
                                            </div>
                                        @endif
                                    </div>


                                    <div
                                        class="py-3 border-top border-block-start-dashed d-sm-flex justify-content-between">
                                        <button type="button" class="btn btn-dark" id="actionBtn" data-action="back"
                                            data-current-tab="mediasTab"
                                            data-target-tab="shippingSpecificationsTab">Back</button>
                                        <button type="submit" class="btn btn-primary" id="actionBtn" data-action="next"
                                            data-current-tab="mediasTab">Next</button>
                                    </div>


                                </div>
                                <div class="tab-pane text-muted" id="variantsTab" data-next-tab="advanceSeoTab"
                                    data-prev-tab="mediasTab" role="tabpanel">
                                    <form id="variantsTabForm" class="row gx-5">
                                        <div class="invalid-feedback fw-bold variantsTabErrorDiv"></div>
                                        <div
                                            class="py-3 border-top border-block-start-dashed d-sm-flex justify-content-between">
                                            <button type="button" class="btn btn-dark" id="actionBtn"
                                                data-action="back" data-current-tab="variantsTab"
                                                data-target-tab="mediasTab">Back</button>
                                            <button type="submit" class="btn btn-primary" id="actionBtn"
                                                data-action="next" data-current-tab="variantsTab"
                                                data-current-action="second_step">Next</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane text-muted" id="advanceSeoTab" data-next-tab=""
                                    data-prev-tab="variantsTab" role="tabpanel">
                                    <form id="advanceSeoTabForm" class="row gx-5 gy-5">
                                        <div class="col-xl-12">
                                            <label for="meta_title" class="form-label">Meta Title</label>
                                            <input type="text" class="form-control" id="meta_title" name="meta_title"
                                                placeholder="Meta TItle"
                                                value="{{ !empty($productDetails->meta_title) ? $productDetails->meta_title : '' }}">
                                            <div class="invalid-feedback fw-bold"></div>
                                        </div>
                                        <div class="col-xl-12">
                                            <label for="meta_description" class="form-label">Meta Description</label>
                                            <textarea class="form-control" name="meta_description" id="meta_description" cols="30" rows="5">{{ !empty($productDetails->meta_description) ? $productDetails->meta_description : '' }}</textarea>
                                            <div class="invalid-feedback fw-bold"></div>
                                        </div>
                                        <div class="col-xl-12">
                                            <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                            <input type="text" class="form-control" id="meta_keywords"
                                                name="meta_keywords" placeholder="Meta Keywords"
                                                value="{{ !empty($productDetails->meta_keywords) ? $productDetails->meta_keywords : '' }}">
                                            <div class="invalid-feedback fw-bold"></div>
                                        </div>
                                        <div
                                            class="py-3 border-top border-block-start-dashed d-sm-flex justify-content-between">
                                            <button type="button" class="btn btn-dark" id="actionBtn"
                                                data-action="back" data-current-tab="advanceSeoTab"
                                                data-target-tab="variantsTab">Back</button>
                                            <button type="submit" class="btn btn-primary" id="actionBtn"
                                                data-action="next" data-current-tab="advanceSeoTab">Finish</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
    <script>
        let selectedSubcategory = parseInt("<?php echo !empty($productDetails->sub_category_id) ? $productDetails->sub_category_id : ''; ?>");
        let selectedChildcategory = parseInt("<?php echo !empty($productDetails->child_category_id) ? $productDetails->child_category_id : ''; ?>");
    </script>

    <script src="{{ asset('assets/js/custom/product.js') }}"></script>
    <script src="{{ asset('assets/js/repeater.js') }}"></script>

    {{-- <script src="{{ asset('assets/js/fileupload.js') }}"></script> --}}
    <script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/form-validation.js') }}"></script>
    @if (!empty($productDetails->category_id))
        <script>
            jQuery(document).ready(function() {
                setTimeout(() => {

                    $('#category_id').trigger('change');

                }, 200);

            });
        </script>
    @endif
    <script>
        var KTFormRepeater = function() {

            var demo1 = function() {


                $('#kt_repeater_1').repeater({
                    initEmpty: false,

                    defaultValues: {

                    },

                    show: function() {
                        // Triggered when a new item is added
                        var $item = $(this);
                        // Get the counter value (assuming you have a counter element)
                        var counter = $item.parent().find('[data-repeater-item]').index($item);
                        // Iterate over all textareas within the new item
                        $item.find('[name^="productDetailsArr[' + counter + '][value]"]').each(
                        function() {
                            var textarea = this;
                            // Replace each textarea with CKEditor
                            CKEDITOR.replace(this, {
                                filebrowserUploadUrl: '<?php echo URL()->to('base/uploader'); ?>',
                                enterMode: CKEDITOR.ENTER_BR,
                                on: {
                                    instanceReady: function(ev) {
                                        ev.editor.setData(textarea.value);
                                    }
                                }
                            });


                        });
                        $elem = $(this).slideDown();
                        $elem.find('.btn-primary-light').remove();
                        $elem.find('.btn-danger-light').show();

                    },

                    hide: function(deleteElement) {
                        $(this).slideUp(deleteElement);
                    },
                    isFirstItemUndeletable: true
                });


            }

            return {
                // public functions
                init: function() {
                    demo1();
                    // Replace each textarea with CKEditor

                }
            };
        }();

        jQuery(document).ready(function() {
            KTFormRepeater.init();

            $('#detailsTabForm').find('textarea').each(function() {
                var textarea = this;
                // Replace each textarea with CKEditor
                CKEDITOR.replace(this, {
                    filebrowserUploadUrl: '<?php echo URL()->to('base/uploader'); ?>',
                    enterMode: CKEDITOR.ENTER_BR,
                    on: {
                        instanceReady: function(ev) {
                            ev.editor.setData(textarea.value);
                        }
                    }
                });

            });


        });
    </script>
    <script>
        CKEDITOR.replace(<?php echo 'meta_description'; ?>, {
            filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
            enterMode: CKEDITOR.ENTER_BR
        });
        CKEDITOR.config.allowedContent = true;

        function show_message(message, message_type) {
            if (message_type) {

                Swal.fire({
                    icon: message_type,
                    title: message,
                    showConfirmButton: true,
                })
            }

        }

        Dropzone.autoDiscover = false;

        const myDropzone = new Dropzone("#imageDropzone", {
            url: "{{ route('admin-product-upload-images') }}",
            acceptedFiles: ".png,.jpg,.jpeg",
            uploadMultiple: true,
            maxFilesize: 20,
            createImageThumbnails: true,
            maxThumbnailFilesize: 10,
            thumbnailMethod: 'crop',
            parallelUploads: 10,
            autoProcessQueue: true,


            init: function() {

                // this.on("addedfile", function(file) {
                //     // Remove the file from Dropzone

                // });
                // this.on("addedfile", file => {
                //     $('#uploadPictures').prop('disabled', false);
                // });

                // submitButton.addEventListener('click', function() {
                //     $btnName = 'Upload Pictures';
                //     $loadingText = 'Uploading...';
                //     $(this).prop('disabled', true);
                //     $(this).html(
                //         '<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> ' +
                //         $loadingText);
                //     myDropzone.processQueue();

                // });
                const that = this;
                // this.on("complete", function() {
                that.on("success", function(file, responseText) {
                    this.removeAllFiles();
                    if (responseText.status == 'success') {
                        $('.loadImagesData').html('')
                        $('.loadImagesData').html(responseText.data);
                        show_message(responseText.msg, 'success');
                    } else {
                        show_message(responseText, 'error');
                    }
                });
                that.on("error", function(xhr, status, errorThrown) {
                    this.removeAllFiles();
                    if (xhr.responseJSON && xhr.responseJSON.status) {
                        if (xhr.responseJSON.status == 'error') {

                            show_message(xhr.responseJSON.msg, 'error');
                        } else {
                            show_message(xhr.responseJSON, 'error');
                        }
                    } else {
                        show_message(xhr.responseText, 'error');
                    }
                });



                // });


            }

        });

        $(document).on('click', '.removeProductImage', function(e) {
            e.preventDefault();

            const that = this;
            let url = $(this).attr('data-url');

            Swal.fire({
                title: "Are you sure?",
                text: "Want to remove this image ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, remove it",
                cancelButtonText: "No, cancel",
                reverseButtons: true
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: url,
                        method: 'get',
                        // data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $("#loader_img").hide();
                            $('.invalid-feedback').html("");
                            $('.invalid-feedback').removeClass("error");
                            $('.is-invalid').removeClass("is-invalid");

                            error_array = JSON.stringify(response);
                            datas = JSON.parse(error_array);
                            if (datas['status'] == 'success') {
                                $(that).parents('.productPicMainContainer').remove();
                                show_message(datas['msg'], 'success');
                            } else {
                                if (datas['status'] == 'error' && datas['errors']) {
                                    $.each(datas['errors'], function(index, html) {
                                        $("input[name = " + index + "]").addClass(
                                            'is-invalid');
                                        $("input[name = " + index + "]").next()
                                            .addClass(
                                                'error');
                                        $("input[name = " + index + "]").next().html(
                                            html);
                                        $("input[name = " + index + "]").show();

                                    });
                                } else if (datas['status'] == 'error') {
                                    show_message(datas['msg'], 'error');
                                } else {
                                    show_message(datas, 'error');
                                }


                            }

                        },
                        error: function(xhr, status, errorThrown) {
                            if (xhr.responseJSON && xhr.responseJSON.status) {
                                if (xhr.responseJSON.status == 'error') {

                                    show_message(xhr.responseJSON.msg, 'error');
                                } else {
                                    show_message(xhr.responseJSON, 'error');
                                }
                            } else {
                                show_message(xhr.responseText, 'error');
                            }
                            $(that).prop('disabled', false);
                            $(that).text($btnName);

                        }
                    });
                } else if (result.dismiss === "cancel") {
                    Swal.fire(
                        "Cancelled",
                        "Your imaginary file is safe :)",
                        "error"
                    )
                }
            });




        });

        $(document).on('click', '.statusCheckboxProductPicture', function(e) {
            e.preventDefault();

            const that = this;
            let url = $(this).attr('data-url');
            var imageType = ($(this).attr('name') == 'frontImage') ? 'Front Image' : 'Back Image';
            Swal.fire({
                title: "Are you sure?",
                text: "Want to make this image as " + imageType + " ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, make it",
                cancelButtonText: "No, cancel",
                reverseButtons: true
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: url,
                        method: 'get',
                        // data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $("#loader_img").hide();
                            $('.invalid-feedback').html("");
                            $('.invalid-feedback').removeClass("error");
                            $('.is-invalid').removeClass("is-invalid");

                            error_array = JSON.stringify(response);
                            datas = JSON.parse(error_array);
                            if (datas['status'] == 'success') {
                                if ($(that).prop('checked')) {
                                    $(that).prop('checked', false)
                                } else {
                                    $(that).prop('checked', true)
                                }
                                show_message(datas['msg'], 'success');
                            } else {
                                if (datas['status'] == 'error' && datas['errors']) {
                                    $.each(datas['errors'], function(index, html) {
                                        $("input[name = " + index + "]").addClass(
                                            'is-invalid');
                                        $("input[name = " + index + "]").next()
                                            .addClass(
                                                'error');
                                        $("input[name = " + index + "]").next().html(
                                            html);
                                        $("input[name = " + index + "]").show();

                                    });
                                } else if (datas['status'] == 'error') {
                                    show_message(datas['msg'], 'error');
                                } else {
                                    show_message(datas, 'error');
                                }


                            }

                        },
                        error: function(xhr, status, errorThrown) {
                            if (xhr.responseJSON && xhr.responseJSON.status) {
                                if (xhr.responseJSON.status == 'error') {

                                    show_message(xhr.responseJSON.msg, 'error');
                                } else {
                                    show_message(xhr.responseJSON, 'error');
                                }
                            } else {
                                show_message(xhr.responseText, 'error');
                            }
                            $(that).prop('disabled', false);
                            $(that).text($btnName);

                        }
                    });
                }
            });




        });




        // document.getElementById('submitBtnImages').addEventListener('click', function(event) {
        //     event.preventDefault();
        //     const formData = new FormData();

        //     const frontImageURL = document.querySelector('input[name="frontImage"]:checked').value;
        //     const backImageURL = document.querySelector('input[name="backImage"]:checked').value;

        //     const frontImageIndex = [...urlToFileMap.keys()].findIndex(url => url === frontImageURL);
        //     const backImageIndex = [...urlToFileMap.keys()].findIndex(url => url === backImageURL);

        //     if (frontImageIndex !== -1 && backImageIndex !== -1) {
        //         formData.append('frontImage', frontImageIndex);
        //         formData.append('backImage', backImageIndex);

        //         urlToFileMap.forEach((file, url) => {
        //             formData.append('images[]', file);
        //         });

        //         fetch("{{ route('admin-product-store') }}", {
        //                 method: 'POST',
        //                 body: formData,
        //                 headers: {
        //                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                 }
        //             })
        //             .then(response => response.json())
        //             .then(data => {
        //                 console.log(data);
        //                 // Handle response or redirect after successful upload
        //             })
        //             .catch(error => {
        //                 console.error('Error:', error);
        //                 // Handle errors
        //             });
        //     } else {
        //         console.error('Front or back image index not found.');
        //     }
        // });

        $(document).on('click', '#actionBtn', function(e) {
            e.preventDefault();
            if ($(this).attr('data-action') && $(this).attr('data-action') == 'next') {
                $btnName = 'Next';
                $loadingText = 'Processing...';
                $(this).prop('disabled', true);
                $(this).html(
                    '<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> ' +
                    $loadingText);
                const that = this;
                var formData = new FormData($('#' + $(this).attr('data-current-tab') + 'Form')[0]);
                formData.append('current_tab', $(this).attr('data-current-tab'));
                if ($(this).attr('data-current-tab') == 'basicInformationTab') {
                    $.ajax({
                        url: "{{ route('admin-product-store') }}",
                        method: 'post',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $("#loader_img").hide();
                            $('.invalid-feedback').html("");
                            $('.invalid-feedback').removeClass("error");
                            $('.is-invalid').removeClass("is-invalid");

                            error_array = JSON.stringify(response);
                            datas = JSON.parse(error_array);
                            if (datas['status'] == 'success') {
                                $nextTab = $(that).parents('.tab-pane').attr('data-next-tab');
                                $('.nav-link').removeClass('active');
                                $('.tab-pane').removeClass('active show');
                                $('.tab-pane[id=' + $nextTab + ']').addClass('active show');
                                $('.nav-link[href="#' + $nextTab + '"]').addClass('active');
                                // $('.tab-pane[id=mediasTab]').addClass('active show');
                                // $('.nav-link[href="#mediasTab"]').addClass('active');
                            } else {
                                if (datas['status'] == 'error' && datas['errors']) {
                                    $.each(datas['errors'], function(index, html) {
                                        if (index == 'description') {
                                            $("textarea[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("textarea[name = " + index + "]").next().addClass(
                                                'error');
                                            $("textarea[name = " + index + "]").next().html(
                                                html);
                                            $("textarea[name = " + index + "]").show();
                                        } else if (index == 'category_id' || index ==
                                            'sub_category_id' || index == 'child_category_id' ||
                                            index ==
                                            'brand_id') {
                                            $("select[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("select[name = " + index + "]").next()
                                                .next().addClass('error');
                                            $("select[name = " + index + "]").next()
                                                .next().html(html);
                                            $("select[name = " + index + "]").next()
                                                .next().show();
                                            // $("select[name = " + index + "]").show();
                                        } else if (index == 'profile_pic') {
                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").parent().next()
                                                .next()
                                                .addClass('error');
                                            $("input[name = " + index + "]").parent().next()
                                                .next()
                                                .html(html);
                                            $("input[name = " + index + "]").parent().next()
                                                .next()
                                                .show();
                                            $("input[name = " + index + "]").show();
                                        } else if (index == 'gender') {
                                            $("select[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("select[name = " + index + "]").parents(
                                                    '.choices')
                                                .next().addClass('error');
                                            $("select[name = " + index + "]").parents(
                                                    '.choices')
                                                .next().html(html);
                                            $("select[name = " + index + "]").parents(
                                                    '.choices')
                                                .next().show();
                                            $("select[name = " + index + "]").show();
                                        } else if (index == 'date_of_birth') {
                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").next().addClass(
                                                'error');
                                            $("input[name = " + index + "]").next().html(html);
                                            $("input[name = " + index + "]").next().show();
                                            $("input[name = " + index + "]").show();
                                        } else if (index ==
                                            'are_you_working_on_any_other_online_portal') {
                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").parent().next()
                                                .addClass('error');
                                            $("input[name = " + index + "]").parent().next()
                                                .html(
                                                    html);
                                            $("input[name = " + index + "]").parent().next()
                                                .show();
                                            $("input[name = " + index + "]").show();
                                        } else {

                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").next().addClass(
                                                'error');
                                            $("input[name = " + index + "]").next().html(html);
                                            $("input[name = " + index + "]").show();
                                        }


                                    });
                                } else if (datas['status'] == 'error') {
                                    show_message(datas['msg'], 'error');
                                } else {
                                    show_message(datas, 'error');
                                }


                            }
                            $(that).prop('disabled', false);
                            $(that).text($btnName);
                        },
                        error: function(xhr, status, errorThrown) {
                            if (xhr.responseJSON && xhr.responseJSON.status) {
                                if (xhr.responseJSON.status == 'error') {

                                    show_message(xhr.responseJSON.msg, 'error');
                                } else {
                                    show_message(xhr.responseJSON, 'error');
                                }
                            } else {
                                show_message(xhr.responseText, 'error');
                            }
                            $(that).prop('disabled', false);
                            $(that).text($btnName);

                        }
                    });

                } else if ($(this).attr('data-current-tab') == 'detailsTab') {
                    setTextareaValues($(this).attr('data-current-tab') + 'Form');

                    var formData = new FormData($('#' + $(this).attr('data-current-tab') + 'Form')[0]);
                    formData.append('current_tab', $(this).attr('data-current-tab'));

                    $.ajax({
                        url: "{{ route('admin-product-store') }}",
                        method: 'post',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $("#loader_img").hide();
                            $('.invalid-feedback').html("");
                            $('.invalid-feedback').removeClass("error");
                            $('.is-invalid').removeClass("is-invalid");

                            error_array = JSON.stringify(response);
                            datas = JSON.parse(error_array);
                            if (datas['status'] == 'success') {
                                $nextTab = $(that).parents('.tab-pane').attr('data-next-tab');
                                $('.nav-link').removeClass('active');
                                $('.tab-pane').removeClass('active show');
                                $('.tab-pane[id=' + $nextTab + ']').addClass('active show');
                                $('.nav-link[href="#' + $nextTab + '"]').addClass('active');
                            } else {
                                if (datas['status'] == 'error' && datas['errors']) {
                                    $.each(datas['errors'], function(index, html) {
                                        if (index == 'long_description' || index ==
                                            'short_description' || index == 'return_policy' ||
                                            index == 'seller_information') {
                                            $("textarea[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("textarea[name = " + index + "]").next().next()
                                                .addClass(
                                                    'error');
                                            $("textarea[name = " + index + "]").next().next()
                                                .html(
                                                    html);
                                            $("textarea[name = " + index + "]").next().next()
                                                .show();
                                        } else if (index == 'category_id' || index ==
                                            'sub_category_id' || index == 'child_category_id' ||
                                            index ==
                                            'brand_id') {
                                            $("select[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("select[name = " + index + "]").next()
                                                .next().addClass('error');
                                            $("select[name = " + index + "]").next()
                                                .next().html(html);
                                            $("select[name = " + index + "]").next()
                                                .next().show();
                                            // $("select[name = " + index + "]").show();
                                        } else if (index == 'profile_pic') {
                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").parent().next()
                                                .next()
                                                .addClass('error');
                                            $("input[name = " + index + "]").parent().next()
                                                .next()
                                                .html(html);
                                            $("input[name = " + index + "]").parent().next()
                                                .next()
                                                .show();
                                            $("input[name = " + index + "]").show();
                                        } else if (index == 'gender') {
                                            $("select[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("select[name = " + index + "]").parents(
                                                    '.choices')
                                                .next().addClass('error');
                                            $("select[name = " + index + "]").parents(
                                                    '.choices')
                                                .next().html(html);
                                            $("select[name = " + index + "]").parents(
                                                    '.choices')
                                                .next().show();
                                            $("select[name = " + index + "]").show();
                                        } else if (index == 'date_of_birth') {
                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").next().addClass(
                                                'error');
                                            $("input[name = " + index + "]").next().html(html);
                                            $("input[name = " + index + "]").next().show();
                                            $("input[name = " + index + "]").show();
                                        } else if (index ==
                                            'are_you_working_on_any_other_online_portal') {
                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").parent().next()
                                                .addClass('error');
                                            $("input[name = " + index + "]").parent().next()
                                                .html(
                                                    html);
                                            $("input[name = " + index + "]").parent().next()
                                                .show();
                                            $("input[name = " + index + "]").show();
                                        } else {

                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").next().addClass(
                                                'error');
                                            $("input[name = " + index + "]").next().html(html);
                                            $("input[name = " + index + "]").show();
                                        }


                                    });
                                } else if (datas['status'] == 'error') {
                                    show_message(datas['msg'], 'error');
                                } else {
                                    show_message(datas, 'error');
                                }


                            }
                            $(that).prop('disabled', false);
                            $(that).text($btnName);
                        },
                        error: function(xhr, status, errorThrown) {
                            if (xhr.responseJSON && xhr.responseJSON.status) {
                                if (xhr.responseJSON.status == 'error') {

                                    show_message(xhr.responseJSON.msg, 'error');
                                } else {
                                    show_message(xhr.responseJSON, 'error');
                                }
                            } else {
                                show_message(xhr.responseText, 'error');
                            }
                            $(that).prop('disabled', false);
                            $(that).text($btnName);

                        }
                    });

                } else if ($(this).attr('data-current-tab') == 'pricesTab') {

                    $.ajax({
                        url: "{{ route('admin-product-store') }}",
                        method: 'post',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $("#loader_img").hide();
                            $('.invalid-feedback').html("");
                            $('.invalid-feedback').removeClass("error");
                            $('.is-invalid').removeClass("is-invalid");

                            error_array = JSON.stringify(response);
                            datas = JSON.parse(error_array);
                            if (datas['status'] == 'success') {
                                $('#loader-row').nextAll().remove();
                                $('button[data-current-tab="specificationsTab"]').parent().prev(
                                        '.specificationsTabErrorDiv').prevAll()
                                    .remove();
                                $('button[data-current-tab="specificationsTab"]').parent().prev(
                                    '.specificationsTabErrorDiv').before(datas[
                                    'data']);
                                $nextTab = $(that).parents('.tab-pane').attr('data-next-tab');
                                $('.nav-link').removeClass('active');
                                $('.tab-pane').removeClass('active show');
                                $('.tab-pane[id=' + $nextTab + ']').addClass('active show');
                                $('.nav-link[href="#' + $nextTab + '"]').addClass('active');
                            } else {
                                if (datas['status'] == 'error' && datas['errors']) {
                                    $.each(datas['errors'], function(index, html) {
                                        if (index == 'long_description' || index ==
                                            'short_description' || index == 'return_policy' ||
                                            index == 'seller_information') {
                                            $("textarea[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("textarea[name = " + index + "]").next().next()
                                                .addClass(
                                                    'error');
                                            $("textarea[name = " + index + "]").next().next()
                                                .html(
                                                    html);
                                            $("textarea[name = " + index + "]").next().next()
                                                .show();
                                        } else if (index == 'category_id' || index ==
                                            'sub_category_id' || index == 'child_category_id' ||
                                            index ==
                                            'brand_id') {
                                            $("select[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("select[name = " + index + "]").next()
                                                .next().addClass('error');
                                            $("select[name = " + index + "]").next()
                                                .next().html(html);
                                            $("select[name = " + index + "]").next()
                                                .next().show();
                                            // $("select[name = " + index + "]").show();
                                        } else if (index == 'profile_pic') {
                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").parent().next()
                                                .next()
                                                .addClass('error');
                                            $("input[name = " + index + "]").parent().next()
                                                .next()
                                                .html(html);
                                            $("input[name = " + index + "]").parent().next()
                                                .next()
                                                .show();
                                            $("input[name = " + index + "]").show();
                                        } else if (index == 'gender') {
                                            $("select[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("select[name = " + index + "]").parents(
                                                    '.choices')
                                                .next().addClass('error');
                                            $("select[name = " + index + "]").parents(
                                                    '.choices')
                                                .next().html(html);
                                            $("select[name = " + index + "]").parents(
                                                    '.choices')
                                                .next().show();
                                            $("select[name = " + index + "]").show();
                                        } else if (index == 'date_of_birth') {
                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").next().addClass(
                                                'error');
                                            $("input[name = " + index + "]").next().html(html);
                                            $("input[name = " + index + "]").next().show();
                                            $("input[name = " + index + "]").show();
                                        } else if (index ==
                                            'are_you_working_on_any_other_online_portal') {
                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").parent().next()
                                                .addClass('error');
                                            $("input[name = " + index + "]").parent().next()
                                                .html(
                                                    html);
                                            $("input[name = " + index + "]").parent().next()
                                                .show();
                                            $("input[name = " + index + "]").show();
                                        } else {

                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").next().addClass(
                                                'error');
                                            $("input[name = " + index + "]").next().html(html);
                                            $("input[name = " + index + "]").show();
                                        }


                                    });
                                } else if (datas['status'] == 'error') {
                                    show_message(datas['msg'], 'error');
                                } else {
                                    show_message(datas, 'error');
                                }


                            }
                            $(that).prop('disabled', false);
                            $(that).text($btnName);
                        },
                        error: function(xhr, status, errorThrown) {
                            if (xhr.responseJSON && xhr.responseJSON.status) {
                                if (xhr.responseJSON.status == 'error') {

                                    show_message(xhr.responseJSON.msg, 'error');
                                } else {
                                    show_message(xhr.responseJSON, 'error');
                                }
                            } else {
                                show_message(xhr.responseText, 'error');
                            }
                            $(that).prop('disabled', false);
                            $(that).text($btnName);

                        }
                    });

                } else if ($(this).attr('data-current-tab') == 'specificationsTab') {

                    $.ajax({
                        url: "{{ route('admin-product-store') }}",
                        method: 'post',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $("#loader_img").hide();
                            $('.invalid-feedback').html("");
                            $('.invalid-feedback').removeClass("error");
                            $('.is-invalid').removeClass("is-invalid");

                            error_array = JSON.stringify(response);
                            datas = JSON.parse(error_array);
                            if (datas['status'] == 'success') {
                                $('#loader-row').nextAll().remove();
                                $nextTab = $(that).parents('.tab-pane').attr('data-next-tab');
                                $('.nav-link').removeClass('active');
                                $('.tab-pane').removeClass('active show');
                                $('.tab-pane[id=' + $nextTab + ']').addClass('active show');
                                $('.nav-link[href="#' + $nextTab + '"]').addClass('active');
                            } else {
                                if (datas['status'] == 'error' && datas['errors']) {
                                    $.each(datas['errors'], function(index, html) {
                                        if (index == 'long_description' || index ==
                                            'short_description' || index == 'return_policy' ||
                                            index == 'seller_information') {
                                            $("textarea[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("textarea[name = " + index + "]").next().next()
                                                .addClass(
                                                    'error');
                                            $("textarea[name = " + index + "]").next().next()
                                                .html(
                                                    html);
                                            $("textarea[name = " + index + "]").next().next()
                                                .show();
                                        } else if (index == 'category_id' || index ==
                                            'sub_category_id' || index == 'child_category_id' ||
                                            index ==
                                            'brand_id') {
                                            $("select[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("select[name = " + index + "]").next()
                                                .next().addClass('error');
                                            $("select[name = " + index + "]").next()
                                                .next().html(html);
                                            $("select[name = " + index + "]").next()
                                                .next().show();
                                            // $("select[name = " + index + "]").show();
                                        } else if (index ==
                                            'specificationDataArr') {
                                            $('.specificationsTabErrorDiv').addClass('error');
                                            $('.specificationsTabErrorDiv').html(html).show();

                                        } else {

                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").next().addClass(
                                                'error');
                                            $("input[name = " + index + "]").next().html(html);
                                            $("input[name = " + index + "]").show();
                                        }


                                    });
                                } else if (datas['status'] == 'error') {
                                    show_message(datas['msg'], 'error');
                                } else {
                                    show_message(datas, 'error');
                                }


                            }
                            $(that).prop('disabled', false);
                            $(that).text($btnName);
                        },
                        error: function(xhr, status, errorThrown) {
                            if (xhr.responseJSON && xhr.responseJSON.status) {
                                if (xhr.responseJSON.status == 'error') {

                                    show_message(xhr.responseJSON.msg, 'error');
                                } else {
                                    show_message(xhr.responseJSON, 'error');
                                }
                            } else {
                                show_message(xhr.responseText, 'error');
                            }
                            $(that).prop('disabled', false);
                            $(that).text($btnName);

                        }
                    });

                } else if ($(this).attr('data-current-tab') == 'shippingSpecificationsTab') {

                    $.ajax({
                        url: "{{ route('admin-product-store') }}",
                        method: 'post',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $("#loader_img").hide();
                            $('.invalid-feedback').html("");
                            $('.invalid-feedback').removeClass("error");
                            $('.is-invalid').removeClass("is-invalid");

                            error_array = JSON.stringify(response);
                            datas = JSON.parse(error_array);
                            if (datas['status'] == 'success') {
                                $('#loader-row').nextAll().remove();

                                $nextTab = $(that).parents('.tab-pane').attr('data-next-tab');
                                $('.nav-link').removeClass('active');
                                $('.tab-pane').removeClass('active show');
                                $('.tab-pane[id=' + $nextTab + ']').addClass('active show');
                                $('.nav-link[href="#' + $nextTab + '"]').addClass('active');
                            } else {
                                if (datas['status'] == 'error' && datas['errors']) {
                                    $.each(datas['errors'], function(index, html) {
                                        if (index == 'long_description' || index ==
                                            'short_description' || index == 'return_policy' ||
                                            index == 'seller_information') {
                                            $("textarea[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("textarea[name = " + index + "]").next().next()
                                                .addClass(
                                                    'error');
                                            $("textarea[name = " + index + "]").next().next()
                                                .html(
                                                    html);
                                            $("textarea[name = " + index + "]").next().next()
                                                .show();
                                        } else {

                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").next().addClass(
                                                'error');
                                            $("input[name = " + index + "]").next().html(html);
                                            $("input[name = " + index + "]").show();
                                        }


                                    });
                                } else if (datas['status'] == 'error') {
                                    show_message(datas['msg'], 'error');
                                } else {
                                    show_message(datas, 'error');
                                }


                            }
                            $(that).prop('disabled', false);
                            $(that).text($btnName);
                        },
                        error: function(xhr, status, errorThrown) {
                            if (xhr.responseJSON && xhr.responseJSON.status) {
                                if (xhr.responseJSON.status == 'error') {

                                    show_message(xhr.responseJSON.msg, 'error');
                                } else {
                                    show_message(xhr.responseJSON, 'error');
                                }
                            } else {
                                show_message(xhr.responseText, 'error');
                            }
                            $(that).prop('disabled', false);
                            $(that).text($btnName);

                        }
                    });

                } else if ($(this).attr('data-current-tab') == 'mediasTab') {

                    $.ajax({
                        url: "{{ route('admin-product-store') }}",
                        method: 'post',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $("#loader_img").hide();
                            $('.invalid-feedback').html("");
                            $('.invalid-feedback').removeClass("error");
                            $('.is-invalid').removeClass("is-invalid");

                            error_array = JSON.stringify(response);
                            datas = JSON.parse(error_array);
                            if (datas['status'] == 'success') {
                                $('button[data-current-action="second_step"]').parent().prev(
                                        '.variantsTabErrorDiv').prevAll()
                                    .remove();
                                $('button[data-current-action="second_step"]').parent().prev(
                                    '.variantsTabErrorDiv').before(datas[
                                    'data']);
                                $nextTab = $(that).parents('.tab-pane').attr('data-next-tab');
                                $('.nav-link').removeClass('active');
                                $('.tab-pane').removeClass('active show');
                                $('.tab-pane[id=' + $nextTab + ']').addClass('active show');
                                $('.nav-link[href="#' + $nextTab + '"]').addClass('active');
                            } else {
                                if (datas['status'] == 'error' && datas['errors']) {
                                    $.each(datas['errors'], function(index, html) {
                                        if (index == 'long_description' || index ==
                                            'short_description' || index == 'return_policy' ||
                                            index == 'seller_information') {
                                            $("textarea[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("textarea[name = " + index + "]").next().next()
                                                .addClass(
                                                    'error');
                                            $("textarea[name = " + index + "]").next().next()
                                                .html(
                                                    html);
                                            $("textarea[name = " + index + "]").next().next()
                                                .show();
                                        } else if (index == 'category_id' || index ==
                                            'sub_category_id' || index == 'child_category_id' ||
                                            index ==
                                            'brand_id') {
                                            $("select[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("select[name = " + index + "]").next()
                                                .next().addClass('error');
                                            $("select[name = " + index + "]").next()
                                                .next().html(html);
                                            $("select[name = " + index + "]").next()
                                                .next().show();
                                            // $("select[name = " + index + "]").show();
                                        } else if (index ==
                                            'specificationDataArr') {
                                            $('.specificationsTabErrorDiv').addClass('error');
                                            $('.specificationsTabErrorDiv').html(html).show();

                                        } else {

                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").next().addClass(
                                                'error');
                                            $("input[name = " + index + "]").next().html(html);
                                            $("input[name = " + index + "]").show();
                                        }


                                    });
                                } else if (datas['status'] == 'error') {
                                    show_message(datas['msg'], 'error');
                                } else {
                                    show_message(datas, 'error');
                                }


                            }
                            $(that).prop('disabled', false);
                            $(that).text($btnName);
                        },
                        error: function(xhr, status, errorThrown) {
                            if (xhr.responseJSON && xhr.responseJSON.status) {
                                if (xhr.responseJSON.status == 'error') {

                                    show_message(xhr.responseJSON.msg, 'error');
                                } else {
                                    show_message(xhr.responseJSON, 'error');
                                }
                            } else {
                                show_message(xhr.responseText, 'error');
                            }
                            $(that).prop('disabled', false);
                            $(that).text($btnName);

                        }
                    });

                } else if ($(this).attr('data-current-tab') == 'variantsTab' && $(this).attr(
                    'data-current-action') ==
                    'second_step') {
                    $('.variantsTabErrorDiv').html("").hide();
                    formData.append('current_action', 'second_step');
                    $.ajax({
                        url: "{{ route('admin-product-store') }}",
                        method: 'post',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $("#loader_img").hide();
                            $('.invalid-feedback').html("");
                            $('.invalid-feedback').removeClass("error");
                            $('.is-invalid').removeClass("is-invalid");

                            error_array = JSON.stringify(response);
                            datas = JSON.parse(error_array);
                            if (datas['status'] == 'success') {
                                $nextTab = $(that).parents('.tab-pane').attr('data-next-tab');
                                $('.nav-link').removeClass('active');
                                $('.tab-pane').removeClass('active show');
                                $('.tab-pane[id=' + $nextTab + ']').addClass('active show');
                                $('.nav-link[href="#' + $nextTab + '"]').addClass('active');

                            } else {
                                if (datas['status'] == 'error' && datas['errors']) {

                                    $.each(datas['errors'], function(index, html) {
                                        if (index == 'long_description' || index ==
                                            'short_description' || index == 'return_policy' ||
                                            index == 'seller_information') {
                                            $("textarea[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("textarea[name = " + index + "]").next().next()
                                                .addClass(
                                                    'error');
                                            $("textarea[name = " + index + "]").next().next()
                                                .html(
                                                    html);
                                            $("textarea[name = " + index + "]").next().next()
                                                .show();
                                        } else if (index == 'category_id' || index ==
                                            'sub_category_id' || index == 'child_category_id' ||
                                            index ==
                                            'brand_id') {
                                            $("select[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("select[name = " + index + "]").next()
                                                .next().addClass('error');
                                            $("select[name = " + index + "]").next()
                                                .next().html(html);
                                            $("select[name = " + index + "]").next()
                                                .next().show();
                                            // $("select[name = " + index + "]").show();
                                        } else if (index ==
                                            'variantsDataArr') {
                                            $('.variantsTabErrorDiv').addClass('error');
                                            $('.variantsTabErrorDiv').html(html).show();

                                        } else {

                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").next().addClass(
                                                'error');
                                            $("input[name = " + index + "]").next().html(html);
                                            $("input[name = " + index + "]").show();
                                        }


                                    });
                                } else if (datas['status'] == 'error') {
                                    show_message(datas['msg'], 'error');

                                } else {
                                    show_message(datas, 'error');

                                }


                            }
                            $(that).prop('disabled', false);
                            $(that).text($btnName);
                        },
                        error: function(xhr, status, errorThrown) {
                            if (xhr.responseJSON && xhr.responseJSON.status) {
                                if (xhr.responseJSON.status == 'error') {

                                    show_message(xhr.responseJSON.msg, 'error');
                                } else {
                                    show_message(xhr.responseJSON, 'error');
                                }
                            } else {
                                show_message(xhr.responseText, 'error');
                            }
                            $(that).prop('disabled', false);
                            $(that).text($btnName);

                        }
                    });

                } else if ($(this).attr('data-current-tab') == 'advanceSeoTab') {
                    var meta_description = CKEDITOR.instances.meta_description.getData();

                    formData.append('meta_description', meta_description);


                    $.ajax({
                        url: "{{ route('admin-product-store') }}",
                        method: 'post',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $("#loader_img").hide();
                            $('.invalid-feedback').html("");
                            $('.invalid-feedback').removeClass("error");
                            $('.is-invalid').removeClass("is-invalid");

                            error_array = JSON.stringify(response);
                            datas = JSON.parse(error_array);
                            if (datas['status'] == 'success') {
                                show_message(datas['msg'], 'success');
                                setTimeout(() => {

                                    window.location.href = "{{ route('admin-product-list') }}";
                                }, 1000);
                            } else {
                                if (datas['status'] == 'error' && datas['errors']) {
                                    $.each(datas['errors'], function(index, html) {
                                        if (index == 'long_description' || index ==
                                            'short_description' || index == 'return_policy' ||
                                            index == 'seller_information') {
                                            $("textarea[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("textarea[name = " + index + "]").next().next()
                                                .addClass(
                                                    'error');
                                            $("textarea[name = " + index + "]").next().next()
                                                .html(
                                                    html);
                                            $("textarea[name = " + index + "]").next().next()
                                                .show();
                                        } else if (index == 'category_id' || index ==
                                            'sub_category_id' || index == 'child_category_id' ||
                                            index ==
                                            'brand_id') {
                                            $("select[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("select[name = " + index + "]").next()
                                                .next().addClass('error');
                                            $("select[name = " + index + "]").next()
                                                .next().html(html);
                                            $("select[name = " + index + "]").next()
                                                .next().show();
                                            // $("select[name = " + index + "]").show();
                                        } else if (index == 'profile_pic') {
                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").parent().next()
                                                .next()
                                                .addClass('error');
                                            $("input[name = " + index + "]").parent().next()
                                                .next()
                                                .html(html);
                                            $("input[name = " + index + "]").parent().next()
                                                .next()
                                                .show();
                                            $("input[name = " + index + "]").show();
                                        } else if (index == 'gender') {
                                            $("select[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("select[name = " + index + "]").parents(
                                                    '.choices')
                                                .next().addClass('error');
                                            $("select[name = " + index + "]").parents(
                                                    '.choices')
                                                .next().html(html);
                                            $("select[name = " + index + "]").parents(
                                                    '.choices')
                                                .next().show();
                                            $("select[name = " + index + "]").show();
                                        } else if (index == 'date_of_birth') {
                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").next().addClass(
                                                'error');
                                            $("input[name = " + index + "]").next().html(html);
                                            $("input[name = " + index + "]").next().show();
                                            $("input[name = " + index + "]").show();
                                        } else if (index ==
                                            'are_you_working_on_any_other_online_portal') {
                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").parent().next()
                                                .addClass('error');
                                            $("input[name = " + index + "]").parent().next()
                                                .html(
                                                    html);
                                            $("input[name = " + index + "]").parent().next()
                                                .show();
                                            $("input[name = " + index + "]").show();
                                        } else {

                                            $("input[name = " + index + "]").addClass(
                                                'is-invalid');
                                            $("input[name = " + index + "]").next().addClass(
                                                'error');
                                            $("input[name = " + index + "]").next().html(html);
                                            $("input[name = " + index + "]").show();
                                        }


                                    });
                                } else if (datas['status'] == 'error') {
                                    show_message(datas['msg'], 'error');
                                } else {
                                    show_message(datas, 'error');
                                }


                            }
                            $(that).prop('disabled', false);
                            $(that).text($btnName);
                        },
                        error: function(xhr, status, errorThrown) {
                            if (xhr.responseJSON && xhr.responseJSON.status) {
                                if (xhr.responseJSON.status == 'error') {

                                    show_message(xhr.responseJSON.msg, 'error');
                                } else {
                                    show_message(xhr.responseJSON, 'error');
                                }
                            } else {
                                show_message(xhr.responseText, 'error');
                            }
                            $(that).prop('disabled', false);
                            $(that).text($btnName);

                        }
                    });

                }
            } else if ($(this).attr('data-action') && $(this).attr('data-action') == 'back') {
                $targetTab = $(this).attr('data-target-tab');
                $('.nav-link').removeClass('active');
                $('.tab-pane').removeClass('active show');
                $('.tab-pane[id=' + $targetTab + ']').addClass('active show');
                $('.nav-link[href="#' + $targetTab + '"]').addClass('active');
            }



        });

        function setTextareaValues(formId) {
            $('#' + formId).find('textarea').each(function() {
                var editor = CKEDITOR.instances[this.name];
                if (editor) {
                    this.value = editor.getData();
                }
            });
        }

        $(document).on('click', '.createVariantBtn', function() {
            $btnName = 'Create Variant';
            $loadingText = 'Processing...';
            $(this).prop('disabled', true);
            $(this).html('<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> ' +
                $loadingText);
            const that = this;
            var formData = new FormData($('#' + $(this).attr('data-current-tab') + 'Form')[0]);
            formData.append('current_tab', $(this).attr('data-current-tab'));
            $('.variantsTabErrorDiv').html("");
            formData.append('current_action', 'first_step');
            $.ajax({
                url: "{{ route('admin-product-store') }}",
                method: 'post',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {

                    $('.invalid-feedback').html("");
                    $('.invalid-feedback').removeClass("error");
                    $('.is-invalid').removeClass("is-invalid");

                    error_array = JSON.stringify(response);
                    datas = JSON.parse(error_array);
                    if (datas['status'] == 'success') {
                        $('button[data-current-action="second_step"]').parent().prev(
                                '.variantsTabErrorDiv')
                            .prevAll().not('#kt_repeater_2').remove();

                        $('button[data-current-action="second_step"]').parent().prev(
                                '.variantsTabErrorDiv')
                            .before(
                                datas[
                                    'data']);

                    } else {
                        if (datas['status'] == 'error' && datas['errors']) {

                            $.each(datas['errors'], function(index, html) {
                                if (index == 'long_description' || index ==
                                    'short_description' || index == 'return_policy' ||
                                    index == 'seller_information') {
                                    $("textarea[name = " + index + "]").addClass(
                                        'is-invalid');
                                    $("textarea[name = " + index + "]").next().next()
                                        .addClass(
                                            'error');
                                    $("textarea[name = " + index + "]").next().next().html(
                                        html);
                                    $("textarea[name = " + index + "]").next().next()
                                        .show();
                                } else if (index == 'category_id' || index ==
                                    'sub_category_id' || index == 'child_category_id' ||
                                    index ==
                                    'brand_id') {
                                    $("select[name = " + index + "]").addClass(
                                        'is-invalid');
                                    $("select[name = " + index + "]").next()
                                        .next().addClass('error');
                                    $("select[name = " + index + "]").next()
                                        .next().html(html);
                                    $("select[name = " + index + "]").next()
                                        .next().show();
                                    // $("select[name = " + index + "]").show();
                                } else if (index ==
                                    'variantsDataArr') {
                                    $('.variantsTabErrorDiv').addClass('error');
                                    $('.variantsTabErrorDiv').html(html).show();

                                } else {

                                    $("input[name = " + index + "]").addClass('is-invalid');
                                    $("input[name = " + index + "]").next().addClass(
                                        'error');
                                    $("input[name = " + index + "]").next().html(html);
                                    $("input[name = " + index + "]").show();
                                }


                            });
                        } else if (datas['status'] == 'error') {
                            show_message(datas['msg'], 'error');

                        } else {
                            show_message(datas, 'error');

                        }


                    }
                    $(that).prop('disabled', false);
                    $(that).text($btnName);
                },
                error: function(xhr, status, errorThrown) {
                    if (xhr.responseJSON && xhr.responseJSON.status) {
                        if (xhr.responseJSON.status == 'error') {

                            show_message(xhr.responseJSON.msg, 'error');
                        } else {
                            show_message(xhr.responseJSON, 'error');
                        }
                    } else {
                        show_message(xhr.responseText, 'error');
                    }
                    $(that).prop('disabled', false);
                    $(that).text($btnName);

                }
            });
        })



        $('#specificationValuesSelect').select2({
            // tags: true,
            placeholder: 'Select Values',
            tokenSeparators: [',', ' '], // Separate tags by comma or space
            createTag: function(params) {
                return {
                    id: params.term,
                    text: params.term,
                    newTag: true
                };
            }
        });
    </script>
@endpush
