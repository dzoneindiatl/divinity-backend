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
                    <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home 1</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ ucfirst($prefix) }} Setting</li>
                </ol>
            </nav>
        </div>
    </div>


    <!-- Page Header Close -->
    <div class="row">
        <div class="col-xl-12">
            <form action="{{ URL('settings/prefix') }}/{{ $prefix }}" method="post"
                enctype="multipart/form-data" id="settingsForm">
                @csrf
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title d-flex justify-content-between">
                            <div>
                            </div>
                            <div>
                                <ul class="nav nav-tabs card-header-tabs" id="invoiceSettingTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="basic-info-tab" data-bs-toggle="tab"
                                            data-bs-target="#basic-info" type="button" role="tab"
                                            aria-controls="basic-info" aria-selected="true">Basic Info</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="invoice-setting-tab" data-bs-toggle="tab"
                                            data-bs-target="#invoice-setting" type="button" role="tab"
                                            aria-controls="invoice-setting" aria-selected="false">Invoice Setting</button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body add-products p-0">
                        <div class="tab-content p-4" id="invoiceSettingTabContent">
                            <div class="tab-pane fade show active" id="basic-info" role="tabpanel"
                                aria-labelledby="basic-info-tab">
                                <div class="row gx-5">
                                    <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12">
                                        <div class="card custom-card shadow-none mb-0 border-0">
                                            <div class="card-body p-0">
                                                <div class="row gy-3">
                                                    <?php
                                                if (!empty($result)) {
                                                    $i = 0;
                                                    $half = floor(count($result) / 2);
                                                    foreach ($result as $setting) {
                                                        $text_extention = '';
                                                        $key = $setting['key'];
                                                        $keyE = explode('.', $key);
                                                        $keyTitle = $keyE['1'];

                                                        $label = $keyTitle;
                                                        if ($setting['title'] != null) {
                                                            $label = $setting['title'];
                                                        }

                                                        $inputType = 'text';
                                                        if ($setting['input_type'] != null) {
                                                            $inputType = $setting['input_type'];
                                                        } ?>
                                                    <input type="hidden" name="Setting[{{ $i }}]['type']"
                                                        value="{{ $inputType ?? '' }}">
                                                    <input type="hidden" name="Setting[{{ $i }}]['id']"
                                                        value="{{ $setting['id'] ?? '' }}">
                                                    <input type="hidden" name="Setting[{{ $i }}]['key']"
                                                        value="{{ $setting['key'] ?? '' }}">

                                                    <?php
                                                        switch ($inputType) {
                                                            case 'checkbox': ?>
                                                    <div class="col-xl-6">
                                                        <label class="form-label"
                                                            style="width:300px;"><?php echo $label; ?></label>
                                                        <div class="mws-form-item clearfix">
                                                            <ul class="mws-form-list inline">
                                                                <?php
                                                                $checked = $setting['value'] == 1 ? true : false;
                                                                $val = !empty($setting['value']) ? $setting['value'] : 0;
                                                                ?>
                                                                <input type="checkbox"
                                                                    name="Setting[{{ $i }}]['value']"
                                                                    value="{{ $val ?? '' }}">

                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <?php
                                                        break;
                                                        case 'text': 
                                                    ?>
                                                    <div class="col-xl-6">
                                                        <label class="form-label"><?php echo $label; ?></label>
                                                        <input type="{{ $inputType }}"
                                                            name="Setting[{{ $i }}]['value']"
                                                            value="{{ $setting['value'] ?? '' }}" class="form-control"
                                                            id="$key">
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                    <?php
                                                        break;
                                                        case 'select': 
                                                    ?>
                                                    <div class="col-xl-6">
                                                        <label class="form-label"><?php echo $label; ?></label>
                                                        <select name="Setting[{{ $i }}]['value']"
                                                            class="form-control " id="$key">
                                                            <option value="pay_later">Pay Later</option>
                                                            <option value="pay_now">Pay Now</option>
                                                        </select>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                    <?php
                                                                break;
                                                            case 'file': ?>
                                                    <div class="col-xl-6">
                                                        <label class="form-label"><?php echo $label; ?></label>
                                                        <input type="{{ $inputType }}"
                                                            name="Setting[{{ $i }}]['value']"
                                                            class="form-control" accept="image/*" id="$key" ]>
                                                        @if (!empty($setting['value']))
                                                            <img height="70" width="70"
                                                                src="{{ isset($setting['value']) ? $setting['value'] : '' }}" />
                                                        @endif
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                    <?php
                                                                break;
                                                            case 'textarea': ?>
                                                    <div class="col-xl-12">
                                                        <label class="form-label"><?php echo $label; ?></label>
                                                        <textarea name="Setting[{{ $i }}]['value']" id="textarea_{{ $i }}"
                                                            class="form-control textarea_resize" rows=3,cols=3>{!! $setting['value'] ?? '' !!}</textarea>
                                                    </div>
                                                    <?php
                                                                break;
                                                        }
                                                        //if ($i == $half)
                                                        //    echo '</div><div class="row">';
                                                        $i++;
                                                    }
                                                }
                                                ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="invoice-setting" role="tabpanel"
                                aria-labelledby="invoice-setting-tab">
                                <div class="row gy-3">
                                    <div class="col-xl-6">
                                        <label class="form-label">Country</label>
                                        <select id="country-dropdown" name="invoice[country_id]" class="form-control">
                                            <option value="">Select Country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ $invoiceSetting && $invoiceSetting->country_id == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xl-6">
                                        <label class="form-label">State</label>
                                        <select id="state-dropdown" name="invoice[state_id]" class="form-control">
                                            <option value="">Select State</option>
                                        </select>
                                    </div>
                                    <div class="col-xl-6">
                                        <label class="form-label">City</label>
                                        <select id="city-dropdown" name="invoice[city_id]" class="form-control">
                                            <option value="">Select City</option>
                                        </select>
                                    </div>
                                    {{-- <div class="col-xl-6">
                                            <label class="form-label">Pincode</label>
                                            <input type="text" class="form-control" id="pincode" value="{{ $invoiceSetting->pincode}}"
                                                name="invoice[pincode]" />
                                        </div> --}}
                                    {{-- <div class="col-xl-12">
                                        <label class="form-label">Address</label>
                                        <input type="text" class="form-control" id="address"
                                            name="invoice[address]" value="{{ $invoiceSetting->address }}" />
                                    </div> --}}
                                    {{-- <div class="col-xl-6">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" id="name" name="invoice[name]"
                                            value="{{ $invoiceSetting->name }}" />
                                    </div> --}}
                                    {{-- <div class="col-xl-6">
                                        <label class="form-label">Nature Spilly</label>
                                        <input type="text" class="form-control" id="nature_spilly"
                                            name="invoice[nature_spilly]" value="{{ $invoiceSetting->nature_spilly }}" />
                                    </div>
                                    <div class="col-xl-6">
                                        <label class="form-label">Invoice Number</label>
                                        <input type="text" class="form-control" id="invoice_number"
                                            name="invoice[invoice_number]"
                                            value="{{ $invoiceSetting->invoice_number }}" />
                                    </div>
                                    <div class="col-xl-6">
                                        <label class="form-label">Packet ID</label>
                                        <input type="text" class="form-control" id="packet_id"
                                            name="invoice[packet_id]" value="{{ $invoiceSetting->packet_id }}" />
                                    </div>
                                    <div class="col-xl-6">
                                        <label class="form-label">Website Name</label>
                                        <input type="text" class="form-control" id="website_name"
                                            name="invoice[website_name]" value="{{ $invoiceSetting->website_name }}" />
                                    </div> --}}
                                    <!-- <div class="col-xl-6">
                                                                                                                                                    <label class="form-label">Signature</label>
                                                                                                                                                    <input type="text" class="form-control" id="signature"
                                                                                                                                                        name="invoice[signature]" />
                                                                                                                                                </div> -->
                                    <div class="col-xl-6">
                                        <label class="form-label">Signature</label>
                                        <input type="file" name="invoice[signature]" class="form-control"
                                            accept="image/*" id="$key">
                                        @if (!empty($invoiceSetting['signature']))
                                            <img height="70" width="70"
                                                src="{{ Config('constant.SIGNATURE_IMAGE_URL') . $invoiceSetting['signature'] ? Config('constant.SIGNATURE_IMAGE_URL') . $invoiceSetting['signature'] : '' }}" />
                                        @endif
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    {{-- <div class="col-xl-6">
                                        <label class="form-label">Designation</label>
                                        <input type="text" class="form-control" id="designation"
                                            name="invoice[designation]" value="{{ $invoiceSetting->designation }}" />
                                    </div> --}}
                                    <div class="col-xl-6">
                                        <label class="form-label">Status</label>
                                        <select class="form-control" id="status" name="invoice[is_active]">
                                            <option value="">Select Status</option>
                                            <option value="1"
                                                {{ $invoiceSetting && $invoiceSetting->is_active == '1' ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="0"
                                                {{ $invoiceSetting && $invoiceSetting->is_active == '0' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                    </div>
                                    {{-- <div class="col-xl-6">
                                        <label class="form-label">Invoice Setting</label>
                                        <input type="text" class="form-control" id="invoice_setting"
                                            name="invoice[invoice_setting]"
                                            value="{{ $invoiceSetting->invoice_setting }}" />
                                    </div> --}}
                                    <div class="col-xl-12">
                                        <label class="form-label">Note</label>
                                        <textarea class="form-control" id="note" name="invoice[note]" rows="3">{{ $invoiceSetting ? $invoiceSetting->note : '' }}</textarea>
                                    </div>

                                    <div class="col-xl-2">
                                        <label class="form-label">Cash On Delivery Limit</label>
                                        <input class="form-control" id="cash_on_limit"
                                            name="invoice[cash_on_limit]"value="{{ $invoiceSetting ? $invoiceSetting->cash_on_limit : '' }}">
                                    </div>
                                    <div class="col-xl-2">
                                        <label class="form-label">Order Number Prefix</label>
                                        <input class="form-control" id="order_prefix"
                                            name="invoice[order_prefix]"value="{{ $invoiceSetting?->order_prefix ? $invoiceSetting->order_prefix : '' }}">
                                    </div>
                                    <div class="col-xl-2">
                                        <label class="form-label">Invoice Number Prefix</label>
                                        <input class="form-control" id="invoice_number"
                                            name="invoice[invoice_number]"value="{{ $invoiceSetting?->invoice_number ? $invoiceSetting->invoice_number : '' }}">
                                    </div>
                                    <div class="col-xl-4">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control" id="address"
                                            name="invoice[address]">{{ $invoiceSetting?->address ? $invoiceSetting->address : '' }}</textarea>
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

    </form>
    </div>
    </div>
@endsection

@push('scripts')
    <!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->
    <!-- Select2 Cdn -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugin/tagify/tagify.min.js') }}"></script>

    <!-- Internal Select-2.js -->
    <script src="{{ asset('assets/js/select2.js') }}"></script>

    <script src="{{ asset('assets/libs/dropzone/dropzone-min.js') }}"></script>

    <script src="{{ asset('assets/js/custom/product.js') }}"></script>

    {{-- <script src="{{ asset('assets/js/fileupload.js') }}"></script> --}}
    <script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
    <!-- <script src="{{ asset('assets/js/form-validation.js') }}"></script> -->


    <script type="text/javascript">
        document.querySelectorAll('.textarea_resize').forEach(function(textarea) {
            CKEDITOR.replace(textarea, {
                filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
                enterMode: CKEDITOR.ENTER_BR
            });
        });

        // Dependent dropdowns for Country, State, City
        $(document).ready(function() {
            // Function to load states
            function loadStates(countryId, selectedStateId = null) {
                if (countryId) {
                    $.ajax({
                        url: '{{ route('admin-settings.getStates', ':country') }}'.replace(':country',
                            countryId),
                        type: 'GET',
                        success: function(data) {
                            $('#state-dropdown').empty().append(
                                '<option value="">Select State</option>');
                            $.each(data, function(key, value) {
                                var selected = (selectedStateId && value.id ==
                                    selectedStateId) ? 'selected' : '';
                                $('#state-dropdown').append('<option value="' + value.id +
                                    '" ' + selected + '>' + value.name + '</option>');
                            });
                            if (selectedStateId) {
                                loadCities(selectedStateId,
                                    '{{ $invoiceSetting ? $invoiceSetting->city_id : '' }}');
                            }
                        }
                    });
                } else {
                    $('#state-dropdown').empty().append('<option value="">Select State</option>');
                    $('#city-dropdown').empty().append('<option value="">Select City</option>');
                }
            }

            // Function to load cities
            function loadCities(stateId, selectedCityId = null) {
                if (stateId) {
                    $.ajax({
                        url: '{{ route('admin-settings.getCities', ':state') }}'.replace(':state',
                            stateId),
                        type: 'GET',
                        success: function(data) {
                            $('#city-dropdown').empty().append('<option value="">Select City</option>');
                            $.each(data, function(key, value) {
                                var selected = (selectedCityId && value.id == selectedCityId) ?
                                    'selected' : '';
                                $('#city-dropdown').append('<option value="' + value.id + '" ' +
                                    selected + '>' + value.name + '</option>');
                            });
                        }
                    });
                } else {
                    $('#city-dropdown').empty().append('<option value="">Select City</option>');
                }
            }

            // On country change
            $('#country-dropdown').change(function() {
                var countryId = $(this).val();
                loadStates(countryId);
            });

            // On state change
            $('#state-dropdown').change(function() {
                var stateId = $(this).val();
                loadCities(stateId);
            });

            // On page load, if country is selected, load states
            var selectedCountry = $('#country-dropdown').val();
            if (!selectedCountry) {
                selectedCountry = '{{ $defaultCountryId ?? '' }}';
                if (selectedCountry) {
                    $('#country-dropdown').val(selectedCountry);
                }
            }
            if (selectedCountry) {
                loadStates(selectedCountry, '{{ $invoiceSetting ? $invoiceSetting->state_id : '' }}');
            }
        });

        function isEmail(email) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test(email);
        }

        var empty_msg = 'This field is required';
        var numuric_empty_msg = 'This field is allow only numuric value';
        var image_validation = 'Please upload a valid image. Valid extensions are jpg, jpeg, png, jpeg';
        var allowedExtensions = ['gif', 'GIF', 'jpeg', 'JPEG', 'PNG', 'png', 'jpg', 'JPG'];

        function submit_form() {
            var $inputs = $('.mws-form :input.valid');
            var error = 0;
            $inputs.each(function() {
                if ($(this).val().trim() == '') {
                    $(this).next().html(empty_msg);
                    error = 1;
                } else {
                    if ($(this).attr('id') == 'Site.email') {
                        if (!isEmail($(this).val().trim())) {
                            $(this).next().html("Please enter a valid email");
                            error = 1;
                        } else {
                            $(this).next().html("");
                        }
                    } else if ($(this).attr('id') == 'Reading.records_per_page') {
                        if (!$.isNumeric($(this).val().trim())) {
                            $(this).next().html(numuric_empty_msg);
                            error = 1;
                        } else {
                            $(this).next().html("");
                        }
                    } else {
                        $(this).next().html("");
                    }
                }
            });
            if (error == 0) {
                $('.mws-form').submit();
            }
        }
        $('#settingsForm').each(function() {
            $(this).find('input').keypress(function(e) {
                if (e.which == 10 || e.which == 13) {
                    submit_form();
                    return false;
                }
            });
        });
    </script>
@endpush
