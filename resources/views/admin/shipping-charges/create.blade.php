@extends('admin.layout.master')

@push('styles')
<link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">

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
                <li class="breadcrumb-item active" aria-current="page">Create Shipping Charges</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->

<div class="row">
    <div class="col-xl-12">
        <form action="{{ route('admin-shipping-charges.store') }}" method="post" id="shippingcompanyForm"  enctype="multipart/form-data">
            @csrf
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Create Shipping Charges
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                   
                        <div class="col-xl-6">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="name" class="form-label"><span class="text-danger">* </span>Country</label>
                                    <select class="js-states form-control"  name="country_id" id="country_id" required>
                                    <option value="" selected>Select Country</option>
                                    @forelse ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @empty
                                    <option value="" selected>No Data found</option>
                                    @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="name" class="form-label"><span class="text-danger">* </span>Zone</label>
                                    <select class="js-states form-control"  name="shipping_zone_id" id="shipping_zone_id" required>
                                            <option value="" selected>Select Zone</option>   
                                            @forelse ($shippingzones as $shippingzone)
                                                <option value="{{ $shippingzone->id }}">{{ $shippingzone->name }}</option>
                                            @empty
                                        <option value="" selected>No Data found</option>
                                        @endforelse                                 
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-8">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="name" class="form-label"><span class="text-danger">* </span>State</label>
                                    <select class="js-example-placeholder-single js-states form-control"  name="state_id[]" id="state_id" multiple="multiple" required>
                                    <!-- <option value="" selected>Select State</option>   -->                                                                  
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="name" class="form-label"><span class="text-danger">* </span>City</label>
                                    <select class="js-example-placeholder-single js-states form-control"  name="city_id[]" id="city_id" multiple="multiple" required>
                                     <!-- <option value="" selected>Select City</option>                                                  -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Pincode Not Delivery</label>
                                    <textarea name="pincode_not_delivery" rows="3" class="form-control" id="pincode_not_delivery" placeholder="302020,302021,302022,..."></textarea>
                                </div>
                            </div>
                        </div> 
                        <div class="col-xl-4">
                        <div class="card-body p-0">
                            <div class="mb-3">
                                <label for="name" class="form-label"><span class="text-danger">* </span>Shipping Method</label><br>
                                <input type="radio" id="free" name="shipping_method" checked value="free">
                                <label for="free">Free</label>&nbsp;&nbsp;                                                    
                                <input type="radio" id="flat" name="shipping_method" value="flat">  
                                <label for="flat">Flat</label>   
                            </div>
                        </div>
                    </div>                       
                    </div>

                    <div class="row" id="shipping_weight_row" style="display: none;">
                        <div class="col-xl-4">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Amount List</label><br>
                                     <div id="shipping_weight_table"></div>
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
<script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>
<script src="{{ asset('assets/js/form-validation.js') }}"></script>
<script src="{{ asset('assets/js/custom/category.js') }}"></script>

<script>
      $(document).ready(function() {
    // When the country changes, fetch the available states
    $('#country_id').change(function() {
        var country_id = $(this).val();  // Get selected country_id
        var shipping_zone_id = $('#shipping_zone_id').val();  // Get selected shipping zone_id

        // Only send AJAX if both country and shipping zone are selected
        if (country_id && shipping_zone_id) {
            $.ajax({
                url: '/admin/getstate/' + country_id,  // Endpoint to fetch states
                method: 'GET',
                data: {
                    shipping_zone_id: shipping_zone_id  // Send selected shipping zone ID
                },
                success: function(response) {
                    // Clear the state dropdown
                    $('#state_id').empty();

                    // Add the default "Select State" option
                    $('#state_id').append('<option value="">Select State</option>');

                    // Get all states that are already associated with any shipping zone
                    var existingStates = response.existingStates;

                    // Loop through the states and add them to the dropdown
                    $.each(response.states, function(index, state) {
                        // Check if the state is in existingStates (i.e., linked to an active shipping zone)
                        var isDisabled = existingStates.includes(state.id) ? 'disabled' : ''; 

                        // Append the option, disabling it if it should be disabled
                        $('#state_id').append('<option value="' + state.id + '" ' + isDisabled + '>' + state.name + '</option>');
                    });

                    // If no states are found, add a "No Data found" option
                    if (response.states.length === 0) {
                        $('#state_id').append('<option value="" disabled>No Data found</option>');
                    }
                },
                error: function() {
                    alert('Error fetching states');
                }
            });
        } else {
            // If no country or shipping zone is selected, clear the state dropdown
            $('#state_id').empty();
            $('#state_id').append('<option value="">Select State</option>');
        }
    });

    // When the shipping zone changes, trigger the AJAX if country is selected
    $('#shipping_zone_id').change(function() {
        var country_id = $('#country_id').val();  // Get selected country_id
        var shipping_zone_id = $(this).val();  // Get selected shipping zone_id

        // If both country and shipping zone are selected, make the request
        if (country_id && shipping_zone_id) {
            $('#country_id').trigger('change');  // Trigger the country change event
        }
    });
});


