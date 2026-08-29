@extends('admin.layout.master')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
<style>
    #editModal .modal-dialog {
    max-width: 800px;
}
</style>
@endpush

@section('content')

@include('admin.layout.response_message')

<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <h1 class="page-title fw-semibold fs-18 mb-0">Contact Enquiry</h1>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact Enquiry</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    Contact Enquiry
                </div>
             
            </div>
            <div class="card-body">
               <table id="datatable-basic" class="table table-bordered text-nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Subject</th>
                       
                        <th>Message</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contactEnquiries as $index => $subscribes)
                        <tr data-id="{{ $subscribes->id }}">
                            <td>{{ $contactEnquiries->firstItem() + $index }}</td>
                            <td class="name-text">{{ $subscribes->name ?? "N/A" }}</td>
                            <td class="phone-text">{{ $subscribes->phone ?? "N/A" }}</td>
                            <td class="email-text">{{ $subscribes->email ?? "N/A" }}</td>
                            <td class="subject-text">{{ $subscribes->subject ?? "N/A" }}</td>
                            
                            <td class="message-text">{{ $subscribes->message ?? "N/A" }}</td>
                            <td>{{ $subscribes->created_at ? $subscribes->created_at->format('d M Y') : "N/A" }}</td>
                            <td>
                                <!--<button class="btn btn-info edit-btn">-->
                                <!--    <i class="ri-edit-line"></i>-->
                                <!--</button>-->
                                <button class="btn btn-danger delete-btn">
                                    <i class="ri-delete-bin-5-line"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
                
            </table>
            {{ $contactEnquiries->links() }}
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
          <h5 class="modal-title">Edit Contact Enquiry</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3 edit_pop_sec row">
              <div class="col-md-6 edit_pop_sec1">
            <label>Name</label>
            <input type="text" class="form-control" name="name" id="edit_name" required>
            </div>
            <div class="col-md-6 edit_pop_sec1">
             <label>Phone Number</label>
            <input type="text" class="form-control" name="phone" id="edit_phone" required>
            </div>
            <div class="col-md-6 edit_pop_sec1">
             <label>Email</label>
            <input type="email" class="form-control" name="email" id="edit_email" required>
            </div>
            <div class="col-md-6 edit_pop_sec1">
             <label>Subject</label>
            <input type="text" class="form-control" name="subject" id="edit_subject" required>
            </div>
          </div>
           <div class="mb-3">
            <label for="message" class="form-label">Message *</label>
            <textarea name="message" id="edit_message" rows="4" class="form-control" required></textarea>
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
$(document).ready(function () {

    // Edit Button Click
    $('.edit-btn').on('click', function () {
        let row = $(this).closest('tr');
        let id = row.data('id');
        let name = row.find('.name-text').text();
        let phone = row.find('.phone-text').text();
        let email = row.find('.email-text').text();
        let subject = row.find('.subject-text').text();
        let message = row.find('.message-text').text();
        

        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_phone').val(phone);
        $('#edit_email').val(email);
        $('#edit_subject').val(subject);
        $('#edit_message').val(message);
        

        $('#editModal').modal('show');
    });

    // Update Form Submit
    $('#editForm').on('submit', function (e) {
        e.preventDefault();

        let id = $('#edit_id').val();
        let formData = $(this).serialize();

        $.ajax({
            url: "{{ url('/admin/contact-enquiries/update') }}/" + id,
            type: "POST",
            data: formData,
           success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                
                    $('#editModal').modal('hide');
                
                    // update row in table (all fields)
                    let row = $('tr[data-id="' + id + '"]');
                    row.find('.name-text').text($('#edit_name').val());
                    row.find('.phone-text').text($('#edit_phone').val());
                    row.find('.email-text').text($('#edit_email').val());
                    row.find('.subject-text').text($('#edit_subject').val());
                    row.find('.message-text').text($('#edit_message').val());
                },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let error = xhr.responseJSON.errors.email[0];
                    Swal.fire({ icon: 'error', title: 'Validation Error', text: error });
                }
            }
        });
    });

    // Delete Button Click
    $('.delete-btn').on('click', function () {
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
                    url: "{{ url('/admin/contact-enquiries/delete') }}/" + id,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (response) {
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