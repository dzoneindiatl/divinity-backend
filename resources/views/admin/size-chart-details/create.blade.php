@extends('admin.layout.master')

@push('styles')
<link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
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
                <li class="breadcrumb-item"><a href="{{ route('admin-size-charts.index')}}">Size Chart</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin-size-chart-details.index', base64_encode($dep_id))}}">Size Chart Detail</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Size Chart Detail</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="{{ route('admin-size-chart-details.add', base64_encode($dep_id)) }}" method="post" id="shippingAreaForm" enctype="multipart/form-data">
            @csrf
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Create Size Chart Detail
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="name" class="form-label"><span class="text-danger">* </span>Name</label>
                                    <input type="hidden" id="tableHeadersInput" name="tableHeadersInput">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Enter Name">
                                    @if ($errors->has('name'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('name') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="name" class="form-label"><span class="text-danger">* </span>Country</label>
                                    <select class="js-example-placeholder-single js-states form-control" multiple="multiple" name="countryData[]">
                                        @forelse ($listQuery['country'] as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @empty
                                        <option value="" selected>No Data found</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="name" class="form-label"><span class="text-danger">* </span>Currency</label>
                                    <select class="js-example-placeholder-single js-states form-control" multiple="multiple" name="currencyData[]">
                                        @forelse ($listQuery['currency'] as $currency)
                                        <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                        @empty
                                        <option value="" selected>No Data found</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="name" class="form-label"><span class="text-danger">* </span>Units</label>
                                    <select class="js-example-placeholder-single js-states form-control" name="unitData">
                                        <option value="1">{{ 'Inches' }}</option>
                                        <option value="2">{{ 'Centimeter' }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Assign To
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6" id="categoryDropdown">
                            <label for="reference" class="form-label">Categories</label>
                            <select class="js-example-placeholder-single js-states form-control" multiple="multiple" name="categoryData[]">
                                @forelse ($categories as $category)
                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                @empty
                                <option value="" selected>No Data found</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="col-xl-6" id="productDropdown">
                            <label for="reference" class="form-label">Products</label>
                            <select class="js-example-placeholder-single js-states form-control" multiple="multiple" name="productData[]">
                                @forelse ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @empty
                                <option value="" selected>No Data found</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="variantValuesRow card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Size Chart Detail Values
                    </div>
                </div>
                <div class="card-body">
                    <div id="dynamicTableSection">
                        <label for="numRows">Number of Rows:</label>
                        <input type="number" id="numRows" name="numRows" min="1" value="1">
                        
                        <label for="numColumns">Number of Columns:</label>
                        <input type="number" id="numColumns" name="numColumns" min="1" value="1">
                        <button type="button" onclick="createTable()">Create Table</button>
                        <table id="dynamicTable" class="table table-bordered">
                            <thead>
                                <tr id="tableHeaders">
                                    <!-- Headers will be added dynamically here -->
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <!-- Rows will be added dynamically here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="variantValuesRow card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Size Chart Measure Detail
                    </div>
                </div>
            </div>
            
            <div class="card custom-card">
                <div class="card-header">

                    <div class="card-title">

                        Images

                    </div>

                </div>
                <div class="card-body">
                    <div class="col-xl-12 mb-4">
                        <label for="content_description" class="form-label">Content Description</label>
                        <textarea class="form-control" name="content_description"></textarea>
                    </div>
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
<script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<!-- Select2 Cdn -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/plugin/tagify/tagify.min.js') }}"></script>
<!-- Internal Select-2.js -->
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>
<script src="{{ asset('assets/js/repeater.js')}}"></script>
<script src="{{ asset('assets/js/custom/sizecharts.js')}}"></script>
<script src="{{ asset('assets/js/form-validation.js') }}"></script>
<script>
     function createTable() {
        var numRows = document.getElementById('numRows').value;
        var numColumns = document.getElementById('numColumns').value;
        var tableHeaders = document.getElementById('tableHeaders');
        var tableBody = document.getElementById('tableBody');

        // Clear existing table content
        tableHeaders.innerHTML = '';
        tableBody.innerHTML = '';

        // Add header cells
        for (var i = 0; i < numColumns; i++) {
            var headerCell = document.createElement('th');
            headerCell.contentEditable = true;
            headerCell.innerHTML = 'Header ' + (i + 1);
            headerCell.setAttribute('data-header-index', i);
            tableHeaders.appendChild(headerCell);
        }

        // Add rows and cells
        for (var i = 0; i < numRows; i++) {
            var newRow = tableBody.insertRow();
            for (var j = 0; j < numColumns; j++) {
                var newCell = newRow.insertCell();
                newCell.innerHTML = '<input type="text" name="cell[' + i + '][' + j + ']" class="form-control" placeholder="Value">';
            }
        }

        // Update the hidden input with the headers
        updateTableHeaders();
    }

    function updateTableHeaders() {
        var headers = [];
        var tableHeaders = document.getElementById('tableHeaders').children;
        for (var i = 0; i < tableHeaders.length; i++) {
            headers.push(tableHeaders[i].innerText.trim()); // Trim to remove any leading or trailing whitespace
        }
        document.getElementById('tableHeadersInput').value = JSON.stringify(headers);
    }

    // Listen to changes in the headers to update the hidden input
    document.getElementById('tableHeaders').addEventListener('input', updateTableHeaders);
    
    
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
    </script>
@endpush