</script>



<script>
    $(document).ready(function() {
        // Initialize the select2 plugin for multiple states selection
        $('#state_id').select2();

        // Handle the change event when states are selected
        $('#state_id').change(function() {
            var state_ids = $(this).val(); // Get selected states

            if (state_ids && state_ids.length > 0) {
                // AJAX request to fetch cities based on selected states
                $.ajax({
                    url: '/admin/getcities',  // Laravel route to fetch cities
                    method: 'GET',
                    data: {
                        state_ids: state_ids  // Pass selected state IDs to the controller
                    },
                    success: function(data) {
                        // Clear the city dropdown
                        $('#city_id').empty();
                        $('#city_id').append('<option value="" selected>Select City</option><option value="all">All</option>');

                        // If cities are available, populate the dropdown
                        if (data.length > 0) {
                            $.each(data, function(index, city) {
                                $('#city_id').append('<option value="' + city.id + '">' + city.name + '</option>');
                            });
                        } else {
                            // If no cities found, show "No Data" message
                            $('#city_id').append('<option value="" selected>No Data found</option>');
                        }
                    },
                    error: function() {
                        alert('Error fetching cities.');
                    }
                });
            } else {
                // If no states are selected, clear the city dropdown
                $('#city_id').empty();
                $('#city_id').append('<option value="" selected>Select City</option>');
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Function to fetch shipping weight data based on zone and method
        function fetchShippingWeightData() {
            var zone_id = $('#shipping_zone_id').val(); // Get selected shipping zone
            var shipping_method = $('input[name="shipping_method"]:checked').val(); // Get selected shipping method
            
            // Only proceed if 'Flat' is selected
            if (zone_id && shipping_method === 'flat') {
                // Show the row and the table when the 'Flat' method is selected
                $('#shipping_weight_row').show();

                // AJAX request to fetch shipping weight list for the selected zone
                $.ajax({
                    url: '/admin/get-shipping-weight-amount-list-zone-wise',  // Laravel route for getting shipping weight list
                    method: 'GET',
                    data: { shipping_zone_id: zone_id },  // Passing selected zone id
                    success: function(data) {
                        // Clear previous data
                        $('#shipping_weight_table').empty();

                        // Check if data is available
                        if (data.length > 0) {
                            var table = '<table class="table table-bordered table-hovered table-striped"><thead><tr><th>Weight</th><th>Price</th></tr></thead><tbody>';
                            
                            // Loop through the data and create rows for the table
                            $.each(data, function(index, weight) {
                                table += '<tr>';
                                table += '<td>' + weight.weight_min + ' - ' + weight.weight_max + ' ' + weight.weight_type + '</td>';
                                table += '<td>' + weight.amount + ' RS.</td>';
                                table += '</tr>';
                            });
                            table += '</tbody></table>';
                            $('#shipping_weight_table').html(table);  // Append the table to the div
                        } else {
                            $('#shipping_weight_table').html('<p>No weight data found for the selected zone.</p>');
                        }
                    },
                    error: function() {
                        alert('Error fetching shipping weight data.');
                    }
                });
            } else {
                // If no zone is selected or shipping method is not "Flat", hide the row
                $('#shipping_weight_row').hide();
            }
        }

        // Zone dropdown change hone par call
        $('#shipping_zone_id').change(function() {
            fetchShippingWeightData(); // Fetch shipping weight data when zone changes
        });

        // Shipping method change hone par call
        $('input[name="shipping_method"]').change(function() {
            fetchShippingWeightData(); // Fetch shipping weight data when shipping method changes
        });

        // Initially hide the row
        $('#shipping_weight_row').hide();
    });
</script> 
@endpush