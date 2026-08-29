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
    <h1 class="page-title fw-semibold fs-18 mb-0">Footer Subcategories</h1>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{  route('admin-footer-category.index')}}">Footer Categories</a></li>
                <li class="breadcrumb-item active" aria-current="page">Footer Subcategories</li>
            </ol>
        </nav>
    </div>
</div>
<div class="mb-2">
    <a class="btn btn-dark" href="{{  route('admin-footer-category.index') }}">Back</a>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="d-flex">
                    <div class="card-title">
                        Footer Subcategories
                    </div>
                    <!-- <a href="{{ route('admin-footer-sub-category.priority.manage', base64_encode($dep_id)) }}" class="btn btn-secondary" style="margin-left: 10px;">
                        Manage Priority
                    </a> -->
                </div>
                <div class="prism-toggle">
                    <a href="{{route('admin-'.$model.'.add',base64_encode($dep_id))}}" class="btn btn-primary mb-3">Add
                        New Footer Subcategory</a>
                </div>
            </div>
            <div class="card-body">
                <table id="datatable-basic" data-sorting="" data-order="" class="table table-bordered text-nowrap"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center sortable" data-column="name">Title</th>
                            <!-- <th class="text-center">Order Number</th> -->
                            <th class="text-center sortable" data-column="name">Date</th>
                            <th class="text-center sortable" data-column="name">Slug</th>
                            <th class="text-center sortable" data-column="name">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="powerwidgetsFooterSubCategory" class="sortableCategoriesFooterSubCategory" style="text-align: center;">
                        <tr id="loader-row" style="display: none;">
                            <td colspan="5" style="text-align: center;">
                                <button class="btn btn-light" type="button" disabled="">
                                    <span class="spinner-grow spinner-grow-sm align-middle" role="status"
                                        aria-hidden="true"></span> Loading...
                                </button>
                            </td>
                        </tr>
                        @if(!$results->isEmpty())
                        @foreach($results as $result)
                        <tr class="list-data-row items-inner" data-total-count="{{ $totalResults }}" data-id = "{{ $result->id }}">
                        <td>{{ $result->title ?? "N/A" }}</td>
                        <!-- <td>{{ $result->order_number ?? "N/A" }}</td> -->
                        <td>{{ date("Y-m-d",strtotime($result->created_at)) }}</td>
                        <td>{{ $result->slug }}</td>
                        <td>
                            @if($result->is_active == 1)
                            <span class="badge bg-success">Activated</span>
                            @else
                            <span class="badge bg-danger">Deactivated</span>
                            @endif
                        </td>
                        <td>
                        <div class="hstack gap-2 flex-wrap text-center">
                            @if($result->is_active == 1)
                            
                            <a title="Click To Deactivate"
                                href='{{route("admin-".$model.".status",array($result->id,1))}}'
                                class="btn btn-danger" id="deactivate-button"
                                ><i class="ri-close-line"></i>
                                
                            </a>
                            @else
                            <a title="Click To Activate"
                                href='{{route("admin-".$model.".status",array($result->id,0))}}'
                                class="btn btn-success" id="activate-button"
                                ><i class="ri-check-line"></i>
                                
                            </a>
                            @endif

                            
                                <a href="{{route('admin-'.$model.'.edit',base64_encode($result->id))}}"
                                    class="btn btn-info"><i class="ri-edit-line"></i></a>
                                <form method="POST"
                                    action="{{route('admin-'.$model.'.delete',base64_encode($result->id))}}">
                                    @csrf
                                    <input name="_method" type="hidden" value="GET">
                                    <button type="submit" class="btn btn-danger" id="confirm-button"><i
                                            class="ri-delete-bin-5-line"></i></button>
                                </form>
                          

                           
                            </div>
                        </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="6" style="text-align:center;"> {{ trans("Record not found.") }}</td>
                        </tr>
                        @endif

                    </tbody>
                </table>
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
<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js" defer="defer"></script>

<!-- Internal Datatables JS -->
<script src="{{ asset('assets/js/datatables.js') }}"></script>

<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script type="text/javascript">
    new Sortable(powerwidgetsFooterSubCategory, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: function(evt) {
            var counter = 1;
            var requestData = [];
            $(".items-inner").each(function() {
                requestData.push({
                    "id": $(this).attr("data-id"),
                    "order": counter
                });
                counter++;
            });

            $.ajax({
                url: "{{  route('admin-footer-subcategory.updateSubFooterCategoryOrderLogic') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "requestData": requestData,
                },
                success: function(response) {
                    Swal.fire({
                        title: "Success",
                        text: "Order updated successfully!",
                        icon: "success"
                    });
                }
            });
        },
    });
</script>

<script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>

<script>
        // $('#prioritySaveBtn').on('click', function(e) {
        //     e.preventDefault();
        //     let order = [];

        //     $('.sortableCategoriesFooterSubCategory tr').each(function() {
        //         order.push($(this).data('id'));
        //     });
        //     const APP_URL = "{{ config('app.url') }}";
        //     // Optional: send via AJAX

        //     console.log(order);
        //     return false;
        //     $.ajax({
        //         url: APP_URL + "category/update-priority",
        //         method: 'POST',
        //         data: {
        //             _token: $('meta[name="csrf-token"]').attr('content'),
        //             order: order
        //         },
        //         success: function(response) {
        //             if (response.status == 'success') {
        //                 alert(response.message);
        //             } else {
        //                 alert(response.message || 'Something went wrong');
        //             }
        //         }
        //     });
        // });
    </script>

    <script>
        // $(document).on('change', '.toggle-status', function() {
        //     var id = $(this).data('id');
        //     var field = $(this).data('field');
        //     var value = $(this).is(':checked') ? 1 : 0;
        //     $.ajax({
        //         url: "{{ route('admin-update.category.status') }}",
        //         type: "POST",
        //         data: {
        //             _token: "{{ csrf_token() }}",
        //             id: id,
        //             field: field,
        //             value: value
        //         },
        //         success: function(response) {
        //             if (response.success) {
        //                 console.log('Updated successfully');
        //             } else {
        //                 alert('Something went wrong');
        //             }
        //         },
        //         error: function() {
        //             alert('Server error, please try again later.');
        //         }
        //     });
        // });
    </script>

@endpush

