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
                <li class="breadcrumb-item"><a href="{{  route('admin-size-charts.index')}}">Size Chart</a></li>
                <li class="breadcrumb-item"><a href="{{  route('admin-size-chart-details.index', base64_encode($SizeChartDetail->size_chart_id))}}">Size Chart Detail</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Size Chart Detail</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->

       

<div class="row">
    <div class="col-xl-12">
        <form action="{{ route('admin-' . $model . '.edit', base64_encode($SizeChartDetail->id)) }}" method="post" id="shippingAreaForm" enctype="multipart/form-data">
            @csrf
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Edit Size Chart Detail
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="name" class="form-label"><span class="text-danger">* </span>Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Enter Name" value="{{ $SizeChartDetail->name }}">
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
                                        <option value="{{ $currency->id }}">{{ $currency->id }}</option>
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
                                        <option value="1" {{ ($SizeChartDetail->unite == 1) ? 'selected' : '' }}>{{ 'Inches' }}</option>
                                        <option value="2" {{ ($SizeChartDetail->unite == 2) ? 'selected' : '' }}>{{ 'Centimeter' }}</option>
                                    </select>
                                </div>
                            </div>
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
                    @if(!empty($sizeChartDetailValue))
                        <?php
                        $tableHeaders = $sizeChartDetailValue[0]['table_headers'];
                        $tableData = $sizeChartDetailValue[0]['table_data'];
                        ?>
                        <input type="hidden" name="tableHeadersInput" id="tableHeadersInput" value="{{ json_encode($tableHeaders) }}">
                        <div id="kt_repeater_1" class="ml-7">
                            <div class="form-group row" id="kt_repeater_1">
                                <div data-repeater-list="dataArr" class="col-lg-12">
                                    <button type="button" id="addColumn" class="btn btn-primary mb-3">Add Column</button>
                                    <button type="button" id="addRow" class="btn btn-primary mb-3">Add Row</button>
                                    <table class="table table-bordered" id="sizeChartTable">
                                        <thead>
                                            <tr id="headersRow">
                                                @foreach($sizeChartDetailValue[0]['table_headers'] as $key => $header)
                                                    <th><input type="text" name="headers[{{ $loop->index }}]" value="{{ $header }}"></th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($sizeChartDetailValue[0]['table_data'] as $rowIndex => $dataRow)
                                                <tr>
                                                    @foreach($dataRow as $colIndex => $dataCell)
                                                        <td><input type="text" name="cell[{{ $rowIndex }}][{{ $colIndex }}]" value="{{ $dataCell }}"></td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @else
                        <div id="kt_repeater_1" class="ml-7">
                            <div class="form-group row" id="kt_repeater_1">
                                <div data-repeater-list="dataArr" class="col-lg-12">
                                    <button type="button" id="addColumn" class="btn btn-primary mb-3">Add Column</button>
                                    <button type="button" id="addRow" class="btn btn-primary mb-3">Add Row</button>
                                    <table class="table table-bordered" id="sizeChartTable">
                                        <thead>
                                            <tr id="headersRow">
                                                <th><input type="text" name="headers[0]" value="Header 1"></th>
                                                <th><input type="text" name="headers[1]" value="Header 2"></th>
                                                <th><input type="text" name="headers[2]" value="Header 3"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input type="text" name="cell[0][0]" value="dsad"></td>
                                                <td><input type="text" name="cell[0][1]" value="dadda"></td>
                                                <td><input type="text" name="cell[0][2]" value="dd"></td>
                                            </tr>
                                            <tr>
                                                <td><input type="text" name="cell[1][0]" value="sad"></td>
                                                <td><input type="text" name="cell[1][1]" value="ada"></td>
                                                <td><input type="text" name="cell[1][2]" value="ada"></td>
                                            </tr>
                                            <tr>
                                                <td><input type="text" name="cell[2][0]" value="adad"></td>
                                                <td><input type="text" name="cell[2][1]" value="d"></td>
                                                <td><input type="text" name="cell[2][2]" value="s"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
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
<script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Internal Select-2.js -->
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>
<script src="{{ asset('assets/js/form-validation.js') }}"></script>
<script src="{{ asset('assets/plugin/tagify/tagify.min.js') }}"></script>
<script src="{{ asset('assets/js/repeater.js')}}"></script>
<script src="{{ asset('assets/js/custom/sizecharts.js')}}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sizeChartTable = document.getElementById("sizeChartTable");
        const headersRow = document.getElementById("headersRow");
        const addColumnButton = document.getElementById("addColumn");
        const addRowButton = document.getElementById("addRow");

        // Function to add a new column
        addColumnButton.addEventListener("click", function() {
            const headerCells = headersRow.querySelectorAll("th");
            const newColumnIndex = headerCells.length;
            const newHeaderCell = document.createElement("th");
            newHeaderCell.innerHTML = `<input type="text" name="headers[${newColumnIndex}]" value="Header ${newColumnIndex + 1}">`;
            headersRow.appendChild(newHeaderCell);

            sizeChartTable.querySelectorAll("tbody tr").forEach((row) => {
                const newCell = document.createElement("td");
                newCell.innerHTML = `<input type="text" name="cell[${row.rowIndex - 1}][${newColumnIndex}]" value="">`;
                row.appendChild(newCell);
            });
        });

        // Function to add a new row
        addRowButton.addEventListener("click", function() {
            const rows = sizeChartTable.querySelectorAll("tbody tr");
            const newRow = document.createElement("tr");
            const newRowIndex = rows.length;
            const columnsCount = headersRow.querySelectorAll("th").length;

            for (let i = 0; i < columnsCount; i++) {
                const newCell = document.createElement("td");
                newCell.innerHTML = `<input type="text" name="cell[${newRowIndex}][${i}]" value="">`;
                newRow.appendChild(newCell);
            }

            sizeChartTable.querySelector("tbody").appendChild(newRow);
        });
    });
</script>
@endpush