@foreach ($groupedCombinations as $primaryId => $combos)
    @php
        $primaryValue = \App\Models\VariantValue::find($primaryId);
         $mainVariantValueId = \App\Models\ProductVariantValue::where('product_id', $product_id)
        ->where('is_main', '1')
        ->value('variant_value_id');

        $images = \App\Models\ProductGraphics::where('product_id', $product_id)->where('variant_id',$primaryId)->where('graphic_type','image')->get();
        $videos = \App\Models\ProductGraphics::where('product_id', $product_id)->where('variant_id',$primaryId)->where('graphic_type','video')->get();
        $imagePath = config('constant.PRODUCT_IMAGE_PATH');
        
    @endphp
    <div class="card mb-4 shadow-sm variant_group_row" id="variant_{{ $primaryId }}">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <input class="form-check-input me-2" type="radio" required
                       name="main_variant" value="{{ $primaryId }}"
                       id="variant_radio_{{ $primaryId }}"
                         {{ $primaryId == $mainVariantValueId ? 'checked' : '' }}
                       >

                <label for="variant_radio_{{ $primaryId }}" class="fw-bold mb-0 me-2">
                    Primary Variant: {{ $primaryValue->name }} — {{ count($combos) }} combinations
                </label>

                <button type="button" class="btn btn-sm btn-outline-success"
                        onclick="openRestoreModal('{{ $primaryId }}')">
                    <i class="ri-add-line"></i>
                </button>
            </div>
        </div>


        <div class="row align-items-start mb-4">
            <div class="col-auto m-3">
                <button type="button" class="btn btn-outline-secondary image_upload_button"
                        data-bs-toggle="modal" data-bs-target="#uploadModal_{{ $primaryId }}">
                    <div class="text-center">
                        <div class="fs-2 fw-bold">+</div>
                        <div class="small">Add Images & Video</div>
                    </div>
                </button>
            </div>

            <div class="col">
                {{-- Image Previews --}}
                <div class="image-thumbnails-1 d-flex flex-wrap gap-2 mb-2 mt-2">
                @foreach($images as $image)
                        <div class="image-preview-container position-relative" style="width: 100px; height: 100px;">
                           <img src="{{ $imagePath . $image->graphic }}" alt="Product Image"
                                class="rounded border w-100 h-100" style="object-fit: cover;">
                            <button type="button"
                class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0 delete-image"
                data-id="{{ $image->id }}"
                style="width: 22px; height: 22px; line-height: 1;">×</button>
                        </div>
                    @endforeach
                </div>

                <div class="image-thumbnails d-flex flex-wrap gap-2 mb-2 mt-2">
                </div>

                {{-- Video Previews --}}
                <div class="video-thumbnails-1 d-flex flex-wrap gap-2 mt-2">
                    @foreach($videos as $video)
                        <div class="image-preview-container position-relative" style="width: 100px; height: 100px;">
                            <video class="rounded border w-100 h-100" style="object-fit: cover;" controls>
                                <source src="{{ asset($imagePath . $video->graphic) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                             <button type="button"
                class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0 delete-image"
                data-id="{{ $video->id }}"
                style="width: 22px; height: 22px; line-height: 1;">×</button>
                        </div>
                    @endforeach
                </div>
                <div class="video-thumbnails d-flex flex-wrap gap-2 mt-2">
                </div>
            </div>
            
        </div>

        <!-- Upload Modal -->
        <div class="modal fade" id="uploadModal_{{ $primaryId }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content p-3">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Files for Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Images</label>
                            <input type="file" 
                                name="variant_images[{{ $primaryId }}][]" 
                                accept="image/*" 
                                multiple
                                onchange="previewImages(event, {{ $primaryId }})" 
                                class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Video</label>
                            <input type="file" 
                                name="variant_video[{{ $primaryId }}]" 
                                accept="video/*"
                                onchange="previewVideo(event, {{ $primaryId }})" 
                                class="form-control">
                        </div>
                        <hr/>
                        <h6 class="fw-bold mb-2">Image Preview</h6>
                        <div id="preview_images_{{ $primaryId }}" class="d-flex flex-wrap gap-2"></div>

                        <h6 class="fw-bold mt-4 mb-2">Video Preview</h6>
                        <div id="preview_video_{{ $primaryId }}" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
            </div>
        </div>


        

        <div class="card-body">
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped variant-combo-table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Variant Name</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Discount Type</th>
                            <th>Discount</th>
                             <th>Sale Price</th>
                            <th>Qty</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="variant_body_{{ $primaryId }}">
                        @foreach ($combos as $combo)
                            @php
                                $ids = explode('_', $combo);
                                $variantValues = \App\Models\VariantValue::whereIn('id', $ids)->get()->keyBy('id');
                                $names = collect($ids)->map(fn($id) => $variantValues[$id]->name ?? '')->toArray();
                                $variantName = implode(' ', $names);
                                $variantSKU = strtolower(implode('_', $names));
                                
                                
                                $valueIds = array_map('intval', $ids);
                                $savedCombo = \App\Models\ProductVariantCombination::where('product_id', $product_id)
                                    ->where('combination_id',json_encode($valueIds))
                                    ->first();

                                $product = \App\Models\Product::where('id',$product_id)->first();

                            @endphp

                            
                            <tr id="variant_combo_{{ $combo }}" 
                                data-combo-id="{{ $combo }}" 
                                data-variant-name="{{ $variantName }}" 
                                data-variant-sku="SKU_{{ $variantSKU }}">

                                <td>
                                    <span class="v_name">{{ $variantName }}</span>
                                    <input type="hidden" name="variant_name[]" value="{{ $variantName }}">
                                    <input type="hidden" name="combo[]" value="{{ $combo }}">
                                    <input type="hidden" name="variant_id[]" value="{{ $primaryId }}">
                                </td>

                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text dynamic-sku-prefix">{{$product->sku ?? 'SKU_'}}</span>
                                        <input 
                                            type="text" 
                                            name="variant_sku[]" 
                                            class="v_input_sku form-control" 
                                            value="{{ $savedCombo->sku ?? strtolower($variantSKU) }}">
                                    </div>
                                </td>

                                <td>
                                    <input type="number" min="0" name="variant_price[]" 
                                        value="{{ $savedCombo->price ?? '' }}" 
                                        class="v_input_price form-control" placeholder="Price">
                                </td>

                                <td>
                                    <select name="variant_discount_type[]" class="discount_type_popup form-control">
                                        <option value="" {{ ($savedCombo->discount_type ?? '') === '' ? 'selected' : '' }}>None</option>
                                        <option value="flat" {{ ($savedCombo->discount_type ?? '') === 'flat' ? 'selected' : '' }}>Flat</option>
                                        <option value="percentage" {{ ($savedCombo->discount_type ?? '') === 'percentage' ? 'selected' : '' }}>%</option>
                                    </select>
                                </td>

                                <td>
                                    <input type="number" min="0" name="variant_discount[]" 
                                        value="{{ $savedCombo->discount ?? '' }}" 
                                        class="discount_popup form-control" placeholder="Discount">
                                </td>

                                <td>
                                    <input type="number" min="0" name="variant_sale_price[]" 
                                        value="{{ $savedCombo->selling_price ?? '' }}" 
                                        class="v_input_sale_price form-control" placeholder="Sale Price" readonly>
                                </td>

                                <td>
                                    <input type="number" min="0" name="variant_qty[]" 
                                        value="{{ $savedCombo->qty ?? '' }}" 
                                        class="v_input_quantity form-control" placeholder="Qty">
                                </td>

                                <td>
                                    <a href="javascript:void(0)" class="text-danger" onclick="deleteVariantRow('{{ $primaryId }}', '{{ $combo }}', this)">
                                        <i class="ri-delete-bin-5-line fs-5"></i>
                                    </a>
                                </td>
                            </tr>


                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endforeach

