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
                <li class="breadcrumb-item active" aria-current="page">Create Size Chart</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->

<div class="row">
    <div class="col-xl-12">
        <form action="{{ route('admin-size-charts.store') }}" method="post" id="shippingcompanyForm"
            enctype="multipart/form-data">
            @csrf
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Create Size Chart
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="country_id" class="form-label"><span class="text-danger">* </span>Country</label>
                                    <select class="js-example-placeholder-single js-states form-control" name="country_id">
                                        @forelse ($countries as $country)
                                        <option value="{{ $country['id'] }}" {{$country['id']=='102' ? 'selected' : ""}}>{{ $country['name'] }}</option>
                                        @empty
                                        <option value="" selected>No Data found</option>
                                        @endforelse
                                    </select>
                                    @if ($errors->has('country_id'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('country_id') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="name" class="form-label"><span class="text-danger">* </span>Size chart Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Enter Name">
                                    @if ($errors->has('name'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('name') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-xl-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('title') is-invalid @enderror" name="description" id="description" cols="30" rows="5">{!! isset($size_charts->description) ? $size_charts->description: old('description') !!}</textarea>
                            @if ($errors->has('description'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('description') }}
                            </div>
                            @endif
                        </div> -->
                    </div>
                </div>
            </div>
           
            <div class="card custom-card">

                <div class="card-header">

                    <div class="card-title">

                        Assign to

                    </div>

                </div>

                <div class="card-body" id="category-section">

                    <div class="row">
                        <div class="col-xl-4" id="category">

                            <div class="card-body p-0">

                                <div class="mb-3">

                                    <label for="category" class="form-label"><span class="text-danger">*
                                        </span>Category</label>

                                    <select class="form-control @error('category_id') is-invalid @enderror" name="category_id" id="prdct_category_id" onchange="getRelatedSubCategories();">
                                        <option value="">None</option>
                                        @forelse ($categories as $category)
                                        <option value="{{ $category->id }}" {{(old('category_id') == $category->id) ? 'selected' : ''}}>
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

                        <div class="col-xl-4" style="display: none;" id="sub_category">

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
                        <div class="col-xl-4" style="display: none;" id="child_category">
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
                    </div>

                </div>

            </div>
            <!-- end category subcategory childcategory assign -->
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                    Description
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">                        
                        <div class="col-xl-6 mb-3">
                            <label for="description" class="form-label">Centimeter Description</label>
                            <textarea class="form-control @error('title') is-invalid @enderror" name="centimeter_details" id="centimeter_details" cols="50" rows="7">{!! isset($size_charts->centimeter_details) ? $size_charts->centimeter_details: old('centimeter_details') !!}</textarea>
                            @if ($errors->has('centimeter_details'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('centimeter_details') }}
                            </div>
                            @endif
                        </div>
                        <div class="col-xl-6 mb-3">
                            <label for="inch_details" class="form-label">INCH Description</label>
                            <textarea class="form-control @error('title') is-invalid @enderror" name="inch_details" id="inch_details" cols="50" rows="7">{!! isset($size_charts->inch_details) ? $size_charts->inch_details: old('inch_details') !!}</textarea>
                            @if ($errors->has('inch_details'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('inch_details') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- start generate size chart table -->
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Images
                    </div>
                </div>
                <div class="card-body">
                    <!-- <div class="col-xl-12 mb-4">
                        <label for="content_description" class="form-label">Content Description</label>
                        <textarea class="form-control" name="content_description"></textarea>
                    </div> -->
                    <div id="image_details_container">
                        <!-- Initial Row -->
                        <div class="row image_details_row border border-primary rounded mb-2 p-2">
                            <div class="col-xl-12">
                                <button type="button" class="btn btn-danger btn-sm float-end remove_row_button">Remove</button>
                            </div>
                            <div class="col-xl-6">
                                <label for="chart_image" class="form-label">Image</label>
                                <input type="file" class="form-control" name="chart_image[]">
                            </div>
                            <div class="col-xl-6">
                                <label for="file" class="form-label">Heading</label>
                                <input type="text" class="form-control" name="image_heading[]">
                            </div>
                            <div class="col-xl-12">
                                <label for="file" class="form-label">Description</label>
                                <textarea class="form-control" name="image_description[]"></textarea>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" id="add_more_button">Add More Images</button>
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
<script src="{{ asset('assets/js/repeater.js')}}"></script>

<script>
    CKEDITOR.replace(<?php echo 'centimeter_details'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;

    CKEDITOR.replace(<?php echo 'inch_details'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;

    function getRelatedSubCategories()
     {
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
                // Show the subcategory div if subcategories are available
                $('#sub_category').show();
            } else {
                html += '<option value="">No Subcategories Available</option>';
                $('#sub_category').hide(); // Hide if no subcategories
            }
            $("#prdct_sub_category_id").html(html);
            $('#prdct_sub_category_id').select2({
                placeholder: "Choose Sub Category",
                width: "100%"
            });
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ' + status + ' ' + error);
        }
    });
}

function getchildcategory() {
    var display = document.getElementById('child_category');
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
                $('#prdct_child_category_id').select2({
                    placeholder: "Choose item",
                    width: "100%"
                });

                $('#prdct_child_category_id').val(currentSelections).trigger('change');

                // Show the child category div if child categories are available
                $('#child_category').show();
            } else {
                $("#prdct_child_category_id").html('<option value="">No Product Available</option>');
                $('#child_category').hide(); // Hide if no child categories
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

    document.addEventListener('DOMContentLoaded', function() {
        function createImageRow() {
            const newRow = document.createElement('div');
            newRow.className = 'row image_details_row border border-primary rounded mb-2 p-2';
            newRow.innerHTML = `
                <div class="col-xl-12">
                    <button type="button" class="btn btn-danger btn-sm float-end remove_row_button">Remove</button>
                </div>
                <div class="col-xl-6">
                    <label for="chart_image" class="form-label">Image</label>
                    <input type="file" class="form-control" name="chart_image[]">
                </div>
                <div class="col-xl-6">
                    <label for="file" class="form-label">Heading</label>
                    <input type="text" class="form-control" name="image_heading[]">
                </div>
                <div class="col-xl-12">
                    <label for="file" class="form-label">Description</label>
                    <textarea class="form-control" name="image_description[]"></textarea>
                </div>
            `;
            return newRow;
        }

        function createOptionRow() {
            const newOptionRow = document.createElement('div');
            newOptionRow.className = 'row mb-3 option-group border border-primary rounded mb-2 p-2';
            newOptionRow.innerHTML = `
                            <div class="col-xl-12">
                                <button type="button" class="btn btn-danger btn-sm float-end remove-btn">Remove</button>
                            </div>
                            <div class="col-xl-12 mb-3">
                                <label class="form-label"><span class="text-danger">* </span>Option Name</label>
                                <input type="text" class="form-control" name="option_name[]" placeholder="Enter option name">
                            </div>
                            <div class="col-xl-12 mb-3 input-container" id="inputFieldsContainer">
                                <label class="form-label"><span class="text-danger">* </span>Option Value</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="option_value[]" placeholder="Enter option value">
                                    <button type="button" class="btn btn-danger" onclick="removeOptionValue(this)"><i class="bi bi-trash3-fill"></i></button>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <button type="button" class="btn btn-primary add-btn">More value</button>
                            </div>
            `;
            return newOptionRow;
        }

        document.getElementById('add_more_button').addEventListener('click', function() {
            const newRow = createImageRow();
            document.getElementById('image_details_container').appendChild(newRow);
        });
        document.getElementById('add-option').addEventListener('click', function() {
            const newOptionRow = createOptionRow();
            document.getElementById('options-container').appendChild(newOptionRow);
        });

        document.getElementById('image_details_container').addEventListener('click', function(event) {
            if (event.target.classList.contains('remove_row_button')) {
                event.target.closest('.row').remove();
            }
        });

        document.getElementById('options-container').addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-btn')) {
                event.target.closest('.row').remove();
            }
        });
        document.querySelector('.add-btn').addEventListener('click', function() {
            const inputFieldsContainer = document.getElementById('inputFieldsContainer');
            const clonedContainer = inputFieldsContainer.cloneNode(true);
            const inputs = clonedContainer.querySelectorAll('input');
            inputs.forEach(input => input.value = '');
            const parentContainer = inputFieldsContainer.parentElement;
            parentContainer.insertBefore(clonedContainer, this.parentElement);
        });

    });

    function removeOptionValue(button) {
        const container = button.closest('.input-container');
        if (container) {
            container.remove();
        }
    }
</script>
@endpush