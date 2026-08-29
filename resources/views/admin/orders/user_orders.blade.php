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
        <h1 class="page-title fw-semibold fs-18 mb-0">Orders</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Orders</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">
                        Orders
                    </div>
                    <div class="prism-toggle">
                        <a href="javascript:void(0);" class="btn btn-primary dropdown-toggle mr-2" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne6">
                            Search
                        </a>
                    </div>
                </div>
                <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6">
                    <div id="collapseOne6" class="collapse m-3 <?php echo !empty($searchVariable) ? 'show' : ''; ?>" data-parent="#accordionExample6">
                        <div>
                            <form id="listSearchForm" class="row mb-6">
                                <div class="col-lg-3  mb-lg-5 mb-6">

                                    <label>Status</label>
                                    <select name="status" class="form-control select2init"
                                        value="{{ $searchVariable['status'] ?? '' }}">
                                        <option value="">All</option>
                                        @foreach ($status_array as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ !empty($searchVariable) && $searchVariable['status'] == $key ? 'selected' : '' }}>
                                                {{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label>Order Number</label>
                                    <input type="text" class="form-control" name="order_number"
                                        placeholder="Order Number" value="{{ $searchVariable['order_number'] ?? '' }}">
                                </div>
                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label for="date_from" class="form-label"><span class="text-danger">* </span>Date
                                        From</label>
                                    <input type="date" class="form-control @error('date_from') is-invalid @enderror"
                                        id="date_from" name="date_from" placeholder="Date From">

                                </div>
                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label for="date_to" class="form-label"><span class="text-danger">* </span>Date
                                        To</label>
                                    <input type="date" class="form-control @error('date_to') is-invalid @enderror"
                                        id="date_to" name="date_to" placeholder="Date To">

                                </div>


                            </form>
                            <div class="row mt-8">
                                <div class="col-lg-12">
                                    <button class="btn btn-primary btn-primary--icon" id="kt_search_btn">
                                        <span>
                                            <i class="la la-search"></i>
                                            <span>Search</span>
                                        </span>
                                    </button>
                                    &nbsp;&nbsp;
                                    <a href='{{ route('admin-' . "$model.index") }}'
                                        class="btn btn-secondary btn-secondary--icon">
                                        <span>
                                            <i class="la la-close"></i>
                                            <span>Clear Search</span>
                                        </span>
                                    </a>
                                </div>
                            </div>
                            <hr>
                        </div>
                    </div>
                </div>
                <form id="bulkPrintForm">
                    <div class="container mt-4">
                        <div class="row">
                            <div class="col-2 mt-4">
                                <label for="selectOptions" class="form-label">Select All:</label>
                                <input type="checkbox" id="selectAll">
                            </div>
                            <div class="col-2 mt-2">
                                <button id="bulk-print" class="btn btn-outline-primary"><i class="bi bi-printer"></i>Bulk
                                    Print</button>
                            </div>
                            <div class="container col-5 d-flex justify-content-end align-items-center">
                                <button type="button" class="btn btn-outline-primary my-1 me-2" fdprocessedid="g9dg58f">
                                    Total Orders:
                                    <span class="badge ms-2 totalDataCount">{{ $totalResults }}</span>
                                </button>
                            </div>
                        </div>
                    </div><br>

                    <table id="datatable-basic" data-sorting="" data-order="" class="table table-bordered text-nowrap"
                        style="width:100%">
                        <thead>
                            <tr id="tableHeaders">
                                <th class="sortable" data-column="order_number">Order Number <i
                                        class="sort-icon ri-sort-asc"></i></th>
                                <th class="sortable" data-column="name">Customer<i class="sort-icon ri-sort-asc"></i></th>
                                <th class="sortable" data-column="total">Total Amount <i
                                        class="sort-icon ri-sort-asc"></i></th>
                                <th class="sortable" data-column="total">Payment Status <i
                                        class="sort-icon ri-sort-asc"></i></th>
                                <th class="sortable" data-column="name">Items<i class="sort-icon ri-sort-asc"></i></th>
                                <th class="sortable" data-column="payment_method">Payment Method <i
                                        class="sort-icon ri-sort-asc"></i></th>
                                <th class="sortable" data-column="status">Order Status <i
                                        class="sort-icon ri-sort-asc"></i></th>
                                <th class="sortable" data-column="created_at">Created On <i
                                        class="sort-icon ri-sort-asc"></i></th>
                                <th>View Print</th>
                                <th>Action </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="loader-row" style="display: none;">
                                <td colspan="7" style="text-align: center;">
                                    <button class="btn btn-light" type="button" disabled="">
                                        <span class="spinner-grow spinner-grow-sm align-middle" role="status"
                                            aria-hidden="true"></span> Loading...
                                    </button>
                                </td>
                            </tr>
                            @if ($results->isNotEmpty())
                                @include('admin.orders.load_more_data', ['results' => $results])
                            @else
                                <tr>
                                    <td colspan="7" style="text-align: center;">No results found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    @if ($results->isNotEmpty() && $totalResults > Config('Reading.records_per_page'))
                        <div class="my-3" style="display: flex; justify-content: center;">
                            <button class="btn btn-primary-light btn-border-down" fdprocessedid="l5zhli" id="load-more"
                                data-offset="{{ Config('Reading.records_per_page') }}" data-default-offset="0"
                                data-limit="{{ Config('Reading.records_per_page') }}"
                                data-default-limit="{{ Config('Reading.records_per_page') }}">
                                <span class="loadMoreText me-2">Load More</span>
                                <span class="loading"><i class="ri-refresh-line fs-16"></i></span>
                            </button>
                        </div>
                    @else
                        <div class="my-3" style="display: flex; justify-content: center;">
                            <button class="btn btn-primary-light btn-border-down" style="display:none;"
                                fdprocessedid="l5zhli" id="load-more"
                                data-offset="{{ Config('Reading.records_per_page') }}" data-default-offset="0"
                                data-limit="{{ Config('Reading.records_per_page') }}"
                                data-default-limit="{{ Config('Reading.records_per_page') }}">
                                <span class="loadMoreText me-2">Load More</span>
                                <span class="loading"><i class="ri-refresh-line fs-16"></i></span>
                            </button>
                        </div>
                    @endif
                </form>

            </div>
        </div>
    </div>
    </div>
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
    <script>
        var routeName = '{{ route($listRouteName) }}';
        // Your DataTables initialization or other JavaScript logic here
    </script>
    <!-- Internal Datatables JS -->
    <script src="{{ asset('assets/js/datatables.js') }}"></script>

    <!-- Sweetalerts JS -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.status').change(function() {
                var status = $(this).val();
                var id = $(this).data('order');
                var url = "{{ route('admin-orders.change-status') }}";
                Swal.fire({
                    title: "Are you sure?",
                    text: "Want to chane this status ?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, change it",
                    cancelButtonText: "No, cancel",
                    reverseButtons: true
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            type: "POST",
                            url: url,
                            data: {
                                id: id,
                                status: status
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: response.status,
                                    title: response.message,
                                    showConfirmButton: true,
                                })
                            },
                            error: function(data) {
                                console.log('Error:', data);
                            }
                        });
                    } else {
                        swal("Cancelled", "Your current status remain same :)", "error");
                    }
                });
            });
        });
        $(document).ready(function() {
            $('#selectAll').on('click', function() {
                $('.order-checkbox').prop('checked', this.checked);
            });

            $('.order-checkbox').on('click', function() {
                if (!$(this).prop('checked')) {
                    $('#selectAll').prop('checked', false);
                } else if ($('.order-checkbox:checked').length === $('.order-checkbox').length) {
                    $('#selectAll').prop('checked', true);
                }
            });
            $('#bulk-print').click(function(event) {
                event.preventDefault();
                var formData = new FormData($('#bulkPrintForm')[0]);

                $.ajax({
                    url: "{{ route('admin-orders.generate.bulk.invoice') }}",
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        // alert('Success response received');
                        if (data.pdf_url) {
                            // alert('PDF URL: ' + data.pdf_url);
                            window.open(data.pdf_url, '_blank');
                        } else {
                            alert('No PDF URL found in response');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error: ' + textStatus + ' - ' + errorThrown);
                    }
                });
            });
        });
    </script>
@endpush
