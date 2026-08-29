@extends('admin.layout.master')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')

@include('admin.layout.response_message')
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Countrys</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Countrys</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between mb-3">
                    <div class="card-title">
                        Countrys
                    </div>
                    <div class="prism-toggle">
                        <a href="javascript:void(0);" class="btn btn-primary dropdown-toggle mr-2" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne6">
                            Search
                        </a>
                        <a href="{{ route('admin-country.create') }}" class="btn btn-primary">Add Country</a>
                    </div>
                </div>

                <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6">
                    <div id="collapseOne6"
                        class="collapse m-3 {{ request()->anyFilled(['is_active', 'date_from', 'date_to']) ? 'show' : '' }}"
                        data-parent="#accordionExample6">
                        <div>
                            <form id="listSearchForm" class="row mb-6" method="GET"
                                action="{{ route('admin-country.index') }}">
                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label>Status</label>
                                    <select name="is_active" class="form-control select2init">
                                        <option value="" {{ request('is_active') === null ? 'selected' : '' }}>All
                                        </option>
                                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Activate
                                        </option>
                                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>
                                            Deactivate</option>
                                    </select>
                                </div>

                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label for="date_from" class="form-label">Date From</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from"
                                        value="{{ request('date_from') }}">
                                </div>

                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label for="date_to" class="form-label">Date To</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to"
                                        value="{{ request('date_to') }}">
                                </div>

                                <div class="col-lg-12 mt-3">
                                    <button type="submit" class="btn btn-primary btn-primary--icon">
                                        <span><i class="la la-search"></i> <span>Search</span></span>
                                    </button>
                                    &nbsp;&nbsp;
                                    <a href="{{ route('admin-country.index') }}"
                                        class="btn btn-secondary btn-secondary--icon">
                                        <span><i class="la la-close"></i> <span>Clear Search</span></span>
                                    </a>
                                </div>
                            </form>
                            <hr>
                        </div>
                    </div>
                </div>

                <table class="table table-bordered text-nowrap w-100 m-3" id="datatable-country">
                    <thead>
                        <tr id="tableHeaders">
                            <th>#</th>
                            <th>Name</th>
                            <th>Flag</th>
                            <th>Sort Name</th>
                            <th>Code</th>
                            <th>Currency Symbol</th>
                            <th>Currency Amount</th>
                            <th>Country Time Zone</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <style>
        td {
            padding: .75rem;
            vertical-align: middle;
            line-height: 1.462;
            font-size: .813rem;
            font-weight: 500;
        }
    </style>
@endsection

@push('scripts')
    <!-- Datatables Cdn -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.6/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
<script>

</script>
<script src="{{ asset('assets/js/datatables.js') }}"></script>

<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>



    <script>
        $(document).ready(function() {
            $('#datatable-country').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin-country.index') }}",
                    data: function(d) {
                        d.is_active = $('select[name="is_active"]').val();
                        d.date_from = $('input[name="date_from"]').val();
                        d.date_to = $('input[name="date_to"]').val();
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables Ajax Error:', error);
                        console.error('Response:', xhr.responseText);
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'country_flag',
                        name: 'country_flag'
                    },
                    {
                        data: 'sortname',
                        name: 'sortname'
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'currency_symbol',
                        name: 'currency_symbol'
                    },
                    {
                        data: 'currency_amount',
                        name: 'currency_amount'
                    },
                    {
                        data: 'country_time_zone',
                        name: 'country_time_zone'
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
    <script>
        $(document).on('click', '.status-toggle', function() {
            let id = $(this).data('id');
            let status = $(this).data('status');
            let button = $(this);

            $.ajax({
                url: `/admin/country/status/${id}/${status}`,
                method: 'GET',
                success: function(res) {
                    $('#datatable-country').DataTable().ajax.reload(null, false);
                },
                error: function(err) {
                    alert('Failed to update status');
                }
            });
        });
    </script>
@endpush