<!-- Restore Modal (Global) -->
<div class="modal fade" id="restoreVariantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content p-3">
            <div class="modal-header">
                <h5 class="modal-title">Restore Deleted Variants</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="restoreVariantList" class="list-group"></div>
            </div>
            <div class="modal-footer">
                <span class="btn btn-success" onclick="restoreSelectedVariants()">Restore Selected</span>
            </div>
        </div>
    </div>
</div>


<div style="display: none;">
    @foreach ($groupedDeleted as $primaryId => $combinations)
        <table>
            <tbody id="restore_pool_{{ $primaryId }}">
                @foreach ($combinations as $combo)
                    @php
                        $ids = explode('_', $combo);
                        $variantValues = \App\Models\VariantValue::whereIn('id', $ids)->get()->keyBy('id');
                        $names = collect($ids)->map(fn($id) => $variantValues[$id]->name ?? '')->toArray();
                        $variantName = implode(' ', $names);
                        $variantSKU = strtolower(implode('_', $names));
                    @endphp

                    <tr id="variant_combo_{{ $combo }}" data-combo-id="{{ $combo }}"
                        data-variant-name="{{ $variantName }}" data-variant-sku="SKU_{{ $variantSKU }}"
                        style="display: none;">
                        <td>
                            <span class="v_name">{{ $variantName }}</span>

                            <input type="hidden" name="combo[]" value="{{ $combo }}">
                            <input type="hidden" name="variant_name[]" value="{{ $variantName }}">
                            <input type="hidden" name="variant_id[]" value="{{ $primaryId }}">
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text dynamic-sku-prefix">SKU_</span>
                                <input type="text" name="variant_sku[]" class="v_input_sku form-control" value="{{ $variantSKU }}">
                            </div>
                        </td>
                        <td><input type="number" min="0" name="variant_price[]" class="v_input_price form-control" value="0.00"></td>
                        <td>
                            <select name="variant_discount_type[]" class="discount_type_popup form-control">
                                <option value="" selected>None</option>
                                <option value="flat">Flat</option>
                                <option value="percentage">%</option>
                            </select>
                        </td>
                        <td><input type="number" min="0" name="variant_discount[]" class="discount_popup form-control" value=""></td>
                        <td><input type="number" min="0" name="variant_sale_price[]" class="v_input_sale_price form-control" value="0" readonly></td>
                        <td><input type="number" min="0" name="variant_qty[]" class="v_input_quantity form-control" value="0"></td>
                        <td>
                            <a href="javascript:void(0)" class="text-danger" onclick="deleteVariantRow('{{ $primaryId }}', '{{ $combo }}', this)">
                                <i class="ri-delete-bin-5-line fs-5"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</div>


