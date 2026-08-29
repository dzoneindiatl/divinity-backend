@extends('admin.layout.master')

@push('styles')
<link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
@endpush
@section('content')
@include('admin.layout.response_message')
<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Review</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->

<div class="row">
    <div class="col-xl-12">
        <form action="{{route('admin-admin_users-refundedapprovalupdate',['refundId' => base64_encode($refund->id), 'userId' => base64_encode($refund->user_id)])}}" method="post"  enctype="multipart/form-data">
            @csrf
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Edit Refund
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">                       
                        <div class="col-xl-12 mb-3 oldSlug">
                            <label for="category" class="form-label">Refund Amount<span class="text-danger">*
                            </span></label>
                            <input type="text" class="form-control" name="amount" value="{{ $refund->amount }}" required>
                        </div>  
                        <div class="col-xl-6 mb-3 oldSlug">
                            <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div> 
                    </div>
                </div>           

           
        </form>
    </div>
</div>


@endsection
@push('scripts')
<!-- Select2 Cdn -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Internal Select-2.js -->
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>
<script src="{{ asset('assets/js/form-validation.js') }}"></script>
<script src="{{ asset('assets/plugin/tagify/tagify.min.js') }}"></script>
<script src="{{ asset('assets/js/custom/category.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Add an event listener to the taxesSelect dropdown
        $('#taxesSelect').on('change', function () {

            var selectedTaxes = $(this).val();
            $('.taxContainers').hide();
            $('.taxContainers').find('input').prop('disabled',true);
            // Check if any taxes are selected
            if (selectedTaxes && selectedTaxes.length > 0) {

                // Create a text field for each selected tax
                selectedTaxes.forEach(function (taxId) {
                   $('.taxDiv'+taxId).show();
                   $('.taxDiv'+taxId).find('input').prop('disabled',false);
                });
            } else {
                // Hide the tax count fields div if no taxes are selected
                $('#taxCountFields').hide();
            }
        });

    });
</script>

<script>
    CKEDITOR.replace(<?php echo 'description'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;
        CKEDITOR.replace('seo_data', {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;
</script>

@endpush