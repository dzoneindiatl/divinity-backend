@extends('admin.layout.master')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}">
@endpush
@section('content')

<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
    <div class="card-header justify-content-between">
        <div class="card-title">
            <h4>{{ $userDetails->name ?? "N/A" }}
                <h4>
        </div>
        <?php
        $createdAt = new DateTime($userDetails->created_at);
        $now = new DateTime(); // Current date and time

        // Calculate the difference
        $interval = $createdAt->diff($now);
        $years = $interval->y;
        $months = $interval->m;
        $days = $interval->d;

        ?>
        <span> {{!empty($userDetails->addresses[0]->city->name) ? $userDetails->addresses[0]->city->name:"NA"}} ,{{!empty($userDetails->addresses[0]->state->code) ? $userDetails->addresses[0]->state->code:"NA"}}, {{!empty($userDetails->addresses[0]->country->name) ? $userDetails->addresses[0]->country->name:"NA"}}</span>
        <span class="li">Customer for about
            <?php
            if ($years > 0) {
                $output = "$years Years";
            } elseif ($months > 0) {
                $output = "$months Months";
            } else {
                $output = "$days Days";
            }
            echo $output;
            ?>
        </span>
    </div>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">User Details</li>
            </ol>
        </nav>
    </div>
</div>
<!-- <div class="row"> -->
<div class="row">
    <div class="col-xl-8" style="margin-left: 20px;">
        <div class="card custom-card">
            <div class="card-body">
                <div class="row gx-5">
                    <!-- <div class="col-xxl-3 col-xl-12"> -->
                    <div class="row">
                        <div class="col-lg-4 col-sm-12" style="border-right: 1px solid;">
                            <p class="fs-15 fw-semibold mb-2">All time</p>
                        </div>
                        <div class="col-lg-4 col-sm-12" style="border-right: 1px solid;">
                            <p class="fs-15 fw-semibold mb-2">Amount Spent</p>
                            <?php
                            $amount_spent = 0;
                            foreach ($userDetails->orders as $order) {
                                $amount_spent += $order->total;
                            }
                            ?>
                            <p>{{'₹ '.number_format($amount_spent ?? 0,2)}}</p>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <p class="fs-15 fw-semibold mb-2">Orders</p>
                            <p>{{count($userDetails->orders)}}</p>
                        </div>
                    </div>
                    <!-- </div> -->
                    <div class="col-xxl-9 col-xl-12">
                        <div class="row gx-5">
                            <div class="col-xl-8 mt-xxl-0 mt-3">
                                <div class="row">
                                    <div class="col-xl-10">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3" style="margin-left: 20px;">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    Customer
                </div>
                <div class="dropdown">
                    <a href="#" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots"></i>
                    </a>
                    <ul class="dropdown-menu text-nowrap" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="{{route('admin-admin_users.edit',base64_encode($userDetails->id))}}">Edit contact information</a></li>
                        <button class="btn dropdown-item" data-bs-target="#addressModalToggle" data-bs-toggle="modal">Manage addresses</button>
                        <form method="GET" action="{{route('admin-admin_users.delete',base64_encode($userDetails->id))}}">
                            @csrf
                            <input name="_method" type="hidden" value="DELETE">
                            <button type="submit" class="btn text-danger dropdown-item" id="confirm-button">Delete customer</button>
                        </form>
                    </ul>
                </div>

            </div>
            <div class="card-body">
                <div class="row gx-5">
                    <div class="col-xxl-12 col-xl-12">
                        <div class="row">
                            <div class="col-xxl-12 col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-md-5 mb-3">
                                <p class="fs-15 fw-semibold mb-2">Contact information</p>
                                <a href="mailto:{{ $userDetails->email ?? 'N/A' }}" style="color:blue" id="email-link">{{ $userDetails->email ?? "N/A" }}</a>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <span>
                                    <i class="bi bi-clipboard" id="copy-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Copy email" style="cursor: pointer;"></i>
                                </span>
                                <p class="fs-15 fw-semibold mb-2">Default Address</p>
                                <p> {{!empty($userDetails->addresses[0]->address) ? $userDetails->addresses[0]->address:"NA"}},<br>{{!empty( $userDetails->addresses[0]->city->name) ? $userDetails->addresses[0]->city->name:"NA"}} -{{!empty($userDetails->addresses[0]->postal_code) ? $userDetails->addresses[0]->postal_code:"NA"}} ,{{!empty($userDetails->addresses[0]->state->name) ? $userDetails->addresses[0]->state->name:"NA"}}, {{!empty($userDetails->addresses[0]->country->name) ? $userDetails->addresses[0]->country->name:"NA"}}<br>{{!empty($userDetails->addresses[0]->phone_number) ? $userDetails->addresses[0]->phone_number:"NA"}}</p>
                                <p class="fs-15 fw-semibold mb-2">Marketing</p>
                                @php
                                $email_subscription = \App\Models\Subscriber::where('email', $userDetails->email)->exists();
                                @endphp

                                @if($email_subscription)
                                <i class="bi bi-dot" style="color:green;"></i>Email Subscribed
                                @else
                                <i class="bi bi-dot" style="color:red;"></i>Email Not subscribed
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-8" style="margin-left: 20px;">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    Last order placed
                </div>
            </div>
            <div class="card-body">
                <div class="row" style="border: 1px solid;border-radius:5px">
                    <div class="col-xl-10">
                        @if(!empty($userDetails->orders[0]))
                        <p class="fs-15 fw-semibold mb-2"><a href="{{route('admin-orders.view',base64_encode($userDetails->orders[0]->id))}}" style="color: #005BD3;">{{'#'.$userDetails->orders[0]->order_number}}</a>&nbsp;&nbsp;&nbsp;
                            <span class="badge bg-secondary">{{$userDetails->orders[0]->status}}</span>&nbsp;&nbsp;&nbsp;
                            <span> {{'₹ '.number_format($userDetails->orders[0]->total ?? 0, 2)}}</span>
                        </p>
                        <p>{{($userDetails->orders[0]->created_at)->format('F j, Y \a\t g:i a')}}</p>
                        @else
                        "NA"
                        @endif
                    </div>
                </div>
                <div class="row" style="border: 1px solid;border-radius:5px">
                    <div class="col-xl-10">
                        @if(!empty($userDetails->orders[0]))
                        @foreach($userDetails->orders[0]->items as $item)
                        <p><span>{{$item->product->name}}</span>, <span> x {{$item->qty}}</span><span>{{ '  ₹ '.number_format($item->total ?? 0,2)}}</span></p>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <!-- <div class="card-footer">
                <div class="col-lg-6">
                    <a href="{{route('admin-orders.user_orders',base64_encode($userDetails->id))}}" class="btn btn-secondary">View all orders</a> <button type="button" class="btn btn-primary">Create Order</button>
                </div>
            </div> -->
        </div>
    </div>
    <!-- <div class="col-xl-3" style="margin-left: 20px;">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    Tags
                </div>
            </div>
            <div class="card-body">
                <input class="form-control w-100" type="text">
            </div>
        </div>
    </div> -->
