@extends('admin.layout.master')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #4CAF50;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }
    </style>
@endpush

@section('content')
    @include('admin.layout.response_message')

    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Banners</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Banners</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">
                        Banners
                    </div>
                    <div class="prism-toggle">
                        <a href="javascript:void(0);" class="btn btn-primary dropdown-toggle mr-2" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne6">
                            Search
                        </a>
                        <a href="{{ route('admin-Banner.create') }}" class="btn btn-primary">Add Banner</a>
                    </div>
                </div>

                <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6">
                    <div id="collapseOne6" class="collapse m-3 <?php echo !empty($searchVariable) ? 'show' : ''; ?>" data-parent="#accordionExample6">
                        <div>
                            <form id="listSearchForm" class="row mb-6">
                                <div class="col-lg-3  mb-lg-5 mb-6">

                                    <label>Status</label>
                                    <select name="is_active" class="form-control select2init"
                                        value="{{ $searchVariable['is_active'] ?? '' }}">
                                        <option value="">All</option>
                                        <option value="1">Activate</option>
                                        <option value="0">Deactivate</option>
                                    </select>
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

                <div class="container mt-4">
                    <button type="button" class="btn btn-outline-primary my-1 me-2" fdprocessedid="g9dg58f"> Total Records:
                        <span class="badge ms-2 totalDataCount">{{ $totalResults }}</span> </button>

                </div>


                <table id="datatable-basic" data-sorting="" data-order="" class="table table-bordered text-nowrap"
                    style="width:100%">
                    <thead>
                        <tr id="tableHeaders">
                            <th>Image/Video </th>

                            <th>Type </th>
                            <th>Title </th>
                            <th>Show on Home Page Slider </th>
                            <th>Show on Home Page Banner </th>
                            <th>Show on Home Page Offer Banner </th>
                            <th>Shop Button </th>
                            <th>Video Auto Play </th>
                            <th class="sortable" data-column="created_at">Added On <i class="sort-icon ri-sort-asc"></i>
                            </th>
                            <th class="sortable" data-column="is_active">Status <i class="sort-icon ri-sort-asc"></i>
                            </th>
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
                            @include('admin.Banner.load_more_data', ['results' => $results])
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
                            fdprocessedid="l5zhli" id="load-more" data-offset="{{ Config('Reading.records_per_page') }}"
                            data-default-offset="0" data-limit="{{ Config('Reading.records_per_page') }}"
                            data-default-limit="{{ Config('Reading.records_per_page') }}">
                            <span class="loadMoreText me-2">Load More</span>
                            <span class="loading"><i class="ri-refresh-line fs-16"></i></span>
                        </button>
                    </div>
                @endif
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

    <script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>

    <!-- Internal Datatables JS -->
    <script src="{{ asset('assets/js/datatables.js') }}"></script>

    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>
    <script>
        $(document).on('change', '.toggle-status', function() {
            var id = $(this).data('id');
            var field = $(this).data('field');
            var value = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: "{{ route('admin-Banner.update.toggle.status') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    field: field,
                    value: value
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Updated successfully');
                    } else {
                        alert('Something went wrong');
                    }
                },
                error: function() {
                    alert('Server error, please try again later.');
                }
            });
        });
    </script>
@endpush
