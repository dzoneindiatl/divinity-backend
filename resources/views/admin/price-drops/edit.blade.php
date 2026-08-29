@extends('admin.layout.master')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.css') }}">
<link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
@endpush

@section('content')
<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Price Drop</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="{{route('admin-price-drops.update',$price_drop_details->id)}}" method="post" enctype="multipart/form-data" id="editUserForm">
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
                                            <div class="col-xl-12">
                                                <label for="gain_type" class="form-label"><span class="text-danger">*
                                                    </span>Price Type</label>
                                                <select class="js-example-placeholder-single form-control @error('gain_type') is-invalid @enderror" name="gain_type" id="gain_type">
                                                    <option value="" selected>Select Gain Type</option>
                                                    <option value="gain" {{(!empty($price_drop_details->gain_type) && $price_drop_details->gain_type == 'gain') ? 'selected' : '' }}>
                                                        Gain</option>
                                                    <option value="drop" {{(!empty($price_drop_details->gain_type) && $price_drop_details->gain_type == 'drop') ? 'selected' : '' }}>
                                                        Drop</option>
                                                </select>
                                                @if ($errors->has('gain_type'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('gain_type') }}
                                                </div>
                                                @endif
                                            </div>
                                            <div class="col-xl-6" id="categoryDropdown">
                                                <label for="prdct_category_id" class="form-label"><span class="text-danger">*
                                                    </span>Category</label>
                                                <select class="form-control @error('category_id') is-invalid @enderror" name="category_id" id="prdct_category_id" onchange="getRelatedSubCategories(this.value);" required>
                                                    <option value="" selected disabled>Select category</option>
                                                    @forelse ($categories as $category)
                                                    <option value="{{ $category->id }}" {{(!empty($selected_category_id) && $selected_category_id == $category->id) ? 'selected' : ''}}>
                                                        {{ $category->name }}
                                                    </option>
                                                    @empty
                                                    <option value="">No Data found</option>
                                                    @endforelse
                                                </select>
                                            </div>
                                            <div class="col-xl-6" id="subcategoryDropdown">
                                                <label for="prdct_sub_category_id" class="form-label">Sub Category</label>
                                                <select name="sub_category_id" id="prdct_sub_category_id" class="js-example-placeholder-single js-states form-control" onchange="getchildcategory(this.value);">
                                                    <option value="" selected disabled>Select sub category</option>
                                                    @forelse ($sub_categories as $category)
                                                    <option value="{{ $category->id }}" {{(!empty($selected_subcategory_id) && $selected_subcategory_id == $category->id) ? 'selected' : ''}}>
                                                        {{ $category->name }}
                                                    </option>
                                                    @empty
                                                    <option value="">No Data found</option>
                                                    @endforelse
                                                </select>
                                            </div>
                                            <div class="col-xl-6" id="childcategoryDropdown">
                                                <label for="reference" class="form-label">Child category</label>
                                                <select name="child_category_id" id="prdct_child_category_id" class="js-example-placeholder-single js-states form-control" onchange="getproduct(this.value);">
                                                    <option value="" selected disabled>Select sub category</option>
                                                    @forelse ($sub_categories as $category)
                                                    <option value="{{ $category->id }}" {{(!empty($selected_childcategory_id) && $selected_childcategory_id == $category->id) ? 'selected' : ''}}>
                                                        {{ $category->name }}
                                                    </option>
                                                    @empty
                                                    <option value="">No Data found</option>
                                                    @endforelse
                                                </select>
                                            </div>
                                            <div class="col-xl-6" id="productDropdown">
                                                <label for="product_id" class="form-label">Products</label>
                                                <select class="js-example-placeholder-single js-states form-control" multiple="multiple" name="Product_id[]" id="product_id">
                                                    @forelse ($products as $product)
                                                    <option value="{{ $product->id }}" {{in_array($product->id, $price_drop_assigned) ? 'selected' : ''}}>{{ $product->name }}</option>
                                                    @empty
                                                    <option value="" selected>No Data found</option>
                                                    @endforelse
                                                </select>
                                                @if ($errors->has('productData'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('productData') }}
                                                </div>
                                                @endif
                                            </div>
                                            <div class="col-xl-6">
                                                <label for="drop_type" class="form-label">
                                                    <span class="text-danger">*</span> Drop Type
                                                </label>
                                                <select class="js-example-placeholder-single form-control @error('drop_type') is-invalid @enderror" name="drop_type" id="drop_type">
                                                    <option value="" disabled selected>Select Drop Type</option>
                                                    <option value="flat" {{ (old('drop_type') == 'flat' || $price_drop_details->drop_type == 'flat') ? 'selected' : '' }}>
                                                        Flat
                                                    </option>
                                                    <option value="percentage" {{ (old('drop_type') == 'percentage' || $price_drop_details->drop_type == 'percentage') ? 'selected' : '' }}>
                                                        Percentage
                                                    </option>
                                                </select>
                                                @error('drop_type')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>



                                            <div class="col-xl-6">
                                                <label for="amount" class="form-label"><span class="text-danger">*
                                                    </span>Amount / Percentage</label>
                                                <input type="text" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{isset($price_drop_details->amount) ? $price_drop_details->amount: old('amount')}}" placeholder="Amount">
                                                @if ($errors->has('amount'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('amount') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="start_date" class="form-label"><span class="text-danger">* </span>Start Date</label>
                                                <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ isset($price_drop_details->start_date) ? date('Y-m-d\TH:i', strtotime($price_drop_details->end_date)) : old('start_date') }}" placeholder="Start Date">
                                                @if ($errors->has('start_date'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('start_date') }}
                                                </div>
                                                @endif
                                            </div>
                                            <div class="col-xl-6">
                                                <label for="end_date" class="form-label"><span class="text-danger">* </span>End Date</label>
                                                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ isset($price_drop_details->end_date) ? date('Y-m-d\TH:i', strtotime($price_drop_details->end_date))  : old('end_date') }}" placeholder="End Date">
                                                @if ($errors->has('end_date'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('end_date') }}
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
<script src="{{ asset('assets/plugin/tagify/tagify.min.js') }}"></script>

