@extends('admin.layout.master')
@section('content')
    @include('admin.layout.response_message')

    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Coupons</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Coupons</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">
                        
                    </div>
                    <div class="prism-toggle">
                        <a href="javascript:void(0);" class="btn btn-primary dropdown-toggle mr-2" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne6">
                            Search
                        </a>
                        <a href="{{ route('admin-coupons.create') }}" class="btn btn-primary" style="margin-right: 10px;">

                            Add New Coupons
                        </a>
                        <a href="{{ route('admin-admin_users.export-coupon') }}" class="btn btn-success"
                            style="margin-right: 10px;">Export</a>
                    </div>
                </div>
                <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6">
                    <div id="collapseOne6" class="collapse m-3 <?php echo !empty($searchVariable) ? 'show' : ''; ?>" data-parent="#accordionExample6">
                        <div>
                            <form id="listSearchForm1" class="row mb-6">
                                <div class="col-lg-3  mb-lg-5 mb-6">

                                    <label>Status</label>
                                    <select name="is_active" class="form-control select2init"
                                        value="{{ $searchVariable['is_active'] ?? request()->is_active }}">
                                        <option value=""
                                            {{ request()->is_active === null || request()->is_active === '' ? 'selected' : '' }}>
                                            All</option>
                                        <option value="1" {{ request()->is_active == 1 ? 'selected' : '' }}>Activate
                                        </option>
                                        <option value="0"
                                            {{ request()->is_active !== null && request()->is_active === '0' ? 'selected' : '' }}>
                                            Deactivate</option>
                                    </select>
                                </div>
                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label>Name</label>
                                    <input type="text" class="form-control" name="name" placeholder=" Name"
                                        value="{{ $searchVariable['name'] ?? request()->name }}">
                                </div>
                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label for="date_from" class="form-label"><span class="text-danger">* </span>Date
                                        From</label>
                                    <input type="date" class="form-control @error('date_from') is-invalid @enderror"
                                        id="date_from" name="date_from" placeholder="Date From"
                                        value="{{ request()->date_from }}">

                                </div>
                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label for="date_to" class="form-label"><span class="text-danger">* </span>Date
                                        To</label>
                                    <input type="date" class="form-control @error('date_to') is-invalid @enderror"
                                        id="date_to" name="date_to" placeholder="Date To"
                                        value="{{ request()->date_to }}">

                                </div>


                                <div class="row mt-8">
                                    <div class="col-lg-12">
                                        <button type="submit" class="btn btn-primary btn-primary--icon" id="kt_search_btn">
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

                <div class="container mt-4">
                    <button type="button" class="btn btn-outline-primary my-1 me-2" fdprocessedid="g9dg58f"> Total Coupons:
                        <span class="badge ms-2 totalDataCount">{{ $totalResults }}</span> </button>

                </div>
                <table id="datatable-basic" data-sorting="" data-order="" class="table table-bordered text-nowrap"
                    style="width:100%">
                    <thead>
                        <tr id="tableHeaders">
                            <th class="sortable" data-column="name">Name <i class="sort-icon ri-sort-asc"></i></th>
                            <th class="sortable" data-column="coupon_type">Coupon Code <i class="sort-icon ri-sort-asc"></i>
                            </th>
                            <th class="sortable" data-column="amount">Discount <i class="sort-icon ri-sort-asc"></i></th>

                            <th class="sortable" data-column="available_coupons">Total Available <i
                                    class="sort-icon ri-sort-asc"></i></th>
                            <th class="sortable" data-column="per_user_avalibity">Per User Available Coupon<i
                                    class="sort-icon ri-sort-asc"></i></th>
                            <th class="sortable" data-column="user_type">User type<i class="sort-icon ri-sort-asc"></i>
                            </th>

                            <th class="sortable" data-column="end_date">Date <i class="sort-icon ri-sort-asc"></i></th>
                            <th class="sortable" data-column="is_active">Status <i class="sort-icon ri-sort-asc"></i>
                            </th>
                            <th class="sortable" data-column="show_on_detail">Show on detail<i
                                    class="sort-icon ri-sort-asc"></i>
                            </th>
                            <th>Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- <tr id="loader-row" style="display: none;">
                                <td colspan="8" style="text-align: center;">
                                    <button class="btn btn-light" type="button" disabled="">
                                        <span class="spinner-grow spinner-grow-sm align-middle" role="status"
                                            aria-hidden="true"></span> Loading...
                                    </button>
                                </td>
                            </tr> -->
                        @if ($results->isNotEmpty())
                            @include('admin.coupons.load_more_data', ['results' => $results])
                        @else
                            <tr>
                                <td colspan="8" style="text-align: center;">No results found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                @if (0 && $results->isNotEmpty() && $totalResults > Config('Reading.records_per_page'))
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
                            fdprocessedid="l5zhli" id="load-more" data-offset="{{ Config('Reading.records_per_page') }}"
                            data-default-offset="0" data-limit="{{ Config('Reading.records_per_page') }}"
                            data-default-limit="{{ Config('Reading.records_per_page') }}">
                            <span class="loadMoreText me-2">Load More</span>
                            <span class="loading"><i class="ri-refresh-line fs-16"></i></span>
                        </button>
                    </div>
                @endif


                @if ($results->isNotEmpty())
                    <div class="my-3 mx-3">
                        {{ $results->links('pagination::bootstrap-5') }}
                    </div>
                @endif

            </div>
        </div>
    </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="couponUsesModal" tabindex="-1" aria-labelledby="couponUsesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="couponUsesModalLabel">Coupon Uses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="couponUsesModalBody">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Container for the toggle */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 25px;
        }

        /* Hide default checkbox */
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        /* The slider */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 34px;
        }

        .slider::before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 9px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        /* Toggle ON */
        input:checked+.slider {
            background-color: #0d6efd;
            /* Bootstrap primary color */
        }

        input:checked+.slider::before {
            transform: translateX(26px);
        }

        /* Optional: Focus effect */
        input:focus+.slider {
            box-shadow: 0 0 1px #0d6efd;
        }

        /* Responsive text label */
        .toggle-label {
            margin-left: 10px;
            font-weight: 500;
            vertical-align: middle;
        }
    </style>
