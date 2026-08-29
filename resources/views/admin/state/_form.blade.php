<style>
    .info-icon{
        background: #333;padding: 0px 6px;color: #fff;border-radius:50%;cursor: pointer;font-weight: bold;font-size: 12px;
    }
</style>
<form action="{{ isset($state) ? route('admin-state.update') : route('admin-state.store') }}"
    method="POST" enctype="multipart/form-data" id="stateForm">
    @csrf
    @if (isset($state))
    @method('PUT')
        <input type="hidden" name="id" value="{{ base64_encode($state->id) }}">
    @endif

    <div class="card custom-card">
        <div class="card-header">
            <div class="card-title">Basic Info</div>
        </div>
        <div class="card-body add-products p-0">
            <div class="p-4">
                <div class="row gx-5">
                    <div class="row gy-3">
                        <div class="col-xl-3">
                            <label for="name" class="form-label">State Name</label>
                            <input type="text" name="name" required
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $state->name ?? '') }}" placeholder="State Name">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-xl-3">
                            <label for="shortname" class="form-label">Short Name</label>
                            <input type="text" name="shortname" required
                                class="form-control @error('shortname') is-invalid @enderror"
                                value="{{ old('shortname', $state->shortname ?? '') }}" placeholder="State short name">
                            @error('shortname')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-xl-3">
                            <label for="code" class="form-label">State Code</label>
                            <input type="text" name="code" required
                                class="form-control @error('code') is-invalid @enderror"
                                value="{{ old('code', $state->code ?? '') }}" placeholder="State Code">
                            @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    
                        <div class="col-xl-3">
                            <label for="country_id" class="form-label">country Name</label>
                            <select name="country_id" required
                                class="form-control form-select @error('country_id') is-invalid @enderror">
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                <option value="{{ $country->id }}"
                                    {{ old('country_id', isset($state) ? $state->country_id : ($endesid ?? '')) == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('country_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-xl-3">
                            <label for="is_free_shipping" class="form-label">Free Shipping <span title="The shipping charge will be ₹0 if Free Shipping is set to 'Yes' for this state. The weight range does not apply to this state." data-bs-toggle="tooltip" class="info-icon">i</span></label>
                            <select name="is_free_shipping" required
                                class="form-control form-select @error('is_free_shipping') is-invalid @enderror">
                                <option value="0"
                                    {{ old('is_free_shipping', isset($state) ? $state->is_free_shipping : ($endesid ?? '')) == 0 ? 'selected' : '' }}>No
                                </option>
                                <option value="1"
                                    {{ old('is_free_shipping', isset($state) ? $state->is_free_shipping : ($endesid ?? '')) == 1 ? 'selected' : '' }}>Yes
                                </option>
                            </select>
                            @error('is_free_shipping')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-xl-3">
                            <label for="free_shipping_min_cart_amount" class="form-label">Minimum Cart Value For Free Shipping <span title="If Free Shipping is set to 'No' and the customer's cart amount exceeds the specified threshold, Free Shipping will be applied to the order. However, if the cart amount is below the specified threshold, the weight-based shipping rate will apply." data-bs-toggle="tooltip" class="info-icon">i</span></label>
                            <input type="number" name="free_shipping_min_cart_amount"
                                class="form-control @error('free_shipping_min_cart_amount') is-invalid @enderror"
                                value="{{ old('free_shipping_min_cart_amount', $state->free_shipping_min_cart_amount ?? '') }}" placeholder="Minimum Cart Value For Free Shipping">
                            @error('free_shipping_min_cart_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                    </div>

                    <!-- Weight Range Section -->
                    <div class="row gy-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Delivery Weight Ranges</h6>
                                <button type="button" class="btn btn-sm btn-primary" id="addWeightRange">
                                    <i class="ri-add-line"></i> Add More
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Weight Range Container -->
                    <div id="weightRangesContainer">
                        @php
                            $existingRanges = old('weight_ranges', isset($state) && $state->weight_ranges ? $state->weight_ranges : []);
                            $hasExistingRanges = !empty($existingRanges);
                        @endphp
                        
                        @if($hasExistingRanges)
                            @foreach($existingRanges as $index => $range)
                                <div class="row gy-3 weight-range-row" data-index="{{ $index }}">
                                    <div class="col-xl-4">
                                        <label class="form-label">Weight From (gm)</label>
                                        <input type="number" name="weight_ranges[{{ $index }}][weight_from]" 
                                            class="form-control @error('weight_ranges.'.$index.'.weight_from') is-invalid @enderror" 
                                            value="{{ old('weight_ranges.'.$index.'.weight_from', $range['weight_from'] ?? '') }}"
                                            placeholder="0" min="1" step="1">
                                        @error('weight_ranges.'.$index.'.weight_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-4">
                                        <label class="form-label">Weight To (gm)</label>
                                        <input type="number" name="weight_ranges[{{ $index }}][weight_to]" 
                                            class="form-control @error('weight_ranges.'.$index.'.weight_to') is-invalid @enderror" 
                                            value="{{ old('weight_ranges.'.$index.'.weight_to', $range['weight_to'] ?? '') }}"
                                            placeholder="100" min="1" step="1">
                                        @error('weight_ranges.'.$index.'.weight_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-3">
                                        <label class="form-label">Delivery Charge (₹)</label>
                                        <input type="number" name="weight_ranges[{{ $index }}][delivery_charge]" 
                                            class="form-control @error('weight_ranges.'.$index.'.delivery_charge') is-invalid @enderror" 
                                            value="{{ old('weight_ranges.'.$index.'.delivery_charge', $range['delivery_charge'] ?? '') }}"
                                            placeholder="20" min="1" step="1">
                                        @error('weight_ranges.'.$index.'.delivery_charge')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-sm btn-danger remove-weight-range" 
                                            {{ count($existingRanges) <= 1 ? 'disabled' : '' }}>
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Default weight range row -->
                            <div class="row gy-3 weight-range-row" data-index="0">
                                <div class="col-xl-4">
                                    <label class="form-label">Weight From (gm)</label>
                                    <input type="number" name="weight_ranges[0][weight_from]" 
                                        class="form-control @error('weight_ranges.0.weight_from') is-invalid @enderror" 
                                        placeholder="0" min="1" step="1">
                                    @error('weight_ranges.0.weight_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-xl-4">
                                    <label class="form-label">Weight To (gm)</label>
                                    <input type="number" name="weight_ranges[0][weight_to]" 
                                        class="form-control @error('weight_ranges.0.weight_to') is-invalid @enderror" 
                                        placeholder="100" min="1" step="1">
                                    @error('weight_ranges.0.weight_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-xl-3">
                                    <label class="form-label">Delivery Charge (₹)</label>
                                    <input type="number" name="weight_ranges[0][delivery_charge]" 
                                        class="form-control @error('weight_ranges.0.delivery_charge') is-invalid @enderror" 
                                        placeholder="20" min="1" step="1">
                                    @error('weight_ranges.0.delivery_charge')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-xl-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-sm btn-danger remove-weight-range" disabled>
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>                    

                    <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
                        <button type="submit"
                            class="btn btn-primary">{{ isset($state) ? 'Update' : 'Submit' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get the highest existing index to continue from
    const existingRows = document.querySelectorAll('.weight-range-row');
    let weightRangeIndex = existingRows.length > 0 ? Math.max(...Array.from(existingRows).map(row => parseInt(row.dataset.index))) : 0;
    const container = document.getElementById('weightRangesContainer');
    
    // Add weight range functionality
    document.getElementById('addWeightRange').addEventListener('click', function() {
        weightRangeIndex++;
        
        const newRow = document.createElement('div');
        newRow.className = 'row gy-3 weight-range-row';
        newRow.setAttribute('data-index', weightRangeIndex);
        
        newRow.innerHTML = `
            <div class="col-xl-4">
                <label class="form-label">Weight From (gm)</label>
                <input type="number" name="weight_ranges[${weightRangeIndex}][weight_from]" 
                    class="form-control" placeholder="0" min="1" step="1">
            </div>
            <div class="col-xl-4">
                <label class="form-label">Weight To (gm)</label>
                <input type="number" name="weight_ranges[${weightRangeIndex}][weight_to]" 
                    class="form-control" placeholder="100" min="1" step="1">
            </div>
            <div class="col-xl-3">
                <label class="form-label">Delivery Charge (₹)</label>
                <input type="number" name="weight_ranges[${weightRangeIndex}][delivery_charge]" 
                    class="form-control" placeholder="20" min="1" step="1">
            </div>
            <div class="col-xl-1 d-flex align-items-end">
                <button type="button" class="btn btn-sm btn-danger remove-weight-range">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        `;
        
        container.appendChild(newRow);
        updateRemoveButtons();
    });
    
    // Remove weight range functionality
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-weight-range')) {
            e.target.closest('.weight-range-row').remove();
            updateRemoveButtons();
        }
    });
    
    // Update remove buttons state
    function updateRemoveButtons() {
        const rows = container.querySelectorAll('.weight-range-row');
        const removeButtons = container.querySelectorAll('.remove-weight-range');
        
        // Enable/disable remove buttons based on number of rows
        removeButtons.forEach(button => {
            button.disabled = rows.length <= 1;
        });
    }
    
    // Initialize remove buttons state
    updateRemoveButtons();
    
    // Add real-time validation for overlapping ranges
    container.addEventListener('input', function(e) {
        if (e.target.matches('input[name*="weight_from"], input[name*="weight_to"]')) {
            validateWeightRanges();
        }
    });
    
    // Prevent form submission if there are overlapping ranges
    document.getElementById('stateForm').addEventListener('submit', function(e) {
        if (hasOverlappingRanges()) {
            e.preventDefault();
            alert('Please fix overlapping weight ranges before submitting the form.');
        }
    });
    
    // Function to validate weight ranges for overlaps
    function validateWeightRanges() {
        const rows = container.querySelectorAll('.weight-range-row');
        const ranges = [];
        
        // Collect all weight ranges
        rows.forEach((row, index) => {
            const weightFrom = parseInt(row.querySelector('input[name*="weight_from"]').value) || 0;
            const weightTo = parseInt(row.querySelector('input[name*="weight_to"]').value) || 0;
            
            if (weightFrom > 0 && weightTo > 0) {
                ranges.push({
                    index: index,
                    from: weightFrom,
                    to: weightTo,
                    fromInput: row.querySelector('input[name*="weight_from"]'),
                    toInput: row.querySelector('input[name*="weight_to"]')
                });
            }
        });
        
        // Clear previous validation errors
        container.querySelectorAll('.weight-range-error').forEach(error => error.remove());
        container.querySelectorAll('.is-invalid').forEach(input => {
            input.classList.remove('is-invalid');
        });
        
        // Check for overlaps
        for (let i = 0; i < ranges.length; i++) {
            for (let j = i + 1; j < ranges.length; j++) {
                const range1 = ranges[i];
                const range2 = ranges[j];
                
                if (rangesOverlap(range1, range2)) {
                    showOverlapError(range1, range2, i + 1, j + 1);
                }
            }
        }
    }
    
    // Function to check if two ranges overlap
    function rangesOverlap(range1, range2) {
        return Math.max(range1.from, range2.from) < Math.min(range1.to, range2.to);
    }
    
    // Function to show overlap error
    function showOverlapError(range1, range2, index1, index2) {
        // Add error class to inputs
        range1.fromInput.classList.add('is-invalid');
        range1.toInput.classList.add('is-invalid');
        range2.fromInput.classList.add('is-invalid');
        range2.toInput.classList.add('is-invalid');
        
        // Add error message
        const errorMessage = `Range ${index1} (${range1.from}-${range1.to}gm) overlaps with Range ${index2} (${range2.from}-${range2.to}gm)`;
        
        // Add error message after the first range
        if (!range1.fromInput.parentNode.querySelector('.weight-range-error')) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'weight-range-error text-danger small mt-1';
            errorDiv.textContent = errorMessage;
            range1.fromInput.parentNode.appendChild(errorDiv);
        }
    }
    
    // Function to check if there are any overlapping ranges
    function hasOverlappingRanges() {
        const rows = container.querySelectorAll('.weight-range-row');
        const ranges = [];
        
        // Collect all weight ranges
        rows.forEach((row, index) => {
            const weightFrom = parseInt(row.querySelector('input[name*="weight_from"]').value) || 0;
            const weightTo = parseInt(row.querySelector('input[name*="weight_to"]').value) || 0;
            
            if (weightFrom > 0 && weightTo > 0) {
                ranges.push({
                    from: weightFrom,
                    to: weightTo
                });
            }
        });
        
        // Check for overlaps
        for (let i = 0; i < ranges.length; i++) {
            for (let j = i + 1; j < ranges.length; j++) {
                if (rangesOverlap(ranges[i], ranges[j])) {
                    return true;
                }
            }
        }
        
        return false;
    }
});
</script>