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
    <h1 class="page-title fw-semibold fs-18 mb-0">Referral Histories</h1>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Referral Histories</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                Referral Histories
                </div>
                <div class="prism-toggle">
                    <a href="javascript:void(0);" class="btn btn-primary dropdown-toggle mr-2" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne6">
                        Search
                    </a>
                    <!-- <button type="button" class="btn btn-primary mr-2" data-bs-toggle="modal" data-bs-target="#exampleModal">Tree View</button> -->
                </div>
            </div>

            <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6">
                <div id="collapseOne6" class="collapse m-3 <?php echo !empty($searchVariable) ? 'show' : ''; ?>"
                    data-parent="#accordionExample6">
                    <div>
                        <form id="listSearchForm" class="row mb-6">

                            <div class="col-lg-3 mb-lg-5 mb-6">
                                <label>Referred By</label>
                                <input type="text" class="form-control" name="by_name" placeholder="Referred By"
                                    value="{{$searchVariable['by_name'] ?? '' }}">
                            </div>
                            <div class="col-lg-3 mb-lg-5 mb-6">
                                <label>Referred To</label>
                                <input type="text" class="form-control" name="to_name" placeholder="Referred To"
                                    value="{{$searchVariable['to_name'] ?? '' }}">
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
                    </div>
                </div>
            </div>

            <div class="container mt-4">
                <button type="button" class="btn btn-outline-primary my-1 me-2" fdprocessedid="g9dg58f"> Total Invited Records:
                    <span class="badge ms-2 totalDataCount">{{ $totalResults }}</span> </button>

            </div>

            <table id="datatable-basic" data-sorting="" data-order="" class="table table-bordered text-nowrap"
                style="width:100%">
                <thead>
                        <th class="sortable" data-column="by_name">S.No.<i class="sort-icon ri-sort-asc"></i></th>
                        <th class="sortable" data-column="by_name">Registration date<i class="sort-icon ri-sort-asc"></i></th>
                        <th class="sortable" data-column="by_name">Referee<i class="sort-icon ri-sort-asc"></i></th>
                        <th class="sortable" data-column="by_name">Referee code<i class="sort-icon ri-sort-asc"></i></th>
                        <th class="sortable" data-column="by_name">Referee amount<i class="sort-icon ri-sort-asc"></i></th>
                        <th class="sortable" data-column="by_name">Wellet balance<i class="sort-icon ri-sort-asc"></i></th>
                        <th class="sortable" data-column="by_name">Referrer code<i class="sort-icon ri-sort-asc"></i></th>
                        <th class="sortable" data-column="by_name">Referrer amount<i class="sort-icon ri-sort-asc"></i></th>
                        <th class="sortable" data-column="by_name">Referrer/Referee<i class="sort-icon ri-sort-asc"></i></th>
                        <!-- <th class="sortable" data-column="by_name">Valid Till<i class="sort-icon ri-sort-asc"></i></th>
                        <th class="sortable" data-column="by_name">Status<i class="sort-icon ri-sort-asc"></i></th> -->
                        <th>Action </th>
                    </tr>
                </thead>
                <tbody id="powerwidgets">
                    <tr id="loader-row" style="display: none;">
                        <td colspan="7" style="text-align: center;">
                            <button class="btn btn-light" type="button" disabled="">
                                <span class="spinner-grow spinner-grow-sm align-middle" role="status"
                                    aria-hidden="true"></span> Loading...
                            </button>
                        </td>
                    </tr>
                    @if($results->isNotEmpty())
                    @include('admin.referral_histories.load_more_data', ['results' => $results])
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

<!-- Search Users Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Select User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 text-center">
                    <h5 class="mb-10 font-weight-bold text-dark text-center">
                    Select the user whose tree view you want to see</h5>

                    <div class="addppl_modal_search">
                        <div class="theme-input1">
                        <div class="input-icon">
                            <input placeholder="Search Users" class="form-control form-control-solid form-control-lg " type="text" name="search_user" id="search_user">
                            <span><i class="flaticon2-search-1 icon-md"></i></span>
                        </div>


                        </div>
                    </div>
                    <div class="invalid-feedback error_msg"></div>
                    <div class="spinner-border text-secondary userSpinner" style="display:none;" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="userData append_div mt-3">
                        {!! $usersHtml !!}
                    </div>


                </div>
            </div>

        </div>
    </div>
</div>


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


<script>
    $('#search_user').on('keyup',function(){
        $value = $(this).val();
        $('.append_div').html('');
        $('.userSpinner').show();
        $.ajax({
            url: '{{route("admin-referral_histories.userSearch")}}',
            type: 'POST',
            data: {
                'value': $value
            }, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(r) {
                if(r.success != 0){

                    $('.append_div').html(r);

                }
                $('.userSpinner').hide();
            }
        });
    });
</script>
@endpush

@stop