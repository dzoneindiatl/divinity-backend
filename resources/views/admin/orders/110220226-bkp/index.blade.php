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
        <div class="col-xxl-2 col-xl-2 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-top">
                        <div class="flex-fill">
                            <div class="d-flex flex-wrap align-items-center justify-content-between fs-14 mb-2">
                                <span class="flex-fill">Total Order Items</span>
                            </div>
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <h5 class="fw-semibold mb-0">{{ !empty($orderItemCounts)?array_sum($orderItemCounts): $totalorder ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(!empty($itemCounts))
            @foreach ($itemCounts as $itemstatus=>$itemcount )
                <div class="col-xxl-2 col-xl-2 col-lg-6 col-md-6">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex align-items-top">
                                <div class="flex-fill">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between fs-14 mb-2">
                                        <span class="flex-fill">{{ $itemstatus }}</span>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                                        <h5 class="fw-semibold mb-0">{{ $itemcount ?? 0 }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             @endforeach
        @endif

        <div class="col-xxl-2 col-xl-2 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-top">
                        <div class="flex-fill">
                            <div class="d-flex flex-wrap align-items-center justify-content-between fs-14 mb-2">
                                <span class="flex-fill">Total Revenue</span>
                            </div>
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <h5 class="fw-semibold mb-0">RS. {{ $totalrevenue ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
        <h5>Today's Orders</h5>
        <div class="col-xxl-2 col-xl-2 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-top">
                        <div class="flex-fill">
                            <div class="d-flex flex-wrap align-items-center justify-content-between fs-14 mb-2">
                                <span class="flex-fill">Total Order Items</span>
                            </div>
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <h5 class="fw-semibold mb-0">{{ $totalItemTodayCount ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(!empty($itemTodaysCounts))
           @foreach ($itemTodaysCounts as $todayitemstatus=>$todayitemcount )
                <div class="col-xxl-2 col-xl-2 col-lg-6 col-md-6">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex align-items-top">
                                <div class="flex-fill">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between fs-14 mb-2">
                                        <span class="flex-fill">{{ $todayitemstatus }}</span>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                                        <h5 class="fw-semibold mb-0">{{ $todayitemcount ?? 0 }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             @endforeach
        @endif
        <div class="col-xxl-2 col-xl-2 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-top">
                        <div class="flex-fill">
                            <div class="d-flex flex-wrap align-items-center justify-content-between fs-14 mb-2">
                                <span class="flex-fill">Total Revenue</span>
                            </div>
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <h5 class="fw-semibold mb-0">RS. {{ $totalTodayRevenue ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                        @can('view_order')
                            <a href="{{ route('admin-orders.export-orders') }}" class="btn btn-success"
                                style="margin-right: 10px;">
                                Export
                            </a>
                        @endcan
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
                                                {{ $value['name'] ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label>Order Number</label>
                                    <input type="text" class="form-control" name="order_number"
                                        placeholder="Order Number" value="{{ old('order_number',$searchVariable['order_number'] ?? request()->order_number) }}" >
                                </div>
                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label for="date_from" class="form-label"><span class="text-danger">* </span>Date
                                        From</label>
                                    <input type="date" class="form-control @error('date_from') is-invalid @enderror"
                                        id="date_from"  value="{{ old('date_from',request()->date_from) }}" name="date_from" placeholder="Date From">

                                </div>
                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label for="date_to" class="form-label"><span class="text-danger">* </span>Date
                                        To</label>
                                    <input type="date" class="form-control @error('date_to') is-invalid @enderror"
                                        id="date_to" name="date_to" value="{{ old('date_to',request()->date_to) }}" placeholder="Date To">

                                </div>

                                <div class="row mt-8">
                                    <div class="col-lg-12">
                                        <button class="btn btn-primary btn-primary--icon" id="kt_search_btn1">
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

                            </form>
                            
                            <hr>
                        </div>
                    </div>
                </div>
                <form id="bulkPrintForm">
                    <div class="container1 mt-4">
                        <div class="row1">
                            <div class="m-2">
                                <button type="button" class="btn btn-outline-primary my-1 me-2" fdprocessedid="g9dg58f">
                                    Total Orders:
                                    <span class="badge ms-2 totalDataCount">{{ $totalResults }}</span>
                                </button>
                            </div>
                            <div class="col-2 mt-4">
                                <label for="selectOptions" class="form-label">Select All:</label>
                                <input type="checkbox" id="selectAll">
                            </div>
                            <div class="col-2 mt-2">
                                <button id="bulk-print" class="btn btn-outline-primary"><i class="bi bi-printer"></i>Bulk Print</button>
                            </div> 
                            
                        </div>
                    </div><br>

                    <table id="datatable-basic" data-sorting="" data-order="" class="table table-bordered text-nowrap"
                        style="width:100%">
                        <thead>
                            <tr id="tableHeaders">
                                <th class="sortable" data-column="order_number">Order Number <i class="sort-icon ri-sort-asc"></i></th>
                                <th class="sortable" data-column="name">Customer<i class="sort-icon ri-sort-asc"></i></th>
                                <th class="sortable" data-column="total">Total Amount <i class="sort-icon ri-sort-asc"></i></th>
                                <th class="sortable" data-column="total">Payment Status <i  class="sort-icon ri-sort-asc"></i></th>
                                <th class="sortable" data-column="name">Items<i class="sort-icon ri-sort-asc"></i></th>
                                <th class="sortable" data-column="payment_method">Payment Method <i  class="sort-icon ri-sort-asc"></i></th>
                                <th class="sortable" data-column="status">Order Status <i class="sort-icon ri-sort-asc"></i></th>
                                <th class="sortable" data-column="created_at">Created On <i class="sort-icon ri-sort-asc"></i></th> 
                                <th class="sortable" data-column="created_at">Order Action <i class="sort-icon ri-sort-asc"></i></th>
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
                        <!-- <div class="my-3" style="display: flex; justify-content: center;">
                                        <button class="btn btn-primary-light btn-border-down" fdprocessedid="l5zhli" id="load-more"
                                            data-offset="{{ Config('Reading.records_per_page') }}" data-default-offset="0"
                                            data-limit="{{ Config('Reading.records_per_page') }}"
                                            data-default-limit="{{ Config('Reading.records_per_page') }}">
                                            <span class="loadMoreText me-2">Load More</span>
                                            <span class="loading"><i class="ri-refresh-line fs-16"></i></span>
                                        </button>
                                    </div> -->
                    @else
                        <!-- <div class="my-3" style="display: flex; justify-content: center;">
                                        <button class="btn btn-primary-light btn-border-down" style="display:none;"
                                            fdprocessedid="l5zhli" id="load-more"
                                            data-offset="{{ Config('Reading.records_per_page') }}" data-default-offset="0"
                                            data-limit="{{ Config('Reading.records_per_page') }}"
                                            data-default-limit="{{ Config('Reading.records_per_page') }}">
                                            <span class="loadMoreText me-2">Load More</span>
                                            <span class="loading"><i class="ri-refresh-line fs-16"></i></span>
                                        </button>
                                    </div> -->
                    @endif
                    @if ($results->isNotEmpty())
                        <div class="my-3 mx-3">
                            {{ $results->links('pagination::bootstrap-5') }}
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

            $('.basicstatus, .exchangedstatus, .shippedstatus').change(function() {
                var status = $(this).val();
                var id = $(this).data('order');
                var url = "{{ route('admin-orders.change-status') }}";
                var inputHtml = '';

                // Check if the selected status is "shipped"
                if ($(this).hasClass('shippedstatus') && status === 'shipped') {
                    // For shipped status, include all fields
                    inputHtml =
                        `
            <input id="delivery_partner_name" placeholder="Delivery Partner Name" style="width:100%; margin-top:10px;" class="form-control" />
            <input id="awb_number" placeholder="AWB No." style="width:100%; margin-top:10px;" class="form-control" />
            <input id="tracking_url" placeholder="Tracking URL" style="width:100%; margin-top:10px;" class="form-control" />
            <textarea id="remark" placeholder="Type your remark here..." style="width:100%; height:100px;" class="form-control"></textarea>`;
                } else {
                    // For other statuses, include only the remark field
                    inputHtml =
                        `
            <textarea id="remark" placeholder="Type your remark here..." style="width:100%; height:100px;" class="form-control"></textarea>`;
                }

                // Status change confirmation
                Swal.fire({
                    title: "Are you sure?",
                    text: "Want to change this status?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, change it",
                    cancelButtonText: "No, cancel",
                    reverseButtons: true
                }).then(function(result) {
                    if (result.value) {
                        // Popup with relevant input fields
                        Swal.fire({
                            title: 'Enter your details',
                            html: inputHtml,
                            showCancelButton: true,
                            confirmButtonText: 'Submit',
                            preConfirm: () => {
                                // Access the values after the popup is displayed
                                return {
                                    remark: $('#remark').val(),
                                    awb_number: (status === 'shipped') ? $(
                                        '#awb_number').val() : null,
                                    tracking_url: (status === 'shipped') ? $(
                                        '#tracking_url').val() : null,
                                    delivery_partner_name: (status === 'shipped') ? $(
                                        '#delivery_partner_name').val() : null
                                };
                            }
                        }).then(function(remarkResult) {
                            // Ajax call for status update
                            if (remarkResult.isConfirmed) {
                                $.ajax({
                                    type: "POST",
                                    url: url,
                                    data: {
                                        id: id,
                                        status: status,
                                        remark: remarkResult.value.remark || null,
                                        awb_number: remarkResult.value.awb_number,
                                        tracking_url: remarkResult.value
                                            .tracking_url,
                                        delivery_partner_name: remarkResult.value
                                            .delivery_partner_name
                                    },
                                    success: function(response) {
                                        Swal.fire({
                                            icon: response.status,
                                            title: response.message,
                                            showConfirmButton: true,
                                        }).then(() => {
                                            location
                                                .reload(); // Page refresh
                                        });
                                    },
                                    error: function(data) {
                                        console.log('Error:', data);
                                    }
                                });
                            }
                        });
                    } else {
                        Swal.fire("Cancelled", "Your current status remains the same :)", "error");
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
