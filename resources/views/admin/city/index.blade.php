@extends('admin.layout.master')
@section('content')
    @include('admin.layout.response_message')

    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Cityes</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cityes</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between mb-3">
                    <div class="card-title">
                        Cityes
                    </div>
                    <div class="prism-toggle">
                        <a href="javascript:void(0);" class="btn btn-primary dropdown-toggle mr-2" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne6">
                            Search
                        </a>
                        <a href="{{ route('admin-city.create', ['endesid' => request('endesid') ?? '']) }}" class="btn btn-primary">Add City</a>
                        {{-- <a href="{{ route('admin-city.create') }}" class="btn btn-primary">Add City</a> --}}
                    </div>
                </div>

                <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6">
                    <div id="collapseOne6"
                        class="collapse m-3 {{ request()->anyFilled(['is_active', 'date_from', 'date_to']) ? 'show' : '' }}"
                        data-parent="#accordionExample6">
                        <div>
                            <form id="listSearchForm" class="row mb-6" method="GET"
                                action="{{ route('admin-city.index') }}">
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
                                    <a href="{{ route('admin-city.index') }}" class="btn btn-secondary btn-secondary--icon">
                                        <span><i class="la la-close"></i> <span>Clear Search</span></span>
                                    </a>
                                </div>
                            </form>
                            <hr>
                        </div>
                    </div>
                </div>

                <table class="table table-bordered text-nowrap w-100 m-3" id="datatable-city">
                  <thead>
    <tr id="tableHeaders">
        <th>#</th>
        <th>State Name</th>
        <th>Name</th>
        <th>Sort Name</th>
        <th>Code</th>
        <th>Std Code</th>
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            // Get `endesid` from the request or set it to an empty string if not present
            let endesid = "{{ request('endesid') ?? '' }}";
            console.log(endesid);

            // Initialize the DataTable for City
            $('#datatable-city').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin-city.index') }}",  // URL for fetching city data
                    data: function(d) {
                        // Pass extra filters or parameters if needed
                        d.is_active = $('select[name="is_active"]').val();
                        d.date_from = $('input[name="date_from"]').val();
                        d.date_to = $('input[name="date_to"]').val();
                        d.endesid = endesid;  // Pass the `endesid` parameter to the backend
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'state_name',
                        name: 'state.name'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'short_name',
                        name: 'short_name'
                    },
                    {
                        data: 'postal_code',
                        name: 'postal_code'
                    },
                    {
                        data: 'std_code',
                        name: 'std_code'
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
                    }
                ]
            });
        });
    </script>

    <script>
        $(document).on('click', '.status-toggle', function() {
            let id = $(this).data('id');
            let status = $(this).data('status');
            let button = $(this);

            // Append `endesid` to the URL for the status update request
            let endesid = "{{ request('endesid') ?? '' }}";  // Get the `endesid` value

            $.ajax({
                url: `/admin/city/status/${id}/${status}?endesid=${endesid}`,  // Add `endesid` as a query parameter
                method: 'GET',
                success: function(res) {
                    // Reload the city DataTable after status update
                    $('#datatable-city').DataTable().ajax.reload(null, false);
                },
                error: function(err) {
                    alert('Failed to update status');
                }
            });
        });
    </script>
@endpush