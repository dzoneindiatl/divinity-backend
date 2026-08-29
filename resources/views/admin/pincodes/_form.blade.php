<form
    action="{{ isset($country) ? route('admin-pincodes.update', base64_encode($country->id)) : route('admin-pincodes.store') }}"
    method="POST" enctype="multipart/form-data" id="countryForm">
    @csrf
    @if (isset($country))
        @method('PUT')
    @endif

    <div class="card custom-card">
        <div class="card-header">
            <div class="card-title">Basic Info</div>
        </div>
        <div class="card-body add-products p-0">
            <div class="p-4">
                <div class="row gx-5">
                    <div class="row gy-3">
                        <div class="col-xl-4">
                            <label for="country_id" class="form-label">Country Name</label>
                            <select name="country_id" id="country_id" required
                                    class="form-control form-select @error('country_id') is-invalid @enderror">
                                <option value="">Select Country</option>
                                @foreach($countries as $item)
                                    <option value="{{ $item->id }}" {{ (isset($country) && $item->id == old('country_id', $country->country_id)) ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-xl-4">
                            <label for="state_id" class="form-label">State Name</label>
                            <select name="state_id" id="state_id" required
                                    class="form-control form-select @error('state_id') is-invalid @enderror">
                                <option value="">Select State</option>
                                
                                @if(isset($states) && $states->isNotEmpty())
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}" {{ (isset($country) && $state->id == old('state_id', $country->state_id)) ? 'selected' : '' }}>
                                            {{ $state->name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option disabled>No states available</option>
                                @endif

                            </select>
                            @error('state_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-xl-4">
                            <label for="city_id" class="form-label">City Name</label>
                            <select name="city_id" id="city_id" required
                                    class="form-control form-select @error('city_id') is-invalid @enderror">
                                <option value="">Select City</option>
                                @if(isset($cities) && $cities->isNotEmpty())
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ (isset($country) && $city->id == old('city_id', $country->city_id)) ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option disabled>No cities available</option>
                                @endif

                            </select>
                            @error('city_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                     <hr class="my-4">
                        <div id="delivery-pincode-wrapper">
                            <div class="row gy-3 delivery-pincode-row">
                                <div class="col-xl-4">
                                    <label for="delivery_sets[0][delivery_type]" class="form-label">Delivery Type</label>
                                        <select name="delivery_sets[0][delivery_type]" required id="delivery_select" class="form-control form-select">
                                            <option value="">Select Delivery Type</option>
                                            <option value="1" {{ (old('delivery_type') ?? $country->delivery ?? '') == 1 ? 'selected' : '' }}>Available for Delivery</option>
                                            <option value="2" {{ (old('delivery_type') ?? $country->delivery ?? '') == 2 ? 'selected' : '' }}>Non Delivery</option>
                                            <option value="3" {{ (old('delivery_type') ?? $country->delivery ?? '') == 3 ? 'selected' : '' }}>Delivery with Extra Charge</option>
                                        </select>
                                    </div>
                                    <div class="col-xl-4" id="add_delivery_ect" {{ $country->delivery !== 3 ? 'style=display:none;' : ''}}>
                                        <label for="extra_delivery_charge">Extra Delivery Charge</label>
                                        <input type="number" value="{{ isset($country->extra_delivery_charge) ? $country->extra_delivery_charge : '' }}" step="0.0001" class="form-control"id="extra_delivery_charge" name="delivery_sets[0][extra_delivery_charge]" placeholder="Extra Delivery Charge">
                                     </div>
                                     <div class="col-xl-2">
                                        <label for="pincode[0][pincode]" class="form-label">Pincode(comma-separated)</label>                                        
                                      <textarea name="delivery_sets[0][pincode]" class="form-control" placeholder="e.g. 110001,110002">{{ isset($country->pincode) ? str_replace(['[', ']', '"'], '', $country->pincode) : '' }}</textarea>

                                     </div>
                                     <div class="col-xl-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger remove-row">Remove</button>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary" id="add-more">+ Add More</button>
                            </div>
                            <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">{{ isset($country) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>



            </div>
        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        let $countryId = $('#country_id');
        let $stateId = $('#state_id');
        let $cityId = $('#city_id');
        $countryId.change(function () {
            let countryIdValue = $(this).val();
            if (countryIdValue) {
                $.get(`/admin/get-states/${countryIdValue}`, function (states) {
                    $stateId.html('<option value="">Select State</option>');
                    $cityId.html('<option value="">Select City</option>');
                    $.each(states, function (index, state) {
                        $stateId.append(`<option value="${state.id}">${state.name}</option>`);
                    });
                });
            } else {
                $stateId.html('<option value="">Select State</option>');
                $cityId.html('<option value="">Select City</option>');
            }
        });
        $stateId.change(function () {
            const stateIdValue = $(this).val();
            if (stateIdValue) {
                $.get(`/admin/get-cities/${stateIdValue}`, function (cities) {
                    $cityId.html('<option value="">Select City</option>');
                    $.each(cities, function (index, city) {
                        $cityId.append(`<option value="${city.id}">${city.name}</option>`);
                    });
                });
            } else {
                $cityId.html('<option value="">Select City</option>');
            }
        });
    });
</script>
<script>
$(document).ready(function(){
    let rowIndex = 1; 
    $('#add-more').on('click', function(){
        let newRow = `
            <div class="row gy-3 delivery-pincode-row">
                <div class="col-xl-4">
                    <label class="form-label">Delivery Type</label>
                    <select name="delivery_sets[${rowIndex}][delivery_type]" class="form-control form-select delivery-select">
                        <option value="">Select Delivery Type</option>
                        <option value="1">Available for Delivery</option>
                        <option value="2">Non Delivery</option>
                        <option value="3">Delivery with Extra Charge</option>
                    </select>
                </div>

                  <div class="col-xl-4 extra-charge" style="display:none;">
                    <label class="form-label">Extra Delivery Charge</label>
                    <input type="number" step="0.0001" name="delivery_sets[${rowIndex}][extra_delivery_charge]" class="form-control" placeholder="Extra Delivery Charge">
                 </div>

                <div class="col-xl-2">
                    <label class="form-label">Pincode (comma-separated)</label>
                   <textarea name="delivery_sets[${rowIndex}][pincode]" class="form-control" placeholder="e.g. 110001,110002"></textarea>
                </div>

                <div class="col-xl-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-row">Remove</button>
                </div>

                
            </div>
        `;

        $('#delivery-pincode-wrapper').append(newRow);
        rowIndex++;
    });

    
    $(document).on('click', '.remove-row', function(){
        $(this).closest('.delivery-pincode-row').remove();
    });

    
    $(document).on('change', '.delivery-select', function(){
        let row = $(this).closest('.delivery-pincode-row');
        if ($(this).val() == '3') {
            row.find('.extra-charge').show();
        } else {
            row.find('.extra-charge').hide();
        }
    });

});
</script>
<script>
$(document).ready(function(){
    $('#delivery_select').change(function(){
        if ($(this).val() == '3') {
            $('#add_delivery_ect').show();
        } else {
            $('#add_delivery_ect').hide();
        }
    });

    if ($('#delivery_select').val() == '3') {
        $('#add_delivery_ect').show();
    }
});
</script>





