@foreach ($groupedCombinations as $primaryId => $combos)
    @php
        $primaryValue = \App\Models\VariantValue::find($primaryId);
        $mainVariantValueId = \App\Models\ProductVariantValue::where('product_id', $productId)->where('is_main', '1')->value('variant_value_id');
        $images = \App\Models\ProductGraphics::where('product_id', $productId)->where('variant_id',$primaryId)->where('graphic_type','image')->get();
        $videos = \App\Models\ProductGraphics::where('product_id', $productId)->where('variant_id',$primaryId)->where('graphic_type','video')->get();
        $imagePath = config('constant.PRODUCT_IMAGE_PATH');
    @endphp
    <div class="card mb-4 shadow-sm variant_group_row" id="variant_{{ $primaryId }}">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <input class="form-check-input me-2" type="radio" required name="main_variant" dissabled value="{{ $primaryId }}" id="variant_radio_{{ $primaryId }}" {{ $primaryId == $mainVariantValueId ? 'checked' : '' }}>
                <label for="variant_radio_{{ $primaryId }}" class="fw-bold mb-0 me-2">
                    Primary Variant: {{ $primaryValue->name }} — {{ count($combos) }} combinations
                </label>
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
                                $savedCombo = \App\Models\ProductVariantCombination::where('product_id', $productId)->where('combination_id',json_encode($valueIds))->first();
                                $product = \App\Models\Product::where('id',$productId)->first();
                            @endphp
                            <tr id="variant_combo_{{ $combo }}" data-product-id="{{ $productId }}" data-combo-id="{{ $combo }}" data-variant-name="{{ $variantName }}" data-variant-sku="SKU_{{ $variantSKU }}">
                                <td>
                                    <span class="v_name">{{ $variantName }}</span>
                                    <input type="hidden" name="variant_name[]" value="{{ $variantName }}">
                                    <input type="hidden" name="combo[]" value="{{ $combo }}">
                                    <input type="hidden" name="variant_id[]" value="{{ $primaryId }}">
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text dynamic-sku-prefix">{{$product->sku ?? 'SKU_'}}</span>
                                        <input type="text" name="variant_sku[]" class="v_input_sku form-control" value="{{ $savedCombo->sku ?? strtolower($variantSKU) }}">
                                    </div>
                                </td>
                                <td>
                                    <input type="number" min="0" name="variant_price[]" value="{{ $savedCombo->price ?? '' }}" class="v_input_price form-control" placeholder="Price">
                                </td>
                                <td>
                                    <select name="variant_discount_type[]" class="discount_type_popup form-control">
                                        <option value="" {{ ($savedCombo->discount_type ?? '') === '' ? 'selected' : '' }}>None</option>
                                        <option value="flat" {{ ($savedCombo->discount_type ?? '') === 'flat' ? 'selected' : '' }}>Flat</option>
                                        <option value="percentage" {{ ($savedCombo->discount_type ?? '') === 'percentage' ? 'selected' : '' }}>%</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" min="0" name="variant_discount[]" value="{{ $savedCombo->discount ?? '' }}" class="discount_popup form-control" placeholder="Discount">
                                </td>
                                <td>
                                    <input type="number" min="0" name="variant_sale_price[]" value="{{ $savedCombo->selling_price ?? '' }}" class="v_input_sale_price form-control" placeholder="Sale Price" readonly>
                                </td>
                                <td>
                                    <input type="number" min="0" name="variant_qty[]" value="{{ $savedCombo->qty ?? '' }}" class="v_input_quantity form-control" placeholder="Qty">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endforeach
<script>
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
    $(document).on('blur', '.v_input_quantity', function () {
        let qty = $(this).val(); // Get input value
        let comboId = $(this).closest('tr').data('combo-id');
        let productId = $(this).closest('tr').data('product-id');
        $.ajax({
            url: '{{ route("admin-product-update.product.qty") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                combo_id: comboId,
                qty: qty
            },
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Quantity updated successfully.',
                    showConfirmButton: true,
                });
            }
        });
    });
</script>
