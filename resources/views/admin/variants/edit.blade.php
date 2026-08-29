@extends('admin.layout.master')

@push('styles')
<link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" />

<!-- Include Pickr JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>
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
                <li class="breadcrumb-item active" aria-current="page">Edit Variant</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="{{route('admin-'.$model.'.update',base64_encode($recordDetails->id))}}" method="post"
            id="variantForm" autocomplete="off" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Edit Variant
                    </div>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card-body p-0">

                                <div class="mb-3">
                                    <label for="name" class="form-label"><span class="text-danger">* </span>Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Enter Name"
                                        value="{{ $recordDetails->name ?? '' }}">
                                    @if ($errors->has('name'))
                                    <div class=" invalid-feedback">
                                        {{ $errors->first('name') }}
                                    </div>
                                    @endif
                                </div>


                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="product_type" class="form-label"><span class="text-danger">*
                                        </span> Variant Design Type</label>
                                    <select name="product_type" id="product_type"
                                        class="form-control @error('product_type') is-invalid @enderror">
                                        <option value="">Select Design Type</option>
                                        <option value="1" {{$recordDetails->type ==1?'selected':''}}>Only Round</option>
                                        <option value="2" {{$recordDetails->type ==2?'selected':''}}>Only Box</option>
                                        <option value="7" {{$recordDetails->type ==7?'selected':''}}>Only Rectangle</option>
                                        <option value="3" {{$recordDetails->type ==3?'selected':''}}>Round with image</option>
                                        <option value="4" {{$recordDetails->type ==4?'selected':''}}>Round with color</option>
                                        <option value="5" {{$recordDetails->type ==5?'selected':''}}>Box with color</option>
                                        <option value="6" {{$recordDetails->type ==6?'selected':''}}>Box with Images</option>
                                    </select>                                        
                                    @if ($errors->has('product_type'))
                                    <div class=" invalid-feedback">
                                        {{ $errors->first('product_type') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="variantValuesRow card custom-card">

                <div class="card-header">
                    <div class="card-title">
                        Variant Values
                    </div>
                </div>
                <div class="card-body">
                    @if(!empty(($variantValuesData)))
                    <?php $iterationCount = 0; ?>
                    <div id="kt_repeater_1" class="ml-7">
                        <div class="form-group row" id="kt_repeater_1">
                            <div data-repeater-list="dataArr" class="col-lg-12">
                                @foreach($variantValuesData as $dataKey => $dataVal)
                                @if(!empty($dataVal['name']))
                                <div data-repeater-item class="form-group row align-items-center mb-0">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="name">Name</label><span class="text-danger">
                                            </span>
                                            <input type="text" name="name"
                                                class="form-control form-control-solid form-control-lg  @error('name') is-invalid @enderror"
                                                value="{{!empty($dataVal['name']) ? $dataVal['name'] : ''}}" id="variant_{{ $dataVal['id'] }}">

                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-3"
                                        style="display: {{ (!empty($recordDetails->name) && (strtolower($recordDetails->name) == 'color')) ? 'block' : 'none' }}">
                                        <div class="form-group">
                                            <label for="color">Color</label><span class="text-danger">
                                            </span>
                                            <input type="color" name="color_code"
                                                class="form-control form-control-color border-0  @error('color_code') is-invalid @enderror"
                                                value="{{!empty($dataVal['color_code']) ? $dataVal['color_code'] : ''}}">
                                            <input type="hidden" class="colorCodeInputHidden" name="color_code_hidden"
                                                value="{{!empty($dataVal['color_code']) ? $dataVal['color_code'] : '#000000'}}">

                                        </div>
                                    </div>



                                    <div class="col-md-2">
                                        @if($iterationCount == 0)
                                        <a href="javascript:;" data-repeater-create=""
                                            class="btn btn-sm font-weight-bolder btn btn-primary-light btn-border-down">
                                            <i class="la la-plus"></i>Add More Variant Value
                                        </a>
                                        <a href="javascript:;" data-repeater-delete=""
                                            class="btn btn-sm font-weight-bolder btn btn-danger-light btn-border-down"
                                            style="display:none;">
                                            <i class="la la-trash-o"></i>Delete Variant Value
                                        </a>
                                        @else
                                        <a href="javascript:;" data-repeater-create=""
                                            class="btn btn-sm font-weight-bolder btn btn-primary-light btn-border-down"
                                            style="display:none;">
                                            <i class="la la-plus"></i>Add More Variant Value
                                        </a>
                                        <a href="javascript:;" data-repeater-delete=""
                                            class="btn btn-sm font-weight-bolder btn btn-danger-light btn-border-down variantDlt">
                                            <i class="la la-trash-o"></i>Delete Variant Value
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                @endif


                                <?php $iterationCount++; ?>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @else
                    <div id="kt_repeater_1" class="ml-7">
                        <div class="form-group row" id="kt_repeater_1">

                            <div data-repeater-list="dataArr" class="col-lg-12">
                                <div data-repeater-item class="form-group row align-items-center mb-0">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="name">Name</label><span class="text-danger">
                                            </span>
                                            <input type="text" name="name"
                                                class="form-control form-control-solid form-control-lg  @error('name') is-invalid @enderror"
                                                value="">

                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-3 colorPickerIn" style="display: none;">
                                        <div class="form-group">
                                            <label for="color">Color</label><span class="text-danger">
                                            </span>
                                            <input type="color" name="color_code"
                                                class="form-control form-control-color border-0 @error('color_code') is-invalid @enderror"
                                                value="#136ad0">

                                            <input type="hidden" name="color_code_hidden" class="colorCodeInputHidden"
                                                value="#000000">

                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="javascript:;" data-repeater-create=""
                                            class="btn btn-sm font-weight-bolder btn btn-primary-light btn-border-down">
                                            <i class="la la-plus"></i>Add More Variant Value
                                        </a>
                                        <a href="javascript:;" data-repeater-delete=""
                                            class="btn btn-sm font-weight-bolder btn btn-danger-light btn-border-down variantDlt"
                                            style="display:none;">
                                            <i class="la la-trash-o"></i>Delete Variant Value
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    @endif
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
<script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
<script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>
<!-- <script src="{{ asset('assets/js/form-validation.js') }}"></script> -->
<script src="{{ asset('assets/plugin/tagify/tagify.min.js') }}"></script>
<script src="{{ asset('assets/js/repeater.js')}}"></script>
<script src="{{ asset('assets/js/custom/variants.js')}}"></script>


<script type="text/javascript">
$(document).ready(function() {
    $('#name').on('input', function() {
        var name = $(this).val().toLowerCase();

        // Check if the name contains "color" or "Color"
        if (name.toString() === 'color') {
            $('.colorPickerIn').show();
        } else {
            $('.colorPickerIn').hide();
        }
    });
    
    //  $(document).on('click', '.variantDlt', function () {
    //     // Get the full id: e.g., "variant_39"
    //     var fullId = $(this).closest('[data-repeater-item]')
    //                     .find('input[id^="variant_"]')
    //                     .attr('id');

    //     // Split the string to get just the number (after the underscore)
    //     var parts = fullId.split('_');
    //     var variantId = parts[1]; // "39"
    //      console.log("====="+variantId);
    //   if(variantId){
    //       // Send to Laravel controller via AJAX
    //         $.ajax({
    //             url: "{{ route('admin-ajax-delete-variant-value') }}",         
    //             type: 'POST',
    //             data: {
    //                 id: variantId,
    //                 _token: $('meta[name="csrf-token"]').attr('content') // CSRF token from your meta tag
    //             },
    //             success: function (response) {
    //                 console.log(response);
    //                 //alert(response.message); // or handle response
    //                  show_message(response.message, 'success');
    //             }
    //         });
    //     }
    // });

    function show_message(message, message_type) {
        if (message_type) {
            Swal.fire({
                icon: message_type,
                title: message,
                showConfirmButton: true,
            })
        }
    }

});
</script>

@endpush