@extends('admin.layout.master')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.css') }}">
<link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>

@endpush

@section('content')
<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit referral Setting</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="{{ route('admin-referral_setting.update',base64_encode($setting_edit->id)) }}" method="post" enctype="multipart/form-data" id="createCouponForm">
            @csrf
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Basic Info
                    </div>
                </div>
                <div class="card-body add-products p-0">

                    <div class="p-4">
                        <div class="row gx-5">
                            <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12">
                                <div class="card custom-card shadow-none mb-0 border-0">
                                    <div class="card-body p-0">
                                        <div class="row">
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    <label for="sender_amount">Sender Amount</label><span class="text-danger"> *</span>
                                                    <input type="number" name="sender_amount" class="form-control form-control-solid form-control-lg @error('sender_amount') is-invalid @enderror" value="{{ old('sender_amount', $setting_edit->sender_amount) }}" step="0.01">
                                                    @if ($errors->has('sender_amount'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('sender_amount') }}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    <label for="receiver_amount">Receiver Amount</label><span class="text-danger"> *</span>
                                                    <input type="number" name="receiver_amount" class="form-control form-control-solid form-control-lg @error('receiver_amount') is-invalid @enderror" value="{{ old('receiver_amount', $setting_edit->receiver_amount) }}" step="0.01">
                                                    @if ($errors->has('receiver_amount'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('receiver_amount') }}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    <label for="start_date">Start Date</label><span class="text-danger"> *</span>
                                                    <input type="date" name="start_date" class="form-control form-control-solid form-control-lg @error('start_date') is-invalid @enderror" value="{{ old('start_date', $setting_edit->start_date) }}">
                                                    @if ($errors->has('start_date'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('start_date') }}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    <label for="end_date">End Date</label><span class="text-danger"> *</span>
                                                    <input type="date" name="end_date" class="form-control form-control-solid form-control-lg @error('end_date') is-invalid @enderror" value="{{ old('end_date', $setting_edit->end_date) }}">
                                                    @if ($errors->has('end_date'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('end_date') }}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    <label for="validity">Validity (Days)</label><span class="text-danger"> *</span>
                                                    <input type="number" name="validity" class="form-control form-control-solid form-control-lg @error('validity') is-invalid @enderror" value="{{ old('validity', $setting_edit->validity) }}" step="1">
                                                    @if ($errors->has('validity'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('validity') }}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>


        </form>
    </div>
</div>

@endsection

@push('scripts')
<!-- Select2 Cdn -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/plugin/tagify/tagify.min.js') }}"></script>

<!-- Internal Select-2.js -->
<script src="{{ asset('assets/js/select2.js') }}"></script>

<script src="{{ asset('assets/libs/dropzone/dropzone-min.js') }}"></script>

<script src="{{ asset('assets/js/custom/product.js') }}"></script>
<script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/js/repeater.js')}}"></script>

<script>
    $(document).ready(function () {
    // Initialize form validation
    $("form").validate({
        rules: {
            sender_amount: {
                required: true,
                number: true,
                min: 0
            },
            receiver_amount: {
                required: true,
                number: true,
                min: 0
            },
            start_date: {
                required: true,
                date: true,
                todayOrFuture: true
            },
            end_date: {
                required: true,
                date: true,
                greaterThan: "[name='start_date']"
            },
            validity: {
                required: true,
                number: true,
                min: 1
            }
        },
        messages: {
            sender_amount: {
                required: "Please enter the sender amount.",
                number: "Please enter a valid number.",
                min: "Amount cannot be negative."
            },
            receiver_amount: {
                required: "Please enter the receiver amount.",
                number: "Please enter a valid number.",
                min: "Amount cannot be negative."
            },
            start_date: {
                required: "Please select a start date.",
                date: "Please enter a valid date.",
                todayOrFuture: "Start date must be today or a future date."
            },
            end_date: {
                required: "Please select an end date.",
                date: "Please enter a valid date.",
                greaterThan: "End date must be greater than start date."
            },
            validity: {
                required: "Please enter the validity period.",
                number: "Please enter a valid number.",
                min: "Validity must be at least 1 day."
            }
        },
        errorElement: "div",
        errorPlacement: function (error, element) {
            error.addClass("invalid-feedback");
            if (element.prop("type") === "checkbox") {
                error.insertAfter(element.next("label"));
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function (element) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid");
        }
    });

    // Custom validation method to check if the date is today or in the future
    $.validator.addMethod("todayOrFuture", function (value, element) {
        if (!value) {
            return true; // Ignore empty values
        }
        var inputDate = new Date(value);
        var today = new Date();
        today.setHours(0, 0, 0, 0); // Remove time part for comparison
        return this.optional(element) || inputDate >= today;
    }, "Start date must be today or a future date.");

    // Custom validation method to check if end date is after start date
    $.validator.addMethod("greaterThan", function (value, element, params) {
        if (!value || !$(params).val()) {
            return true; // Ignore empty values
        }
        var endDate = new Date(value);
        var startDate = new Date($(params).val());

        // Ensure both dates are in the same format for comparison
        return this.optional(element) || endDate > startDate;
    }, "End date must be greater than start date.");
});

</script>

@endpush