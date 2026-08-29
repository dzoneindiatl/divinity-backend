<!-- ===========================
| Edit Variant Modal Section |
=========================== -->

<div id="formErrorPopup" class="alert alert-danger d-none">
    <strong>⚠️ Please fix the following:</strong>
    <ul class="mb-0" id="formErrorList"></ul>
</div>

<div class="modal fade" id="editVariantModal" tabindex="-1" role="dialog" aria-labelledby="editVariantModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-slideout" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header" style="background: #f5f2f2 !important;">
                <h5 class="modal-title" id="editVariantModalLabel">Edit Variant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" style="background: #f5f2f2 !important;">
                <input type="hidden" id="v_id" value="">
                <form id="edit_varient_form">
                    <div class="row">
                        <!-- Variant Name -->
                        <div class="col-12 mb-3">
                            <label for="v_name">Variant Name *</label>
                            <input type="text" class="form-control" name="v_name" id="v_name"
                                placeholder="Variant Name">
                        </div>

                        <!-- SKU -->
                        <div class="col-12 mb-3">
                            <label for="v_sku">SKU *</label>
                            <input type="text" class="form-control" name="v_sku" id="v_sku"
                                placeholder="Variant SKU">
                        </div>

                        <!-- Price & Selling Price -->
                        <div class="col-12 mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="v_price">Price *</label>
                                    <input type="text" class="form-control" name="v_price" id="v_price"
                                        placeholder="Variant Price">
                                </div>
                                <div class="col-md-6">
                                    <label for="v_sprice">Selling Price *</label>
                                    <input type="text" class="form-control" name="v_sprice" id="v_sprice"
                                        placeholder="Selling Price">
                                </div>
                            </div>
                        </div>

                        <!-- Discount Type & Value -->
                        <div class="col-12 mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="discount_type_popup">Discount Type</label>
                                    <select name="discount_type_popup" id="discount_type_popup"
                                        class="form-control js-example-placeholder-single js-states">
                                        <option value="">Select Discount Type</option>
                                        <option value="flat">Flat</option>
                                        <option value="percentage">Percentage</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="discount_popup">Discount</label>
                                    <input type="text" class="form-control" name="discount_popup" id="discount_popup"
                                        placeholder="Discount">
                                </div>
                            </div>
                        </div>

                        <!-- Quantity -->
                        <div class="col-12 mb-3">
                            <label for="v_quantity">Quantity *</label>
                            <input type="text" class="form-control v-quantity" name="v_quantity" id="v_quantity"
                                placeholder="Variant Quantity">
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12 mt-3">
                            <button type="button" onclick="submit_form('edit_varient_form')"
                                class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form id="productForm" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-9">
            <div class="card-header mb-3">
                <div class="card-title">
                    <h6>Basic Information</h6>
                </div>
            </div>
            <hr>
            <div class="card-body">
                <div class="row">
                    <input type="hidden" name="product_id" id="product_id" value="{{ $product->id }}">
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" placeholder="Product Name"
                                value="{{ $product->name }}" required>
                            @if ($errors->has('name'))
                                <div class=" invalid-feedback">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- Add Code of Mohit -->
                    <div class="col-md-6  mb-3">
                        <div class="form-group">
                            <label for="weight_type">Country Of Origin <span class="text-danger">*</span></label>
                            <select name="country_origin" class="form-control" required>
                                <option value="select value">Select Country</option>
                                @if (!empty($countries))
                                    @foreach ($countries as $attval)
                                        <option @if ($product->country_origin == $attval->id) selected @endif
                                            value="{{ $attval->id }}">{{ $attval->name }}
                                        </option>
                                    @endforeach
                                @endif

                            </select>
                        </div>
                    </div>
                    <!-- End Code of Mohit -->
                    <div class="col-md-12 ">
                        <div class="row">
                            <div class="col  mb-3">
                                <div class="form-group">
                                    <label for="sku">SKU <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                        id="sku" value="{{ $product->sku }}" name="sku" required
                                        placeholder="SKU">
                                    @if ($errors->has('name'))
                                        <div class=" invalid-feedback">
                                            {{ $errors->first('sku') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col  mb-3">
                                <div class="form-group">
                                    <label for="hsn">HSN</label>
                                    <input type="text" class="form-control" id="hsn" name="hsn"
                                        placeholder="HSN" value="{{ $product->hsn }}">
                                </div>
                            </div>
                            <div class="col  mb-3">
                                <div class="form-group">
                                    <label for="bar_code">Barcode</label>
                                    <input type="text" class="form-control" id="bar_code" name="bar_code"
                                        value="{{ $product->bar_code }}" placeholder="Barcode">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-header mb-3 ">
                <div class="card-title">
                    <h6>Product Descriptions</h6>
                </div>
            </div>
            <hr>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12  mb-3">
                        <div class="form-group">
                            <label for="short_description">Short Description</label>
                            <textarea class="form-control" required name="short_description" id="short_description" rows="4"> {{ $product->short_description }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="description">Product Description <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control ck_content  @error('description') is-invalid @enderror" name="description"
                                        id="description" rows="4"> {{ $product->description }}</textarea>
                                    @if ($errors->has('description'))
                                        <div class=" invalid-feedback">
                                            {{ $errors->first('description') }}
                                        </div>
                                    @endif
                                    <div class="invalid-feedback-1" id="p-description"></div>
                                </div>
                            </div>
                            <div class="col  mb-3">
                                <div class="form-group">
                                    <label for="specification">Product Specification <span
                                            class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control ck_content @error('specification') is-invalid @enderror" name="specification"
                                        id="specification" rows="4">{{ $product->specification }}</textarea>
                                    @if ($errors->has('specification'))
                                        <div class=" invalid-feedback">
                                            {{ $errors->first('specification') }}
                                        </div>
                                    @endif
                                    <div class="invalid-feedback-1" id="p-specification"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col  mb-3">
                                <div class="form-group">
                                    <label for="product_details">Product Details</label>
                                    <textarea class="form-control ck_content" name="product_details" id="product_details" rows="4">{{ $product->product_details }}</textarea>
                                </div>
                            </div>

                            <div class="col  mb-3">
                                <div class="form-group">
                                    <label for="others">Others</label>
                                    <textarea class="form-control ck_content" name="others" id="others" rows="4">{{ $product->others }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col  mb-3">
                                <div class="form-group">
                                    <label for="wash_care">Wash Care </label>
                                    <textarea class="form-control ck_content" name="wash_care" id="wash_care" rows="4">{{ $product->wash_care }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="accordion" id="productAccordion">

                {{-- 1. Shipping Information --}}
                <div class="accordion-item card custom-card">
                    <h2 class="accordion-header" id="headingShipping">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseShipping" aria-expanded="true" aria-controls="collapseShipping">
                            Shipping Information
                        </button>
                    </h2>
                    <div id="collapseShipping" class="accordion-collapse collapse show"
                        data-bs-parent="#productAccordion">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="weight">Weight</label>
                                        <input type="text" class="form-control" name="weight" id="weight"
                                            value="{{ $product->weight }}" />
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="weight_type">Weight Type</label>
                                        <select
                                            class="form-control select2-original @error('weight_type') is-invalid @enderror"
                                            name="weight_type" id="weight_type">
                                            <option value="">Select Type</option>
                                            <option value="grm"
                                                {{ $product->weight_type == 'grm' ? 'selected' : '' }}>GRM
                                            </option>
                                            <option value="kg"
                                                {{ $product->weight_type == 'kg' ? 'selected' : '' }}>KG
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Related Products --}}
                <div class="accordion-item card custom-card">
                    <h2 class="accordion-header" id="headingRelated">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseRelated" aria-expanded="false" aria-controls="collapseRelated">
                            Related Products
                        </button>
                    </h2>

                    @php
                        $relatedProductIds = explode(',', $product->related_products ?? '');
                    @endphp


                    <input type="hidden" id="preselected_subcategory"
                        value="{{ $product->related_product_subcategory_id ?? '' }}">
                    <input type="hidden" id="preselected_products" value='@json($relatedProductIds)'>
                    <div id="collapseRelated" class="accordion-collapse collapse" data-bs-parent="#productAccordion">
                        <div class="accordion-body">
                            <div class="row">
                                {{-- Category --}}
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="categorys_id" class="form-label">Category</label>
                                        <select name="categorys_id" id="categorys_id" class="form-control"
                                            onchange="getsubcategory(this.value);">
                                            <option value="">Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $product->related_product_categores_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="categorysidError">
                                            {{ $errors->first('categorys_id') }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Subcategory --}}
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="subcategory_id" class="form-label">Subcategory</label>
                                        <select name="subcategory_id" id="subcategory_id" class="form-control"
                                            onchange="getproduct(this.value);">
                                            <option value="">Select Subcategory</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Related Products --}}
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="Productid" class="form-label">Related Product</label>
                                        <select name="Product_id[]" id="Productid"
                                            class="form-control product_select2" multiple>
                                            <option value="">Select Product</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Attributes --}}
                <div class="accordion-item card custom-card">
                    <h2 class="accordion-header" id="headingAttributes">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseAttributes" aria-expanded="false"
                            aria-controls="collapseAttributes">
                            Attributes
                        </button>
                    </h2>
                    <div id="collapseAttributes" class="accordion-collapse collapse"
                        data-bs-parent="#productAccordion">
                        <div class="accordion-body">
                            <div class="row" id="attribute-pairs-container"></div>
                            <div class="row">
                                <div class="col-12">
                                    <button id="add-attribute-button" class="btn btn-primary" type="button"
                                        onclick="addAttributePair()">+ Select
                                        Attribute</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. Pricing --}}
                <div class="card-header mb-3">
                    <div class="card-title">
                        <h6>Pricing </h6>
                    </div>
                </div>
                <hr>

                <div class="row">
                    <div class="col-4 mb-3">
                        <label for="buying_price">MRP <span class="text-danger">*</span></label>
                        <input type="number" id="buying_price" name="buying_price" class="form-control" required
                            value="{{ $product->buying_price }}" />
                    </div>

                    <div class="col-4 mb-3">
                        <label for="discount_type">Discount Type</label>
                        <select name="discount_type" id="discount_type" class="form-control">
                            <option value="">Select Discount Type</option>
                            <option {{ $product->discount_type == 'flat' ? 'selected' : '' }} value="flat">Flat
                            </option>
                            <option {{ $product->discount_type == 'percentage' ? 'selected' : '' }}
                                value="percentage">Percentage</option>
                        </select>
                    </div>
                    <div class="col-4 mb-3">
                        <label for="discount">Discount</label>
                        <input type="number" id="discount" name="discount" class="form-control"
                            value="{{ $product->discount }}" />
                    </div>

                    <div class="col-4 mb-3">
                        <label for="selling_price">Selling Price <span class="text-danger">*</span></label>
                        <input type="number" id="selling_price" name="selling_price" class="form-control" required
                            readonly value="{{ $product->selling_price }}" />
                    </div>
                    <div class="col-4 mb-3">
                        <label for="qty">Quantity <span class="text-danger">*</span></label>
                        <input type="number" id="qty" name="qty" class="form-control" required
                            value="{{ $product->qty }}" />
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col mb-3">
                        <label for="maxProduct">Maximum Selling Limit(per user)</label>
                        <input type="number" class="form-control" id="maxProduct" name="max_selling_units"
                            value="{{ $product->max_selling_units }}">
                    </div>
                    <div class="col mb-3">
                        <label for="minProduct">Minimum Stock Limit</label>
                        <input type="number" class="form-control" id="minProduct" name="min_selling_units"
                            value="{{ $product->min_selling_units }}">
                    </div>
                </div>

            </div>



            {{-- Modal for New Attribute --}}
            <div class="modal fade" id="addAttributeModal" tabindex="-1" aria-labelledby="addAttributeModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Attribute</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="attributes_container">
                            <div class="mb-3">
                                <label for="attributeName" class="form-label">Attribute Name</label>
                                <select class="form-select attribute-select" id="attributeName" name="attributeName">
                                    <option value="">Select an option</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="attributeValue" class="form-label">Attribute Value</label>
                                <input type="text" class="form-control" id="attributeValue"
                                    placeholder="Enter multiple values separated by comma">
                                <small class="text-muted">Enter multiple values separated by comma (e.g. red, green,
                                    blue)</small>
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

            {{-- Variant Section --}}
            <div class="card mt-3">
                <!-- <div class="card-header d-flex justify-content-between align-items-center">
                <h6>Variants</h6>
                <div><strong>Grand Total Qty:</strong> <span id="t-qty" class="t-qty">0</span></div>
            </div> -->
                <div class="card-body" id="variant_group_details">
                    {!! $variantReleatedProduct !!}
                </div>
            </div>

            <div class="mb-3 text-first btn_add">
                <button type="button" class="btn btn-primary prevBtn"
                    onclick="onclickPrevious('Setp1')">Preview</button>
                <button type="button" class="btn btn-primary nextBtn">Next</button>
            </div>
        </div>

        <div class="col-md-3 mb-3 mt-3">
            <div class="card-header">
                <div class="card-title">
                    <h6>Settings</h6>
                </div>
            </div>
            <hr>

            <div class="card-body">

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_new" name="is_new"
                        @if (isset($product) && $product->is_new == 1) checked @endif />
                    <label class="form-check-label" for="status">New In</label>
                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_new"
                        name="is_new_arrivals" @if (isset($product) && $product->is_new_arrivals == 1) checked @endif />
                    <label class="form-check-label" for="is_new">New Arrivals</label>
                </div>


                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_featured"
                        name="is_featured" @if (isset($product) && $product->is_featured == 1) checked @endif />
                    <label class="form-check-label" for="is_featured">Featured Products</label>
                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="status" name="trending"
                        @if (isset($product) && $product->trending == 1) checked @endif />
                    <label class="form-check-label" for="status">Trending Products</label>
                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="status"
                        name="best_selling" @if (isset($product) && $product->best_selling == 1) checked @endif />
                    <label class="form-check-label" for="status">Best Selling</label>
                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="status"
                        name="best_seller" @if (isset($product) && $product->best_seller == 1) checked @endif />
                    <label class="form-check-label" for="status">Best Sellers</label>
                </div>
            </div>


            <div class="card-header mb-3 mt-3">
                <div class="card-title">
                    <h6>Categories</h6>
                </div>
            </div>
            <hr>

            @php
                $selectedCategories = is_array(json_decode($product->category_id, true))
                    ? json_decode($product->category_id, true)
                    : [];
                $selectedSubCategories = is_array(json_decode($product->sub_category_id, true))
                    ? json_decode($product->sub_category_id, true)
                    : [];

                $selectedChildCategories = is_array(json_decode($product->child_category_id, true))
                    ? json_decode($product->child_category_id, true)
                    : [];
            @endphp

            <div class="card-body">

                @if ($activeCategorie->category_type_id == 2)
                    {{-- Main Category --}}
                    <div class="form-check">
                        <input class="form-check-input main-cat-checkbox" type="checkbox"
                            id="main_cat_{{ $activeCategorie->id }}" name="category_id[]"
                            value="{{ $activeCategorie->id }}" {{ $activeCategorie->id ? 'checked' : '' }}
                            onchange="toggleSubCategories({{ $activeCategorie->id }})">
                        <label class="form-check-label" for="main_cat_{{ $activeCategorie->id }}">
                            {{ $activeCategorie->name }}
                        </label>
                    </div>

                    {{-- Subcategories --}}
                    @if ($activeCategorie && $activeCategorie->children)
                        <div id="subcategories_{{ $activeCategorie->id }}" class="ms-3">
                            @foreach ($activeCategorie->children as $sub)
                                <div class="form-check">
                                    <input class="form-check-input sub-cat-checkbox" type="checkbox"
                                        id="sub_cat_{{ $sub->id }}" name="sub_category_id[]"
                                        value="{{ $sub->id }}" @checked(
                                            $product->main_sub_category_id == $sub->id ||
                                                (isset($selectedSubCategories) && in_array($sub->id, $selectedSubCategories)))
                                        onchange="toggleChildCategories({{ $sub->id }})">
                                    <label class="form-check-label" for="sub_cat_{{ $sub->id }}">
                                        {{ $sub->name }}
                                    </label>
                                </div>

                                {{-- Child Categories --}}
                                @if ($sub->children)
                                    <div id="childcategories_{{ $sub->id }}" class="ms-4">
                                        @foreach ($sub->children as $child)
                                            <div class="form-check">
                                                <input class="form-check-input child-cat-checkbox" type="checkbox"
                                                    id="child_cat_{{ $child->id }}" name="child_category_id[]"
                                                    value="{{ $child->id }}" @checked(
                                                        $product->main_child_category_id == $child->id ||
                                                            (isset($selectedChildCategories) && in_array($child->id, $selectedChildCategories)))>
                                                <label class="form-check-label" for="child_cat_{{ $child->id }}">
                                                    {{ $child->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>



            <div class="card-header mt-3 mb-3">
                <div class="card-title">
                    <h6>Collection</h6>
                </div>
            </div>

            <hr>


            <!-- Add Code by mohit for multiple category selected -->
            <div class="card-body">
                @foreach ($categories as $category)
                    @if ($category->category_type_id == 1)
                        {{-- Main Category --}}
                        <div class="form-check">
                            <input class="form-check-input main-cat-checkbox" type="checkbox"
                                id="main_cat_{{ $category->id }}" name="category_id[]" value="{{ $category->id }}"
                                {{ in_array($category->id, $selectedCategories) ? 'checked' : '' }}
                                onchange="toggleSubCategories({{ $category->id }})">
                            <label class="form-check-label" for="main_cat_{{ $category->id }}">
                                {{ $category->name }}
                            </label>
                        </div>

                        {{-- Subcategories --}}
                        <div id="subcategories_{{ $category->id }}" class="ms-3">
                            @foreach ($category->children as $sub)
                                <div class="form-check">
                                    <input class="form-check-input sub-cat-checkbox" type="checkbox"
                                        id="sub_cat_{{ $sub->id }}" name="sub_category_id[]"
                                        value="{{ $sub->id }}"
                                        {{ in_array($sub->id, $selectedSubCategories) ? 'checked' : '' }}
                                        onchange="toggleChildCategories({{ $sub->id }})">
                                    <label class="form-check-label" for="sub_cat_{{ $sub->id }}">
                                        {{ $sub->name }}
                                    </label>
                                </div>

                                {{-- Child Categories --}}
                                <div id="childcategories_{{ $sub->id }}"
                                    class="ms-4 {{ in_array($sub->id, (array) $product_details->sub_category_id) ? '' : 'd-none' }}">
                                    @foreach ($sub->children as $child)
                                        <div class="form-check">
                                            <input class="form-check-input child-cat-checkbox" type="checkbox"
                                                {{ in_array($child->id, $selectedChildCategories) ? 'checked' : '' }}
                                                id="child_cat_{{ $child->id }}" name="child_category_id[]"
                                                value="{{ $child->id }}">
                                            <label class="form-check-label" for="child_cat_{{ $child->id }}">
                                                {{ $child->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>


            <!-- End code by Mohit -->

            <!-- <div class="card-header mt-3">
                <div class="card-title">
                    <h6>Tags</h6>
                </div>
            </div>
            <hr> -->

            @php
                $selectedTags = explode(',', $product->product_tags ?? '');
            @endphp
            <!-- <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="tags"> </label>
                            <select class="form-control product_select2" name="product_tags[]" id="tags"
                                multiple>
                                @foreach ($tags as $key => $tag)
                                    <option value="{{ $key }}"
                                        {{ in_array($key, $selectedTags) ? 'selected' : '' }}>
                                        {{ $tag }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div> -->

        </div>
    </div>
</form>


<div class="modal fade" id="addVariantGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Variant Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addVariantGroupForm">
                    <input type="hidden" name="base_variant_id" id="base_variant_id" value="">
                    <div class="mb-3">
                        <label class="form-label">Select Variant Value</label>
                        <select name="variant_value_id" id="variant_value_id" class="form-select">
                            <option value="">Select</option>
                            <!-- JS will populate options here -->
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="addSelectedVariantGroup()">Add</button>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>


<script>
    var getAttributesValues = "{{ route('admin-product-attribute-values') }}";
    var getSubCategory = "{{ route('admin-product-ajax-subcategory') }}";
    var getProduct = "{{ route('admin-product-ajax-getproduct') }}";
    var productLimit = "{{ url('admin/product/update-product-variant-limit/') }}";
    window.attributes = @json($attributesData);
    window.preselectedAttributes = @json($preselectedAttributes);
</script>
<script src="{{ asset('assets/js/product/add-product.js') }}"></script>
<script>
    $(document).ready(function() {

        $('#productForm').validate({
            errorClass: 'is-invalid',
            errorElement: 'div',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group, .mb-3').append(error);
            },
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
        });

        $('.nextBtn').on('click', function(e) {
            const $btn = $('.nextBtn');
            const originalHtml = $btn.html();
            e.preventDefault();

            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            const form = $('#productForm');

            if (form.valid()) {
                const formData = new FormData(form[0]);

                $.ajax({
                    url: "{{ route('admin-product-save.step3') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // CSRF token
                    },
                    beforeSend: function() {
                        $btn.prop('disabled', true).html(`
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            Processing...
                        `);
                    },
                    success: function(response) {
                        $('#tab2').html("");
                        $('#formTabs .nav-link').removeClass('active');
                        $('#formTabs .nav-link[data-tab="tab4"]').addClass('active');
                        $('#tab2').html(response.seoView);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;

                            // Clear previous errors from popup
                            $('#formErrorList').empty();
                            $('#formErrorPopup').addClass('d-none');

                            // Clear field errors
                            $('.invalid-feedback').remove();
                            $('.is-invalid').removeClass('is-invalid');

                            // Loop through errors
                            $.each(errors, function(key, messages) {
                                // Show in popup
                                messages.forEach(msg => {
                                    $('#formErrorList').append('<li>' +
                                        msg + '</li>');
                                });

                                // Show field-level error if input exists
                                let field = $('[name="' + key + '"]');

                                // Handle array inputs like variant_name[0]
                                if (!field.length && key.includes('.')) {
                                    const [base, index] = key.split('.');
                                    field = $('[name="' + base + '[' + index +
                                        ']"]');
                                }

                                if (field.length) {
                                    field.addClass('is-invalid');
                                    field.after(
                                        '<div class="invalid-feedback d-block">' +
                                        messages[0] + '</div>');
                                }
                            });

                            // Finally, show the popup
                            $('#formErrorPopup').removeClass('d-none');
                        } else {
                            alert('Something went wrong. Please try again.');
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(originalHtml);
                    }

                });
            }
        });
    });


    $(document).on('blur', 'input[type="number"]', function() {
        let val = parseFloat($(this).val());
        if (val < 0 || isNaN(val)) {
            $(this).val(0);
        }
    });

    $(document).on('keydown', 'input[type="number"]', function(e) {
        // Block "-" key
        if (e.key === '-' || e.keyCode === 189) {
            e.preventDefault();
        }
    });


    function onclickPrevious(value) {
        const $btn = $('.prevBtn');
        const originalHtml = $btn.html();
        const formData = new FormData();
        formData.append('product_id', $('#product_id').val());
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('step', "step2");

        $.ajax({
            url: "{{ route('admin-product-previousStep') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $btn.prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Processing...
            `);
            },
            success: res => {
                if (res.success) {
                    $('#formTabs .nav-link').removeClass('active');
                    $('#formTabs .nav-link[data-tab="tab2"]').addClass('active');
                    $('#tab2').html("").html(res.mainView);
                } else {
                    alert(res.message || 'Something went wrong.');
                }


            },
            error: xhr => {
                const msg = xhr.status === 422 ?
                    Object.values(xhr.responseJSON.errors).map(e => e[0]).join('\n') :
                    'Server error';
                alert(msg);
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    }
</script>