<!-- Internal Select-2.js -->
<script src="{{ asset('assets/js/select2.js') }}"></script>

<script src="{{ asset('assets/libs/dropzone/dropzone-min.js') }}"></script>

<script src="{{ asset('assets/js/custom/product.js') }}"></script>

{{-- <script src="{{ asset('assets/js/fileupload.js') }}"></script> --}}
<script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<!-- <script src="{{ asset('assets/js/form-validation.js') }}"></script> -->
<script src="{{ asset('assets/js/repeater.js')}}"></script>


<script>
    // $(document).ready(function() {
    //     // Listen for changes in the assign_type dropdown
    //     $('#assign_type').change(function() {
    //         var selectedValue = $(this).val();

    //         // Hide all dropdowns initially
    //         $('#categoryDropdown, #productDropdown').hide();

    //         // Show the dropdown corresponding to the selected assign_type
    //         if (selectedValue === 'category') {
    //             $('#categoryDropdown').show();
    //         } else if (selectedValue === 'product') {
    //             $('#productDropdown').show();
    //         }
    //     });

    //     // Check the initial value of assign_type and show the corresponding dropdown
    //     var initialAssignType = $('#assign_type').val();
    //     if (initialAssignType === 'category') {
    //         $('#categoryDropdown').show();
    //     } else if (initialAssignType === 'product') {
    //         $('#productDropdown').show();
    //     }

    //     // Trigger change event on assign_type dropdown to initialize visibility
    //     $('#assign_type').trigger('change');
    // });
    $(document).ready(function() {
        $('#product_id').select2({
            placeholder: "Choose item",
            width: "100%"
        });
        $("form").validate({
            rules: {
                gain_type: {
                    required: true
                },
                category_id: {
                    required: true
                },
                drop_type: {
                    required: true
                },
                amount: {
                    required: true,
                    number: true
                },
                start_date: {
                    required: true,
                    futureDate: true
                },
                end_date: {
                    required: true,
                    futureDate: true,
                    greaterThan: "#start_date"
                }
            },
            messages: {
                gain_type: {
                    required: "Please select a gain type"
                },
                category_id: {
                    required: "Please select a category"
                },
                sub_category_id: {
                    required: "Please select a subcategory"
                },
                child_category_id: {
                    required: "Please select a child category"
                },
                Product_id: {
                    required: "Please select at least one product"
                },
                drop_type: {
                    required: "Please select a drop type"
                },
                amount: {
                    required: "Please enter an amount",
                    number: "Please enter a valid number"
                },
                start_date: {
                    required: "Please select a start date",
                    futureDate: "Start date must be in the future"
                },
                end_date: {
                    required: "Please select an end date",
                    futureDate: "End date must be in the future",
                    greaterThan: "End date must be greater than the start date"
                }
            },
            errorClass: "invalid-feedback",
            validClass: "valid",
            errorElement: "div",
            highlight: function(element, errorClass, validClass) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).addClass("is-valid").removeClass("is-invalid");
            }
        });
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

                } else {

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
        getchildcategory(null);
        getproduct(null);
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
                    var currentSelections = $('#product_id').val() || [];

                    var options = '<option value="add_item" disabled>Add another Item</option>';

                    $.each(response.childcat, function(index, item) {
                        options += '<option value="' + item.id + '">' + item.name + '</option>';
                    });

                    $('#prdct_child_category_id').html(options);

                    $('#prdct_child_category_id').select2({
                        placeholder: "Choose item",
                        width: "100%"
                    });

                    $('#prdct_child_category_id').val(currentSelections).trigger('change');
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
                $("#prdct_child_category_id").html('<option value="">Error fetching Product</option>');
                $('#prdct_child_category_id').select2({
                    placeholder: "Choose item",
                    width: "100%"
                });
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
                    var currentSelections = $('#product_id').val() || [];
                    var options = '<option value="add_item" disabled>Add another Item</option>';
                    $.each(response.subproducts, function(index, item) {
                        options += '<option value="' + item.id + '">' + item.name + '</option>';
                    });
                    $('#product_id').html(options);
                    // alert($('#product_id').length);
                    $('#product_id').select2({
                        placeholder: "Choose item",
                        width: "100%",
                    });

                    $('#product_id').val(currentSelections).trigger('change');
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
                $("#product_id").html('<option value="">Error fetching Product</option>');
                $('#product_id').select2({
                    placeholder: "Choose item",
                    width: "100%"
                });
            }
        });
    }
</script>

@endpush