@endsection

@push('scripts')
    <!-- Datatables Cdn -->
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajans/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttox/libs/pdfmake/0.2.6/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script>
        var routeName = '{{ route($listRouteName) }}';
    </script>
    <script src="{{ asset('assets/js/datatables.js') }}"></script>

    <!-- Sweetalerts JS -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>


    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>


    <script>
        $(function() {
            $(document).on('change', '.showDetailPageToggleInput', function() {
                let status = $(this).prop('checked') ? 1 : 0;
                $('.showDetailPageToggleInput').prop('checked', false);
                $(this).prop('checked', status);
                $('.showDetailPageToggleLabel').text('No');
                let id = $(this).data('id');
                parent = $(this).parents('.showDetailPageToggle');
                if (status) {
                    parent.find('.toggle-label').text('Yes');
                } else {
                    parent.find('.toggle-label').text('No');
                }
                $.ajax({
                    url: "{{ route('admin-coupons.updateDetailPageDisplayStatus') }}",
                    method: 'post',
                    dataType: 'json',
                    data: {
                        id: id,
                        status: status
                    },
                    success: function(resp) {
                        if (resp.status == 'error') {
                            Swal.fire('Error', resp.message, 'error')
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Something went wrong!', 'error');
                    }
                });

            });

            $(document).on('click', '.couponUses', function() {
                let cid = $(this).data('cid');

                $.ajax({
                    url: "{{ route('admin-coupons.couponUses') }}",
                    method: 'post',
                    dataType: 'json',
                    data: {
                        id: cid
                    },
                    success: function(resp) {
                        if (resp.status == 'success') {
                            $('#couponUsesModalBody').html(resp.html);
                            $('#couponUsesModal').modal('show');
                        } else {
                            Swal.fire('Error', resp.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Something went wrong!', 'error');
                    }
                });

            });
        });
    </script>
@endpush
