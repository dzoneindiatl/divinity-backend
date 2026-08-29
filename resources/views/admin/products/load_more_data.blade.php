<style>
    .price-popup-link {
        cursor: pointer;
        color: #0d6efd;
        font-weight: 600;
        text-decoration: underline;
        transition: all 0.2s ease;
    }

    .price-popup-link:hover {
        color: #084298;
        background-color: rgba(13,110,253,0.08);
        padding: 4px 6px;
        border-radius: 4px;
    }
    /* Toggle Switch */
    .switch {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 24px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .3s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
    }

    /* ON state */
    input:checked + .slider {
        background-color: #198754; /* green */
    }

    input:checked + .slider:before {
        transform: translateX(22px);
    }

    /* Rounded */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>

@if ($productLsit->isNotEmpty())
    @php
        $i = $offset + 1;
    @endphp
    @forelse($productLsit as $product)
        <tr class="list-data-row items-inner" data-id="{{ $product->id }}" data-total-count="{{ $totalResults }}">
            <!-- <td><i class="ri-drag-move-2-line move-line "></i></td> -->
            <td>{{ $i++ }} <input type="checkbox" class="product-checkbox" value="{{ $product->id }}"
                    name="product[{{ $product->id }}]" onclick="event.stopPropagation();"> </td>
            
            @php 
            $activeVarientId = activeVarientByProductId($product->id);
            $activeVarientID = 0;
            $firstImage ='';
            $secondImage = '';
            if(!empty($activeVarientId)){
                $firstImage = getActiveFrontImg($product->id,$activeVarientId);
                $secondImage  = getActiveBackImg($product->id,$activeVarientId);
            } else {
                $firstImage = getActiveFrontImg($product->id,$product->id);
                $secondImage  = getActiveBackImg($product->id,$product->id);
            }
            if (!empty($firstImage)){ 
            @endphp
            <td><a href="{{ env('WEBSITE_URL').  $product->sku . '/' . productSlug($product->short_description) }}"
                    target="_blank">

                    <img src="{{ env('WEBSITE_URL') . 'uploads/products/' . $firstImage }}"
                        height="70px" width="70px" style="border-radius: 10%" class="if">
                </a>
            </td>
            @php } elseif(!empty($product->frontProductImage) || !empty($product->firstProductImage)){  @endphp
            <td>
                <img src="https://commons.wikimedia.org/wiki/File:No_Image_Available.jpg" height="70px"
                    width="70px" style="border-radius: 10%" class="elseif">
            </td>
                <!-- <td><a href="{{ env('WEBSITE_URL'). '/'. $product->sku . '/' . productSlug($product->short_description) }}"
                        target="_blank">

                        <img src="{{ $product->frontProductImage?->graphic ?? $product->firstProductImage?->graphic }}"
                            height="70px" width="70px" style="border-radius: 10%" class="t123">
                    </a>
                </td> -->
             @php } else {   @endphp
                <td>
                    <img src="https://commons.wikimedia.org/wiki/File:No_Image_Available.jpg" height="70px"
                        width="70px" style="border-radius: 10%" class="else">
                </td>
            @php }  @endphp

            <td class="move-line">
                <a href="{{ env('WEBSITE_URL') . $product->sku . '/' . productSlug($product->short_description) }}"
                    target="_blank">
                    <span class="product-name-tooltip showViewIcon" title="{{ $product->name ?? 'N/A' }}">
                        {{ Str::limit($product->name ?? 'N/A', 20) }}
                    </span>
                    <span class="product-name-tooltip" title="{{ $product->name ?? 'N/A' }}" style="color: brown;">
                        SKU - {{ $product->sku }}
                    </span>
                </a>
            </td>   
            <td class="move-line">
                Category - {{ $product->mainCategory->name ?? 'Not available' }}
                <br>
                Sub Category - {{ $product->mainSubCategory->name ?? 'Not available' }}
            </td>
            <!-- <td class="move-line">MRP - INR {{ $product->buying_price ?? 0 }}.00 <br> Selling Price INR -
                {{ $product->selling_price ?? 0 }}.00</td> -->

            <td class="move-line">
                <a href="javascript:void(0)"
                class="open-price-modal price-popup-link"
                data-product-id="{{ $product->id }}"
                title="Click to view all prices">
                    <i class="bi bi-eye me-1"></i>
                    MRP - INR {{ $product->buying_price ?? 0 }}.00 <br>
                    Selling Price INR - {{ $product->selling_price ?? 0 }}.00
                </a>
            </td> 
            <td class="move-line">
                Best Seller
                <input type="checkbox"
                    class="toggle-checkbox"
                    data-field="best_seller"
                    data-product-id="{{ $product->id }}"
                    {{ $product->best_seller ? 'checked' : '' }}>
                <br>

                New Arrivals
                <input type="checkbox"
                    class="toggle-checkbox"
                    data-field="is_new_arrivals"
                    data-product-id="{{ $product->id }}"
                    {{ $product->is_new_arrivals ? 'checked' : '' }}>
                <br>

                Is Active
                <input type="checkbox"
                    class="toggle-checkbox"
                    data-field="is_active"
                    data-product-id="{{ $product->id }}"
                    {{ $product->is_active ? 'checked' : '' }}>
            </td>
            <?php 
            $checked = 'checked';
            if($product->in_stock==1){
                $checked = '';
            }
            ?>
            <td>
                <label class="switch">
                    <input type="checkbox"
                        class="toggle-checkbox"
                        data-field="in_stock"
                        data-product-id="{{ $product->id }}"
                        {{ $checked }}>
                    <span class="slider round"></span>
                </label>
            </td>
            <td class="move-line publish-status 
                {{ $product->is_active ? '' : 'text-danger' }}"
                id="publish-{{ $product->id }}">
                
                {{ $product->is_active ? 'Published' : 'Unpublished' }}
            </td>
            <td class="move-line">
                <div class="input-group qty-group" data-product-id="{{ $product->id }}">
                    {{ getTotalvarientQty($product->id) }} &nbsp;&nbsp;

                    <!-- <button type="button" class="btn btn-outline-success update-qty">
                        <i class="ri-save-line"></i> {{-- Use any icon library: RemixIcon, FontAwesome, etc. --}}
                    </button> -->
                </div>
            </td>
            <!-- <td class="move-line">
                <div class="input-group variant-group" data-product-id="{{ $product->id }}">
                    <a class="btn btn-outline-success productVariantModal">{{ $product->qty }}</a>
                </div>
            </td> -->
            <td class="move-line">{{   ( getTotalvarientQty($product->id) - getOrderedVarientQty($product->id) ) }}</td>
            <td class="move-line">
                <div class="hstack gap-2 flex-wrap">

                    @can('edit_product')
                        <a href="{{ route('admin-product-create-new-product', ['token' => encrypt($product->id)]) }}"
                            class="btn btn-info" title="Edit Product"><i class="ri-edit-line"></i></a>
                    @endcan

                    @can('copy_product')
                        <a href="{{ route('admin-product-copy', ['id' => $product->id]) }}"
                            id="edit-product-{{ $product->id }}" class="btn btn-info" title="Copy">
                            <i class="ri-file-copy-line"></i>
                        </a>
                    @endcan
                    @can('view_product')
                        <a href="{{ env('WEBSITE_URL').'product/' . $product->name . '/' . $product->sku }}"
                        target="_blank" class="btn btn-warning" title="View Product"><i class="ri-eye-line"></i></a>
                    @endcan
                    @can('delete_product')
                        <a href="{{ route('admin-product-delete', ['token' => encrypt($product->id)]) }}"
                            class="btn btn-danger"><i class="ri-delete-bin-5-line"></i></a>
                    @endcan
                    @can('view_review_product')
                        <a href="{{ route('admin-product-review', ['token' => encrypt($product->id)]) }}"
                            class="btn btn-info" title="View Review">View Review</a>
                    @endcan
                </div>
            </td>
        </tr>
    @empty
    @endforelse