<script>
window.currentPrimaryId = '';
function deleteVariantRow(primaryId, comboId, el) {
    const tableBody = document.getElementById(`variant_body_${primaryId}`);
    const visibleRows = tableBody.querySelectorAll('tr:not([style*="display: none"])');

    // Prevent deleting the last visible row
    if (visibleRows.length <= 1) {
        alert("At least one variant combination must remain.");
        return;
    }

    const row = document.getElementById(`variant_combo_${comboId}`);
    if (!row) return;

    const restoreList = document.getElementById(`restore_pool_${primaryId}`) || createRestorePool(primaryId);
    restoreList.appendChild(row);
    row.style.display = 'none';
}

function openRestoreModal(primaryId) {
    window.currentPrimaryId = primaryId;

    const pool = document.getElementById(`restore_pool_${primaryId}`);
    const listContainer = document.getElementById('restoreVariantList');
    listContainer.innerHTML = '';

    if (pool) {
        const rows = pool.querySelectorAll('tr');
        rows.forEach(row => {
            const comboId = row.getAttribute('data-combo-id');
            const name = row.getAttribute('data-variant-name');
            const sku = row.getAttribute('data-variant-sku');

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.className = 'form-check-input me-2';
            checkbox.value = comboId;

            const label = document.createElement('label');
            label.className = 'form-check-label';
            label.textContent = `${name} — ${sku}`;

            const wrapper = document.createElement('div');
            wrapper.className = 'form-check mb-2';
            wrapper.appendChild(checkbox);
            wrapper.appendChild(label);

            listContainer.appendChild(wrapper);
        });
    }

    new bootstrap.Modal(document.getElementById('restoreVariantModal')).show();
}

function restoreSelectedVariants() {
    const checkboxes = document.querySelectorAll('#restoreVariantList input[type="checkbox"]:checked');
    const tbody = document.getElementById(`variant_body_${window.currentPrimaryId}`);
    const pool = document.getElementById(`restore_pool_${window.currentPrimaryId}`);

    checkboxes.forEach(cb => {
        const row = pool.querySelector(`tr[data-combo-id="${cb.value}"]`);
        if (row) {
            row.style.display = '';
            tbody.appendChild(row);
        }
    });

    bootstrap.Modal.getInstance(document.getElementById('restoreVariantModal')).hide();
}

