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
            <form action="{{ route('admin-new-size-charts.store') }}" method="post" id="shippingcompanyForm"
                enctype="multipart/form-data">
                @csrf


                <div class="card custom-card">

                    <div class="card-header">

                        <div class="card-title">

                            Assign to

                        </div>

                    </div>

                    <div class="card-body" id="category-section">

                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card-body p-0">
                                    <div class="mb-3">

                                        <label for="category" class="form-label"><span class="text-danger">*
                                            </span>Category</label>

                                        <select class="form-control @error('category') is-invalid @enderror"
                                            name="category">
                                            <option value="Upper">Upper</option>
                                            <option value="Bottom">Bottom</option>
                                        </select>
                                        <div class="invalid-feedback" id="categoryidError">
                                            {{ $errors->first('category') }}
                                        </div>

                                    </div>
                                </div>

                            </div>

                            <div class="col-xl-12">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table id="sizeChartTable" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Size</th>
                                                    <th>Chest</th>
                                                    <th>Waist</th>
                                                    <th>Hip</th>
                                                    <th>Shoulder</th>
                                                    <th>Armhole</th>
                                                    <th>Sleeve Length</th>
                                                    <th>Length</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><input type="text" name="size[]" required class="form-control"
                                                            placeholder="e.g., S, M, L"></td>
                                                    <td><input type="number" name="chest[]" step="0.1"
                                                            class="form-control"></td>
                                                    <td><input type="number" name="waist[]" required step="0.1"
                                                            class="form-control"></td>
                                                    <td><input type="number" name="hip[]" required step="0.1"
                                                            class="form-control"></td>
                                                    <td><input type="number" name="shoulder[]" step="0.1"
                                                            class="form-control"></td>
                                                    <td><input type="number" name="armhole[]" step="0.1"
                                                            class="form-control"></td>
                                                    <td><input type="number" name="sleeve_length[]" step="0.1"
                                                            class="form-control">
                                                    </td>
                                                    <td><input type="number" name="length[]" required step="0.1"
                                                            class="form-control"></td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger removeRow">X</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <button type="button" id="addRow" class="btn btn-primary">Add More</button>

                                </div>

                            </div>


                        </div>

                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    Assign To Size Chat Bottom Section
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-xl-12 mb-3">
                                        <label for="description" class="form-label">Bottom Content</label>
                                        <textarea class="form-control @error('title') is-invalid @enderror" name="description" id="description" cols="30"
                                            rows="5">{!! isset($bottom_data->description) ? $bottom_data->description : old('description') !!}</textarea>
                                        @if ($errors->has('description'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('description') }}
                                            </div>
                                        @endif
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
    <script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>
    <script src="{{ asset('assets/js/form-validation.js') }}"></script>
    <script src="{{ asset('assets/js/custom/category.js') }}"></script>
    <script src="{{ asset('assets/js/repeater.js') }}"></script>

    <script>
        CKEDITOR.replace(<?php echo 'description'; ?>, {
            filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
            enterMode: CKEDITOR.ENTER_BR
        });
        CKEDITOR.config.allowedContent = true;

        document.getElementById("addRow").addEventListener("click", function() {
            let table = document.getElementById("sizeChartTable").getElementsByTagName('tbody')[0];
            let newRow = table.insertRow();

            newRow.innerHTML = `<td><input type="text" name="size[]" required class="form-control" placeholder="e.g., S, M, L"></td>
        <td><input type="number" name="chest[]" step="0.1" class="form-control"></td>
        <td><input type="number" name="waist[]" required step="0.1" class="form-control"></td>
        <td><input type="number" name="hip[]" required step="0.1" class="form-control"></td>
        <td><input type="number" name="shoulder[]" step="0.1" class="form-control"></td>
        <td><input type="number" name="armhole[]" step="0.1" class="form-control"></td>
        <td><input type="number" name="sleeve_length[]" step="0.1" class="form-control"></td>
        <td><input type="number" name="length[]" required step="0.1" class="form-control"></td>
        <td><button type="button" class="btn btn-danger removeRow">X</button></td>
    `;

            addRemoveEvent();
        });

        function addRemoveEvent() {
            document.querySelectorAll(".removeRow").forEach(button => {
                button.addEventListener("click", function() {
                    this.closest("tr").remove();
                });
            });
        }

        addRemoveEvent();
    </script>
@endpush