</div>
<!-- <div class="row">
    <div class="col-xl-8" style="margin-left: 20px;">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    Timeline
                </div>
            </div>
            <div class="card-body">
                <textarea name="" id="" class="form-control w-100"></textarea>
                <br>
                <button class="btn btn-primary">
                    Post
                </button>
            </div>
        </div>
    </div>
</div> -->
<!-- </div> -->



<!-- model for address -->
<div class="modal fade" id="addressModalToggle" aria-hidden="true" aria-labelledby="addressModalToggleLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addressModalToggleLabel">Addresses</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- list start from here -->
                <!-- <pre> -->
                @foreach($userDetails->addresses as $address)
                <div class="card">
                    <div class="card-body">
                        <p id="address-display">
                            <strong>Name:</strong> {{$address->name ?? 'NA'}}<br>
                            <strong>Email:</strong> {{$address->email ?? 'NA'}}<br>
                            <strong>Phone:</strong> {{$address->phone_number ?? 'NA'}}<br>
                            <strong>Alternate Phone:</strong> {{$address->alternate_number}}<br>
                            <strong>Pincode:</strong> {{$address->postal_code ?? 'NA'}}<br>
                            <strong>City:</strong> {{$address->city->name ?? 'NA'}}<br>
                            <strong>State:</strong> {{$address->state->name ?? 'NA'}}<br>
                            <strong>Landmark:</strong> {{$address->landmark ?? 'NA'}}<br>
                            <strong>Address:</strong> {{$address->address ?? 'NA'}}<br>
                            <strong>Country:</strong> {{$address->country->name ?? 'NA'}}<br>
                        </p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editAddressModal" data-id="{{$address->id ?? ''}}" data-name="{{$address->name ?? ''}}" data-email="{{$address->email ?? ''}}" data-phone="{{$address->phone_number ?? ''}}" data-alternate-phone="{{$address->alternate_number ?? ''}}" data-pincode="{{$address->postal_code ?? ''}}" data-country="{{$address->country->id ?? ''}}" data-city="{{$address->city->id ?? ''}}" data-state="{{$address->state->id ?? ''}}" data-landmark="{{$address->landmark ?? ''}}" data-address="{{$address->address ?? ''}}">Edit</button>
                    </div>
                </div>
                @endforeach
                <!-- list till here -->
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" data-bs-target="#addAddressModal" data-bs-toggle="modal">Add new address</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addAddressModal" aria-hidden="true" aria-labelledby="addressModalToggleLabel2" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addressModalToggleLabel2">Add new address</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" id="addAddressForm">
                <div class="modal-body">
                    <input name="user_id" type="hidden" id="userId" value="{{$userDetails->id}}">
                    <div class="mb-3">
                        <label for="country" class="form-label">country</label>
                        <select name="country" id="country" class="form-select add_country">
                            @foreach($countries as $country)
                            <option value="{{$country->id}}">{{$country->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input name="name" type="text" class="form-control" id="name">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input name="email" type="email" class="form-control" id="email">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input name="phone_number" type="text" class="form-control" id="phone">
                    </div>
                    <div class="mb-3">
                        <label for="alternatePhone" class="form-label">Alternate Phone</label>
                        <input name="alternate_number" type="text" class="form-control" id="alternatePhone">
                    </div>
                    <div class="mb-3">
                        <label for="pincode" class="form-label">Pincode</label>
                        <input name="postal_code" type="text" class="form-control" id="pincode">
                    </div>
                    <div class="mb-3">
                        <label for="state" class="form-label">State</label>
                        <select name="state" id="state" class="form-select add_state">
                            @foreach($states as $state)
                            <option value="{{$state->id}}">{{$state->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <select name="city" id="city" class="form-select add_city">
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="landmark" class="form-label">Landmark</label>
                        <input name="landmark" type="text" class="form-control" id="landmark">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea name="address" class="form-control" id="address" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Address Modal -->
<div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true" style="z-index:999;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAddressModalLabel">Edit Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAddressForm">
                <div class="modal-body">
                    <!-- Hidden field to store address ID -->
                    <input name="adddress_id" type="hidden" id="addressId" value="">
                    <input name="user_id" type="hidden" id="userId" value="{{$userDetails->id}}">
                    <div class="mb-3">
                        <label for="country" class="form-label">country</label>
                        <select name="country" id="country" class="form-select">
                            @foreach($countries as $country)
                            <option value="{{$country->id}}">{{$country->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input name="name" type="text" class="form-control" id="name">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input name="email" type="email" class="form-control" id="email">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input name="phone_number" type="text" class="form-control" id="phone">
                    </div>
                    <div class="mb-3">
                        <label for="alternatePhone" class="form-label">Alternate Phone</label>
                        <input name="alternate_number" type="text" class="form-control" id="alternatePhone">
                    </div>
                    <div class="mb-3">
                        <label for="pincode" class="form-label">Pincode</label>
                        <input name="postal_code" type="text" class="form-control" id="pincode">
                    </div>
                    <div class="mb-3">
                        <label for="state" class="form-label">State</label>
                        <select name="state" id="state" class="form-select">
                            @foreach($states as $state)
                            <option value="{{$state->id}}">{{$state->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <select name="city" id="city" class="form-select">
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="landmark" class="form-label">Landmark</label>
                        <input name="landmark" type="text" class="form-control" id="landmark">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea name="address" class="form-control" id="address" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" form="editAddressForm">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Custom-Switcher JS -->
<script src="{{ asset('assets/js/custom-switcher.min.js') }}"></script>

<!-- Swiper JS -->
<script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>

<script src="{{ asset('assets/js/product-details.js') }}"></script>

<script>
    //copy to clipboard
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    document.getElementById('copy-icon').addEventListener('click', function() {
        const email = document.getElementById('email-link').innerText;
        const textarea = document.createElement('textarea');
        textarea.value = email;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        var tooltip = bootstrap.Tooltip.getInstance(document.getElementById('copy-icon'));
        tooltip.setContent({
            '.tooltip-inner': 'Copied to clipboard'
        });
        tooltip.show();
        setTimeout(function() {
            tooltip.hide();
        }, 1000);
    });


    //latest added


    document.addEventListener('DOMContentLoaded', function() {
        var editAddressModal = document.getElementById('editAddressModal');
        editAddressModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;

            var id = button.getAttribute('data-id');
            var name = button.getAttribute('data-name');
            var email = button.getAttribute('data-email');
            var phone = button.getAttribute('data-phone');
            var alternatePhone = button.getAttribute('data-alternate-phone');
            var pincode = button.getAttribute('data-pincode');
            var stateId = button.getAttribute('data-state');
            var cityId = button.getAttribute('data-city');
            var countryId = button.getAttribute('data-country');
            var landmark = button.getAttribute('data-landmark');
            var address = button.getAttribute('data-address');

            editAddressModal.querySelector('#name').value = name;
            editAddressModal.querySelector('#email').value = email;
            editAddressModal.querySelector('#phone').value = phone;
            editAddressModal.querySelector('#alternatePhone').value = alternatePhone;
            editAddressModal.querySelector('#pincode').value = pincode;
            editAddressModal.querySelector('#landmark').value = landmark;
            editAddressModal.querySelector('#address').value = address;
            editAddressModal.querySelector('#state').value = stateId;
            editAddressModal.querySelector('#city').value = cityId;
            editAddressModal.querySelector('#country').value = countryId;
            editAddressModal.querySelector('#addressId').value = id;

            var stateSelect = document.getElementById('state');
            var citySelect = document.getElementById('city');

            stateSelect.addEventListener('change', function() {
                var stateId = this.value;
                citySelect.innerHTML = '<option value="" selected disabled>Select City</option>';

                if (stateId) {
                    fetch(`/getChildByParent?type=city&parent_id=${stateId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                for (const [id, name] of Object.entries(data.data)) {
                                    var option = document.createElement('option');
                                    option.value = id;
                                    option.text = name;
                                    citySelect.add(option);
                                }
                                citySelect.value = cityId;
                            }
                        })
                        .catch(error => console.error('Error fetching cities:', error));
                }
            });
            stateSelect.dispatchEvent(new Event('change'));
        });

        function show_message(message, message_type) {

            if (message_type) {



                Swal.fire({

                    icon: message_type,

                    title: message,

                    showConfirmButton: true,

                })

            }



        }

        document.getElementById('editAddressForm').addEventListener('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(this);
            var addressId = document.getElementById('addressId').value;


            fetch(`/user_address_edit/${addressId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status = 'success') {
                        var modal = bootstrap.Modal.getInstance(editAddressModal);
                        modal.hide();
                        show_message('Updated successfully', 'success');

                    } else {
                        show_message('Failed updating', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error updating address:', error);
                    alert('Failed to update address.');
                });
        });
    });





    //newly added for new address
    document.addEventListener('DOMContentLoaded', function() {
        const addStateSelect = document.querySelector('.add_state');
        const addCitySelect = document.querySelector('.add_city');

        // Function to fetch cities based on selected state
        function fetchCities(stateId) {
            fetch(`/getChildByParent?type=city&parent_id=${stateId}`)
                .then(response => response.json())
                .then(data => {
                    const cities = data.data;
                    addCitySelect.innerHTML = ''; // Clear existing options
                    for (const [id, name] of Object.entries(cities)) {
                        const option = document.createElement('option');
                        option.value = id;
                        option.textContent = name;
                        addCitySelect.appendChild(option);
                    }
                })
                .catch(error => {
                    console.error('Error fetching cities:', error);
                    alert('Failed to fetch cities.');
                });
        }

        // Event listener for state change
        addStateSelect.addEventListener('change', function() {
            const stateId = addStateSelect.value;
            if (stateId) {
                fetchCities(stateId);
            } else {
                addCitySelect.innerHTML = '<option>Select city</option>';
            }
        });

        // Form submission handler
        document.getElementById('addAddressForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            fetch('/user_address_save', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status = 'success') {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addAddressModal'));
                        modal.hide();
                        show_message('Address added successfully', 'success');
                    } else {
                        show_message('Failed to add address', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error adding address:', error);
                    alert('Failed to add address.');
                });
        });
    });
</script>

@endpush