function createRestorePool(primaryId) {
    const pool = document.createElement('tbody');
    pool.id = `restore_pool_${primaryId}`;
    pool.style.display = 'none';
    document.body.appendChild(pool);
    return pool;
}

function calculateSellingModifyPrice(row) {
    const price = parseFloat(row.find('.v_input_price').val()) || 0;
    const discount = parseFloat(row.find('.discount_popup').val()) || 0;
    const discountType = row.find('.discount_type_popup').val();

    let sellingPrice = 0;

    if (discountType === 'flat') {
        sellingPrice = price - discount;
    } else if (discountType === 'percentage') {
        sellingPrice = price - ((price * discount) / 100);
    } else {
        sellingPrice = price;
    }

    // Prevent negative value, allow decimal up to 2 places
    sellingPrice = Math.max(sellingPrice, 0);

    row.find('.v_input_sale_price').val(sellingPrice.toFixed(2));
}

$(document).on('input change', '.v_input_price, .discount_popup, .discount_type_popup', function () {
    const row = $(this).closest('tr');
    calculateSellingModifyPrice(row);
});

$(document).on('input change', '.v_input_price, .discount_popup, .discount_type_popup', function () {
    const row = $(this).closest('tr');
    calculateSellingModifyPrice(row);
});


$(document).on('blur', 'input[type="number"]', function () {
    let val = parseFloat($(this).val());
    if (val < 0 || isNaN(val)) {
        $(this).val(0);
    }
});

$(document).on('keydown', 'input[type="number"]', function (e) {
    // Block "-" key
    if (e.key === '-' || e.keyCode === 189) {
        e.preventDefault();
    }
});




</script>

<script>
    $(document).on('click', '.delete-image', function () {
        const imageId = $(this).data('id');
        const container = $(this).closest('.image-preview-container');

        if (!confirm("Are you sure you want to delete this image?")) return;

        $.ajax({
            url: "{{ route('admin-product-fileDelete') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: imageId
            },
            success: function (response) {
                if (response.success) {
                    container.remove();
                } else {
                    alert(response.message || 'Failed to delete image.');
                }
            },
            error: function () {
                alert('Server error. Please try again.');
            }
        });
    });
</script>


<style>
/* Table base */
.variant-combo-table th,
.variant-combo-table td {
    vertical-align: middle;
    padding: 0.75rem;
    font-size: 14px;
}

/* Group styling */
.variant_group_row {
    border: 1px solid #dee2e6;
    border-radius: 8px;
}

.variant_group_row .card-header {
    background-color: #f8f9fa !important;
    border-bottom: 1px solid #dee2e6;
    padding: 0.75rem 1rem;
}

/* Input styles */
.variant-combo-table input[type="text"],
.variant-combo-table input[type="number"],
.variant-combo-table select {
    border-radius: 0.375rem;
    border: 1px solid #ced4da;
    padding: 0.45rem 0.75rem;
    font-size: 14px;
    height: 38px;
    box-shadow: none;
    transition: all 0.2s ease-in-out;
}

.variant-combo-table input[type="text"]:focus,
.variant-combo-table input[type="number"]:focus,
.variant-combo-table select:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, 0.25);
}

/* Input group prefix */
.variant-combo-table .input-group-text.dynamic-sku-prefix {
    background-color: #f1f3f5;
    border-radius: 0.375rem 0 0 0.375rem;
    border: 1px solid #ced4da;
    padding: 0.45rem 0.75rem;
    font-size: 14px;
}

/* Adjust input next to prefix */
.variant-combo-table .input-group input {
    border-radius: 0 0.375rem 0.375rem 0;
}

/* Actions column */
.variant-combo-table td a.text-danger {
    font-size: 18px;
    transition: 0.2s;
}

.variant-combo-table td a.text-danger:hover {
    color: #dc3545;
    transform: scale(1.1);
}

/* Select dropdown appearance */
.variant-combo-table select {
    background-color: #fff;
    background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='%23666' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1em;
    appearance: none;
    padding-right: 2.5rem;
}
</style>