@else
    <tr class="noresults-row">
        <td colspan="8" style="text-align: center;">No results found.</td>
    </tr>
@endif


<!-- Price Modal -->
<div class="modal fade" id="priceModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Product & Variant Prices</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="priceModalBody">
                <div class="text-center">Loading...</div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).on('click', '.update-qty', function () {
           let productId = $(this).closest('.qty-group').data('product-id');
           let qty = $(this).closest('.qty-group').find('.qty-input').val();
            $.ajax({
                url: '{{ route("admin-product-update.product.qty") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    qty: qty
                },
                success: function (response) {
                     Swal.fire({
                            icon: 'success',
                            title: 'Quantity updated successfully.',
                            showConfirmButton: true,
                        });
                    //alert(response.message);
                }
            });
        });

    $(document).on('change', '.toggle-checkbox', function () {
        let productId = $(this).data('product-id');
        let field = $(this).data('field');
        let value = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: "{{ route('admin-product-update.product.feature') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                product_id: productId,
                field: field,
                value: value
            },
            success: function (response) {

                if (field === 'is_active') {

                    let publishEl = $('#publish-' + productId);

                    if (value === 1) {
                        publishEl
                            .text('Published')
                            .removeClass('text-danger');
                    } else {
                        publishEl
                            .text('Unpublished')
                            .addClass('text-danger');
                    }
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Updated successfully.',
                    showConfirmButton: true,
                });
            },

            error: function () {
                alert('Something went wrong!');
            }
        });
    });
    
    
</script>

<script>
    $(document).on('click', '.open-price-modal', function () {
        let productId = $(this).data('product-id');

        let modalEl = document.getElementById('priceModal');
        let modal = new bootstrap.Modal(modalEl);
        modal.show();

        $('#priceModalBody').html('Loading...');

        $.ajax({
            url: "{{ url('product/prices') }}/" + productId,
            type: "GET",
            success: function (response) {
                $('#priceModalBody').html(response);
            },
            error: function () {
                $('#priceModalBody').html('Error loading data');
            }
        });
    });
</script>



