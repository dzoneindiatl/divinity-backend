@extends('admin.layout.master')
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">

    <link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.css') }}">

    <link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/css/picker.css') }}" rel="stylesheet" type="text/css" />

    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" /> -->

    <link href="{{ asset('assets/css/coloris.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">


    <script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>

    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

<style>
    .accordion-button {

        cursor: pointer;


    }

    div#clr-picker {
        z-index: 9999;
    }

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


    .modal {
        display: none;
        position: fixed;
        z-index: 1050;
        overflow: hidden;
        outline: 0;
    }

    .modal-dialog {
        max-width: 500px;
    }

    .img-preview {
        max-width: 100px;
        max-height: 100px;
        margin: 5px;
    }

    .img-thumbnail {
        max-width: 100px;
        max-height: 100px;
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

    .form-switch .form-check-input.portfolioImgAciveInput {
        margin-left: 7em !important;
    }
</style>



@section('content')

    @include('admin.layout.response_message')

    <!-- Page Header -->

    <?php // print_r($errors->all()); die;
    // print_r(session()->get('currentProductId')); die;
    ?>

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <a class="btn btn-dark" href="{{ url()->previous() }}"> Back </a>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ ucfirst($type) }} Product</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="" method="post" id="productForm" enctype="multipart/form-data">
            <div class="col-12 accordion" id="accordian-product">
                <input type="hidden" name="newProductid" value="{{ isset($product) ? $product->id : 0 }}">

                @csrf

                <div class="card custom-card accordion-item">

                    <div class="card-header accordion-button" data-bs-toggle="collapse" data-bs-target="#basicinfo-section"
                        aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">

                        <div class="card-title">

                            {{ ucfirst($type) }} Product

                        </div>

                    </div>


                    <div class="card-body accordion-collapse collapse show" id="basicinfo-section">

                        <div class="row">

                            <div class="col-12">

                                <div class="card-body p-0">

                                    <div class="mb-3">

                                        <label for="name" class="form-label"><span class="text-danger">*
                                            </span>Title</label>

                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" placeholder="Enter Product Name"
                                            value="{{ old('name', isset($product) ? $product->name : '') }}">


                                        <div class="invalid-feedback" id="nameError">

                                            {{ $errors->first('name') }}

                                        </div>


                                    </div>

                                </div>

                            </div>

                            <div class="col-4">

                                <div class="card-body p-0">

                                    <div class="mb-3">

                                        <label for="name" class="form-label"><span class="text-danger">*
                                            </span>SKU</label>

                                        <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                            id="sku" name="sku"
                                            value="{{ isset($product) ? $product->sku : old('sku') }}"
                                            placeholder="Enter Product SKU">

                                        <div class="invalid-feedback" id="skuError">

                                            {{ $errors->first('sku') }}

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="col-4">

                                <div class="card-body p-0">

                                    <div class="mb-3">

                                        <label for="name" class="form-label">HSN</label>

                                        <input type="text" class="form-control @error('hsn') is-invalid @enderror"
                                            id="hsn" name="hsn"
                                            value="{{ isset($product) ? $product->hsn : old('hsn') }}"
                                            placeholder="Enter Product HSN">

                                    </div>

                                </div>

                            </div>

                            <div class="col-4">

                                <div class="card-body p-0">

                                    <div class="mb-3">

                                        <label for="name" class="form-label">Bar Code</label>

                                        <input type="text" class="form-control @error('bar_code') is-invalid @enderror"
                                            id="bar_code" name="bar_code"
                                            value="{{ isset($product) ? $product->bar_code : old('bar_code') }}"
                                            placeholder="Enter Product Bar Code">

                                        @if ($errors->has('bar_code'))
                                            <div class="invalid-feedback">

                                                {{ $errors->first('bar_code') }}

                                            </div>
                                        @endif

                                    </div>

                                </div>

                            </div>



                            <div class="col-12">

                                <div class="card-body p-0">

                                    <div class="mb-3">

                                        <label for="massage" class="form-label">Short Description</label>

                                        <textarea type="text" class="form-control @error('massage') is-invalid @enderror" id="massage" name="massage"
                                            value="" placeholder="Enter Product massage">{{ isset($product) ? $product->list_description : old('massage') }}</textarea>

                                        <div class="invalid-feedback" id="massageError">

                                            {{ $errors->first('massage') }}

                                        </div>

                                    </div>

                                </div>

                            </div>
                            <div class="col-6 mb-3">

                                <label for="description" class="form-label">Description</label>

                                <textarea class="form-control @error('title') is-invalid @enderror" name="description" id="description"
                                    cols="30" rows="5">{!! isset($product->description) ? $product->description : old('description') !!}</textarea>

                                @if ($errors->has('description'))
                                    <div class=" invalid-feedback">

                                        {{ $errors->first('description') }}

                                    </div>
                                @endif

                            </div>

                            <div class="col-6 mb-3">

                                <label for="meta_description" class="form-label"><span class="text-danger">*
                                    </span>Specification</label>

                                <textarea class="form-control @error('specification') is-invalid @enderror" name="specification" id="specification"
                                    cols="30" rows="5">{!! isset($product->specification) ? $product->specification : old('specification') !!}</textarea>



                                <div class="invalid-feedback" id="specificationError">

                                    {{ $errors->first('specification') }}

                                </div>


                            </div>

                            <div class="col-6 mb-3">

                                <label for="product_details" class="form-label">Product Details</label>

                                <textarea class="form-control @error('title') is-invalid @enderror" name="product_details" id="product_details"
                                    cols="30" rows="5">{!! isset($product->product_details) ? $product->product_details : old('product_details') !!}</textarea>

                            </div>

                            <div class="col-6 mb-3">

                                <label for="others" class="form-label">Others</label>

                                <textarea class="form-control @error('others') is-invalid @enderror" name="others" id="others" cols="30"
                                    rows="5">{!! isset($product->others) ? $product->others : old('others') !!}</textarea>

                            </div>


                            <div class="col-3">
                                <div class="card-body p-0">
                                    <div class="mb-3">
                                        <label for="material" class="form-label"><span class="text-danger">*
                                            </span>Material</label>
                                        <input type="text" class="form-control @error('material') is-invalid @enderror"
                                            id="material" name="material"
                                            value="{{ isset($product) ? $product->material : old('material') }}"
                                            placeholder="Enter Product Material">
                                        <div class="invalid-feedback" id="materialError">
                                            {{ $errors->first('material') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="card-body p-0">
                                    <div class="mb-3">
                                        <label for="weight" class="form-label"><span
                                                class="text-danger">*</span>Weight</label>
                                        <input type="text" class="form-control @error('weight') is-invalid @enderror"
                                            id="weight" name="weight"
                                            value="{{ isset($product) ? $product->weight : old('weight') }}"
                                            placeholder="Enter Product Weight">

                                        <div class="invalid-feedback" id="weightError">
                                            {{ $errors->first('weight') }}
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="card-body p-0">
                                    <div class="mb-3">
                                        <label for="weight_type" class="form-label"><span
                                                class="text-danger">*</span>Weight Type </label>
                                        <select
                                            class="select2-original form-control @error('weight_type') is-invalid @enderror"
                                            name="weight_type" id="weight_type" required>
                                            <option value="">Select Type</option>
                                            <option value="grm"
                                                {{ (isset($product) && $product->weight_type == 'grm') || old('weight_type') == 'grm' ? 'selected' : '' }}>
                                                GRM</option>
                                            <option value="kg"
                                                {{ (isset($product) && $product->weight_type == 'kg') || old('weight_type') == 'kg' ? 'selected' : '' }}>
                                                KG</option>
                                        </select>

                                        <div class="invalid-feedback" id="weight_typeError">
                                            {{ $errors->first('weight_type') }}
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="col-2">
                                <div class="card-body p-0">
                                    <div class="mb-3">
                                        <label for="style" class="form-label"><span class="text-danger">*
                                            </span>Style</label>
                                        <input type="text" class="form-control @error('style') is-invalid @enderror"
                                            id="style" name="style"
                                            value="{{ isset($product) ? $product->style : old('style') }}"
                                            placeholder="Enter Product Style">
                                        <div class="invalid-feedback" id="styleError">
                                            {{ $errors->first('style') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="card-body p-0">
                                    <div class="mb-3">
                                        <label for="country_origin" class="form-label">Country Of Origin</label>
                                        <input type="text"
                                            class="form-control @error('country_origin') is-invalid @enderror"
                                            id="country_origin" name="country_origin"
                                            value="{{ isset($product) ? $product->country_origin : old('country_origin') }}"
                                            placeholder="Enter Product Country Origin">
                                        @if ($errors->has('country_origin'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('country_origin') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>



                            <div class="col-12 mb-3">

                                <label for="wash_care" class="form-label"><span class="text-danger">* </span>Wash
                                    Care</label>

                                <textarea class="form-control @error('wash_care') is-invalid @enderror" name="wash_care" id="wash_care"
                                    cols="30" rows="5">{!! isset($product->wash_care) ? $product->wash_care : old('wash_care') !!}</textarea>

                                <div class="invalid-feedback" id="washcareError">

                                    {{ $errors->first('wash_care') }}

                                </div>

                            </div>

                            <div class="col-12 mb-3">
                                <label for="tags" class="form-label">Tags</label>
                                <select class="js-example-placeholder-single js-states form-control" name="product_tags[]"
                                    id="product_tags" multiple="multiple" required>
                                    @forelse ($producttags as $producttag)
                                        <option value="{{ $producttag->id }}"
                                            {{ in_array($producttag->id, explode(',', $product->product_tags ?? '')) ? 'selected' : '' }}>
                                            {{ $producttag->name }}
                                        </option>
                                    @empty
                                        <option value="" selected>No Data found</option>
                                    @endforelse
                                </select>


                            </div>

                        </div>

                    </div>

                </div>


                <div class="card custom-card accordion-item">
                    <div class="card-header accordion-button" data-bs-toggle="collapse"
                        data-bs-target="#category-section" aria-expanded="true"
                        aria-controls="panelsStayOpen-collapseOne">
                        <div class="card-title">Categories</div>
                    </div>
                    <div class="card-body accordion-collapse collapse show" id="category-section">
                        <div class="row">
                            <div class="col-3">

                                <div class="card-body p-0">

                                    <div class="mb-3">

                                        <label for="brand_id" class="form-label">

                                            <span class="text-danger"></span>Brand

                                        </label>

                                        <select class="select2-original form-control" name="brand_id" id="brand_id">
                                            <option value="">None</option>

                                            @forelse ($brands as $brand)
                                                <option value="{{ $brand->id }}"
                                                    {{ (!empty($product->brand_id) && $product->brand_id == $brand->id) || $brand->is_primary == 1 ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                            @empty
                                                <option value="" selected>No Data found</option>
                                            @endforelse
                                        </select>

                                    </div>

                                </div>

                            </div>
                            <div class="col-3">

                                <div class="card-body p-0">

                                    <div class="mb-3">

                                        <label for="category" class="form-label"><span class="text-danger">*
                                            </span>Category</label>

                                        <select class="form-control @error('category_id') is-invalid @enderror"
                                            name="category_id" id="prdct_category_id"
                                            onchange="getRelatedSubCategories();">

                                            <option value="">None</option>

                                            @forelse ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id', !empty($product) ? $parent_category_id : '') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @empty
                                                @if (old('category_id'))
                                                    <option value="{{ old('category_id') }}" selected>
                                                        {{ old('category_id') }}
                                                    </option>
                                                @else
                                                    <option disabled selected>No categories available</option>
                                                @endif
                                            @endforelse

                                        </select>

                                        <div class="invalid-feedback" id="categoryidError">

                                            {{ $errors->first('prdct_category_id') }}

                                        </div>

                                    </div>

                                </div>

                            </div>
                            <div class="col-3 subCategorieHide" {{ isset($product) ? '' : 'style=display:none' }}>

                                <div class="card-body p-0">

                                    <div class="mb-3">

                                        <label for="sub_category_id" class="form-label">Subcategory</label>

                                        <select name="sub_category_id" id="prdct_sub_category_id"
                                            class="js-example-placeholder-single js-states form-control"
                                            onchange="getchildcategory();">
                                            <option value="">Select Subcategory</option>
                                            @if (isset($product->category->parentcategory->parent_id))
                                                <option value="{{ $product->category->parentcategory->id }}" selected>
                                                    {{ $product->category->parentcategory->name }}</option>
                                            @elseif(isset($product->category->parent_id))
                                                <option value="{{ $product->category->id }}" selected>
                                                    {{ $product->category->name }}</option>
                                            @endif
                                        </select>

                                    </div>

                                </div>

                            </div>
                            <div class="col-3 childCategoryHide" {{ isset($product) ? '' : 'style=display:none' }}>

                                <div class="card-body p-0">

                                    <div class="mb-3">

                                        <label for="child_category_id" class="form-label">Child Category</label>

                                        <select name="child_category_id" id="prdct_child_category_id"
                                            class="js-example-placeholder-single js-states form-control">
                                            <option value="">Select Child category</option>
                                            @if (isset($product->category->parentcategory->parent_id))
                                                <option value="{{ $product->category->parentcategory->parent_id }}"
                                                    selected>
                                                    @foreach ($categories as $category)
                                                        {{ isset($product->category->parentcategory->parent_id) && $product->category->parentcategory->parent_id == $category->id ? $product->category->name : '' }}
                                                    @endforeach
                                                </option>
                                            @endif
                                        </select>

                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="card custom-card accordion-item">

                    <div class="card-header accordion-button" data-bs-toggle="collapse" data-bs-target="#price-section"
                        aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">

                        <div class="card-title"> Related products </div>

                    </div>

                    <div class="card-body accordion-collapse collapse show" id="price-section">

                        <div class="row">

                            <div class="col-4">

                                <div class="card-body p-0">

                                    <div class="mb-3">

                                        <label for="categorys_id" class="form-label">Category</label>

                                        <select name="categorys_id" id="categorys_id"
                                            class="js-example-placeholder-single js-states form-control"
                                            onchange="getsubcategory(this.value);">

                                            <option value="">Select Category</option>
                                            @if (!empty($product) && isset($product))
                                                @foreach ($categories as $categories)
                                                    <option value="{{ $categories->id }}"
                                                        {{ !empty($product->related_product_categores_id) && $product->related_product_categores_id == $categories->id ? 'selected' : '' }}>
                                                        {{ $categories->name }}
                                                    </option>
                                                @endforeach
                                            @else
                                                @foreach ($categories as $categories)
                                                    <option value="{{ $categories->id }}">
                                                        {{ ucfirst($categories->name) }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="invalid-feedback" id="categorysidError">
                                            {{ $errors->first('categorys_id') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-4">

                                <div class="card-body p-0">

                                    <div class="mb-3">

                                        <label for="subcategory_id" class="form-label">Subcategory</label>

                                        <select name="subcategory_id" id="subcategory_id"
                                            class="js-example-placeholder-single js-states form-control"
                                            onchange="getproduct(this.value);">
                                            <option value="">Select Subcategory</option>

                                            @if (!empty($product) && isset($product))
                                                @foreach ($subcategory as $subcategorye)
                                                    <option value="{{ $subcategorye->id }}"
                                                        {{ $subcategorye->id == $product->related_product_subcategory_id ? 'selected' : '' }}>
                                                        {{ ucfirst($subcategorye->name) }}
                                                    </option>
                                                @endforeach
                                            @endif

                                        </select>

                                    </div>

                                </div>

                            </div>
                            @if (!empty($product) && isset($product))
                                @php

                                    if (is_string($product->related_products)) {
                                        $product->related_products = explode(',', $product->related_products);
                                    }

                                @endphp
                            @endif
                            <div class="col-4">
                                <div class="card-body p-0">
                                    <div class="mb-3">
                                        <label for="Productid" class="form-label">Related Product</label>
                                        <select name="Product_id[]" id="Productid"
                                            class="js-example-placeholder-single js-states form-control" multiple>
                                            <option value="">Select Product</option>
                                            @if (!empty($product) && isset($product))
                                                @foreach ($subproducts as $products)
                                                    <option value="{{ $products->id }}"
                                                        {{ is_array($product->related_products) && in_array($products->id, $product->related_products) ? 'selected' : '' }}>
                                                        {{ ucfirst($products->name) }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card custom-card accordion-item">
                    <div class="card-header accordion-button" data-bs-toggle="collapse"
                        data-bs-target="#attributes-section" aria-expanded="true" aria-controls="attributes-section">
                        <div class="card-title">Attributes</div>
                    </div>
                    <div class="card-body accordion-collapse collapse show" id="attributes-section">
                        <div class="row" id="attribute-pairs-container">
                            <!-- Attribute-value pairs will be inserted here -->
                            @if (isset($product))
                                @php
                                    $getColorDetails = [];
                                    $sub_available_units = '0';
                                    $getAttrDetails = \App\Models\ProductAttribute::where('product_id', $product['id'])
                                        ->get()
                                        ->toArray();
                                    $getAttrValDetails = \App\Models\ProductAttributeValue::where(
                                        'product_id',
                                        $product['id'],
                                    )
                                        ->get()
                                        ->toArray();
                                @endphp
                                @if (!empty($getAttrDetails))
                                    @foreach ($getAttrDetails as $attsval)
                                        <div
                                            class="row mb-3 attribute-pair removeeditoption{{ $attsval['attribute_id'] }}">
                                            <div class="col-4">
                                                <div class="card-body p-0">
                                                    <div class="mb-3">
                                                        <label class="form-label">Attribute</label>
                                                        <select name="attribute_ids[]"
                                                            class="form-control attribute-select"
                                                            onchange="getAttributeValues(this)">
                                                            <option value="select value">Select Attribute</option>
                                                            @if (!empty($attributes))
                                                                @foreach ($attributes as $attval)
                                                                    <option
                                                                        @if ($attsval['attribute_id'] == $attval->id) selected @endif
                                                                        value="{{ $attval->id }}">{{ $attval->name }}
                                                                    </option>
                                                                @endforeach
                                                            @endif

                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="card-body p-0">
                                                    <div class="mb-3">
                                                        @php
                                                            $getAttrValDetail = \App\Models\AttributeValue::where(
                                                                'attribute_id',
                                                                $attsval['attribute_id'],
                                                            )
                                                                ->get()
                                                                ->toArray();
                                                        @endphp
                                                        <label class="form-label">Attribute Value</label>
                                                        <select name="attribute_value_ids[]"
                                                            class="form-control value-select">
                                                            <option value="">Select Attribute Value</option>
                                                            @if (!empty($getAttrValDetail))
                                                                @foreach ($getAttrValDetail as $attsval)
                                                                    <option
                                                                        {{ isset($getAttrValDetails) && array_search($attsval['id'], array_column($getAttrValDetails, 'attribute_value_id')) !== false ? 'selected' : '' }}
                                                                        value="{{ $attsval['id'] }}">
                                                                        {{ $attsval['name'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4 d-flex align-items-center">
                                                <div class="card-body p-0">
                                                    <button class="btn btn-danger"
                                                        onclick="removeCustomBox('removeeditoption{{ $attsval['attribute_id'] }}')"
                                                        type="button">Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button id="add-attribute-button" class="btn btn-primary" type="button">+ Select
                                    Attribute</button>
                                <button id="add-new-attribute-button" class="btn btn-secondary" type="button">+ Add New
                                    Attribute</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="addAttributeModal" tabindex="-1" aria-labelledby="addAttributeModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addAttributeModalLabel">Add New Attribute</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="attributes_container">
                                <div class="mb-3">
                                    <label for="attributeName" class="form-label">Attribute Name</label>
                                    <select class="form-select attribute-select" id="attributeName" name="attributeName">
                                        <!-- Options can be added dynamically or pre-defined here -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="attributeValue" class="form-label">Attribute Value</label>
                                    <input type="text" class="form-control" id="attributeValue">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" id="saveAttributeButton" class="btn btn-primary">Save
                                    Attribute</button>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="card custom-card accordion-item configurable">
                </div>


                <div class="card custom-card accordion-item">

                    <div class="card-header accordion-button" data-bs-toggle="collapse" data-bs-target="#price-section"
                        aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                        <div class="card-title">Pricing</div>
                    </div>

                    <div class="card-body accordion-collapse collapse show" id="price-section">
                        <div class="row">
                            <div class="col-3">
                                <div class="card-body p-0">
                                    <div class="mb-3">
                                        <label for="name" class="form-label"><span class="text-danger">*
                                            </span>MRP</label>
                                        <input type="text"
                                            class="form-control @error('buying_price') is-invalid @enderror"
                                            id="buying_price" name="buying_price" placeholder="Enter Product Price"
                                            value="{{ isset($product) ? $product->buying_price : old('buying_price') }}">
                                        @if ($errors->has('buying_price'))
                                            <div class="invalid-feedback">{{ $errors->first('buying_price') }}</div>
                                        @endif
                                    </div>
                                    <div class="invalid-feedback" id="buyingpriceError">
                                        {{ $errors->first('buying_price') }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="card-body p-0">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Discount</label>
                                        <input type="number"
                                            class="form-control @error('discount') is-invalid @enderror" id="discount"
                                            name="discount" value="{{ isset($product) ? $product->discount : 0 }}"
                                            placeholder="Enter Product discount">
                                        @if ($errors->has('discount'))
                                            <div class="invalid-feedback">{{ $errors->first('discount') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="card-body p-0">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Discount type</label>
                                        <select name="discount_type" id="discount_type"
                                            class="js-example-placeholder-single js-states form-control">
                                            <option value="flat"
                                                {{ isset($product) && $product->discount_type == 'flat' ? 'selected' : '' }}>
                                                Flat</option>
                                            <option value="percentage"
                                                {{ isset($product) && $product->discount_type == 'percentage' ? 'selected' : '' }}>
                                                Percentage</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="card-body p-0">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Selling Price</label>
                                        <input type="text"
                                            class="form-control @error('selling_price') is-invalid @enderror"
                                            id="selling_price" name="selling_price"
                                            value="{{ isset($product) ? $product->selling_price : old('selling_price') }}"
                                            readonly>
                                        @if ($errors->has('selling_price'))
                                            <div class="invalid-feedback">{{ $errors->first('selling_price') }}</div>
                                        @endif
                                    </div>
                                    <div class="invalid-feedback" id="sellingpriceError">
                                        {{ $errors->first('selling_price') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <h5>Taxes</h5>
                            <div class="col-3">
                                <div class="card-body p-0">
                                    <div class="mb-3">
                                        <div class="form-radio">
                                            <input class="form-radio-input" name="is_including_taxes" type="radio"
                                                value="1" id="flexCheckInclusiveTaxes"
                                                {{ isset($product) && $product->is_including_taxes ? 'checked' : 'checked' }}>
                                            <label class="form-label form-radio-label"
                                                for="flexCheckInclusiveTaxes">Inclusive of Taxes?</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="card-body p-0">
                                    <div class="mb-3">
                                        <div class="form-radio">
                                            <input class="form-radio-input" name="is_including_taxes" type="radio"
                                                value="0" id="flexCheckExclusiveTaxes"
                                                {{ isset($product) && !$product->is_including_taxes ? 'checked' : '' }}>
                                            <label class="form-label form-radio-label"
                                                for="flexCheckExclusiveTaxes">Exclusive of Taxes?</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <div class="card custom-card accordion-item">
                            <div class="card-header accordion-button" data-bs-toggle="collapse"
                                data-bs-target="#product-type-section" aria-expanded="true"
                                aria-controls="panelsStayOpen-collapseOne">
                                <div class="card-title">
                                    Product Type
                                </div>
                            </div>
                            <div class="card-body accordion-collapse collapse show" id="product-type-section">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card-body p-0">
                                            <div class="mb-3">
                                                <label for="product_type" class="form-label"><span
                                                        class="text-danger">*</span>Product Type</label>
                                                <select name="product_type" id="product_type"
                                                    class="form-control @error('product_type') is-invalid @enderror"
                                                    onchange="displayDiv(this.value)">
                                                    @if (!empty($product))
                                                        <option value="">Select Type</option>
                                                        <option value="1"
                                                            {{ $product->product_type == 1 ? 'selected' : '' }}>Simple
                                                        </option>
                                                        <option value="2"
                                                            {{ $product->product_type == 2 ? 'selected' : '' }}>
                                                            Configurable
                                                        </option>
                                                    @else
                                                        <option value="">Select Type</option>
                                                        <option value="1">Simple</option>
                                                        <option value="2" selected>Configurable</option>
                                                    @endif
                                                </select>
                                                <div class="invalid-feedback" id="producttypeError">
                                                    {{ $errors->first('product_type') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="card custom-card accordion-item">
                            <div class="card-header accordion-button" data-bs-toggle="collapse"
                                data-bs-target="#max-selling-units-section" aria-expanded="true"
                                aria-controls="panelsStayOpen-collapseOne">
                                <div class="card-title">
                                    Selling Units
                                </div>
                            </div>
                            <div class="card-body accordion-collapse collapse show" id="max-selling-units-section">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="card-body p-0">
                                            <div class="mb-3">
                                                <label for="name" class="form-label"><span class="text-danger">*
                                                    </span>Maximum Selling Units</label>
                                                <input type="number"
                                                    class="form-control @error('max_selling_units') is-invalid @enderror"
                                                    id="max_selling_units" name="max_selling_units"
                                                    placeholder="Enter Maximum Selling Units"
                                                    value="{{ isset($product) ? $product->max_selling_units : old('max_selling_units') }}">
                                                @if ($errors->has('max_selling_units'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('max_selling_units') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card-body p-0">
                                            <div class="mb-3">
                                                <label for="name" class="form-label"><span class="text-danger">*
                                                    </span>Minimum Selling Units</label>
                                                <input type="number"
                                                    class="form-control @error('min_selling_units') is-invalid @enderror"
                                                    id="min_selling_units" name="min_selling_units"
                                                    placeholder="Enter Minimun Selling Units"
                                                    value="{{ isset($product) ? $product->min_selling_units : old('min_selling_units') }}">
                                                @if ($errors->has('min_selling_units'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('min_selling_units') }}
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

                <div class="col-4">
                    <div class="card-body p-0">
                        <div class="mb-3">
                            <label for="collectionsid" class="form-label">Collections</label>
                            <select name="collection_ids[]" id="collectionsid"
                                class="js-example-placeholder-single js-states form-control" multiple>
                                @if (!empty($collections))
                                    @foreach ($collections as $collectionKey => $collectionValue)
                                        <option value="{{ $collectionKey }}"
                                            {{ !empty($product->collection_ids) &&
                                            is_string($product->collection_ids) &&
                                            in_array($collectionKey, explode(',', $product->collection_ids))
                                                ? 'selected'
                                                : '' }}>
                                            {{ ucfirst($collectionValue) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card custom-card accordion-item" id="variant-section">
                    <div class="card-header accordion-button" data-bs-toggle="collapse" data-bs-target="#variant-section"
                        aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                        <div class="card-title" style="width: 100%;">
                            <div class="row">
                                <div class="col-10">Variants</div>

                            </div>

                        </div>
                    </div>
                    <div class="card-body default-variant-section accordion-collapse collapse show">
                        @php
                            $getColorDetails = [];
                            $sub_available_units = '0';
                            if (!empty($product['id'])) {
                                $getColorDetails = \App\Models\ProductVariantCombination::where(
                                    'product_id',
                                    $product['id'],
                                )
                                    ->get()
                                    ->toArray();
                                $sub_available_units = \App\Models\ProductVariantCombination::where(
                                    'product_id',
                                    $product['id'],
                                )->sum('available_units');
                            }

                        @endphp
                        <div class="row">
                            <div class="col-md-5 form-group ">
                                <label>Color</label>
                                <select name="color_variants[]" id="color-variants-select"
                                    class="js-example-placeholder-single js-states form-control variant_name" multiple>
                                    <option value="add_item_color">Add a item</option>
                                    @if (!empty($colors) && isset($colors))
                                        @foreach ($colors as $color)
                                            <option
                                                {{ isset($getColorDetail) && array_search($color->id, array_column($getColorDetail, 'color_variant_value_id')) !== false ? 'selected' : '' }}
                                                value="{{ $color->id }}">{{ ucfirst($color->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4 form-group ">
                                <label>Size</label>
                                <select name="size_variants[]" id="size-variants-select"
                                    class="js-example-placeholder-single js-states form-control variant_name" multiple>
                                    <option value="add_item_size">Add a item</option>
                                    @if (!empty($sizes) && isset($sizes))
                                        @foreach ($sizes as $size)
                                            <option
                                                {{ isset($getColorDetails) && array_search($size->id, array_column($getColorDetails, 'size_variant_value_id')) !== false ? 'selected' : '' }}
                                                value="{{ $size->id }}">{{ ucfirst($size->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-2 form-group ">
                                <label>Total Availabe</label>
                                <input type="text" class="form-control" id="totalDisplay"
                                    value="{{ $sub_available_units }}" disabled>
                            </div>
                        </div>

                        <div class="accordion" id="variant-details">


                            @if (!empty($getColorDetail) && $product->product_type == 2)
                                @foreach ($getColorDetail as $colorval)
                                    @php
                                        $sub_available_unit = \App\Models\ProductVariantCombination::where(
                                            'product_id',
                                            $product['id'],
                                        )
                                            ->where('color_variant_value_id', $colorval['color_variant_value_id'])
                                            ->sum('available_units');
                                    @endphp
                                    <div class="accordion-item"
                                        id="color-group-{{ $colorval['color_variant_value_id'] }}">
                                        <div class="accordion-header"
                                            id="color-group-{{ $colorval['color_variant_value_id'] }}">
                                            <div class="accordion-button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse-{{ $colorval['color_variant_value_id'] }}"
                                                aria-expanded="true"
                                                aria-controls="collapse-{{ $colorval['color_variant_value_id'] }}">
                                                <button type="button" class="btn btn-primary upload-variant-image-btn"
                                                    openuploadmodal({{ $colorval['color_variant_value_id'] }})=""
                                                    data-color-id="{{ $colorval['color_variant_value_id'] }}"
                                                    data-product-id="{{ $product['id'] }}">Upload Images</button>
                                                <div class="col-md-2 form-group mb-0">
                                                    {{ $colorval['get_color_detail']['name'] ?? '' }} <span
                                                        class="variant-count-{{ $colorval['color_variant_value_id'] }}">{{ count($colorval['product_variant_combination']) }}
                                                        Variants</span></div>
                                                <div class="col-md-2 form-group mb-0"><input
                                                        class=" form-control total_var_mrp mrp_price total-selling-price-{{ $colorval['color_variant_value_id'] }}"
                                                        type="text" placeholder="MRP"
                                                        value="{{ $colorval['variant_mrp'] }}"
                                                        data-colorid="{{ $colorval['color_variant_value_id'] }}"> </div>
                                                <div class="col-md-2 form-group mb-0"><input
                                                        class=" form-control total-available-unit-{{ $colorval['color_variant_value_id'] }}"
                                                        type="text" placeholder="Total Available Units"
                                                        value="{{ $sub_available_unit }}" disabled=""> </div>
                                            </div>
                                        </div>
                                        <div id="collapse-{{ $colorval['color_variant_value_id'] }}"
                                            class="accordion-collapse collapse show"
                                            aria-labelledby="color-group-{{ $colorval['color_variant_value_id'] }}"
                                            data-bs-parent="#variant-details">
                                            <div class="size-groups">
                                                @php
                                                    $getColorDetail = \App\Models\ProductVariantCombination::where(
                                                        'product_id',
                                                        $colorval['product_id'],
                                                    )
                                                        ->where(
                                                            'color_variant_value_id',
                                                            $colorval['color_variant_value_id'],
                                                        )
                                                        ->with('getSize', 'getColorDetail')
                                                        ->get()
                                                        ->toArray();
                                                @endphp


                                                @if (!empty($getColorDetail))
                                                    @foreach ($getColorDetail as $key => $varval)
                                                        <div class="row"
                                                            data-color-id="{{ $colorval['color_variant_value_id'] }}">
                                                            <div class="col-md-1 form-group mt-3 text-center">
                                                                <h6>{{ ucwords($varval['get_size']['name'] ?? '') }}</h6>
                                                            </div>
                                                            <div class="col-md-2 form-group mt-3">
                                                                <input type="text" class="form-control skuChangeValue"
                                                                    placeholder="SKU"
                                                                    data-color="{{ $varval['get_color_detail']['name'] ?? '' }}"
                                                                    data-size="{{ $varval['get_size']['name'] ?? '' }}"
                                                                    name="variant_sku[{{ $colorval['color_variant_value_id'] }}][{{ $varval['size_variant_value_id'] }}]"
                                                                    value="{{ $varval['sku'] }}" readonly>
                                                            </div>
                                                            <div class="col-md-2 form-group mt-3">
                                                                <input type="number"
                                                                    data-color_id="{{ $colorval['color_variant_value_id'] }}"
                                                                    data-size="{{ $varval['size_variant_value_id'] }}"
                                                                    class="form-control price var_mrp_price mrp_price v_price_{{ $colorval['color_variant_value_id'] }}"
                                                                    placeholder="MRP"
                                                                    name="variant_selling_price[{{ $colorval['color_variant_value_id'] }}][{{ $varval['size_variant_value_id'] }}]"
                                                                    value="{{ $varval['variant_mrp'] }}"
                                                                    id="variant_selling_price[{{ $colorval['color_variant_value_id'] }}][{{ $varval['size_variant_value_id'] }}]">
                                                            </div>
                                                            <div class="col-md-2 form-group mt-3">
                                                                <input type="number" min="0"
                                                                    class="form-control price"
                                                                    placeholder="Total Available Units"
                                                                    name="variant_available_unit[{{ $colorval['color_variant_value_id'] }}][{{ $varval['size_variant_value_id'] }}]"
                                                                    value="{{ $varval['available_units'] }}"
                                                                    onkeyup="updateTotals({{ $colorval['color_variant_value_id'] }})">
                                                            </div>
                                                            <div class="col-md-2 form-group mt-3">
                                                                <input type="number"
                                                                    class="form-control sell_price price var_sp{{ $colorval['color_variant_value_id'] }} sub_sp_{{ $colorval['color_variant_value_id'] }}{{ $varval['size_variant_value_id'] }}"
                                                                    placeholder="Selling Price"
                                                                    name="variant_selling_unit[{{ $colorval['color_variant_value_id'] }}][{{ $varval['size_variant_value_id'] }}]"
                                                                    value="{{ $varval['selling_price'] }}" readonly>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif

                                                @php
                                                    $groupedImages = \App\Models\ProductColorImage::where(
                                                        'product_id',
                                                        $colorval['product_id'],
                                                    )
                                                        ->where('color_id', $colorval['color_variant_value_id'])
                                                        ->get();
                                                @endphp

                                                @if ($groupedImages->isNotEmpty())
                                                    <div class="color-group" style="margin-bottom: 20px;">
                                                        <div class="image-container">
                                                            @foreach ($groupedImages as $colorId => $images)
                                                                <div class="position-relative d-inline-block">
                                                                    <img src="{{ config('constant.PRODUCT_IMAGE_URL') . $images['image'] }}"
                                                                        class="img-thumbnail" />
                                                                    <i class="bi bi-x-circle delete-icon"
                                                                        data-url="{{ route('admin-product-delete-image-new') }}"
                                                                        data-name="{{ $images['image'] }}"
                                                                        style="position: absolute; top: 0; right: 0; cursor: pointer; color: #ff0000;"
                                                                        title="Delete Image"></i>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="modal fade" id="uploadVariantImageModal" tabindex="-1"
                        aria-labelledby="uploadModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="uploadModalLabel">Upload Images</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label for="meta_description" class="form-label">Media</label>
                                            <div id="dropzone-variant-images" class="dropzone"></div>
                                            <div class="card border bg-transparent mt-3 loadVariantImagesData"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body default-variant-section accordion-collapse collapse show"
                        id="attribute-value-section">

                        <div id="accordionvaluesection">

                        </div>
                    </div>

                </div>
            </div>

            <div class="card custom-card accordion-item" id="simple-product-section" style="display: none;">
                <div class="row">
                    <div class="col-12">
                        <div class="card-header accordion-button" data-bs-toggle="collapse"
                            data-bs-target="#simple-product-section-body" aria-expanded="true"
                            aria-controls="simple-product-section-body">
                            <div class="card-title">Variants</div>
                        </div>
                        <div class="card-body accordion-collapse collapse show" id="simple-product-section-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card-body p-0">
                                        <div class="row">

                                            <div class="row">



                                                <div class="form-group col-md-4">
                                                    <label for="variant_value" class="form-label">Variant Value</label>
                                                    <select id="simplevariant" name="simplevariant" class="form-control"
                                                        onchange="displayVariantValues(this.value)">
                                                        <option value="">None</option>
                                                        @foreach ($simpleVariants as $variantKey => $variant)
                                                            <option value="{{ $variantKey }}"
                                                                @if (isset($simpleVeriantValue->variant_id) && $simpleVeriantValue->variant_id == $variantKey) selected @endif>
                                                                {{ $variant }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Values Select Dropdown (Multiple selection) -->
                                                <div class="form-group col-md-4">
                                                    <label for="variant_value" class="form-label">Values</label>
                                                    <select name="variant_valueas[]" id="variant-values"
                                                        class="js-example-placeholder-single js-states form-control variant_values"
                                                        multiple>
                                                        <!-- Dynamic Options will be added here -->
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="variant_value" class="form-label">Total Availabe</label>
                                                    <input type="text" class="form-control"
                                                        id="simple_total_unit_display" value="" disabled="">
                                                </div>

                                                <div id="simple-variant-details">

                                                </div>

                                                {{-- <div class="col-md-4 form-group mt-3">
                                                <div class="form-check">
                                                    <input class="form-check-input variant_value" type="radio" name="variant_type" id="nocolornosize" value="nosizenocolor"  {{ isset($product->productVariants[0]) && $product->productVariants[0]->variant_id==3 ? 'checked' : 'checked' }}>
                                                    <label class="form-check-label" for="nocolornosize">
                                                        No Color No Size
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-2 form-group mt-3">
                                                <div class="form-check">
                                                    <input class="form-check-input variant_value" type="radio" name="variant_type" id="variant_color" value="color"  {{ isset($product->productVariants[0]) && $product->productVariants[0]->variant_id==1 ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="variant_color">
                                                        Color
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-2 form-group mt-3">
                                                <div class="form-check">
                                                    <input class="form-check-input variant_value" type="radio" name="variant_type" id="variant_size" value="size"  {{ isset($product->productVariants[0]) && $product->productVariants[0]->variant_id==2 ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="variant_size">
                                                        Size
                                                    </label>
                                                </div>
                                            </div> --}}
                                            </div>

                                            {{-- <hr> <div class="row">
                                            <div class="col-md-6 form-group color-variant" @if (isset($getColorDetails[0]['color_variant_value_id']) && $getColorDetails[0]['color_variant_value_id'] > 0) @else style="display: none;" @endif>
                                                <label>Color</label>
                                                <select name="color_id" id="colorId" class="js-example-placeholder-single js-states form-control">
                                                    <option value="">Select Color</option>
                                                    @if (!empty($colors) && isset($colors))
                                                        @foreach ($colors as $color)
                                                        <option value="{{ $color->id }}" @if (!empty($getColorDetails[0]['color_variant_value_id']) && $getColorDetails[0]['color_variant_value_id'] == $color->id) selected @endif>{{ ucfirst($color->name) }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-6 form-group size-variant" @if (isset($getColorDetails[0]['size_variant_value_id']) && $getColorDetails[0]['size_variant_value_id'] > 0) @else style="display: none;" @endif>
                                                <label>Size</label>                                    
                                                <select name="size_id" id="sizeId" class="js-example-placeholder-single js-states form-control">
                                                    <option value="">Select Size</option>
                                                    @if (!empty($sizes) && isset($sizes))                                        
                                                        @foreach ($sizes as $size)
                                                        <option value="{{ $size->id }}" @if (!empty($getColorDetails[0]['size_variant_value_id']) && $getColorDetails[0]['size_variant_value_id'] == $size->id) selected @endif>{{ ucfirst($size->name) }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>                                    
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Quantity</label> 
                                                <input type="text" class="form-control" id="qty" name="qty" placeholder="Quantity" value="{{ (isset($product) ? $product->qty : old('qty')) }}">                                
                                            </div>
                                        </div> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="card-body accordion-collapse collapse show" id="media-section">


                            <div class="row">

                                <div class="col-12 mb-3">

                                    <label for="meta_description" class="form-label">Media</label>

                                    <div id="imageDropzone" class="dropzone"></div>

                                </div>

                                <div class="card border bg-transparent mt-3 loadImagesData">

                                    @if (isset($image_data))
                                        @include('admin.product_new.load-images', [
                                            'images' => $image_data,
                                        ])
                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
    </div>

    <div class="card custom-card accordion-item">

        <div class="card-header accordion-button" data-bs-toggle="collapse" data-bs-target="#seo-section"
            aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">

            <div class="card-title">

                SEO Details

            </div>

        </div>

        <div class="card-body accordion-collapse collapse hide" id="seo-section">
            <div class="row">
                <div class="col">
                    <div class="col-12">
                        <div class="card-body p-0">
                            <div class="mb-3">
                                <label for="meta_title" class="form-label">Meta Title</label>
                                <input type="text" class="form-control" id="meta_title" name="meta_title"
                                    placeholder="Enter Product Meta Title"
                                    value="{{ isset($product) ? $product->meta_title : old('meta_title') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card-body p-0">
                            <div class="mb-3">
                                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
                                    placeholder="Enter Product Meta Keywords"
                                    value="{{ isset($product) ? $product->meta_keywords : old('meta_keywords') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="meta_description" class="form-label">Meta Description</label>
                        <textarea class="form-control" name="meta_description" id="meta_description" cols="30" rows="3">{!! isset($product) ? $product->meta_description : old('meta_description') !!}</textarea>
                    </div>
                </div>
                <div class="col">
                    <div class="col-12 mb-3">
                        <label for="seo_content" class="form-label">Site Seo Content</label>
                        <textarea class="form-control" name="seo_content" id="seo_content" cols="30" rows="3">{!! isset($product) ? $product->seo_content : old('seo_content') !!}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal for Image Upload -->
    <!-- <div class="modal" id="imageUploadModal" tabindex="-1" role="dialog">
                                                                                                                                                                                                            <div class="modal-dialog" role="document">
                                                                                                                                                                                                                <div class="modal-content">
                                                                                                                                                                                                                    <div class="modal-header">
                                                                                                                                                                                                                        <h5 class="modal-title">Upload Images</h5>
                                                                                                                                                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                                                                                                                            <span aria-hidden="true">&times;</span>
                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                    <div class="modal-body">
                                                                                                                                                                                                                        <input type="file" id="imageInput" multiple accept="image/*">
                                                                                                                                                                                                                        <div id="imagePreviewContainer" class="d-flex flex-wrap mt-3"></div>
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                    <div class="modal-footer">
                                                                                                                                                                                                                        <button type="button" class="btn btn-primary" id="doneImageUpload">Done</button>
                                                                                                                                                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                </div>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </div> -->



    <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
        <button type="submit" name="draft" value="2" class="btn btn-primary">Save as Draft</button>
        <button type="submit" name="save" style="margin-left: 20px;" value="1" class="btn btn-info">Save &
            Continue</button>
    </div>
    </form>
    </div>



    <!-- Modal -->
    <div class="modal fade" id="addItemModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Another Item</h1>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>

                <div class="modal-body">

                    <div class="non-color">

                        <div class="row">

                            <div class="col-md-12">

                                <label for="add_new_item" class="form-label">Item</label>

                                <input type="hidden" id="add_variant_type" value="size" />

                                <input type="text" class="form-control" id="add_new_item" placeholder="Enter Item" />

                                <input type="text" class="picker form-control" id="color"
                                    placeholder="Enter Item" style="display: none;" />

                            </div>

                        </div>

                    </div>

                    <div class="color" style="display: none;">

                        <div class="row">

                            <input type="hidden" id="add_variant_type" value="color" />

                            <div class="col-md-6">

                                <label for="color_code" class="form-label">Select Color</label>

                                <input type="text" class="picker form-control coloris" id="color_code"
                                    value="#cc458faa" />

                            </div>

                            <div class="col-md-6">

                                <label for="color_name" class="form-label">Color Name</label>

                                <input type="text" class="form-control" id="color_name"
                                    placeholder="Enter Color Name" />

                            </div>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button type="button" class="btn btn-primary btn-add-item">Add</button>

                </div>

            </div>

        </div>

    </div>
    </div>
    </div>
    <?php
    // dd($attributes);
    ?>
@endsection
@push('scripts')
    <!-- Select2 Cdn -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugin/tagify/tagify.min.js') }}"></script>

    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/libs/dropzone/dropzone-min.js') }}"></script>

    <script>
        let selectedSubcategory = parseInt(
            "<?php echo !empty($productDetails->sub_category_id) ? $productDetails->sub_category_id : ''; ?>");

        let selectedChildcategory = parseInt(
            "<?php echo !empty($productDetails->child_category_id) ? $productDetails->child_category_id : ''; ?>");
    </script>
    <script src="{{ asset('assets/js/custom/product.js') }}"></script>
    <script src="{{ asset('assets/js/repeater.js') }}"></script>
    <script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/form-validation.js') }}"></script>
    <script src="{{ asset('assets/js/picker.js') }}"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('assets/js/coloris.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            const attributes = @json($attributes); // Assuming this is your attributes array from backend
            // Map attributes to Select2 format
            // const formattedAttributes = attributes.map(attribute => ({
            //     id: attribute.id, // Or any unique identifier
            //     text: attribute.name // Or any text you want to display
            // }));
            const formattedAttributes = jQuery.map(attributes, function(val, index) {
                return {
                    id: val.id, // Or any unique identifier
                    text: val.name // Or any text you want to display
                };
            })
            // Initialize Select2 with dynamic data
            $('#attributeName').select2({
                placeholder: 'Select an option',
                width: "100%",
                tags: true, // Allow users to add new options
                tokenSeparators: [','], // Allows for multiple selections separated by commas or spaces
                data: formattedAttributes, // Use the formatted attributes here
                dropdownParent: $('#addAttributeModal')
            });
            $('#name').on('blur', function() {
                let name = $(this).val();
                if (!name) {
                    $('#nameError').text('The title field is required.');
                    $(this).addClass('is-invalid');
                } else {
                    $('#nameError').text('');
                    $(this).removeClass('is-invalid');
                }
            });

            $('#sku').on('blur', function() {
                let sku = $(this).val();
                if (!sku) {
                    $('#skuError').text('The SKU field is required.');
                    $(this).addClass('is-invalid');
                } else {
                    $('#skuError').text('');
                    $(this).removeClass('is-invalid');
                }
            });

            $('#is_active').on('blur', function() {
                let is_active = $(this).val();
                if (!is_active) {
                    $('#isactiveError').text('The Status field is required.');
                    $(this).addClass('is-invalid');
                } else {
                    $('#isactiveError').text('');
                    $(this).removeClass('is-invalid');
                }
            });

            $('#massage').on('blur', function() {
                let massage = $(this).val();
                if (!massage) {
                    $('#massageError').text('The Message field is required.');
                    $(this).addClass('is-invalid');
                } else {
                    $('#massageError').text('');
                    $(this).removeClass('is-invalid');
                }
            });

            $('#prdct_category_id').on('blur', function() {
                let prdct_category_id = $(this).val();
                if (!prdct_category_id) {
                    $('#categoryidError').text('The Category field is required.');
                    $(this).addClass('is-invalid');
                } else {
                    $('#categoryidError').text('');
                    $(this).removeClass('is-invalid');
                }
            });

            $('#product_type').on('blur', function() {
                let product_type = $(this).val();
                if (!product_type) {
                    $('#producttypeError').text('The Product TypeError field is required.');
                    $(this).addClass('is-invalid');
                } else {
                    $('#producttypeError').text('');
                    $(this).removeClass('is-invalid');
                }
            });

            $('#categorys_id').on('blur', function() {
                let categorys_id = $(this).val();
                if (!categorys_id) {
                    $('#categorysidError').text('The Categorys field is required.');
                    $(this).addClass('is-invalid');
                } else {
                    $('#categorysidError').text('');
                    $(this).removeClass('is-invalid');
                }
            });

            $('#buying_price').on('blur', function() {
                let buying_price = $(this).val();
                if (!buying_price) {
                    $('#buyingpriceError').text('The Buying Price field is required.');
                    $(this).addClass('is-invalid');
                } else {
                    $('#buyingpriceError').text('');
                    $(this).removeClass('is-invalid');
                }
            });

            $('#selling_price').on('blur', function() {
                let selling_price = $(this).val();
                if (!selling_price) {
                    $('#sellingpriceError').text('The MRP field is required.');
                    $(this).addClass('is-invalid');
                } else {

                    $('#sellingpriceError').text('');
                    $(this).removeClass('is-invalid');
                }
            });

            $('#specification').on('blur', function() {
                let specification = $(this).val();
                if (!specification) {
                    $('#specificationError').text('The Specification field is required.');
                    $(this).addClass('is-invalid');
                } else {
                    $('#specificationError').text('');
                    $(this).removeClass('is-invalid');
                }
            });

            $('#material').on('blur', function() {
                let material = $(this).val();
                if (!material) {
                    $('#materialError').text('The Material field is required.');
                    $(this).addClass('is-invalid');
                } else {
                    $('#materialError').text('');
                    $(this).removeClass('is-invalid');
                }
            });
            $('#weight').on('blur', function() {
                let weight = $(this).val();
                if (!weight) {
                    $('#weightError').text('The weight field is required.');
                    $(this).addClass('is-invalid');
                } else {
                    $('#weightError').text('');
                    $(this).removeClass('is-invalid');
                }
            });
            $('#weight_type').on('blur', function() {
                let weight_type = $(this).val();
                if (!weight_type) {
                    $('#weight_typeError').text('The weight type field is required.');
                    $(this).addClass('is-invalid');
                } else {
                    $('#weight_typeError').text('');
                    $(this).removeClass('is-invalid');
                }
            });

            $('#style').on('blur', function() {
                let style = $(this).val();
                if (!style) {
                    $('#styleError').text('The Style field is required.');
                    $(this).addClass('is-invalid');
                } else {
                    $('#styleError').text('');
                    $(this).removeClass('is-invalid');
                }
            });

            $('#wash_care').on('blur', function() {
                let wash_care = $(this).val();
                if (!wash_care) {
                    $('#washcareError').text('The Wash Care field is required.');
                    $(this).addClass('is-invalid');
                } else {
                    $('#washcareError').text('');
                    $(this).removeClass('is-invalid');
                }
            });

            // $(document).delegate(".skuChangeValue", "blur", function() {
            //     let skuChangeValue = $(this).val();
            //     let skuColor = $(this).attr('data-color');
            //     let skuSize = $(this).attr('data-size');
            //     if (skuChangeValue != '') {
            //         $(this).val(skuChangeValue+'_'+skuColor+'_'+skuSize)
            //     } 
            // });

            // Add similar handlers for other fields
            // $('#color-variants-select').on('change', updateVariantDetails);
            // $('#size-variants-select').on('change', updateVariantDetails);
            $('#color-variants-select').on('change', function() {
                var selectedValues = $(this).val();
                if ($.inArray('add_item_color', selectedValues) === -1) {
                    updateVariantDetails();
                }
            });

            $('#size-variants-select').on('change', function() {
                var selectedValues = $(this).val();
                if ($.inArray('add_item_size', selectedValues) === -1) {
                    updateVariantDetails();
                }
            });
        });

        function updateVariantDetails() {
            const selectedColors = $('#color-variants-select').select2('data');
            console.log(selectedColors);
            const selectedSizes = $('#size-variants-select').select2('data');
            const variantDetailsDiv = document.getElementById('variant-details');

            const existingValues = {};
            selectedColors.forEach(color => {
                selectedSizes.forEach(size => {
                    const colorId = color.id;
                    const sizeId = size.id;
                    existingValues[`${colorId}_${sizeId}_sku`] = $(
                        `input[name="variant_sku[${colorId}][${sizeId}]"]`).val();
                    existingValues[`${colorId}_${sizeId}_selling_price`] = $(
                        `input[name="variant_selling_price[${colorId}][${sizeId}]"]`).val();
                    existingValues[`${colorId}_${sizeId}_available_unit`] = $(
                        `input[name="variant_available_unit[${colorId}][${sizeId}]"]`).val();
                    existingValues[`${colorId}_${sizeId}_selling_unit`] = $(
                        `input[name="variant_selling_unit[${colorId}][${sizeId}]"]`).val();
                });
            });

            // Clear the variant details div
            variantDetailsDiv.innerHTML = '';

            selectedColors.forEach((color, index) => {
                const colorDivId = `color-group-${color.id}`;
                const collapseId = `collapse-${color.id}`;

                const colorDiv = document.createElement('div');
                colorDiv.className = "accordion-item";
                colorDiv.id = colorDivId;
                colorDiv.innerHTML = `
                    <div class="accordion-header" id="${colorDivId}">                        
                        <div class="accordion-button"  data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="true" aria-controls="${collapseId}">
                            <button type="button" class="btn btn-primary upload-variant-image-btn" openUploadModal(${color.id}) data-color-id="${color.id}">Upload Images</button>
                            <div class="col-md-2 form-group mb-0">${color.text}<span class="variant-count-${color.id}"></span></div>
                            <div class="col-md-2 form-group mb-0"><input class=" form-control total_var_mrp mrp_price total-selling-price-${color.id}" type="text" id="tot_mrp_price" placeholder="MRP" data-colorid="${color.id}"/> </div>
                            <div class="col-md-2 form-group mb-0"><input class=" form-control total-available-unit-${color.id}" type="text" placeholder="Total Available Units" disabled/> </div> 
                        </div>
                    </div>
                    <div id="${collapseId}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" aria-labelledby="${colorDivId}" data-bs-parent="#variant-details">
                        <div class="size-groups"></div>
                    </div>
                
            `;
                variantDetailsDiv.appendChild(colorDiv);

                /* colorDiv.querySelector('.upload-image-button').addEventListener('click', function(event) {
                    event.preventDefault();
                    openImageUploadModal(colorDiv);
                }); */
                var variantCounts = 0;
                selectedSizes.forEach(size => {
                    const variantDiv = document.createElement('div');
                    variantDiv.className = "row";
                    variantDiv.dataset.colorId = color.id;
                    variantDiv.innerHTML = `
                    <div class="col-md-1 form-group mt-3 text-center">
                        <h6>${size.text}</h6>
                    </div>
                    <div class="col-md-2 form-group mt-3">
                        <input type="text" class="form-control skuChangeValue" placeholder="SKU" data-color="${color.text}" data-size="${size.text}" name="variant_sku[${color.id}][${size.id}]" readonly>
                    </div>
                    <div class="col-md-2 form-group mt-3">
                        <input type="number" data-color_id="${color.id}" data-size="${size.id}" class="form-control var_mrp_price mrp_price v_price_${color.id}" placeholder="MRP" name="variant_selling_price[${color.id}][${size.id}]" id="variant_selling_price[${color.id}][${size.id}]">
                    </div>
                    <div class="col-md-2 form-group mt-3">
                        <input type="number" class="form-control price" placeholder="Total Available Units" name="variant_available_unit[${color.id}][${size.id}]" min="0">
                    </div>
                    <!-- <div class="col-md-2 form-group mt-3">
                        <input type="number" class="form-control price var_sp${color.id}" placeholder="Max Selling Units" name="variant_selling_unit[${color.id}][${size.id}]"> 
                    </div> -->
                    <div class="col-md-2 form-group mt-3">
                        <input type="number" class="form-control sub_sp_${color.id}${size.id} var_sp_${color.id} sell_price" data-color="${color.id}" data-size="${size.id}" id="var_selling_price[${color.id}][${size.id}]" placeholder="Selling Price" name="variant_selling_unit[${color.id}][${size.id}]" readonly>
                    </div>
                `;
                    colorDiv.querySelector('.size-groups').appendChild(variantDiv);
                    var prdSKU = ($('#sku').val() != '') ? $('#sku').val() : 'SKU';
                    // Re-apply the existing values
                    //$(`input[name="variant_sku[${color.id}][${size.id}]"]`).val(existingValues[`${color.id}_${size.id}_sku`]+'_'+color.text+"_"+size.text);

                    $(`input[name="variant_sku[${color.id}][${size.id}]"]`).val(prdSKU + '_' + color.text +
                        '_' + size.text);
                    //$(`input[name="variant_sku[${color.id}][${size.id}]"]`).val(sku+'_'+color.text+'_'+size.text);
                    $(`input[name="variant_selling_price[${color.id}][${size.id}]"]`).val(existingValues[
                        `${color.id}_${size.id}_selling_price`]);
                    $(`input[name="variant_available_unit[${color.id}][${size.id}]"]`).val(existingValues[
                        `${color.id}_${size.id}_available_unit`]);
                    $(`input[name="variant_selling_unit[${color.id}][${size.id}]"]`).val(existingValues[
                        `${color.id}_${size.id}_selling_unit`]);

                    $(document).ready(function() {
                        // Get the selling price value
                        var sellingPrice = $('#selling_price').val();

                        // Set the selling price to the variant selling unit input(s)
                        $(`input[name^="variant_selling_unit[${color.id}][${size.id}]"]`).val(
                            sellingPrice);
                    });

                    //variantDiv.querySelector(`input[name="variant_selling_price[${color.id}][${size.id}]"]`).addEventListener('input', () => updateTotals(color.id));
                    variantDiv.querySelector(
                            `input[name="variant_available_unit[${color.id}][${size.id}]"]`)
                        .addEventListener('input', () => updateTotals(color.id));
                    variantCounts++;
                    colorDiv.querySelector(`.variant-count-${color.id}`).innerHTML = variantCounts +
                        " Variants"
                });
                colorDiv.querySelector(`.total-selling-price-${color.id}`).value = document.getElementById(
                    'buying_price').value;
            });
            updateAllSellingPrices(existingValues, color.id);
            //updateAllSKUProdcut(existingValues,color.id);
            updateAllMaxUnit(existingValues, color.id);
            document.getElementById('sku').addEventListener('input', updateAllVariantsInput('sku'));
            document.getElementById('buying_price').addEventListener('input', updateAllVariantsInput('buying_price'));
            document.getElementById('max_selling_units').addEventListener('input', updateAllVariantsInput(
                'max_selling_units'));
            document.getElementById('selling_price').addEventListener('input', updateAllVariantsInput('selling_price'));
        }

        function updateTotals(colorId) {
            // let totalSellingPrice = 0;
            let totalAvailableUnits = 0;
            let total = 0;
            /*  document.querySelectorAll(`input[name^="variant_selling_price[${colorId}]"]`).forEach(input => {
                 const value = parseFloat(input.value);
                 if (!isNaN(value)) {
                     totalSellingPrice += value;
                 }
             }); */

            document.querySelectorAll(`input[name^="variant_available_unit[${colorId}]"]`).forEach(input => {
                const value = parseInt(input.value, 10);
                console.log(value);
                if (!isNaN(value)) {
                    totalAvailableUnits += value;
                }
            });

            document.querySelectorAll('input[name^="variant_available_unit"]').forEach(input => {
                const value = parseFloat(input.value) || 0;
                total += value;
                const totalDisplay = document.getElementById('totalDisplay');
                if (totalDisplay) {
                    totalDisplay.value = total;
                }

            });

            //$(`.total-selling-price-${colorId}`).val(totalSellingPrice);
            $(`.total-available-unit-${colorId}`).val(totalAvailableUnits);
        }

        function updateAllSellingPrices(existingValues, colorId) {
            const baseSellingPrice = parseFloat(document.getElementById('buying_price').value);
            if (!isNaN(baseSellingPrice)) {
                document.querySelectorAll('input[name^="variant_selling_price"]').forEach(input => {
                    const colorId = input.name.match(/variant_selling_price\[(.*?)\]/)[1];
                    const sizeId = input.name.match(/\[(\d+)\]/g)[1].replace(/\[|\]/g, '');
                    if (existingValues[`${colorId}_${sizeId}_selling_price`] === undefined || existingValues[
                            `${colorId}_${sizeId}_selling_price`] === '') {
                        input.value = baseSellingPrice;
                        input.dispatchEvent(new Event('input')); // Trigger input event to update totals
                        //updateTotals(colorId);
                    }
                });
            }
        }

        function updateAllSKUProdcut(existingValues, colorId) {
            const productSKU = document.getElementById('sku').value;
            if (productSKU !== "") {
                document.querySelectorAll('input[name^="variant_sku"]').forEach(input => {
                    const colorId = input.name.match(/variant_sku\[(.*?)\]/)[1];
                    const sizeId = input.name.match(/\[(\d+)\]/g)[1].replace(/\[|\]/g, '');
                    if (existingValues[`${colorId}_${sizeId}_sku`] === undefined || existingValues[
                            `${colorId}_${sizeId}_sku`] === '') {
                        input.value = productSKU;
                        input.dispatchEvent(new Event('input')); // Trigger input event to update totals
                    }
                });
            }
        }

        function updateAllMaxUnit(existingValues, colorId) {
            const baseSellingUnit = parseFloat(document.getElementById('max_selling_units').value);
            if (!isNaN(baseSellingUnit)) {
                document.querySelectorAll('input[name^="variant_selling_unit"]').forEach(input => {
                    const colorId = input.name.match(/variant_selling_unit\[(.*?)\]/)[1];
                    const sizeId = input.name.match(/\[(\d+)\]/g)[1].replace(/\[|\]/g, '');
                    if (existingValues[`${colorId}_${sizeId}_selling_unit`] === undefined || existingValues[
                            `${colorId}_${sizeId}_selling_unit`] === '') {
                        input.value = baseSellingUnit;
                        input.dispatchEvent(new Event('input')); // Trigger input event to update totals
                    }
                });
            }
        }

        function updateAllVariantsInput(inputType) {
            switch (inputType) {
                case 'sku':

                    break;
                case 'buying_price':
                    const baseSellingPrice = parseFloat(document.getElementById('buying_price').value);
                    if (!isNaN(baseSellingPrice)) {
                        document.querySelectorAll('input[name^="variant_selling_price"]').forEach(input => {
                            console.log(input)
                            input.value = baseSellingPrice;
                            //input.dispatchEvent(new Event('input')); // Trigger input event to update totals
                        });
                    }
                    break;
                case 'max_selling_units':

                    break;

                default:
                    break;
            }

        }
        var selectedColorId = "";
        $(document).on('click', '.upload-variant-image-btn', function() {
            const colorId = $(this).data('color-id');
            const prdId = $(this).data('product-id');
            $('.loadVariantImagesData').html('')
            $.ajax({
                type: "POST",
                url: "{{ route('admin-product-get-image-data') }}",
                data: {
                    color_id: colorId,
                    product_id: prdId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    openUploadModal(colorId);
                    if (response.status) {
                        //console.log(response.status); 
                        //console.log(response.data); 
                        $('.loadVariantImagesData').html(response.data)
                    }

                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + error);
                    $('.loadVariantImagesData').html('');
                    openUploadModal(colorId);
                }
            });
        });

        function openUploadModal(colorId) {
            if (selectedColorId && selectedColorId != colorId) {
                $('.loadVariantImagesData').html('')
            }
            selectedColorId = colorId;
            $('#uploadVariantImageModal').modal('show');
        }
        Dropzone.autoDiscover = false;
        const variantDropzone = new Dropzone("#dropzone-variant-images", {
            url: "{{ route('admin-product-upload-images-new') }}",
            acceptedFiles: ".png,.jpg,.jpeg,.mp4,.wmv,",
            uploadMultiple: true,
            maxFilesize: 50,
            createImageThumbnails: true,
            maxThumbnailFilesize: 10,
            thumbnailMethod: 'crop',
            parallelUploads: 10,
            autoProcessQueue: true,
            init: function() {
                const that = this;
                that.on("sending", function(file, xhr, formData) { // Add additional data here
                    formData.append("color_id", selectedColorId);

                });
                that.on("success", function(file, responseText) {
                    this.removeAllFiles();
                    if (responseText.status == 'success') {
                        $('.loadVariantImagesData').html('')
                        $('.loadVariantImagesData').html(responseText.data);
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

            }



        });

        function removeCustomBox(cls) {
            $('.' + cls).remove();
        }

        function openNewImageUploadModal(row) {
            const fileInput = row.querySelector('input[type="file"]');
            fileInput.click();

            fileInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const uploadedImageCell = row.querySelector('.uploaded-image-cell');

                        // Clear the previous content
                        uploadedImageCell.innerHTML = '';

                        // Display the new image with a remove button
                        uploadedImageCell.innerHTML = `
                        <img src="${event.target.result}" class="uploaded-image" width="100">
                        <button class="btn btn-danger btn-sm remove-image-button">Remove Image</button>
                    `;

                        // Add event listener for the remove button
                        uploadedImageCell.querySelector('.remove-image-button').addEventListener('click',
                            function() {
                                uploadedImageCell.innerHTML =
                                    '<button class="btn btn-primary upload-image-button">Choose Image</button>';
                                fileInput.value = ''; // Clear the file input
                                uploadedImageCell.querySelector('.upload-image-button').addEventListener(
                                    'click',
                                    function(event) {
                                        event.preventDefault();
                                        openNewImageUploadModal(row);
                                    });
                            });
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        function saveAttribute() {
            // Gather data from form
            var formData = {
                attributeName: $('#attributeName').val(),
                attributeValue: $('#attributeValue').val(),
                _token: $('input[name="_token"]').val() // Laravel CSRF token
            };

            // Send AJAX request
            $.ajax({
                url: "{{ route('admin-product-save-attribute') }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    // Handle success response
                    console.log('Attribute saved successfully');
                    // Optionally, update UI or close modal
                    $('#addAttributeModal').modal('hide');
                    // Example: Update attribute select options via JavaScript
                    var newOption = $('<option>', {
                        value: response.id,
                        text: response.name
                    });
                    $('#attribute_id').append(newOption);
                },
                error: function(xhr) {
                    // Handle error response
                    console.error('Error saving attribute');
                }
            });
        }

        function addvarient() {
            const variantId = new Date().getTime();
            var html =
                ` <div class="card" style="border:solid 1px black;">
                                <div class="card-header accordion-button" data-bs-toggle="collapse" data-bs-target="#customvarient_` +
                variantId + `"
                                    aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                                    <div class="card-title" id="title_` + variantId +
                `">
                                        Variance Name
                                    </div>
                                </div>
                                <div class="card-body default-variant-section accordion-collapse collapse show" id="customvarient_` +
                variantId + `">
                                        <div class="row">
                                            <div class="col-md-12 form-group mt-3">
                                                <label>Option Name</label>
                                                <input type="text" name="varientname[` + variantId +
                `]" id='varientname' placeholder="Option Name like Size, Color" class="form-control myfiledata " attr_id="` +
                variantId + `">
                                            </div>
                                        </div>
                                           <div class="optiondiv">
                                                <div class="mainoptiondiv varientoptiondiv_` + variantId + `">
                                                    <div class="row">
                                                        <div class="col-md-11 form-group mt-3">
                                                            <label>Option Value</label>
                                                            <input type="text" id='optionname' name="optionname[` +
                variantId +
                `][]" placeholder="Option value Like L, XL, XXL" class="form-control myfiledata varientvalueoption varientopvalue_` +
                variantId + `" attr_id="` + variantId +
                `">
                                                        </div>
                                                        <div class="col-md-1 form-group">
                                                            <label style="visibility: hidden;">Option Value</label>
                                                            <a href="javascript:void(0);" class="btn btn-danger removeoptionvalue" attr_id="` +
                variantId +
                `"><i class="fa fa-remove"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group mt-3 text-left">
                                                    <a href="javascript:void(0);" class="btn btn-danger removevarient" attr_id="` +
                variantId +
                `">Delete</a>
                                                </div>
                                                <div class="col-md-6 form-group mt-3 text-right" style="text-align: right;">
                                                    <a href="javascript:void(0);" class="btn btn-info donevarient" attr_id="` +
                variantId + `">Done</a>
                                                </div>
                                            </div>
                                </div>

                            </div>`;
            $('#accordionSUBPRODUCT').append(html);
            $('.default-variant-section').addClass('show');
        }

        $(document).on('keyup', ".varientvalueoption", function() {
            var variantId = $(this).attr('attr_id');
            var allFilled = true;
            $(".varientopvalue_" + variantId).each(function() {
                if ($(this).val().trim() === "") {
                    allFilled = false;
                    return false;
                }
            });

            if (allFilled) {
                var html = `<div class="mainoptiondiv varientoptiondiv_` + variantId + `">
                        <div class="row">
                            <div class="col-md-11 form-group mt-3">
                                <label>Option Value</label>
                                <input type="text" id='optionname' name="optionname[` + variantId +
                    `][]" placeholder="Option value Like L, XL, XXL" class="form-control varientvalueoption myfiledata varientopvalue_` +
                    variantId + `" attr_id="` + variantId + `">
                            </div>
                            <div class="col-md-1 form-group">
                                <label style="visibility: hidden;">Option Value</label>
                                <a href="javascript:void(0);" class="btn btn-danger removeoptionvalue" attr_id="` +
                    variantId + `"><i class="fa fa-remove"></i></a>
                            </div>
                        </div>
                    </div>`;
                $(this).closest('.mainoptiondiv').append(html);
            }
        });

        function isFloatNumber(item, evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode == 46) {
                var regex = new RegExp(/\./g)
                var count = $(item).val().match(regex).length;
                if (count > 1) {
                    return false;
                }
            }
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }

        $(document).on('click', ".removeoptionvalue", function() {

            $(this).parent().parent().remove();
        });
        $(document).on('click', ".removevarient", function() {

            $(this).parent().parent().parent().parent().remove();
        });
        $(document).on('click', ".donevarient", function() {

            var varientname = $(this).parent().parent().parent().parent().find('.myfiledata').val();
        });
        $(document).on('change', ".varprice", function() {
            let prices = [];
            $('.varprice').each(function() {
                let price = parseFloat($(this).val());
                if (!isNaN(price)) {
                    prices.push(price);
                }
            });

            if (prices.length > 0) {
                let minPrice = Math.min(...prices);
                let maxPrice = Math.max(...prices);
                $('#globalvarientprice').val(minPrice + '-' + maxPrice);
            } else {
                $('#globalvarientprice').val(0.00 + '-' + 0.00);
            }
        });
        $(document).on('change', ".varqty", function() {
            var sum = 0;
            $('.varqty').each(function() {
                if (parseFloat(this.value) != NaN) {
                    sum += parseFloat(this.value);
                }
            });
            $('#globalvarientqty').val(sum);
        });
        $(document).on('change', "#globalvarientprice", function() {
            var price = this.value;
            $('.varprice').val(price);
        });

        $(document).on('keyup', ".myfiledata", function() {

            var formData = $('#productForm').serialize();
            $.ajax({
                url: "{{ route('admin-product-receivedata') }}",
                method: 'POST',
                data: formData,
                success: function(response) {
                    $('#accordionvaluesection').html(response);
                },
            });
        });

        @if (isset($product) && $product->product_type == 2)
            // var formData = $('#productForm').serialize();
            // $.ajax({
            //     url: "{{ route('admin-product-receivedata') }}",
            //     method: 'POST',
            //     data: formData,
            //     success: function(response) {
            //         $('#accordionvaluesection').html(response);
            //     },
            // });
        @endif

        $(document).on('click', ".checkall", function() {
            if (this.checked) {
                $('.singlecheckbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.singlecheckbox').each(function() {
                    this.checked = false;
                });
            }
        });

        $(document).on('click', ".singlecheckbox", function() {
            if ($('.singlecheckbox:checked').length == $('.singlecheckbox').length) {
                $('.checkall').prop('checked', true);
            } else {
                $('.checkall').prop('checked', false);
            }
        });

        function displayDiv(productType) {
            console.log(productType);
            if (productType == "1") {
                $('#variant-section').hide();
                $('#simple-product-section').show();
            } else {
                $('#variant-section').show();
                $('#simple-product-section').hide();
            }
        }

        $(document).ready(function() {
            const selectedVariantId = $('#simplevariant').val();
            if (selectedVariantId) {
                displayVariantValues(selectedVariantId);
            }
        });

        function displayVariantValues(variantId) {
            let selectIds = '{{ $simpleVeriantValue->variant_values ?? '' }}';
            let selectedIds = selectIds ? selectIds.split(',') : [];
            if (variantId !== '') {
                let url = "{{ route('admin-product-variant-values') }}"; // Route without parameters for POST

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}", // CSRF token for POST requests
                        id: variantId, // Pass the variant ID
                    },
                    success: function(response) {
                        let variantSelect = $('#variant-values'); // Target dropdown or container
                        variantSelect.empty(); // Clear previous values
                        variantSelect.append('<option value="">Select Variant Value</option>');
                        // response.forEach(function (value) {
                        //     variantSelect.append(`<option value="${value.id}">${value.name}</option>`);
                        // });
                        //response = JSON.parse(response);

                        response.data.forEach(function(item) {
                            let colorStyle = item.color_code ?
                                `style="background-color:${item.color_code};"` :
                                '';
                            let isSelected = selectedIds.includes(item.id.toString()) ? 'selected' : '';

                            variantSelect.append(
                                `<option value="${item.id}" ${colorStyle} ${isSelected}>${item.name}</option>`
                            );
                        });
                        // Trigger the change event after updating options
                        variantSelect.trigger('change');
                    },
                    error: function(xhr) {
                        console.error('Error fetching variant values:', xhr.responseText);
                        alert('Failed to load variant values. Please try again.');
                    },
                });
            } else {
                let variantSelect = $('#variant-values');
                variantSelect.empty();
                variantSelect.trigger('change');
            }

        }
        // Initialize the visibility based on the selected product type on page load
        window.onload = function() {
            var productType = document.getElementById('product_type').value;
            displayDiv(productType);
        }

        function updatestatus(status) {
            $('#status').val(status);
        }


        $('#prdct_category_id').select2({
            placeholder: "Choose Category",
            width: "100%"
        });

        function getRelatedSubCategories(category_id) {
            var category_ids = $('#prdct_category_id').val();

            $.ajax({
                type: "GET",
                url: "{{ route('admin-product-ajax-getrelatedsubcategories') }}",
                data: {
                    category_ids: category_ids
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log("Get Response", response);
                    var html = '<option value="">Select Subcategory</option>';

                    if (response.success) {
                        // Check if response.subcategories has more than 0 items
                        if (response.subcategories.length > 0) {
                            $.each(response.subcategories, function(index, subcat) {
                                html += '<option value="' + subcat.id + '">' + subcat.name +
                                    '</option>';
                            });
                            $('.subCategorieHide').show();
                            $('.childCategoryHide').hide();
                        } else {
                            // If no subcategories, show "No Subcategories Available"
                            html += '<option value="">No Subcategories Available</option>';
                            $('.subCategorieHide').hide();
                            $('.childCategoryHide').hide();
                        }
                    } else {
                        $('.subCategorieHide').hide();
                        $('.childCategoryHide').hide();
                        html += '<option value="">No Subcategories Available</option>';
                    }

                    // Update the select dropdown
                    $("#prdct_sub_category_id").html(html);

                    // Initialize the select2 plugin
                    $('#prdct_sub_category_id').select2({
                        placeholder: "Choose Sub Category",
                        width: "100%"
                    });
                },
                error: function(xhr, status, error) {}
            });
        }

        function getproduct(subcategory_id) {
            $.ajax({
                type: "GET",
                url: "{{ route('admin-product-ajax-getproduct') }}",
                data: {
                    subcatid: subcategory_id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        var currentSelections = $('#Productid').val() || [];

                        var options = '<option value="add_item">Add another Item</option>';

                        $.each(response.subproducts, function(index, item) {
                            options += '<option value="' + item.id + '">' + item.name + '</option>';
                        });

                        $('#Productid').html(options);

                        $('#Productid').select2({
                            placeholder: "Choose item",
                            width: "100%"
                        });

                        $('#Productid').val(currentSelections).trigger('change');
                    } else {
                        $("#prdct_child_category_id").html('<option value="">No Product Available</option>');
                        $('#prdct_child_category_id').select2({
                            placeholder: "Choose item",
                            width: "100%"
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + ' ' + error);
                    $("#Productid").html('<option value="">Error fetching Product</option>');
                    $('#Productid').select2({
                        placeholder: "Choose item",
                        width: "100%"
                    });
                }
            });
        }

        function getchildcategory() {
            var subctg_ids = $('#prdct_sub_category_id').val();
            $.ajax({
                type: "GET",
                url: "{{ route('admin-product-ajax-getchildcategory') }}",
                data: {
                    subctgids: subctg_ids
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        console.log("Get ChildCategory Data", response);
                        var currentSelections = $('#Productid').val() || [];

                        var options = '<option value="add_item">Add another Item</option>';

                        // Check if response.childcat has more than 0 items
                        if (response.childcat.length > 0) {
                            $.each(response.childcat, function(index, item) {
                                options += '<option value="' + item.id + '">' + item.name + '</option>';
                            });

                            $('#prdct_child_category_id').html(options);
                            $('.childCategoryHide').show();
                        } else {
                            // If there are no child categories
                            options = '<option value="">No Products Available</option>';
                            $('#prdct_child_category_id').html(options);
                            $('.childCategoryHide').hide();
                        }

                        $('#prdct_child_category_id').select2({
                            placeholder: "Choose item",
                            width: "100%"
                        });

                        // Set the current selected values, if any
                        $('#prdct_child_category_id').val(currentSelections).trigger('change');

                    } else {
                        // If response.success is false, hide the child category dropdown
                        $("#prdct_child_category_id").html('<option value="">No Product Available</option>');
                        $('.childCategoryHide').hide();
                        $('#prdct_child_category_id').select2({
                            placeholder: "Choose item",
                            width: "100%"
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + ' ' + error);
                    $("#prdct_child_category_id").html('<option value="">Error fetching Product</option>');
                    $('#prdct_child_category_id').select2({
                        placeholder: "Choose item",
                        width: "100%"
                    });
                }
            });
        }

        // Add new row
        var counter = 1;
        $(document).on("click", ".addmore", function() {

            var html = `<div class="card-body default-variant-section accordion-collapse collapse show configattribute" id="attribute-section">
            <div class="row">
                    <input type="hidden" name="varient_id[0]" value="0">
                    @foreach ($attributesdata as $key => $vadata)
                     <div class="col-2">
                        <div class="card-body p-0">
                            <div class="mb-3">
                                <label for="attribute_{{ $vadata->id }}" class="form-label">{{ $vadata->name }}</label>
                                <select name="config_attribute[` + counter + `][{{ $vadata->id }}]" id="attribute_{{ $vadata->id }}" class="select2-original form-control variant_name">
                                    <option value="">Choose item</option>
                                    <option value="add_item">Add another Item</option>
                                    @foreach ($variantData[$vadata->id] as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div class="col-2">
                        <div class="card-body p-0">
                            <div class="mb-3">
                                <label for="qty" class="form-label">Qty</label>
                                <input name="qty[` + counter + `]" type="number" id="qty" class="form-control variant_name">
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="card-body p-0">
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input name="price[` + counter + `]" type="number" id="price" class="form-control price">
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="card-body p-0">
                            <div class="mb-3" style="margin-top: 25px;">
                                <a class="btn btn-danger rounded-pill removemore" href="javascript:void(0);">Remove</a>
                            </div>
                        </div>
                    </div>
                </div> 
                </div>`;
            $('.configurable').append(html);
            counter++;
        });

        function addnewvarient() {
            const variantId = new Date().getTime();

            const variant_product = document.getElementById('accordionSUBPRODUCT');
            const colors = @json($colors);
            const sizes = @json($sizes);
            const div = document.createElement('div');
            div.classList.add('row');


            div.innerHTML = `<div class="col-md-2 form-group mt-3">
                        <label>Color</label>
                        <select name="color_ids[]" class="js-example-placeholder-single js-states form-control">
                            <option value="">Select Color</option>
                            ${colors.map(color => `<option value="${color.id}">${color.name}</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-md-2 form-group mt-3">
                        <label>Size</label>                                    
                        <select name="size_ids[]"  class="js-example-placeholder-single js-states form-control">
                            <option value="">Select Size</option>
                            ${sizes.map(size => `<option value="${size.id}">${size.name}</option>`).join('')}
                        </select>                                    
                    </div>
                     <div class="col-md-2 form-group mt-3">
                        <label>SKU</label>                                    
                        <input type="type" class="form-control price"  placeholder="SKU" name="variant_sku[]">                       
                    </div>
                    <div class="col-md-2 form-group mt-3">
                        <label>Price</label>                                    
                        <input type="number" class="form-control price"  placeholder="Price" name="variant_selling_price[]">                       
                    </div>
                    <div class="col-md-2 form-group mt-3">
                        <label>Total Available Units</label>                                    
                        <input type="number" class="form-control price"  placeholder="Total Available Units" name="variant_available_unit[]" min="0">                       
                    </div>
                    <!-- <div class="col-md-2 form-group mt-3">
                        <label>Max Selling Units</label>                                    
                        <input type="number" class="form-control price"  placeholder="Max Selling Units" name="variant_selling_unit[]">                       
                    </div> -->
                    <div class="col-md-2 form-group mt-3">                                                     
                        <div class="uploaded-image-cell"><button class="btn btn-primary upload-image-button">Choose Image</button></div>                   
                    </div>
                    <div class="col-md-2 form-group mt-3">
                     <label>&nbsp;</label>  
                        <button class="btn btn-danger remove-variant-button" type="button">Remove</button>                  
                    </div>
                `;



            variant_product.appendChild(div);
            div.querySelector('.remove-variant-button').addEventListener('click', function() {
                div.remove();
            });
            div.querySelector('.upload-image-button').addEventListener('click', function(event) {
                event.preventDefault();
                openImageUploadModal(div);
            });

        }

        function openImageUploadModal(row) {
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            fileInput.style.display = 'none';
            fileInput.name = "variants_product_image[]"

            fileInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const uploadedImageCell = row.querySelector('.uploaded-image-cell');
                        uploadedImageCell.innerHTML =
                            `<img src="${event.target.result}" class="uploaded-image" width="100">`;
                    };
                    reader.readAsDataURL(file);
                }
            });

            row.appendChild(fileInput);
            fileInput.click();
            //document.body.removeChild(fileInput);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const attributes =
                @json($attributes); // Assuming attributes are passed as a JSON-encoded array from the backend

            document.getElementById('add-attribute-button').addEventListener('click', addAttributePair);
            document.getElementById('add-new-attribute-button').addEventListener('click', () => {
                $('#addAttributeModal').modal('show');
            });
            document.getElementById('saveAttributeButton').addEventListener('click', saveAttributePair);

            function addAttributePair() {
                const attributePairContainer = document.createElement('div');
                attributePairContainer.className = 'row mb-3 attribute-pair';

                const attributeCol = document.createElement('div');
                attributeCol.className = 'col-4';
                attributeCol.innerHTML = `
                <div class="card-body p-0">
                    <div class="mb-3">
                        <label class="form-label">Attribute</label>
                        <select name="attribute_ids[]" class="form-control attribute-select" onchange="getAttributeValues(this)">
                            <option value="select value">Select Attribute</option>
                            ${attributes.map(attribute => `<option value="${attribute.id}">${ucfirst(attribute.name)}</option>`).join('')}
                        </select>
                    </div>
                </div>
            `;

                const valueCol = document.createElement('div');
                valueCol.className = 'col-4';
                valueCol.innerHTML = `
                <div class="card-body p-0">
                    <div class="mb-3">
                        <label class="form-label">Attribute Value</label>
                        <select name="attribute_value_ids[]" class="form-control value-select">
                            <option value="select">Select Attribute Value</option>
                            <!-- Values will be inserted here by JavaScript -->
                        </select>
                    </div>
                </div>
            `;

                const removeCol = document.createElement('div');
                removeCol.className = 'col-4 d-flex align-items-center';
                removeCol.innerHTML = `
                <div class="card-body p-0">
                    <button class="btn btn-danger remove-attribute-button" type="button">Remove</button>
                </div>
            `;

                removeCol.querySelector('.remove-attribute-button').addEventListener('click', function() {
                    attributePairContainer.remove();
                });

                attributePairContainer.appendChild(attributeCol);
                attributePairContainer.appendChild(valueCol);
                attributePairContainer.appendChild(removeCol);

                document.getElementById('attribute-pairs-container').appendChild(attributePairContainer);
            }

            window.getAttributeValues = function(selectElement) {
                const attributeId = selectElement.value;
                const valueSelect = selectElement.closest('.attribute-pair').querySelector('.value-select');

                if (attributeId) {
                    fetch(`{{ route('admin-product-attribute-values') }}?attribute_id=${attributeId}`)
                        .then(response => response.json())
                        .then(data => {
                            const attributeValues = data.attributeValues;
                            let options = '<option value="">Select Attribute Value</option>';
                            attributeValues.forEach(function(value) {
                                options += `<option value="${value.id}">${value.name}</option>`;
                            });
                            valueSelect.innerHTML = options;
                        })
                        .catch(error => {
                            console.error('Error fetching attribute values:', error);
                        });
                } else {
                    valueSelect.innerHTML = '<option value="">Select Attribute Value</option>';
                }
            };

            function saveAttributePair() {
                const attributeName = document.getElementById('attributeName').value.trim();
                const attributeValue = document.getElementById('attributeValue').value.trim();

                if (attributeName && attributeValue) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch('{{ route('admin-product-save-attribute') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                attributeName,
                                attributeValue
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.attribute_id && data.attribute_name) {
                                attributes.push({
                                    id: data.attribute_id,
                                    name: data.attribute_name
                                });

                                // Clear the modal fields
                                document.getElementById('attributeName').value = '';
                                document.getElementById('attributeValue').value = '';

                                // Close the modal
                                $('#addAttributeModal').modal('hide');

                                // Refresh the dropdowns with the new attribute
                                document.querySelectorAll('.attribute-select').forEach(select => {
                                    const newOption = document.createElement('option');
                                    newOption.value = data.attribute_id;
                                    newOption.text = ucfirst(data.attribute_name);
                                    select.appendChild(newOption);
                                });
                                show_message(data.msg, 'success');
                            } else {
                                //this is when added only values of the attribute
                                document.getElementById('attributeName').value = '';
                                document.getElementById('attributeValue').value = '';
                                $('#addAttributeModal').modal('hide');
                                show_message(data.msg, 'success');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }
            }

            function ucfirst(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

        });

        document.addEventListener('DOMContentLoaded', function() {

            /*  document.getElementById('add-option-button').addEventListener('click', function(event) {
                    event.preventDefault();
                    addOption();
                });
            */
            /* document.getElementById('product-variants-form').addEventListener('submit', function(event) {
                updateFormInputNames();  // Ensure all dynamic inputs are properly named for form submission
            }); */

            function addOption() {
                const container = document.getElementById('options-container');
                const optionDiv = document.createElement('div');
                optionDiv.classList.add('option-group');
                optionDiv.innerHTML = `
                <div class="row mb-3">
                    <div class="col">
                        <input type="text" class="form-control option-name" placeholder="Option Name">
                    </div>
                </div>
                <div class="row mb-3 value-container">
                    <div class="col">
                        <input type="text" class="form-control option-value" placeholder="Option Value">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <button class="btn btn-primary done-button" type="button">Done</button>
                        <button class="btn btn-danger delete-button" type="button">Delete</button>
                    </div>
                </div>
            `;

                container.appendChild(optionDiv);
                optionDiv.querySelector('.done-button').addEventListener('click', function(event) {
                    event.preventDefault();
                    toggleEditMode(optionDiv, false);
                });

                optionDiv.querySelector('.delete-button').addEventListener('click', function(event) {
                    event.preventDefault();
                    container.removeChild(optionDiv);
                    updateTotalUnits();
                });

                optionDiv.querySelector('.value-container').addEventListener('input', function(event) {
                    const inputFields = optionDiv.querySelectorAll('.option-value');
                    const lastInputField = inputFields[inputFields.length - 1];

                    if (lastInputField.value.trim() !== '') {
                        const newInputField = document.createElement('div');
                        newInputField.classList.add('col');
                        newInputField.innerHTML =
                            `<input type="text" class="form-control option-value" placeholder="Option Value">`;

                        optionDiv.querySelector('.value-container').appendChild(newInputField);
                    }
                });

                const imageUploadCell = document.createElement('td');
                imageUploadCell.classList.add('uploaded-image-cell');
                optionDiv.appendChild(imageUploadCell);

                /* optionDiv.querySelector('.upload-image-button').addEventListener('click', function(event) {
                    event.preventDefault();
                    openImageUploadModal(optionDiv);
                }); */
            }

            function toggleEditMode(optionDiv, editMode) {
                const optionName = optionDiv.querySelector('.option-name');
                const optionValues = optionDiv.querySelectorAll('.option-value');
                const doneButton = optionDiv.querySelector('.done-button');

                if (optionName && optionValues.length > 0 && doneButton) {
                    if (editMode) {
                        optionName.disabled = false;
                        optionValues.forEach(input => {
                            input.disabled = false;
                        });
                        doneButton.innerText = 'Save';
                    } else {
                        optionName.disabled = true;
                        optionValues.forEach(input => {
                            input.disabled = true;
                        });
                        doneButton.innerText = 'Done';
                        displayOptionValues(optionDiv, optionName.value, Array.from(optionValues).map(input => input
                            .value).filter(value => value.trim() !== ''));
                    }
                }
            }

            function displayOptionValues(optionDiv, optionName, optionValues) {

                const tableBody = document.getElementById('variant-table').querySelector('tbody');
                const sellingPrice = document.getElementById('selling_price') ? document.getElementById(
                    'selling_price').value : 0;
                const maxSellingUnits = document.getElementById('max_selling_units') ? document.getElementById(
                    'max_selling_units').value : 0;

                if (optionName.toLowerCase() === 'colour') {
                    const existingColorRows = tableBody.querySelectorAll('.color-row');
                    existingColorRows.forEach(row => {
                        if (row.querySelector('.variant-name').textContent.trim().toLowerCase() ===
                            'colour') {
                            tableBody.removeChild(row);
                        }
                    });

                    optionValues.forEach(colorValue => {
                        const row = document.createElement('tr');
                        row.classList.add('color-row');
                        row.innerHTML = `
                        <td class="variant-name">${optionName}</td>
                        <td class="color-value">
                            <span class="size-count">0</span> ${colorValue} variants
                            <table class="table table-striped size-table" style="display: none;">
                                <thead>
                                    <tr>
                                        <th>Size Variant</th>
                                        <th>Price</th>
                                        <th>Units Available</th>
                                        <!-- <th>Max Selling Units</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Size variants will be added here dynamically -->
                                </tbody>
                            </table>
                        </td>
                        <td><input type="number" class="form-control color-price" value="${sellingPrice}" placeholder="Price" name="variants[${colorValue}][price]"></td>
                        <td><input type="number" class="form-control color-available-input" placeholder="Available" min="0" readonly name="variants[${colorValue}][total_units]"></td>
                       <!-- <td><input type="number" class="form-control" value="${maxSellingUnits}" placeholder="Max Selling Units" name="variants[${colorValue}][max_selling_units]"></td> -->
                        <td class="uploaded-image-cell"><button class="btn btn-primary upload-image-button">Choose File</button></td>
                    `;
                        tableBody.appendChild(row);
                        row.querySelector('.color-price').addEventListener('input', updateColorPriceRange);

                        row.addEventListener('click', function(event) {
                            if (event.target.tagName.toLowerCase() !== 'input') {
                                const sizeTable = row.querySelector('.size-table');
                                sizeTable.style.display = sizeTable.style.display === 'none' ?
                                    'table' : 'none';
                            }
                        });

                        row.querySelector('.upload-image-button').addEventListener('click', function(
                            event) {
                            event.preventDefault();
                            openImageUploadModal(row);
                        });
                    });
                } else if (optionName.toLowerCase() === 'size') {
                    const colorRows = tableBody.querySelectorAll('.color-row');
                    colorRows.forEach(row => {
                        const sizeTableBody = row.querySelector('.size-table tbody');
                        optionValues.forEach(sizeValue => {
                            const existingSizeRow = sizeTableBody.querySelector(
                                `tr[data-size="${sizeValue}"]`);
                            if (!existingSizeRow) {
                                const sizeRow = document.createElement('tr');
                                sizeRow.setAttribute('data-size', sizeValue);
                                sizeRow.innerHTML = `
                                <td>${sizeValue}</td>
                                <td><input type="number" class="form-control size-price" value="${sellingPrice}" placeholder="Price" name="variants[${row.querySelector('.color-value').textContent.trim()}][size][${sizeValue}][price]"></td>
                                <td><input type="number" class="form-control size-available-input" placeholder="Available" min="0" name="variants[${row.querySelector('.color-value').textContent.trim()}][size][${sizeValue}][total_units]"></td>
                                <!-- <td><input type="number" class="form-control" value="${maxSellingUnits}" placeholder="Max Selling Units" name="variants[${row.querySelector('.color-value').textContent.trim()}][size][${sizeValue}][max_selling_units]"></td> -->
                            `;
                                sizeTableBody.appendChild(sizeRow);
                                sizeRow.querySelector('.size-price').addEventListener('input',
                                    updateSizePriceRange);
                                sizeRow.querySelector('.size-available-input').addEventListener(
                                    'input', updateTotalUnits);
                            }
                        });

                        const sizeCountSpan = row.querySelector('.size-count');
                        sizeCountSpan.textContent = sizeTableBody.querySelectorAll('tr').length;

                        updateTotalUnits();
                    });
                }

                updateColorPriceRange();
                updateSizePriceRange();
            }

            function openImageUploadModal(row) {
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.accept = 'image/*';
                fileInput.style.display = 'none';

                fileInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            const uploadedImageCell = row.querySelector('.uploaded-image-cell');
                            uploadedImageCell.innerHTML =
                                `<img src="${event.target.result}" class="uploaded-image" width="100">`;
                        };
                        reader.readAsDataURL(file);
                    }
                });

                document.body.appendChild(fileInput);
                fileInput.click();
                document.body.removeChild(fileInput);
            }

            function updateColorPriceRange() {
                const colorPriceInputs = document.querySelectorAll('.color-price');
                const colorPriceRange = document.getElementById('color-price-range');

                if (colorPriceInputs.length > 0 && colorPriceRange) {
                    let minPrice = Infinity;
                    let maxPrice = -Infinity;

                    colorPriceInputs.forEach(input => {
                        const price = parseFloat(input.value) || 0;
                        if (price < minPrice) minPrice = price;
                        if (price > maxPrice) maxPrice = price;
                    });

                    if (minPrice === Infinity) minPrice = 0;
                    if (maxPrice === -Infinity) maxPrice = 0;

                    colorPriceRange.textContent = `$${minPrice.toFixed(2)} - $${maxPrice.toFixed(2)}`;
                }
            }

            function updateSizePriceRange() {
                const sizePriceInputs = document.querySelectorAll('.size-price');
                const sizePriceRange = document.getElementById('size-price-range');

                if (sizePriceInputs.length > 0 && sizePriceRange) {
                    let minPrice = Infinity;
                    let maxPrice = -Infinity;

                    sizePriceInputs.forEach(input => {
                        const price = parseFloat(input.value) || 0;
                        if (price < minPrice) minPrice = price;
                        if (price > maxPrice) maxPrice = price;
                    });

                    if (minPrice === Infinity) minPrice = 0;
                    if (maxPrice === -Infinity) maxPrice = 0;

                    sizePriceRange.textContent = `$${minPrice.toFixed(2)} - $${maxPrice.toFixed(2)}`;
                }
            }

            function updateTotalUnits() {
                const colorRows = document.querySelectorAll('.color-row');
                let totalUnits = 0;

                colorRows.forEach(row => {
                    const sizeRows = row.querySelectorAll('.size-table tbody tr');
                    let colorTotalUnits = 0;

                    sizeRows.forEach(sizeRow => {
                        const availableInput = sizeRow.querySelector('.size-available-input');
                        const availableUnits = parseInt(availableInput.value) || 0;
                        colorTotalUnits += availableUnits;
                    });

                    const colorAvailableInput = row.querySelector('.color-available-input');
                    colorAvailableInput.value = colorTotalUnits;
                    totalUnits += colorTotalUnits;
                });

                document.getElementById('total-units').textContent = totalUnits;
            }

            function updateFormInputNames() {
                const colorRows = document.querySelectorAll('.color-row');
                colorRows.forEach((row, index) => {
                    const colorName = row.querySelector('.color-value').textContent.trim();
                    const priceInput = row.querySelector('.color-price');
                    const availableInput = row.querySelector('.color-available-input');
                    const maxSellingInput = row.querySelector(
                        `[name="variants[${colorName}][max_selling_units]"]`);

                    priceInput.name = `variants[${colorName}][price]`;
                    availableInput.name = `variants[${colorName}][total_units]`;
                    maxSellingInput.name = `variants[${colorName}][max_selling_units]`;

                    const sizeRows = row.querySelectorAll('.size-table tbody tr');
                    sizeRows.forEach(sizeRow => {
                        const sizeName = sizeRow.getAttribute('data-size');
                        const sizePriceInput = sizeRow.querySelector('.size-price');
                        const sizeAvailableInput = sizeRow.querySelector('.size-available-input');
                        const sizeMaxSellingInput = sizeRow.querySelector(
                            `[name="variants[${colorName}][size][${sizeName}][max_selling_units]"]`
                        );

                        sizePriceInput.name = `variants[${colorName}][size][${sizeName}][price]`;
                        sizeAvailableInput.name =
                            `variants[${colorName}][size][${sizeName}][total_units]`;
                        sizeMaxSellingInput.name =
                            `variants[${colorName}][size][${sizeName}][max_selling_units]`;
                    });
                });
            }
        });

        $(document).ready(function() {
            $('input[type=radio][name=variant_type]').change(function() {
                if ($(this).val() == 'color') {
                    $('.color-variant').show();
                    $('.size-variant').hide();
                } else if ($(this).val() == 'size') {
                    $('.color-variant').hide();
                    $('.size-variant').show();
                } else {
                    $('.color-variant').hide();
                    $('.size-variant').hide();
                }
            });
            // Remove row
            $(document).on("click", ".removemore", function() {
                $(this).closest('.row').remove();
            });

            $('.select2-original').select2({

                placeholder: "Choose item",
                width: "100%"
            });

            //$('#color_code').colorPicker();

            Coloris({

                el: '.coloris',

                swatches: [

                    '#264653',

                    '#2a9d8f',

                    '#e9c46a',

                    '#f4a261',

                    '#e76f51',

                    '#d62828',

                    '#023e8a',

                    '#0077b6',

                    '#0096c7',

                    '#00b4d8',

                    '#48cae4'

                ]
            });



            Coloris.setInstance('.picker', {

                theme: 'polaroid',

                themeMode: 'dark',

                formatToggle: true,

                closeButton: true,

                clearButton: true,

                swatches: [

                    '#067bc2',

                    '#84bcda',

                    '#80e377',

                    '#ecc30b',

                    '#f37748',

                    '#d56062'

                ]

            });



            CKEDITOR.replace(<?php echo 'wash_care'; ?>, {

                filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',

                enterMode: CKEDITOR.ENTER_BR

            });

            CKEDITOR.config.allowedContent = true;

            CKEDITOR.replace(<?php echo 'seo_content'; ?>, {

                filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',

                enterMode: CKEDITOR.ENTER_BR

            });

            CKEDITOR.replace(<?php echo 'product_details'; ?>, {

                filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',

                enterMode: CKEDITOR.ENTER_BR

            });

            CKEDITOR.config.allowedContent = true;


            CKEDITOR.replace(<?php echo 'others'; ?>, {

                filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',

                enterMode: CKEDITOR.ENTER_BR

            });

            CKEDITOR.config.allowedContent = true;


            CKEDITOR.replace(<?php echo 'specification'; ?>, {

                filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',

                enterMode: CKEDITOR.ENTER_BR

            });

            CKEDITOR.config.allowedContent = true;



            CKEDITOR.replace(<?php echo 'description'; ?>, {

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

                url: "{{ route('admin-product-upload-images-new') }}",

                acceptedFiles: ".png,.jpg,.jpeg,.mp4,.wmv,",

                uploadMultiple: true,

                maxFilesize: 50,

                createImageThumbnails: true,

                maxThumbnailFilesize: 10,

                thumbnailMethod: 'crop',

                parallelUploads: 10,

                autoProcessQueue: true,

                init: function() {

                    const that = this;

                    that.on("success", function(file, responseText) {

                        this.removeAllFiles();

                        if (responseText.status == 'success') {

                            $('.loadImagesData').html('')

                            $('.loadImagesData').html(responseText.data);
                            console.log(responseText.data);
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

                            method: 'DELETE',

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

                                    $(that).parents('.productPicMainContainer')
                                        .remove();

                                    show_message(datas['msg'], 'success');

                                } else {

                                    if (datas['status'] == 'error' && datas['errors']) {

                                        $.each(datas['errors'], function(index, html) {

                                            $("input[name = " + index + "]")
                                                .addClass(

                                                    'is-invalid');

                                            $("input[name = " + index + "]")
                                                .next().addClass(

                                                    'error');

                                            $("input[name = " + index + "]")
                                                .next().html(html);

                                            $("input[name = " + index + "]")
                                                .show();



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

                                    $("input[name = " + index + "]").next().addClass(

                                        'error');

                                    $("input[name = " + index + "]").next().html(html);

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
            });


            document.addEventListener('DOMContentLoaded', function() {
                const buyingPriceInput = document.getElementById('buying_price');
                const sellingPriceInput = document.getElementById('selling_price');

                buyingPriceInput.addEventListener('input', function() {
                    sellingPriceInput.value = buyingPriceInput.value;
                });
            });

            $(document).on('change', '#discount_type', function(e) {
                e.preventDefault();
                let selling_price = getSellingPrice();
                $('#selling_price').val(selling_price);
                $('.sell_price').val(selling_price);
                let disType = $('#discount_type').val();
                let discount = $('#discount').val();

                // calculate all sub variant price on basis of discount 
                var arr = [];
                i = 0;
                $(".var_mrp_price").each(function() { // create sub variant ids array 
                    arr[i++] = $(this).attr('id');
                });
                $.each(arr, function(index, value) {
                    id = '#' + value;
                    varVal = $("input[name='" + value + "']").val();
                    color = $("input[name='" + value + "']").data('color_id');
                    size = $("input[name='" + value + "']").data('size');

                    if (disType == 'flat') {
                        var sp = Math.round(parseInt(varVal) - parseInt(discount));
                        $('.sub_sp_' + color + size).val(sp);
                    } else {

                        let sp = Math.round(parseInt(varVal) - ((parseInt(varVal) * parseInt(
                            discount)) / 100));
                        $('.sub_sp_' + color + size).val(sp);
                    }
                });
            });

            $(document).on('keyup', '#discount', function(e) {

                e.preventDefault();
                let selling_price = getSellingPrice();
                $('#selling_price').val(selling_price);
                $('.sell_price').val(selling_price);
                let disType = $('#discount_type').val();
                let discount = $(this).val();

                // calculate all sub variant price on basis of discount 
                var arr = [];
                i = 0;
                $(".var_mrp_price").each(function() { // create sub variant ids array 
                    arr[i++] = $(this).attr('id');
                });
                $.each(arr, function(index, value) {
                    id = '#' + value;
                    varVal = $("input[name='" + value + "']").val();
                    color = $("input[name='" + value + "']").data('color_id');
                    size = $("input[name='" + value + "']").data('size');

                    if (disType == 'flat') {
                        var sp = Math.round(parseInt(varVal) - parseInt(discount));
                        $('.sub_sp_' + color + size).val(sp);
                    } else {
                        let sp = Math.round(parseInt(varVal) - ((parseInt(varVal) * parseInt(
                            discount)) / 100));
                        $('.sub_sp_' + color + size).val(sp);
                    }
                });
            });

            $(document).on('input', '#discount', function(e) {
                e.preventDefault();
                let selling_price = getSellingPrice();
                $('#selling_price').val(selling_price);
                $('.sell_price').val(selling_price);
                let disType = $('#discount_type').val();
                let discount = $(this).val();

                // Calculate all sub variant price based on discount 
                var arr = [];
                let i = 0;
                $(".var_mrp_price").each(function() { // Create sub variant ids array 
                    arr[i++] = $(this).attr('id');
                });

                $.each(arr, function(index, value) {
                    let id = '#' + value;
                    let varVal = $("input[name='" + value + "']").val();
                    let color = $("input[name='" + value + "']").data('color_id');
                    let size = $("input[name='" + value + "']").data('size');

                    if (disType === 'flat') {
                        let sp = Math.round(parseInt(varVal) - parseInt(discount));
                        $('.sub_sp_' + color + size).val(sp);
                    } else {
                        let sp = Math.round(parseInt(varVal) - ((parseInt(varVal) * parseInt(
                            discount)) / 100));
                        $('.sub_sp_' + color + size).val(sp);
                    }
                });
            });

            $(document).on('keyup', '#buying_price', function(e) {
                e.preventDefault();
                let selling_price = getSellingPrice();
                $('#selling_price').val(selling_price);
                $('.sell_price').val(selling_price); // variant selling price
                $('.total_var_mrp').val($(this).val()); // variant price
                $('.var_mrp_price').val($(this).val()); // sub variant mrp 
                $('.sell_price').val(selling_price);
            });

            // update subvariant price on basis of variant price
            $(document).on('keyup', '.total_var_mrp', function() {
                let varPrice = $(this).val();
                let color = $(this).data('colorid');
                $('.v_price_' + color).val($(this).val()); // update sub variant mrp 

                // update selling price with disocunt
                let discount = $('#discount').val();
                let disType = $('#discount_type').val();

                if (disType == 'flat') {
                    let sp = Math.round(parseInt(varPrice) - parseInt(discount));
                    $('.var_sp' + color).val(sp);
                } else {
                    let sp = Math.round(parseInt(varPrice) - ((parseInt(varPrice) * parseInt(discount)) /
                        100));
                    $('.var_sp' + color).val(sp);
                }
            });

            $(document).on('keyup', '.var_mrp_price', function() {
                let price = $(this).val();
                let color = $(this).data('color_id');
                let size = $(this).data('size');

                // discount sp for sub varinats
                let discount = $('#discount').val();
                let disType = $('#discount_type').val();

                if (disType == 'flat') {
                    let sp = Math.round(parseInt(price) - parseInt(discount));
                    $('.sub_sp_' + color + size).val(sp);
                    return;
                } else {
                    let sp = Math.round(parseInt(price) - ((parseInt(price) * parseInt(discount)) / 100));
                    $('.sub_sp_' + color + size).val(sp);
                    return;
                }
                $('.sub_sp_' + color + size).val(price);
            });

            // update Sku for all sub variants 
            $(document).on('keyup', '#sku', function() {
                let prdSku = $(this).val();
                prdSku = prdSku.replace(/\s+/g, '_').toUpperCase();
                $(".skuChangeValue").each(function() { // create sub variant ids array 
                    color = $(this).data('color');
                    size = $(this).data('size').toUpperCase();
                    name = $(this).attr("name");
                    subVarSku = (prdSku != '') ? prdSku + '_' + color + '_' + size : 'SKU_' +
                        color + '_' + size;
                    $("input[name='" + name + "']").val(subVarSku);
                });
            });

            $(document).on('change', '.variant_name', function() {

                if ($(this).val() == 'add_item_color' || $(this).val() == 'add_item_size') {

                    if ($(this).val() == 'add_item_color') {
                        $('.color').show();
                        $('.non-color').hide();

                    }
                    if ($(this).val() == 'add_item_size') {
                        $('.color').hide();

                        $('.non-color').show();

                    }
                    $('#variant_type').val('variant_value');

                    $('#addItemModal').modal('show');
                    $(this).val(null).trigger('change');

                } else {

                    var type = $(this).attr('name');

                    var id = $(this).val();

                    if (type == 'attribute_name') {

                        var url = "{{ route('admin-product-attribute-values') }}";

                        var field = "attribute_value";

                    } else {

                        var url = "{{ route('admin-product-variant-values') }}";

                        var field = "variant_value";

                    }

                    $.ajaxSetup({

                        headers: {

                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                        }

                    });

                    $.ajax({

                        type: "POST",

                        url: url,

                        data: {
                            id: id
                        },

                        success: function(response) {

                            if (type == 'attribute_name') {

                                $('#' + field).html(
                                        '<option value="">Select</option><option value="add_item">Add another Item</option>'
                                    )

                                    .select2({

                                        data: $.map(response.data, function(item) {

                                            return {

                                                text: item.name,

                                                id: item.id,

                                            }

                                        }),

                                    });

                            } else {
                                var options =
                                    '<option value="add_item">Add another Item</option>';

                                $.each(response.data, function(index, item) {
                                    options += '<option value="' + item.name + '">' +
                                        item.name + '</option>';
                                });

                                $('#' + field).html(options).select2();
                            }

                        },

                        error: function(data) {
                            console.log('Error:', data);
                        }

                    });

                }
            });


            $(document).on('click', '.btn-add-item', function() {

                var visibleSection = $('.modal-body .non-color:visible').length ? '.non-color' : '.color';


                var variant_type = $(visibleSection).find('input[id="add_variant_type"]').val();

                // var variant_type = $('#add_variant_type').val();

                var item = $('#add_new_item').val();

                var color_name = '';

                console.log("Item: ", item);

                console.log("Oter vType: ", variant_type);
                if (variant_type == 'color') {
                    item = $('#color_code').val();
                    color_name = $('#color_name').val();

                }
                // console.log("Oter vType: ", variant_type);

                if (item != '') {

                    if (variant_type == 'size') {
                        var variant_id = 2;
                        createOrUpdateVariant(item, 'size', variant_id, color_name);
                    } else if (variant_type == 'color') {
                        var variant_id = 1;
                        createOrUpdateVariant(item, 'color', variant_id, color_name);

                    }

                    // console.log("vType: ", variant_type);

                    if (color_name != '') {

                        var op_text = color_name;

                    } else {

                        var op_text = item;

                    }

                    $('#' + variant_type).append("<option value='" + item + "'>" + op_text + "</option>");

                    $('#add_new_item').val('');

                    $('#variant_type').val('');

                    $('#addItemModal').modal('hide');

                    window.location.reload(true);

                }

            });

            $(document).on('click', '.btn-delete-item', function() {

                let url = $(this).attr('data-url');

                Swal.fire({

                    title: "Are you sure?",

                    text: "You Want to remove this Item ?",

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

                            headers: {

                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                            },

                            success: function(response) {

                                if (response.type == 'variants') {

                                    $('#variant-table').html(response.data);

                                } else {

                                    $('#attribute-table').html(response.data);

                                }

                            },

                            error: function(xhr, status, errorThrown) {



                            }

                        });

                    } else if (result.dismiss === "cancel") {

                        Swal.fire(

                            "Cancelled",

                            "Your Item is safe :)",

                            "error"

                        )

                    }

                });

            });

            $(document).on('click', '.btn-update-item', function() {

                var url = $(this).attr('data-url');

                var keys = $(this).attr('data-keys');

                var type = $(this).attr('data-type');

                var price = $('#price-' + keys).val();

                var available = $('#available-' + keys).val();

                $.ajax({

                    url: url,

                    method: 'post',

                    data: {
                        name: keys,
                        price: price,
                        available: available,
                        type: type
                    },

                    headers: {

                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                    },

                    success: function(response) {

                        show_message(response.msg, response.status);

                    },

                    error: function(xhr, status, errorThrown) {

                    }

                });

            })

            function displayDiv(productType) {
                var variantSection = document.getElementById('variant-section');
                var simpleProductSection = document.getElementById('simple-product-section');

                if (productType == "1") {
                    variantSection.style.display = 'none';
                    simpleProductSection.style.display = 'block';
                } else {
                    variantSection.style.display = 'block';
                    simpleProductSection.style.display = 'none';
                }
            }

            // Initialize the visibility based on the selected product type on page load
            window.onload = function() {
                var productType = document.getElementById('product_type').value;
                displayDiv(productType);
            }

            $(document).on('click', '.save_item', function(e) {

                e.preventDefault();

                var type = $(this).data('name');

                var field = $(this).data('title');

                var name_id = $('#' + field + '_name').val();

                var name_text = $("#" + field + "_name option:selected").text();

                var value_id = $('#' + field + '_value').val();

                var value_text = $("#" + field + "_value option:selected").toArray().map(item => item.text)
                    .join();

                var price = $('#selling_price').val();

                console.log('value_text: ', value_text);

                if (name_id == '') {

                    Swal.fire(

                        "Error",

                        "Please select atleast one " + field + " name.",

                        "error"

                    )

                } else if (value_id == '') {

                    Swal.fire(

                        "Error",

                        "Please select atleast one " + field + " value.",

                        "error"

                    )

                } else {

                    let url = "{{ route('admin-product-variant-add') }}";

                    $.ajaxSetup({

                        headers: {

                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                        }

                    });

                    $.ajax({

                        type: "POST",

                        url: url,

                        data: {
                            name_id: name_id,
                            name_text: name_text,
                            value_id: value_id,
                            value_text: value_text,
                            type: type,
                            price: price
                        },

                        success: function(data) {

                            if (data.type == type) {

                                $('#' + field + '-table').html(data.data);

                                $('#' + field + '_name').val(null).trigger('change');

                                $('#' + field + '_value').val(null).trigger('change');

                            }
                            /*  else {

                                                $('.variant_name').append("<option value='"+data.data+"'>"+item+"</option>");

                                                $('#new_item').val('');

                                                $('#'+variant_type).val('');

                                                $('#addItemModal').modal('hide');

                                            } */

                        },

                        error: function(data) {

                            console.log('Error:', data);

                        }

                    });
                }
            })

            $(document).on('keyup', '.stock', function() {

                var parent = $(this).data('parent');

                $('.parent-' + parent).val($(this).val());

            })

            function getAttributeValues(attributeId) {
                const attributeValuesSelect = document.getElementById('attribute_value_id');
                attributeValuesSelect.innerHTML =
                    '<option value="">Select Attribute Value</option>'; // Clear previous values

                if (attributeId) {
                    $.ajax({
                        url: "{{ route('admin-product-attribute-values') }}",
                        method: 'POST',
                        data: {
                            id: attributeId,
                            _token: '{{ csrf_token() }}' // Include CSRF token
                        },
                        success: function(response) {
                            if (response.success) {
                                response.data.forEach(value => {
                                    const option = document.createElement('option');
                                    option.value = value.id;
                                    option.textContent = value.name;
                                    attributeValuesSelect.appendChild(option);
                                });
                            } else {
                                console.error('Error fetching attribute values:', response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error:', error);
                        }
                    });
                }
            }

            function createOrUpdateVariant(item, type, variant_id, color_name) {

                let url = "{{ route('admin-product-variant-add') }}";

                $.ajaxSetup({

                    headers: {

                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                    }

                });

                $.ajax({

                    type: "POST",

                    url: url,

                    data: {
                        item: item,
                        type: type,
                        variant_id: variant_id,
                        color_name: color_name
                    },

                    success: function(data) {

                        //console.log("data: ", data);

                        if (color_name == "") {
                            if (data.status == 'success') {
                                $('#size-variants-select').append("<option value='" + data.data + "'>" +
                                    data.name + "</option>");
                            }
                        } else {
                            if (data.status == 'success') {
                                $('#color-variants-select').append("<option value='" + data.data + '-' +
                                    data.color + "'>" + data.name +
                                    "</option>");
                            }
                        }
                        $('#add_new_item').val('');
                        $('#color_name').val('');
                        $('#color_code').val('');
                        $('#add_new_item').val('');
                        $('#addItemModal').modal('hide');
                        show_message(data.msg, data.status);
                    },

                    error: function(data) {

                        console.log('Error:', data);

                    }

                });

            }

            function createOrUpdateAttribute(item, type, attribute_id = '') {

                let url = "{{ route('admin-product-attribute-add') }}";

                $.ajaxSetup({

                    headers: {

                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                    }

                });

                $.ajax({

                    type: "POST",

                    url: url,

                    data: {
                        item: item,
                        type: type,
                        attribute_id: attribute_id
                    },

                    success: function(data) {

                        //console.log("data: ", data);

                        $('#' + type).append("<option value='" + data.data + "'>" + data.name +
                            "</option>");

                        $('#add_new_item').val('');

                        $('#variant_type').val('');

                        $('#addItemModal').modal('hide');

                    },

                    error: function(data) {

                        console.log('Error:', data);

                    }

                });

            }

            function getSellingPrice() {
                let price = $('#buying_price').val();
                let discount = $('#discount').val();
                let discount_type = $('#discount_type').val();
                if (discount_type == 'flat') {
                    return Math.round(parseInt(price) - parseInt(discount));
                } else {
                    return Math.round(parseInt(price) - ((parseInt(price) * parseInt(discount)) / 100));
                }
            }

            $(document).ready(function() {
                // Function to calculate and update selling price
                function getSellingPrice() {
                    let price = parseInt($('#buying_price').val()) || 0;
                    let discount = parseInt($('#discount').val()) || 0;
                    let discount_type = $('#discount_type').val();

                    let sellingPrice;

                    if (discount_type === 'flat') {
                        sellingPrice = Math.round(price - discount);
                    } else {
                        sellingPrice = Math.round(price - (price * (discount / 100)));
                    }

                    // Ensure selling price doesn't go below zero
                    sellingPrice = Math.max(sellingPrice, 0);
                    // Update the selling price display
                    $('#selling_price').val(sellingPrice);
                }
                // Attach event listeners to relevant inputs
                $('#buying_price, #discount, #discount_type').on('input change', getSellingPrice);
                // Initial calculation
                getSellingPrice();
            });

            function setTextareaValues(formId) {

                $('#' + formId).find('textarea').each(function() {

                    var editor = CKEDITOR.instances[this.name];

                    if (editor) {

                        this.value = editor.getData();

                    }

                });

            }

            function deleteImage(id, imgId, typ) {
                if (confirm('Are you sure you want to delete this image?')) {
                    $.ajax({
                        url: '{{ route('admin-product-productgalleryimgdelete') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id,
                            imgId: imgId,
                            typ: typ
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                    });
                }
            }
        })

        function getsubcategory(category_id) {

            $.ajax({

                type: "GET",

                url: "{{ route('admin-product-ajax-subcategory') }}",

                data: {

                    catid: category_id

                },

                headers: {

                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                },

                success: function(response) {

                    var html = '<option value="">Select Subcategory</option>';

                    if (response.success) {

                        $.each(response.subcategories, function(index, subcat) {

                            html += '<option value="' + subcat.parent_id + '">' + subcat.name +
                                '</option>';

                        });

                    } else {

                        html += '<option value="">No Subcategories Available</option>';

                    }

                    $("#subcategory_id").html(html);

                },

                error: function(xhr, status, error) {

                    console.error('AJAX Error: ' + status + error);

                    $("#subcategory_id").html('<option value="">Error fetching subcategories</option>');

                }

            });
        }

        function updateSimpleVariant() {
            // Define existingUnits as a static-like variable
            updateSimpleVariant.existingUnits = updateSimpleVariant.existingUnits || {};
            const existingUnits = updateSimpleVariant.existingUnits;

            const selectedVariantValues = $('#variant-values').select2('data');
            const variantContainer = $('#simple-variant-details');
            const mrpPriceNew = $('#buying_price').val();
            const sellingPriceNew = $('#selling_price').val();
            const prdSKU = $('#sku').val().trim() !== '' ? $('#sku').val().trim() : 'SKU';

            // Save current unit values to `existingUnits`
            $('input[name^="simple_units["]').each(function() {
                const variantId = $(this).attr('data-colorid'); // Get the variant ID
                existingUnits[variantId] = $(this).val(); // Save the current value
            });

            // Clear existing content
            variantContainer.empty();

            if (selectedVariantValues.length === 0) {
                // Reset total units if no values are selected
                $('#simple_total_unit_display').val(0);
                return;
            }

            selectedVariantValues.forEach(function(item) {
                const variantId = item.id;
                const variantName = item.text.trim();
                if (!variantName) return;

                // Get the existing unit value for this variant if available
                const existingUnitValue = existingUnits[variantId] || '';

                // Create the accordion structure dynamically
                const accordionHtml = `
            <div class="accordion-item" id="color-group-${variantId}">
                <div class="accordion-header" id="heading-${variantId}">
                    <div class="accordion-button" >
                        <input type="hidden" name="simple_varint_sku[${variantId}]"  value="${prdSKU}_${variantName}">
                        <div class="col-md-2 form-group mb-0">${variantName}</div>
                        <div class="col-md-3 form-group mb-0">${prdSKU}_${variantName}</div>
                        <div class="col-md-3 form-group mb-0">
                            <input name="simple_mrp[${variantId}]" class="form-control total_var_mrp mrp_price v_price_${variantId}" 
                                value="${mrpPriceNew}" 
                                type="text" 
                                id="tot_mrp_price-${variantId}" 
                                placeholder="MRP" 
                                data-colorid="${variantId}">
                        </div>
                        <div class="col-md-2 form-group mb-0">
                            <input name="simple_units_selling_price[${variantId}]" class="form-control total_var_mrp mrp_price total-selling-price-${variantId}" 
                                value="${sellingPriceNew}" 
                                type="text" 
                                id="tot_selling_price-${variantId}" 
                                placeholder="Selling Price" 
                                data-colorid="${variantId}">
                        </div>
                        <div class="col-md-2 form-group mb-0">
                            <input name="simple_units[${variantId}]" class="form-control total-available-unit-${variantId}" 
                                type="number" 
                                placeholder="Total Available Units" 
                                value="${existingUnitValue}" 
                                data-colorid="${variantId}" 
                                oninput="simpleteculateTotalUnits()">
                        </div>
                    </div>
                </div>
                <div id="collapse-${variantId}" class="accordion-collapse collapse show" 
                    aria-labelledby="heading-${variantId}" data-bs-parent="#variant-details">
                    <div class="size-groups"></div>
                </div>
            </div>`;
                variantContainer.append(accordionHtml);
            });

            // Recalculate total units on every update
            simpleteculateTotalUnits();
        }

        // Function to calculate the total units after input changes
        function simpleteculateTotalUnits() {
            let totalUnits = 0;

            // Select all inputs with the name attribute starting with "simple_units["
            $('input[name^="simple_units["]').each(function() {
                const value = parseInt($(this).val()); // Parse the value as an integer
                if (!isNaN(value)) {
                    totalUnits += value; // Add to the total only if the value is valid
                }
            });

            // Update the total units display
            $('#simple_total_unit_display').val(totalUnits);
        }

        // Event Listener: Handle change in #variant-values
        $('#variant-values').on('change', function() {
            updateSimpleVariant();
        });




        document.querySelectorAll('.delete-icon').forEach(icon => {
            icon.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                const imageName = this.getAttribute('data-name');

                if (confirm('Are you sure you want to delete this image?')) {
                    fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Ensure to send CSRF token if needed
                            },
                            body: JSON.stringify({
                                name: imageName
                            })
                        })
                        .then(response => {
                            if (response.ok) {
                                // Optionally, remove the image from the DOM
                                this.closest('div.position-relative').remove();
                                alert('Image deleted successfully.'); // Success message
                            } else {
                                alert('Error deleting image.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }
            });
        });
    </script>
@endpush
