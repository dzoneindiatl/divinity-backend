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

    <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>

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

    <div class="col-xl-12 accordion" id="accordian-product">

        <form action="" method="post" id="productForm" enctype="multipart/form-data">

            <input type="hidden" name="newProductid" value="{{(isset($product)?$product->id : 0) }}">

            @csrf

            <div class="card custom-card accordion-item">

                <div class="card-header accordion-button" data-bs-toggle="collapse" data-bs-target="#basicinfo-section" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">

                    <div class="card-title">

                        {{ ucfirst($type) }} Product

                    </div>

                </div>


                <div class="card-body accordion-collapse collapse show" id="basicinfo-section">

                    <div class="row">

                        <div class="col-xl-6">

                            <div class="card-body p-0">

                                <div class="mb-3">

                                    <label for="name" class="form-label"><span class="text-danger">*
                                        </span>Title</label>

                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"  placeholder="Enter Product Name" value="{{ (isset($product) ? $product->name : old('name')) }}">

                    
                                    <div class="invalid-feedback" id="nameError">

                                        {{ $errors->first('name') }}

                                    </div>

        
                                </div>

                            </div>

                        </div>

                        <div class="col-xl-6">

                            <div class="card-body p-0">

                                <div class="mb-3">

                                    <label for="name" class="form-label"><span class="text-danger">* </span>SKU</label>

                                    <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ (isset($product) ? $product->sku : old('sku')) }}" placeholder="Enter Product SKU">

                                    <div class="invalid-feedback" id="skuError">

                                        {{ $errors->first('sku') }}

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-xl-4">

                            <div class="card-body p-0">

                                <div class="mb-3">

                                    <label for="name" class="form-label">HSN</label>

                                    <input type="text" class="form-control @error('hsn') is-invalid @enderror" id="hsn" name="hsn" value="{{ (isset($product) ? $product->hsn : old('hsn')) }}" placeholder="Enter Product HSN">

                                </div>

                            </div>

                        </div>

                        <div class="col-xl-4">

                            <div class="card-body p-0">

                                <div class="mb-3">

                                    <label for="name" class="form-label">Bar Code</label>

                                    <input type="text" class="form-control @error('bar_code') is-invalid @enderror" id="bar_code" name="bar_code" value="{{ (isset($product) ? $product->bar_code : old('bar_code')) }}" placeholder="Enter Product Bar Code">

                                    @if ($errors->has('bar_code'))

                                    <div class="invalid-feedback">

                                        {{ $errors->first('bar_code') }}

                                    </div>

                                    @endif

                                </div>

                            </div>

                        </div>
                      
                       
                        <!-- <div class="col-xl-4">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="is_active" class="form-label"><span class="text-danger">*
                                        </span>Status</label>
                                    <select name="is_active" id="is_active" class="form-control @error('is_active') is-invalid @enderror">
                                        @if(!empty($product))
                                        <option value="">Select</option>
                                        <option value="1" {{$product->is_active ==1?'selected':''}}>Publish</option>
                                        <option value="2" {{$product->is_active ==2?'selected':''}}>Draft</option>
                                        <option value="3" {{$product->is_active ==3?'selected':''}}>UnPublish</option>
                                        @else
                                        <option value="">Select</option>
                                        <option value="1">Publish</option>
                                        <option value="2">Draft</option>
                                        <option value="3">UnPublish</option>
                                        @endif
                                    </select>
                                    <div class="invalid-feedback" id="isactiveError">
                                    {{ $errors->first('is_active') }}
                                </div>
                                </div>
                            </div>
                        </div> -->

                        <div class="col-xl-12">

                            <div class="card-body p-0">

                                <div class="mb-3">

                                    <label for="massage" class="form-label">Short Description</label>

                                    <textarea type="text" class="form-control @error('massage') is-invalid @enderror" id="massage" name="massage" value="" placeholder="Enter Product massage">{{ (isset($product) ? $product->list_description : old('massage')) }}</textarea>

                                    <div class="invalid-feedback" id="massageError">
 
                                        {{ $errors->first('massage') }}

                                    </div>

                                </div>

                            </div>

                        </div>
                        <div class="col-xl-6 mb-3">

                            <label for="description" class="form-label">Description</label>

                            <textarea class="form-control @error('title') is-invalid @enderror" name="description" id="description" cols="30" rows="5">{!! isset($product->description) ? $product->description: old('description') !!}</textarea>

                            @if ($errors->has('description'))

                            <div class=" invalid-feedback">

                                {{ $errors->first('description') }}

                            </div>

                            @endif

                        </div>

                        <div class="col-xl-6 mb-3">

                            <label for="meta_description" class="form-label"><span class="text-danger">*
                                </span>Specification</label>

                            <textarea class="form-control @error('specification') is-invalid @enderror" name="specification" id="specification" cols="30" rows="5">{!! isset($product->specification) ? $product->specification: old('specification') !!}</textarea>

                          

                            <div class="invalid-feedback" id="specificationError">

                                {{ $errors->first('specification') }}

                            </div>
                         

                        </div>

                        <div class="col-xl-6 mb-3">

                            <label for="product_details" class="form-label">Product Details</label>

                            <textarea class="form-control @error('title') is-invalid @enderror" name="product_details" id="product_details" cols="30" rows="5">{!! isset($product->product_details) ? $product->product_details: old('product_details') !!}</textarea>

                        </div>

                        <div class="col-xl-6 mb-3">

                            <label for="others" class="form-label">Others</label>

                            <textarea class="form-control @error('others') is-invalid @enderror" name="others" id="others" cols="30" rows="5">{!! isset($product->others) ? $product->others: old('others') !!}</textarea>

                        </div>

                        
                        <div class="col-xl-6">

                            <div class="card-body p-0">

                                <div class="mb-3">

                                    <label for="material" class="form-label"><span class="text-danger">*
                                        </span>Material</label>

                                    <input type="text" class="form-control @error('material') is-invalid @enderror" id="material" name="material" value="{{ (isset($product) ? $product->material : old('material')) }}" placeholder="Enter Product Material">

                                    <div class="invalid-feedback" id="materialError">

                                        {{ $errors->first('material') }}

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-xl-6">

                            <div class="card-body p-0">

                                <div class="mb-3">

                                    <label for="weight" class="form-label">Weight</label>

                                    <input type="text" class="form-control @error('weight') is-invalid @enderror" id="weight" name="weight" value="{{ (isset($product) ? $product->weight : old('weight')) }}" placeholder="Enter Product Weight">

                                    @if ($errors->has('weight'))

                                    <div class="invalid-feedback">

                                        {{ $errors->first('weight') }}

                                    </div>

                                    @endif

                                </div>

                            </div>

                        </div>

                        <div class="col-xl-6">

                            <div class="card-body p-0">

                                <div class="mb-3">

                                    <label for="style" class="form-label"><span class="text-danger">*
                                        </span>Style</label>

                                    <input type="text" class="form-control @error('style') is-invalid @enderror" id="style" name="style" value="{{ (isset($product) ? $product->style : old('style')) }}" placeholder="Enter Product Style">

                                    <div class="invalid-feedback" id="styleError">

                                        {{ $errors->first('style') }}

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-xl-6">

                            <div class="card-body p-0">

                                <div class="mb-3">

                                    <label for="country_origin" class="form-label">Country Of Origin</label>

                                    <input type="text" class="form-control @error('country_origin') is-invalid @enderror" id="country_origin" name="country_origin" value="{{ (isset($product) ? $product->country_origin : old('country_origin')) }}" placeholder="Enter Product Country Origin">

                                    @if ($errors->has('country_origin'))

                                    <div class="invalid-feedback">

                                        {{ $errors->first('country_origin') }}

                                    </div>

                                    @endif

                                </div>

                            </div>

                        </div>



                        <div class="col-xl-12 mb-3">

                            <label for="wash_care" class="form-label"><span class="text-danger">* </span>Wash
                                Care</label>

                            <textarea class="form-control @error('wash_care') is-invalid @enderror" name="wash_care" id="wash_care" cols="30" rows="5">{!! isset($product->wash_care) ? $product->wash_care: old('wash_care') !!}</textarea>

                            <div class="invalid-feedback" id="washcareError">

                                {{ $errors->first('wash_care') }}

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <div class="card custom-card accordion-item">

                <div class="card-header accordion-button" data-bs-toggle="collapse" data-bs-target="#category-section" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">

                    <div class="card-title">

                        Categories

                    </div>

                </div>

                <div class="card-body accordion-collapse collapse show" id="category-section">

                    <div class="row">

                        <div class="col-xl-4">

                            <div class="card-body p-0">

                                <div class="mb-3">

                                    <label for="brand_id" class="form-label">

                                        <span class="text-danger"></span>Brand

                                    </label>

                                    <select class="select2-original form-control" name="brand_id" id="brand_id">

                                        <option value="">None</option>

                                        @forelse ($brands as $brand)

                                        <option value="{{ $brand->id }}" {{(!empty($product->brand_id) && $product->brand_id == $brand->id) ? 'selected' : ''}}>

                                            {{ $brand->name }}

                                        </option>

                                        @empty

                                        <option value="" selected>No Data found</option>

                                        @endforelse

                                    </select>

                                </div>

                            </div>

                        </div>



                        <div class="col-xl-4">

                            <div class="card-body p-0">

                                <div class="mb-3">

                                    <label for="category" class="form-label"><span class="text-danger">*
                                        </span>Category</label>

                                        <select class="form-control @error('category_id') is-invalid @enderror" name="category_id" id="prdct_category_id" onchange="getRelatedSubCategories();">

                                        <option value="">None</option>

                                        @forelse ($categories as $category)

                                        <option value="{{ $category->id }}" {{(!empty($product) && $parent_category_id == $category->id) ? 'selected' : ''}}>

                                            {{ $category->name }}

                                        </option>

                                        @empty

                                        <option value="">No Data found</option>

                                        @endforelse

                                    </select>

                                    <div class="invalid-feedback" id="categoryidError"> 

                                        {{ $errors->first('prdct_category_id') }}

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-xl-4 subCategorieHide" style="display:none">

                            <div class="card-body p-0">

                                <div class="mb-3">

                                    <label for="sub_category_id" class="form-label">Subcategory</label>

                                    <select name="sub_category_id" id="prdct_sub_category_id" class="js-example-placeholder-single js-states form-control" onchange="getchildcategory();">
                                        <option value="">Select Subcategory</option>
                                        @if(isset($product->category->parentcategory->parent_id))

                                            <option value="{{$product->category->parentcategory->id}}" selected>{{$product->category->parentcategory->name}}</option>

                                        @elseif(isset($product->category->parent_id))

                                            <option value="{{$product->category->id}}" selected>{{$product->category->name}}</option>

                                        @endif
                                    </select>

                                </div>

                            </div>

                        </div>


                        
                        <div class="col-xl-4 childCategoryHide" style="display:none">

                            <div class="card-body p-0">

                                <div class="mb-3">

                                    <label for="child_category_id" class="form-label">Child Category</label>

                                    <select name="child_category_id" id="prdct_child_category_id" class="js-example-placeholder-single js-states form-control">
                                        <option value="">Select Child category</option>
                                        @if(isset($product->category->parentcategory->parent_id))
                                        <option value="{{$product->category->parentcategory->parent_id}}" selected>
                                            @foreach($categories as $category)
                                            {{(isset($product->category->parentcategory->parent_id) && $product->category->parentcategory->parent_id == $category->id) ? $product->category->name : ''}}
                                            @endforeach
                                        </option>
                                        @endif
                                    </select>

                                </div>

                            </div>

                        </div>





                        <div class="col-xl-4">

                            <div class="card-body p-0">

                                <div class="mb-3" style="display:{{ (isset($subCategories) && !empty($subCategories) ? 'block' : 'none') }}" id="subcategory-filter">

                                    <label for="sub_category_id" class="form-label">Sub Category</label>

                                    <select class="select2-original form-control" name="sub_category_id" id="sub_category_id" data-action="{{ route('admin-product-child-category-list') }}">

                                        <option value="">None</option>

                                        @if(isset($subCategories))

                                        @forelse ($subCategories as $category)

                                        <option value="{{ $category->id }}" {{(!empty($product->category_id) && $product->sub_category_id == $category->id) ? 'selected' : ''}}>

                                            {{ $category->name }}

                                        </option>

                                        @empty

                                        <option value="">No Data found</option>

                                        @endforelse

                                        @endif

                                    </select>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

           
            <div class="card custom-card accordion-item">

                <div class="card-header accordion-button" data-bs-toggle="collapse" data-bs-target="#price-section" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">

                    <div class="card-title">

                        Related products

                    </div>

                </div>

                <div class="card-body accordion-collapse collapse show" id="price-section">

                    <div class="row">

                        <div class="col-xl-4">

                            <div class="card-body p-0">

                                <div class="mb-3">

                                    <label for="categorys_id" class="form-label">Category</label>

                                    <select name="categorys_id" id="categorys_id" class="js-example-placeholder-single js-states form-control" onchange="getsubcategory(this.value);">

                                        <option value="">Select Category</option>
                                        @if(!empty($product) && isset($product))
                                        @foreach($categories as $categories)

                                        <option value="{{ $categories->id }}" {{(!empty($product->related_product_categores_id) && $product->related_product_categores_id == $categories->id) ? 'selected' : ''}}>
                                            {{ $categories->name }}
                                        </option>
                                        @endforeach
                                        @else
                                        @foreach($categories as $categories)
                                        <option value="{{$categories->id}}">{{ucfirst($categories->name)}}</option>
                                        @endforeach
                                        @endif
                                    </select> 
                                    <div class="invalid-feedback" id="categorysidError">
                                        {{ $errors->first('categorys_id') }}
                                     </div> 
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">

                            <div class="card-body p-0">

                                <div class="mb-3">

                                    <label for="subcategory_id" class="form-label">Subcategory</label>

                                    <select name="subcategory_id" id="subcategory_id" class="js-example-placeholder-single js-states form-control" onchange="getproduct(this.value);">
                                        <option value="">Select Subcategory</option>

                                        @if(!empty($product) && isset($product))

                                        @foreach($subcategory as $subcategorye)

                                        <option value="{{$subcategorye->id}}" {{($subcategorye->id == $product->related_product_subcategory_id) ? 'selected' :''}}>
                                            {{ucfirst($subcategorye->name)}}
                                        </option>

                                        @endforeach

                                        @endif

                                    </select>

                                </div>

                            </div>

                        </div>
                        @if(!empty($product) && isset($product))

                        @php

                        if (is_string($product->related_products)) {

                        $product->related_products = explode(',', $product->related_products);

                        }

                        @endphp
                        @endif
                        <div class="col-xl-4">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="Productid" class="form-label">Related Product</label>
                                    <select name="Product_id[]" id="Productid" class="js-example-placeholder-single js-states form-control" multiple>
                                        <option value="">Select Product</option>
                                        @if(!empty($product) && isset($product))
                                        @foreach($subproducts as $products)
                                        <option value="{{ $products->id }}" {{ (is_array($product->related_products) && in_array($products->id, $product->related_products)) ? 'selected' : '' }}>
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

                <div class="card-header accordion-button" data-bs-toggle="collapse" data-bs-target="#seo-section" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">

                    <div class="card-title">

                        SEO Details

                    </div>

                </div>

                <div class="card-body accordion-collapse collapse show" id="seo-section">

                    <div class="row">

                        <div class="col-xl-6">

                            <div class="card-body p-0">

                                <div class="mb-3">

                                    <label for="meta_title" class="form-label">Meta Title</label>

                                    <input type="text" class="form-control" id="meta_title" name="meta_title" placeholder="Enter Product Meta Title" value="{{ (isset($product) ? $product->meta_title : old('meta_title')) }}">

                                </div>

                            </div>

                        </div>

                        <div class="col-xl-6">

                            <div class="card-body p-0">

                                <div class="mb-3">

                                    <label for="meta_keywords" class="form-label">Meta Keywords</label>

                                    <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" placeholder="Enter Product Meta Keywords" value="{{ (isset($product) ? $product->meta_keywords : old('meta_keywords')) }}">

                                </div>

                            </div>

                        </div>

                        <div class="col-xl-6 mb-3">

                            <label for="meta_description" class="form-label">Meta Description</label>

                            <textarea class="form-control" name="meta_description" id="meta_description" cols="30" rows="5">{!! isset($product) ? $product->meta_description: old('meta_description') !!}</textarea>

                        </div>

                        <div class="col-xl-6 mb-3">

                            <label for="seo_content" class="form-label">Seo Content</label>

                            <textarea class="form-control" name="seo_content" id="seo_content" cols="30" rows="5">{!! isset($product) ? $product->seo_content: old('seo_content') !!}</textarea>

                        </div>

                    </div>

                </div>

            </div>
            <div class="card custom-card accordion-item">
                <div class="card-header accordion-button" data-bs-toggle="collapse" data-bs-target="#attributes-section" aria-expanded="true" aria-controls="attributes-section">
                    <div class="card-title">Attributes</div>
                </div>
                <div class="card-body accordion-collapse collapse show" id="attributes-section">
                    <div class="row" id="attribute-pairs-container">
                        <!-- Attribute-value pairs will be inserted here -->
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button id="add-attribute-button" class="btn btn-primary" type="button">+ Select Attribute</button>
                            <button id="add-new-attribute-button" class="btn btn-secondary" type="button">+ Add New Attribute</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="addAttributeModal" tabindex="-1" aria-labelledby="addAttributeModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addAttributeModalLabel">Add New Attribute</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                            <button type="button" id="saveAttributeButton" class="btn btn-primary">Save Attribute</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card custom-card accordion-item configurable">
            </div>

            <div class="card custom-card accordion-item">
                <div class="card-header accordion-button" data-bs-toggle="collapse" data-bs-target="#product-type-section" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                    <div class="card-title">
                        Product Type
                    </div>
                </div>
                <div class="card-body accordion-collapse collapse show" id="product-type-section">
                    <div class="row">
                        <div class="col-xl-4">
                            <div class="card-body p-0">
                                <div class="mb-3">                                    
                                    <label for="product_type" class="form-label"><span class="text-danger">*</span>Product Type</label>
                                    <select name="product_type" id="product_type" class="form-control @error('product_type') is-invalid @enderror" onchange="displayDiv(this.value)">
                                        @if(!empty($product))
                                            <option value="">Select Type</option>
                                            <option value="1" {{$product->product_type ==1?'selected':''}}>Simple</option>
                                            <option value="2" {{$product->product_type ==2?'selected':''}}>Configurable</option>
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
            <div class="card custom-card accordion-item">
                <div class="card-header accordion-button" data-bs-toggle="collapse" data-bs-target="#max-selling-units-section" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                    <div class="card-title">
                        Selling Units
                    </div>
                </div>
                <div class="card-body accordion-collapse collapse show" id="max-selling-units-section">
                    <div class="row">
                        <div class="col-xl-3">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="name" class="form-label"><span class="text-danger">* </span>Maximum Selling Units</label>
                                    <input type="number" class="form-control @error('max_selling_units') is-invalid @enderror" id="max_selling_units" name="max_selling_units" placeholder="Enter Maximum Selling Units" value="{{ (isset($product) ? $product->max_selling_units : old('max_selling_units')) }}">
                                    @if ($errors->has('max_selling_units'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('max_selling_units') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="name" class="form-label"><span class="text-danger">* </span>Minimum Selling Units</label>
                                    <input type="number" class="form-control @error('min_selling_units') is-invalid @enderror" id="min_selling_units" name="min_selling_units" placeholder="Enter Minimun Selling Units" value="{{ (isset($product) ? $product->min_selling_units : old('min_selling_units')) }}">
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

            <div class="card custom-card accordion-item">

                <div class="card-header accordion-button" data-bs-toggle="collapse" data-bs-target="#price-section" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                    <div class="card-title">Pricing</div>
                </div>

                    <div class="card-body accordion-collapse collapse show" id="price-section">
                        <div class="row">
                            <div class="col-xl-3">
                                <div class="card-body p-0">
                                    <div class="mb-3">
                                        <label for="name" class="form-label"><span class="text-danger">* </span>MRP</label>
                                        <input type="text" class="form-control @error('buying_price') is-invalid @enderror" id="buying_price" name="buying_price" placeholder="Enter Product Price" value="{{ (isset($product) ? $product->buying_price : old('buying_price')) }}">
                                        @if ($errors->has('buying_price'))
                                        <div class="invalid-feedback">{{ $errors->first('buying_price') }}</div>
                                        @endif
                                    </div>
                                    <div class="invalid-feedback" id="buyingpriceError">
                                        {{ $errors->first('buying_price') }}
                                    </div>   
                                </div>
                            </div>

                            <div class="col-xl-3">
                                <div class="card-body p-0">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Discount</label>
                                        <input type="number" class="form-control @error('discount') is-invalid @enderror" id="discount" name="discount" value="{{ (isset($product) ? $product->discount : 0) }}" placeholder="Enter Product discount">
                                        @if ($errors->has('discount'))
                                        <div class="invalid-feedback">{{ $errors->first('discount') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3">
                                <div class="card-body p-0">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Discount type</label>
                                        <select name="discount_type" id="discount_type" class="js-example-placeholder-single js-states form-control">
                                            <option value="flat" {{ (isset($product) && $product->discount_type == 'flat' ? 'selected' : '') }}>Flat</option>
                                            <option value="percentage" {{ (isset($product) && $product->discount_type == 'percentage' ? 'selected' : '') }}>Percentage</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3">
                                <div class="card-body p-0">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">MRP</label>
                                        <input type="text" class="form-control @error('selling_price') is-invalid @enderror" id="selling_price" name="selling_price" value="{{ (isset($product) ? $product->selling_price : old('selling_price')) }}" readonly>
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

                        <div class="row">
                            <div class="col-xl-3">
                                <div class="card-body p-0">
                                    <div class="mb-3">
                                        <div class="form-radio">
                                            <input class="form-radio-input" name="is_including_taxes" type="radio" value="1" id="flexCheckInclusiveTaxes" {{ isset($product) && $product->is_including_taxes ? 'checked' : '' }}>
                                            <label class="form-label form-radio-label" for="flexCheckInclusiveTaxes">Inclusive of Taxes?</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3">
                                <div class="card-body p-0">
                                    <div class="mb-3">
                                        <div class="form-radio">
                                            <input class="form-radio-input" name="is_including_taxes" type="radio" value="0" id="flexCheckExclusiveTaxes" {{ isset($product) && !$product->is_including_taxes ? 'checked' : '' }}>
                                            <label class="form-label form-radio-label" for="flexCheckExclusiveTaxes">Exclusive of Taxes?</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>

            <div class="card custom-card accordion-item" id="variant-section" style="display: none;">
                <div class="card-header accordion-button" data-bs-toggle="collapse" data-bs-target="#variant-section" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                    <div class="card-title" style="width: 100%;">
                        <div class="row">
                            <div class="col-xl-10">Variants</div>
                            
                        </div>

                    </div>
                </div>
                <div class="card-body default-variant-section accordion-collapse collapse show">

                    <div class="row">
                        <div class="col-md-5 form-group mt-3">
                            <label>Color</label>
                            <select name="color_variants[]" id="color-variants-select"  class="js-example-placeholder-single js-states form-control variant_name" multiple>
                                <option value="add_item_color">Add a item</option>
                                @if(!empty($colors) && isset($colors))
                                @foreach($colors as $color)
                                <option value="{{ $color->id }}">{{ ucfirst($color->name) }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4 form-group mt-3">
                            <label>Size</label>
                            <select name="size_variants[]" id="size-variants-select" class="js-example-placeholder-single js-states form-control variant_name" multiple>
                                <option value="add_item_size">Add a item</option>
                                @if(!empty($sizes) && isset($sizes))
                                @foreach($sizes as $size)
                                <option value="{{ $size->id }}">{{ ucfirst($size->name) }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>       
                        <div class="col-md-2 form-group mt-3">
                            <label>Total Availabe</label>
                            <input type="text" class="form-control" id="totalDisplay" value="0" disabled>
                        </div>             
                    </div>
                
                    <div class="accordion" id="variant-details">

                    </div>
                    <div class="modal fade" id="uploadVariantImageModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="uploadModalLabel">Upload Images</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">                                   
                                    <div class="row">
                                        <div class="col-xl-12 mb-3">
                                            <label for="meta_description" class="form-label">Media</label>
                                            <div id="dropzone-variant-images" class="dropzone"></div>
                                            <div class="card border bg-transparent mt-3 loadVariantImagesData"></div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>  
                    </div>

                    <div class="card-body default-variant-section accordion-collapse collapse show" id="attribute-value-section">

                        <div id="accordionvaluesection">

                        </div>
                    </div>
                
                </div>
            </div>

            <div class="card custom-card accordion-item" id="simple-product-section">
                    <div class="card-header accordion-button" data-bs-toggle="collapse" data-bs-target="#simple-product-section-body" aria-expanded="true" aria-controls="simple-product-section-body">
                        <div class="card-title">Variants</div>
                    </div>
                    <div class="card-body accordion-collapse collapse show" id="simple-product-section-body">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card-body p-0">
                                    <div class="row">
                                        <label for="variant_value" class="form-label">Variant Value</label>
                                        <div class="row">
                                            <div class="col-md-2 form-group mt-3">
                                                <div class="form-check">
                                                    <input class="form-check-input variant_value" type="radio" name="variant_type" id="variant_color" value="color" checked>
                                                    <label class="form-check-label" for="variant_color">
                                                        Color
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-2 form-group mt-3">
                                                <div class="form-check">
                                                    <input class="form-check-input variant_value" type="radio" name="variant_type" id="variant_size" value="size">
                                                    <label class="form-check-label" for="variant_size">
                                                        Size
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-2 form-group mt-3 color-variant">
                                                <label>Color</label>
                                                <select name="color_id" id="colorId" class="js-example-placeholder-single js-states form-control">
                                                    <option value="">Select Color</option>
                                                    @if(!empty($colors) && isset($colors))
                                                        @foreach($colors as $color)
                                                        <option value="{{ $color->id }}">
                                                            {{ ucfirst($color->name) }}
                                                        </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-2 form-group mt-3 size-variant" style="display: none;">
                                                <label>Size</label>                                    
                                                <select name="size_id" id="sizeId" class="js-example-placeholder-single js-states form-control">
                                                    <option value="">Select Size</option>
                                                    @if(!empty($sizes) && isset($sizes))                                        
                                                        @foreach($sizes as $size)
                                                        <option value="{{ $size->id }}">
                                                            {{ ucfirst($size->name) }}
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
                    </div>
                  
               <div class="card-body accordion-collapse collapse show" id="media-section">

                    <div class="row">

                        <div class="col-xl-12 mb-3">

                            <label for="meta_description" class="form-label">Media</label>

                            <div id="imageDropzone" class="dropzone"></div>

                        </div>

                            <div class="card border bg-transparent mt-3 loadImagesData">

                                @if(isset($image_data))

                                @include('admin.product_new.load-images',['images' => $image_data])

                                @endif

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
                <button type="submit" name="save" style="margin-left: 20px;" value="1" class="btn btn-info">Save & Continue</button>
            </div>

            </div>

    </form>

</div>

</div>


    <!-- Modal -->
    <div class="modal fade" id="addItemModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">

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

                                <input type="text" class="picker form-control" id="color" placeholder="Enter Item" style="display: none;" />

                            </div>

                        </div>

                    </div>

                    <div class="color" style="display: none;">

                        <div class="row">

                            <input type="hidden" id="add_variant_type" value="color" />

                            <div class="col-md-6">

                                <label for="color_code" class="form-label">Select Color</label>

                                <input type="text" class="picker form-control coloris" id="color_code" value="#cc458faa" />

                            </div>

                            <div class="col-md-6">

                                <label for="color_name" class="form-label">Color Name</label>

                                <input type="text" class="form-control" id="color_name" placeholder="Enter Color Name" />

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
<script src="{{ asset('assets/js/repeater.js')}}"></script>
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
            const formattedAttributes = attributes.map(attribute => ({
                id: attribute.id, // Or any unique identifier
                text: attribute.name // Or any text you want to display
            }));
            // Initialize Select2 with dynamic data
            $('#attributeName').select2({
                placeholder: 'Select an option',
                width:"100%",
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
                    $('.mrp_price').val(buying_price);
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
        const selectedSizes = $('#size-variants-select').select2('data');
        const variantDetailsDiv = document.getElementById('variant-details');

        const existingValues = {};
        selectedColors.forEach(color => {
            selectedSizes.forEach(size => {
                const colorId = color.id;
                const sizeId = size.id;
                existingValues[`${colorId}_${sizeId}_sku`] = $(`input[name="variant_sku[${colorId}][${sizeId}]"]`).val();
                existingValues[`${colorId}_${sizeId}_selling_price`] = $(`input[name="variant_selling_price[${colorId}][${sizeId}]"]`).val();
                existingValues[`${colorId}_${sizeId}_available_unit`] = $(`input[name="variant_available_unit[${colorId}][${sizeId}]"]`).val();
                existingValues[`${colorId}_${sizeId}_selling_unit`] = $(`input[name="variant_selling_unit[${colorId}][${sizeId}]"]`).val();
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
                            <div class="col-md-2 form-group mb-0"><input class=" form-control mrp_price total-selling-price-${color.id}" type="text" placeholder="MRP" disabled/> </div>
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
            var variantCounts=0;
            selectedSizes.forEach(size => {
                const variantDiv = document.createElement('div');                
                variantDiv.className = "row";
                variantDiv.dataset.colorId = color.id;

                variantDiv.innerHTML = `
                    <div class="col-md-1 form-group mt-3 text-center">
                        <h6>${size.text}</h6>
                    </div>
                    <div class="col-md-2 form-group mt-3">
                        <input type="text" class="form-control skuChangeValue" placeholder="SKU" data-color="${color.text}"  data-size="${size.text}" name="variant_sku[${color.id}][${size.id}]">
                    </div>
                    <div class="col-md-2 form-group mt-3">
                        <input type="number" class="form-control price mrp_price" placeholder="MRP" name="variant_selling_price[${color.id}][${size.id}]" required>
                    </div>
                    <div class="col-md-2 form-group mt-3">
                        <input type="number" class="form-control price" placeholder="Total Available Units" name="variant_available_unit[${color.id}][${size.id}]">
                    </div>
                    <div class="col-md-2 form-group mt-3">
                        <input type="number" class="form-control price" placeholder="Max Selling Units" name="variant_selling_unit[${color.id}][${size.id}]">
                    </div>
                `;
                colorDiv.querySelector('.size-groups').appendChild(variantDiv);
                 // Re-apply the existing values
                $(`input[name="variant_sku[${color.id}][${size.id}]"]`).val(existingValues[`${color.id}_${size.id}_sku`]+'_'+color.text+"_"+size.text);
                $(`input[name="variant_selling_price[${color.id}][${size.id}]"]`).val(existingValues[`${color.id}_${size.id}_selling_price`]);
                $(`input[name="variant_available_unit[${color.id}][${size.id}]"]`).val(existingValues[`${color.id}_${size.id}_available_unit`]);
                $(`input[name="variant_selling_unit[${color.id}][${size.id}]"]`).val(existingValues[`${color.id}_${size.id}_selling_unit`]);

                //variantDiv.querySelector(`input[name="variant_selling_price[${color.id}][${size.id}]"]`).addEventListener('input', () => updateTotals(color.id));
                variantDiv.querySelector(`input[name="variant_available_unit[${color.id}][${size.id}]"]`).addEventListener('input', () => updateTotals(color.id));
                variantCounts++;
                colorDiv.querySelector(`.variant-count-${color.id}`).innerHTML = variantCounts+" Variants"
            });
            colorDiv.querySelector(`.total-selling-price-${color.id}`).value = document.getElementById('buying_price').value;
        });
        updateAllSellingPrices(existingValues,color.id);
        updateAllSKUProdcut(existingValues,color.id);
        updateAllMaxUnit(existingValues,color.id);       
        document.getElementById('sku').addEventListener('input', updateAllVariantsInput('sku'));
        document.getElementById('buying_price').addEventListener('input', updateAllVariantsInput('buying_price'));
        document.getElementById('max_selling_units').addEventListener('input', updateAllVariantsInput('max_selling_units'));
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

    function updateAllSellingPrices(existingValues,colorId) {
        const baseSellingPrice = parseFloat(document.getElementById('buying_price').value);        
        if (!isNaN(baseSellingPrice)) {
            document.querySelectorAll('input[name^="variant_selling_price"]').forEach(input => {
                const colorId = input.name.match(/variant_selling_price\[(.*?)\]/)[1];
                const sizeId = input.name.match(/\[(\d+)\]/g)[1].replace(/\[|\]/g, '');
                if (existingValues[`${colorId}_${sizeId}_selling_price`] === undefined || existingValues[`${colorId}_${sizeId}_selling_price`] === '') {
                    input.value = baseSellingPrice;
                    input.dispatchEvent(new Event('input')); // Trigger input event to update totals
                    //updateTotals(colorId);
                }
            });
        }
    }

    function updateAllSKUProdcut(existingValues,colorId) {
        const productSKU = document.getElementById('sku').value;        
        if (productSKU !== "") {
            document.querySelectorAll('input[name^="variant_sku"]').forEach(input => {
                const colorId = input.name.match(/variant_sku\[(.*?)\]/)[1];
                const sizeId = input.name.match(/\[(\d+)\]/g)[1].replace(/\[|\]/g, '');
                if (existingValues[`${colorId}_${sizeId}_sku`] === undefined || existingValues[`${colorId}_${sizeId}_sku`] === '') {
                    input.value = productSKU;
                    input.dispatchEvent(new Event('input')); // Trigger input event to update totals
                }
            });
        }
    }

    function updateAllMaxUnit(existingValues,colorId) {
        const baseSellingUnit = parseFloat(document.getElementById('max_selling_units').value);        
        if (!isNaN(baseSellingUnit)) {
            document.querySelectorAll('input[name^="variant_selling_unit"]').forEach(input => {
                const colorId = input.name.match(/variant_selling_unit\[(.*?)\]/)[1];
                const sizeId = input.name.match(/\[(\d+)\]/g)[1].replace(/\[|\]/g, '');
                if (existingValues[`${colorId}_${sizeId}_selling_unit`] === undefined || existingValues[`${colorId}_${sizeId}_selling_unit`] === '') {
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
        $('.loadVariantImagesData').html('')
        $.ajax({
            type: "POST",
            url: "{{ route('admin-product-get-image-data') }}",
            data: {
                color_id: colorId
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {  
                openUploadModal(colorId);              
                if (response.status) {
                    console.log(response.status); 
                    console.log(response.data); 
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
        if(selectedColorId && selectedColorId != colorId){
            $('.loadVariantImagesData').html('')
        }
        selectedColorId = colorId;
        $('#uploadVariantImageModal').modal('show');
    }
    Dropzone.autoDiscover = false;
    const variantDropzone = new Dropzone("#dropzone-variant-images", {
        url: "{{route('admin-product-upload-images-new')}}",
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
            that.on("sending", function(file, xhr, formData) {            // Add additional data here
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
                    uploadedImageCell.querySelector('.remove-image-button').addEventListener('click', function() {
                        uploadedImageCell.innerHTML = '<button class="btn btn-primary upload-image-button">Choose Image</button>';
                        fileInput.value = ''; // Clear the file input
                        uploadedImageCell.querySelector('.upload-image-button').addEventListener('click', function(event) {
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
                var newOption = $('<option>', {value: response.id, text: response.name});
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
        var html = ` <div class="card" style="border:solid 1px black;">
                                <div class="card-header accordion-button" data-bs-toggle="collapse" data-bs-target="#customvarient_` + variantId + `"
                                    aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                                    <div class="card-title" id="title_` + variantId + `">
                                        Variance Name
                                    </div>
                                </div>
                                 <div class="card-body default-variant-section accordion-collapse collapse show" id="customvarient_` + variantId + `">
                                        <div class="row">
                                            <div class="col-md-12 form-group mt-3">
                                                <label>Option Name</label>
                                                <input type="text" name="varientname[` + variantId + `]" id='varientname' placeholder="Option Name like Size, Color" class="form-control myfiledata " attr_id="` + variantId + `">
                                            </div>
                                        </div>
                                           <div class="optiondiv">
                                                <div class="mainoptiondiv varientoptiondiv_` + variantId + `">
                                                    <div class="row">
                                                        <div class="col-md-11 form-group mt-3">
                                                            <label>Option Value</label>
                                                            <input type="text" id='optionname' name="optionname[` + variantId + `][]" placeholder="Option value Like L, XL, XXL" class="form-control myfiledata varientvalueoption varientopvalue_` + variantId + `" attr_id="` + variantId + `">
                                                        </div>
                                                        <div class="col-md-1 form-group">
                                                            <label style="visibility: hidden;">Option Value</label>
                                                            <a href="javascript:void(0);" class="btn btn-danger removeoptionvalue" attr_id="` + variantId + `"><i class="fa fa-remove"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group mt-3 text-left">
                                                    <a href="javascript:void(0);" class="btn btn-danger removevarient" attr_id="` + variantId + `">Delete</a>
                                                </div>
                                                <div class="col-md-6 form-group mt-3 text-right" style="text-align: right;">
                                                    <a href="javascript:void(0);" class="btn btn-info donevarient" attr_id="` + variantId + `">Done</a>
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
                                <input type="text" id='optionname' name="optionname[` + variantId + `][]" placeholder="Option value Like L, XL, XXL" class="form-control varientvalueoption myfiledata varientopvalue_` + variantId + `" attr_id="` + variantId + `">
                            </div>
                            <div class="col-md-1 form-group">
                                <label style="visibility: hidden;">Option Value</label>
                                <a href="javascript:void(0);" class="btn btn-danger removeoptionvalue" attr_id="` + variantId + `"><i class="fa fa-remove"></i></a>
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
            url: "{{route('admin-product-receivedata') }}",
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#accordionvaluesection').html(response);
            },
        });
    });

    @if(isset($product) && $product->product_type == 2)
    var formData = $('#productForm').serialize();
    $.ajax({
        url: "{{route('admin-product-receivedata') }}",
        method: 'POST',
        data: formData,
        success: function(response) {
            $('#accordionvaluesection').html(response);
        },
    });
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
                var html = '<option value="">Select Subcategory</option>';

                if (response.success) {

                $.each(response.subcategories, function(index, subcat) {

                html += '<option value="' + subcat.id + '">' + subcat.name + '</option>';

                });

                $('.subCategorieHide').show();
                $('.childCategoryHide').hide();

                } else {
                    $('.subCategorieHide').hide();
                    $('.childCategoryHide').hide();

                html += '<option value="">No Subcategories Available</option>';

                }
                $("#prdct_sub_category_id").html(html);

                $('#prdct_sub_category_id').select2({
                    placeholder: "Choose Sub Category",
                    width: "100%"
                });
            },
            error: function(xhr, status, error) {
                
            }
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
                    var currentSelections = $('#Productid').val() || [];

                    var options = '<option value="add_item">Add another Item</option>';

                    $.each(response.childcat, function(index, item) {
                        options += '<option value="' + item.id + '">' + item.name + '</option>';
                    });

                    $('#prdct_child_category_id').html(options);
                     $('.childCategoryHide').show();
                    $('#prdct_child_category_id').select2({
                        placeholder: "Choose item",
                        width: "100%"
                    });

                    $('#prdct_child_category_id').val(currentSelections).trigger('change');
                } else {
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
                    @foreach($attributesdata as $key=>$vadata)
                     <div class="col-xl-2">
                        <div class="card-body p-0">
                            <div class="mb-3">
                                <label for="attribute_{{ $vadata->id }}" class="form-label">{{ $vadata->name }}</label>
                                <select name="config_attribute[` + counter + `][{{ $vadata->id }}]" id="attribute_{{ $vadata->id }}" class="select2-original form-control variant_name">
                                    <option value="">Choose item</option>
                                    <option value="add_item">Add another Item</option>
                                    @foreach($variantData[$vadata->id] as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div class="col-xl-2">
                        <div class="card-body p-0">
                            <div class="mb-3">
                                <label for="qty" class="form-label">Qty</label>
                                <input name="qty[` + counter + `]" type="number" id="qty" class="form-control variant_name">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2">
                        <div class="card-body p-0">
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input name="price[` + counter + `]" type="number" id="price" class="form-control price">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2">
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
        
        const variant_product =  document.getElementById('accordionSUBPRODUCT');
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
                        <input type="number" class="form-control price"  placeholder="Total Available Units" name="variant_available_unit[]">                       
                    </div>
                    <div class="col-md-2 form-group mt-3">
                        <label>Max Selling Units</label>                                    
                        <input type="number" class="form-control price"  placeholder="Max Selling Units" name="variant_selling_unit[]">                       
                    </div>
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
        fileInput.name ="variants_product_image[]"

        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const uploadedImageCell = row.querySelector('.uploaded-image-cell');
                    uploadedImageCell.innerHTML = `<img src="${event.target.result}" class="uploaded-image" width="100">`;
                };
                reader.readAsDataURL(file);
            }
        });

        row.appendChild(fileInput);
        fileInput.click();
        //document.body.removeChild(fileInput);
    }


    document.addEventListener('DOMContentLoaded', function() {
        const attributes = @json($attributes); // Assuming attributes are passed as a JSON-encoded array from the backend

        document.getElementById('add-attribute-button').addEventListener('click', addAttributePair);
        document.getElementById('add-new-attribute-button').addEventListener('click', () => {
            $('#addAttributeModal').modal('show');
        });
        document.getElementById('saveAttributeButton').addEventListener('click', saveAttributePair);

        function addAttributePair() {
            const attributePairContainer = document.createElement('div');
            attributePairContainer.className = 'row mb-3 attribute-pair';

            const attributeCol = document.createElement('div');
            attributeCol.className = 'col-xl-4';
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
            valueCol.className = 'col-xl-4';
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
            removeCol.className = 'col-xl-4 d-flex align-items-center';
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
                    body: JSON.stringify({ attributeName, attributeValue })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.attribute_id && data.attribute_name) {
                        attributes.push({ id: data.attribute_id, name: data.attribute_name });

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
                    } else{
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
                    newInputField.innerHTML = `<input type="text" class="form-control option-value" placeholder="Option Value">`;

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
                    displayOptionValues(optionDiv, optionName.value, Array.from(optionValues).map(input => input.value).filter(value => value.trim() !== ''));
                }
            }
        }

        function displayOptionValues(optionDiv, optionName, optionValues) {
            
            const tableBody = document.getElementById('variant-table').querySelector('tbody');
            const sellingPrice = document.getElementById('selling_price') ? document.getElementById('selling_price').value : 0;
            const maxSellingUnits = document.getElementById('max_selling_units') ? document.getElementById('max_selling_units').value : 0;

            if (optionName.toLowerCase() === 'colour') {
                const existingColorRows = tableBody.querySelectorAll('.color-row');
                existingColorRows.forEach(row => {
                    if (row.querySelector('.variant-name').textContent.trim().toLowerCase() === 'colour') {
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
                                        <th>Max Selling Units</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Size variants will be added here dynamically -->
                                </tbody>
                            </table>
                        </td>
                        <td><input type="number" class="form-control color-price" value="${sellingPrice}" placeholder="Price" name="variants[${colorValue}][price]"></td>
                        <td><input type="number" class="form-control color-available-input" placeholder="Available" min="0" readonly name="variants[${colorValue}][total_units]"></td>
                        <td><input type="number" class="form-control" value="${maxSellingUnits}" placeholder="Max Selling Units" name="variants[${colorValue}][max_selling_units]"></td>
                        <td class="uploaded-image-cell"><button class="btn btn-primary upload-image-button">Choose File</button></td>
                    `;
                    tableBody.appendChild(row);
                    row.querySelector('.color-price').addEventListener('input', updateColorPriceRange);

                    row.addEventListener('click', function(event) {
                        if (event.target.tagName.toLowerCase() !== 'input') {
                            const sizeTable = row.querySelector('.size-table');
                            sizeTable.style.display = sizeTable.style.display === 'none' ? 'table' : 'none';
                        }
                    });
                
                    row.querySelector('.upload-image-button').addEventListener('click', function(event) {
                        event.preventDefault();
                        openImageUploadModal(row);
                    });
                });
            } else if (optionName.toLowerCase() === 'size') {
                const colorRows = tableBody.querySelectorAll('.color-row');
                colorRows.forEach(row => {
                    const sizeTableBody = row.querySelector('.size-table tbody');
                    optionValues.forEach(sizeValue => {
                        const existingSizeRow = sizeTableBody.querySelector(`tr[data-size="${sizeValue}"]`);
                        if (!existingSizeRow) {
                            const sizeRow = document.createElement('tr');
                            sizeRow.setAttribute('data-size', sizeValue);
                            sizeRow.innerHTML = `
                                <td>${sizeValue}</td>
                                <td><input type="number" class="form-control size-price" value="${sellingPrice}" placeholder="Price" name="variants[${row.querySelector('.color-value').textContent.trim()}][size][${sizeValue}][price]"></td>
                                <td><input type="number" class="form-control size-available-input" placeholder="Available" min="0" name="variants[${row.querySelector('.color-value').textContent.trim()}][size][${sizeValue}][total_units]"></td>
                                <td><input type="number" class="form-control" value="${maxSellingUnits}" placeholder="Max Selling Units" name="variants[${row.querySelector('.color-value').textContent.trim()}][size][${sizeValue}][max_selling_units]"></td>
                            `;
                            sizeTableBody.appendChild(sizeRow);
                            sizeRow.querySelector('.size-price').addEventListener('input', updateSizePriceRange);
                            sizeRow.querySelector('.size-available-input').addEventListener('input', updateTotalUnits);
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
                        uploadedImageCell.innerHTML = `<img src="${event.target.result}" class="uploaded-image" width="100">`;
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
                const maxSellingInput = row.querySelector(`[name="variants[${colorName}][max_selling_units]"]`);

                priceInput.name = `variants[${colorName}][price]`;
                availableInput.name = `variants[${colorName}][total_units]`;
                maxSellingInput.name = `variants[${colorName}][max_selling_units]`;

                const sizeRows = row.querySelectorAll('.size-table tbody tr');
                sizeRows.forEach(sizeRow => {
                    const sizeName = sizeRow.getAttribute('data-size');
                    const sizePriceInput = sizeRow.querySelector('.size-price');
                    const sizeAvailableInput = sizeRow.querySelector('.size-available-input');
                    const sizeMaxSellingInput = sizeRow.querySelector(`[name="variants[${colorName}][size][${sizeName}][max_selling_units]"]`);

                    sizePriceInput.name = `variants[${colorName}][size][${sizeName}][price]`;
                    sizeAvailableInput.name = `variants[${colorName}][size][${sizeName}][total_units]`;
                    sizeMaxSellingInput.name = `variants[${colorName}][size][${sizeName}][max_selling_units]`;
                });
            });
        }
    });

    $(document).ready(function(){
        $('input[type=radio][name=variant_type]').change(function() {
            if($(this).val() == 'color'){
                $('.color-variant' ).show();
                $('.size-variant').hide();    
            }else{
                $('.color-variant' ).hide();
                $('.size-variant').show(); 
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



        CKEDITOR.replace(<?php echo 'meta_description'; ?>, {

            filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',

            enterMode: CKEDITOR.ENTER_BR

        });

        CKEDITOR.config.allowedContent = true;



        CKEDITOR.replace(<?php echo 'seo_content'; ?>, {

            filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',

            enterMode: CKEDITOR.ENTER_BR

        });

        CKEDITOR.config.allowedContent = true;

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

            url: "{{route('admin-product-upload-images-new')}}",

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

            $('.variants-price').val($('#selling_price').val());

        });

        $(document).on('keyup', '#buying_price', function(e) {

            e.preventDefault();

            let selling_price = getSellingPrice();

            $('#selling_price').val(selling_price);

            $('.variants-price').val($('#selling_price').val());

        });

        $(document).on('keyup', '#discount', function(e) {

            e.preventDefault();

            let selling_price = getSellingPrice();

            $('#selling_price').val(selling_price);

            $('.variants-price').val($('#selling_price').val());

        });



        // $('#addItemModal').on('hidden.bs.modal', function() {

        //     $("#color_code").val('');

        //     $("#color_name").val('');

        //     $("#color_code").css("background-color", "");

        //     $("#add_new_item").val('');

        //     $('#variant_type').val('');

        //     $(".color").hide();

        //     $(".non-color").show();

        // })



        $(document).on('change', '.variant_name', function() {

            if ($(this).val() == 'add_item_color' || $(this).val() == 'add_item_size') {

                if($(this).val() == 'add_item_color'){
                    $('.color').show();
                    $('.non-color').hide();

                }
                if($(this).val() == 'add_item_size'){
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
                            var options = '<option value="add_item">Add another Item</option>';

                            $.each(response.data, function(index, item) {
                                options += '<option value="' + item.name + '">' + item.name + '</option>';
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


        // $(document).on('change', '.variant_value', function() {

        //     var attr_name = $(this).attr('name');

        //     if (attr_name == 'attribute_value' && $(this).val() == 'add_item') {

        //         $('#variant_type').val(attr_name);

        //         $('#addItemModal').modal('show');

        //     }

        //     if (attr_name == 'variant_value') {

        //         if ($(this).val()[0] == 'add_item') {

        //             var wanted_id = $(this).val()[0];

        //             var wanted_option = $('#variant_value option[value="' + wanted_id + '"]');

        //             wanted_option.prop('selected', false);

        //             $(this).trigger('change.select2');



        //             if ($("#variant_name option:selected").text().toLowerCase() == 'color') {

        //                 /* $("#new_item").hide();

        //                 $("#color").show(); */

        //                 $('.color').show();

        //                 $('.non-color').hide();

        //             } else {

        //                 /* $("#color").hide();

        //                 $("#new_item").show(); */

        //                 $('.color').hide();

        //                 $('.non-color').show();

        //             }

        //             $('#variant_type').val('variant_value');

        //             $('#addItemModal').modal('show');

        //         }

        //     }

        // });



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
                    var variant_id=2;
                    createOrUpdateVariant(item, 'size', variant_id, color_name);
                } else if (variant_type == 'color') {
                    var variant_id =1;
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

            var value_text = $("#" + field + "_value option:selected").toArray().map(item => item.text).join();

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
        attributeValuesSelect.innerHTML = '<option value="">Select Attribute Value</option>'; // Clear previous values

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

                console.log("data: ", data);

                if (color_name == "") {
                    if(data.status == 'success'){
                        $('#size-variants-select').append("<option value='" + data.data + "'>" + data.name + "</option>");
                    }
                } else {
                    if(data.status == 'success'){
                        $('#color-variants-select').append("<option value='" + data.data + '-' + data.color + "'>" + data.name +
                        "</option>");
                    }
                }
                $('#add_new_item').val('');
                $('#color_name').val('');
                $('#color_code').val('');
                $('#add_new_item').val('');
                $('#addItemModal').modal('hide');
                show_message(data.msg,data.status);
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

                console.log("data: ", data);

                $('#' + type).append("<option value='" + data.data + "'>" + data.name + "</option>");

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

        console.log(discount_type);

        if (discount_type == 'flat') {

            return Math.round(parseInt(price) - parseInt(discount));

        } else {

            return Math.round(parseInt(price) - ((parseInt(price) * parseInt(discount)) / 100));

        }

    }



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
                url: '{{ route("admin-product-productgalleryimgdelete") }}',
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

                    html += '<option value="' + subcat.parent_id + '">' + subcat.name + '</option>';

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
</script>




@endpush