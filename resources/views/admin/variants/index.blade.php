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
    <h1 class="page-title fw-semibold fs-18 mb-0">Variants</h1>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Variants</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    Variants
                </div>
                <div class="prism-toggle">
                    <a href="javascript:void(0);" class="btn btn-primary dropdown-toggle mr-2" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne6">
                        Search
                    </a>
                    @can('create_variant')
                    <a href="{{ route('admin-variants.create') }}" class="btn btn-primary"
                        style="margin-right: 10px;">
                        <!-- Adjust the margin-right as needed -->
                        Add New Variant
                    </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
            <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6">
                <div id="collapseOne6" class="collapse m-3 {{ request('show')? 'show' : ''  }}" data-parent="#accordionExample6">
                    <div>
                        <form id="listSearchForm" class="row mb-6" method="GET" action="{{ route('admin-variants.index') }}">
                            <div class="col-lg-3  mb-lg-5 mb-6">
                                <label>Status</label>
                                <select name="is_active" class="form-control select2init">
                                    <option value="" {{ (request('is_active') ?? '') == '' ? 'selected' : '' }}>All</option>
                                    <option value="1" {{ (request('is_active') ?? '') == '1' ? 'selected' : '' }}>Activate</option>
                                    <option value="0" {{ (request('is_active') ?? '') == '0' ? 'selected' : '' }}>Deactivate</option>
                                </select>
                            </div>

                            <input type="hidden"  name="show" value="show">

                            <div class="col-lg-3 mb-lg-5 mb-6">
                                <label>Name</label>
                                <input type="text" class="form-control" name="name" placeholder=" Name"
                                    value="{{request('name') ?? '' }}">
                            </div>
                           
                            <div class="col-lg-3 mb-lg-5 mb-6">
                                <label for="date_from" class="form-label"><span class="text-danger">* </span>Date
                                    From</label>
                                <input type="date" class="form-control" value="{{ request('date_from') }}" id="date_from" name="date_from" placeholder="Date From">
                            </div>
                            <div class="col-lg-3 mb-lg-5 mb-6">
                                <label for="date_to" class="form-label"><span class="text-danger">* </span>Date
                                    To</label>
                                <input type="date" class="form-control" value="{{ request('date_to') }}" id="date_to" name="date_to" placeholder="Date To">
                            </div>

                            <div class="row mt-8">
                                <div class="col-lg-12">
                                    <button class="btn btn-primary btn-primary--icon" id="kt_search_btn">
                                        <span>
                                            <i class="la la-search"></i>
                                            <span>Search</span>
                                        </span>
                                    </button>
                                    &nbsp;&nbsp;
                                    <a href='{{ route("admin-"."$model.index")}}'
                                        class="btn btn-secondary btn-secondary--icon">
                                        <span>
                                            <i class="la la-close"></i>
                                            <span>Clear Search</span>
                                        </span>
                                    </a>
                                </div>
                        </div>
                        <hr>
                        </form>
                        
                    </div>
                </div>
            </div>
            <div class="container mt-4">
                <button type="button" class="btn btn-outline-primary my-1 me-2" fdprocessedid="g9dg58f"> Total Variants:
                    <span class="badge ms-2 totalDataCount">{{ $totalResults }}</span> </button>

            </div>
                <table id="datatable-basic" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th class="sortable" data-column="name" class="text-center">Name <i class="sort-icon ri-sort-asc"></i></th>
                            <th class="sortable" data-column="name" class="text-center">Variant Design Type <i
                                    class=""></i></th>
                            <th class="sortable" data-column="created_at" class="text-center">Added On <i
                                    class="sort-icon ri-sort-asc"></i></th>
                            <th class="sortable" data-column="is_active" class="text-center">Status <i
                                    class="sort-icon ri-sort-asc"></i></th>
                            <th class="text-center">Action</th>
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
                        @if($results->isNotEmpty())
                        @include('admin.variants.load_more_data', ['results' => $results])
                        @else
                        <tr>
                            <td colspan="7" style="text-align: center;">No results found.</td>
                        </tr>
                        @endif

                    </tbody>
                </table>

                @if($results->isNotEmpty() && $totalResults > Config('Reading.records_per_page'))
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
                    <button class="btn btn-primary-light btn-border-down" style="display:none;" fdprocessedid="l5zhli"
                        id="load-more" data-offset="{{ Config('Reading.records_per_page') }}" data-default-offset="0"
                        data-limit="{{ Config('Reading.records_per_page') }}"
                        data-default-limit="{{ Config('Reading.records_per_page') }}">
                        <span class="loadMoreText me-2">Load More</span>
                        <span class="loading"><i class="ri-refresh-line fs-16"></i></span>
                    </button>
                </div>
                @endif
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

<script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>

<script>
var routeName = '{{route($listRouteName)}}';
// Your DataTables initialization or other JavaScript logic here
</script>

<!-- Internal Datatables JS -->
<script src="{{ asset('assets/js/datatables.js') }}"></script>

<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>
@endpush