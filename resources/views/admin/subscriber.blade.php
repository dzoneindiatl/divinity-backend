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
        <h1 class="page-title fw-semibold fs-18 mb-0">Subscriber</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Subscriber</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">
                        Subscriber
                    </div>
                    <a href="javascript:void(0);" class="btn btn-primary dropdown-toggle mr-2" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne6">
                        Search
                    </a>
                    <a href="{{ route('admin-admin_users.export-subscriber') }}" class="btn btn-success"
                        style="margin-right: 10px;">Export</a>

                </div>
                 <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6">
                    <div id="collapseOne6" class="collapse m-3 {{ request('show') ? 'show' : '' }} ?>"
                        data-parent="#accordionExample6">
                        <div>
                            <!-- <form id="listSearchForm" class="row mb-6"> -->
                            <form id="listSearchForm" class="row mb-6" method="GET"
                                action="{{ route('admin-subscribe') }}">

                                <input type="hidden" name="show" value="show">

                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label>Email</label>
                                    <input type="text" class="form-control" name="email" placeholder="Email"
                                        value="{{ request('email') ?? '' }}">
                                </div>
                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label for="date_from" class="form-label">Date
                                        From</label>
                                    <input type="date" class="form-control" value="{{ request('date_from') }}"
                                        id="date_from" name="date_from" placeholder="Date From">
                                </div>
                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label for="date_to" class="form-label">Date
                                        To</label>
                                    <input type="date" class="form-control" value="{{ request('date_to') }}"
                                        id="date_to" name="date_to" placeholder="Date To">
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
                                        <a href='{{ route('admin-subscribe') }}'
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
                <div class="card-body">
                    <table id="datatable-basic" class="table table-bordered text-nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscribe as $index => $subscribes)
                                <tr data-id="{{ $subscribes->id }}">
                                    <td>{{ $subscribe->firstItem() + $index }}</td>
                                    <td class="email-text">{{ $subscribes->email ?? 'N/A' }}</td>
                                    <td class="email-text">{{ $subscribes->mobile ?? 'N/A' }}</td>
                                    <td>{{ $subscribes->created_at ? $subscribes->created_at->format('d M Y') : 'N/A' }}
                                    </td>
                                    <td>{{ $subscribes->created_at->format('h:i:s A') }}
                                    </td>
                                    <td>
                                        <button class="btn btn-info edit-btn">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button class="btn btn-danger delete-btn">
                                            <i class="ri-delete-bin-5-line"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>

                    </table>
                    {{ $subscribe->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editForm">
                    @csrf
                    <input type="hidden" name="id" id="edit_id">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Subscriber</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
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

    <!-- Internal Datatables JS -->
    <script src="{{ asset('assets/js/datatables.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {

            // Edit Button Click
            $('.edit-btn').on('click', function() {
                let row = $(this).closest('tr');
                let id = row.data('id');
                let email = row.find('.email-text').text();

                $('#edit_id').val(id);
                $('#edit_email').val(email);

                $('#editModal').modal('show');
            });

            // Update Form Submit
            $('#editForm').on('submit', function(e) {
                e.preventDefault();

                let id = $('#edit_id').val();
                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ url('/subscribes/update') }}/" + id,
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        $('#editModal').modal('hide');

                        // update row in table
                        $('tr[data-id="' + id + '"]').find('.email-text').text($('#edit_email')
                            .val());
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let error = xhr.responseJSON.errors.email[0];
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: error
                            });
                        }
                    }
                });
            });

            // Delete Button Click
            $('.delete-btn').on('click', function() {
                let row = $(this).closest('tr');
                let id = row.data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will delete the subscriber!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('/subscribes/delete') }}/" + id,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                row.remove();
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush
