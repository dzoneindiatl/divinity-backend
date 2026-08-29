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
                <li class="breadcrumb-item active" aria-current="page">Edit Tax</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
@php
    $selectedTaxType = old('tax_type', $tax->tax_type ?? '');
    $showFloatingFields = $selectedTaxType === 'floating';
@endphp
<div class="row">
    <div class="col-xl-12">
        <form action="{{route('admin-'.$model.'.update',base64_encode($tax->id))}}" method="post" id="taxForm"
            enctype="multipart/form-data">
            @csrf
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Edit Tax
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-10 mb-3">
                            <label class="form-label">
                                Tax Option <span class="text-danger">*</span>
                            </label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('tax_option') is-invalid @enderror"
                                        type="radio"
                                        name="tax_option"
                                        id="includeTax"
                                        value="inclusive"
                                        checked
                                        {{ old('tax_option', $tax->tax_option ?? '') === 'inclusive' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="includeTax">Inclusive Tax</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('tax_option') is-invalid @enderror"
                                        type="radio"
                                        name="tax_option"
                                        id="excludeTax"
                                        value="exclusive"
                                        {{ old('tax_option', $tax->tax_option ?? '') === 'exclusive' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="excludeTax">Exclusive Tax</label>
                                </div>
                                @if ($errors->has('tax_option'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('tax_option') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-xl-10 mb-3">
                            <label class="form-label">
                                Tax Type <span class="text-danger">*</span>
                            </label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('tax_type') is-invalid @enderror"
                                        type="radio"
                                        name="tax_type"
                                        id="flat"
                                        value="flat"
                                        onclick="changeTaxType('flat')"
                                        {{ old('tax_type', $tax->tax_type ?? '') === 'flat' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="flat">Flat</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('tax_type') is-invalid @enderror"
                                        type="radio"
                                        name="tax_type"
                                        id="floating"
                                        value="floating"
                                        onclick="changeTaxType('floating')"
                                        {{ old('tax_type', $tax->tax_type ?? '') === 'floating' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="floating">Floating</label>
                                </div>
                                @if ($errors->has('tax_type'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('tax_type') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!-- <div class="col-xl-3 mb-3 floating-tax-fields" style="display: {{ $showFloatingFields ? 'flex' : 'none' }};">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="tax_from">Tax From <span class="text-danger">*</span></label>
                                    <input type="text" name="tax_from" class="form-control flat-input" value="{{ $tax->tax_from ?? '' }}">
                                    @if ($errors->has('tax_from'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('tax_from') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 mb-3 floating-tax-fields" style="display: {{ $showFloatingFields ? 'flex' : 'none' }};">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="tax_to">Tax To <span class="text-danger">*</span></label>
                                    <input type="text" name="tax_to" class="form-control flat-input" value="{{ $tax->tax_to ?? '' }}">
                                    @if ($errors->has('tax_to'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('tax_to') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 mb-3">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="tax_rate">Flat (%) <span class="text-danger">*</span></label>
                                    <input type="text" name="tax_rate" class="form-control flat-input" value="{{ $tax->tax_rate ?? '' }}" required>
                                    @if ($errors->has('tax_rate'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('tax_rate') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div> -->
                        <div class="col-xl-10 mb-3 tax_floating_type_div" @if($tax->tax_type == 'flat') style="display:none" @endif>
                            <div class="table-responsive">
                                <table id="taxFloatingTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tax From <span class="text-danger">*</span></th>
                                            <th>Tax To <span class="text-danger">*</span></th>
                                            <th>Tax Rate (%) <span class="text-danger">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody id="floatingTBody">
                                        <tr>
                                            <td>
                                                <input type="number" name="tax_from[]" step="0.1" value="{{ $tax->tax_from ?? '' }}" class="form-control">
                                            </td>
                                            <td>
                                                <input type="number" name="tax_to[]" step="0.1" value="{{ $tax->tax_to ?? '' }}" class="form-control">
                                            </td>
                                            <td>
                                                <input type="text" name="tax_rate_floating[]" value="{{ $tax->tax_rate ?? '' }}" step="0.1" class="form-control">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger removeTaxFloating" style="display: none;">Delete</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="button" id="addFolatingRow" class="btn btn-primary">Add
                                    More</button>
                            </div>
                        </div>
                        <div class="col-xl-10 mb-3 tax_flat_type_div" @if($tax->tax_type == 'floating') style="display:none" @endif>
                            <div class="table-responsive">
                                <table id="taxFlatTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tax Rate (%) <span class="text-danger">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody id="flatTBody">
                                        <tr>
                                            <td>
                                                <input type="text" name="tax_rate_flat[]" step="0.1" value="{{ $tax->tax_rate ?? '' }}" class="form-control" required>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger removeTaxFlat" style="display: none;">Delete</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="button" id="addFlatRow" class="btn btn-primary">Add
                                    More</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
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
<script src="{{ asset('assets/js/custom/category.js') }}"></script>
<script>
    /*function changeTaxType(value) {
        const floatingFields = document.querySelectorAll('.floating-tax-fields');
        const taxFrom = document.querySelector('input[name="tax_from"]');
        const taxTo = document.querySelector('input[name="tax_to"]');
        if (value === 'floating') {
            floatingFields.forEach(field => field.style.display = 'flex');
            taxFrom.setAttribute('required', 'required');
            taxTo.setAttribute('required', 'required');
        } else {
            floatingFields.forEach(field => field.style.display = 'none');
            taxFrom.removeAttribute('required');
            taxTo.removeAttribute('required');
        }
    }*/
    /* on tax type change start here*/
    function changeTaxType(tax_type) {
        const taxFrom = document.querySelector('input[name="tax_from[]"]');
        const taxTo = document.querySelector('input[name="tax_to[]"]');
        const taxRateFloating = document.querySelector('input[name="tax_rate_floating[]"]');
        const taxRateFlat = document.querySelector('input[name="tax_rate_flat[]"]');
        if (tax_type == 'flat') {
            $('.tax_floating_type_div').hide();
            $('.tax_flat_type_div').show();
            taxFrom.removeAttribute('required');
            taxTo.removeAttribute('required');
            taxRateFloating.removeAttribute('required');
            taxRateFlat.setAttribute('required', 'required');
        }
        if (tax_type == 'floating') {
            $('.tax_flat_type_div').hide();
            $('.tax_floating_type_div').show();
            taxFrom.setAttribute('required', 'required');
            taxTo.setAttribute('required', 'required');
            taxRateFloating.setAttribute('required', 'required');
            taxRateFlat.removeAttribute('required');
        }
    }
    /*for floating tax case start here*/
    /*for floating tax case start here*/
    document.getElementById("addFolatingRow").addEventListener("click", function() {
        let table = document.getElementById("taxFloatingTable");
        let tbody = table.querySelectorAll("#floatingTBody")[0];
        let newRow = tbody.insertRow();

        newRow.innerHTML = `<td>
                <input type="number" name="tax_from[]" step="0.1" class="form-control" required>
            </td>
            <td>
                <input type="number" name="tax_to[]" step="0.1" class="form-control" required>
            </td>
            <td>
                <input type="text" name="tax_rate_floating[]" step="0.1" class="form-control" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger removeTaxFloating">Delete</button>
            </td>`;
        addRemoveTaxFloating();
    });

    function addRemoveTaxFloating() {
        document.querySelectorAll(".removeTaxFloating").forEach(button => {
            button.addEventListener("click", function() {
                this.closest("tr").remove();
            });
        });
    }
    addRemoveTaxFloating();
    /*for floating tax case end here*/
    /*for flat tax case start here*/
    document.getElementById("addFlatRow").addEventListener("click", function() {
        let table = document.getElementById("taxFlatTable");
        let tbody = table.querySelectorAll("#flatTBody")[0];
        let newRowFlat = tbody.insertRow();

        newRowFlat.innerHTML = `<td>
                <input type="text" name="tax_rate_flat[]" step="0.1" class="form-control required">
            </td>
                <button type="button" class="btn btn-danger removeTaxFlat">Delete</button>
            </td>`;
        addRemoveTaxFlat();
    });

    function addRemoveTaxFlat() {
        document.querySelectorAll(".removeTaxFlat").forEach(button => {
            button.addEventListener("click", function() {
                this.closest("tr").remove();
            });
        });
    }
    addRemoveTaxFlat();
    /*for flat tax case end here*/
</script>
@endpush