<div id="step2" <?php if(@$product_type==1){ echo "style='display:none'"; } ?> >
    <div id="variantContainer">
        @if(count($selectedVariants) > 0)
            @foreach($selectedVariants as $i => $data)
                <div class="variant-card card p-3 mb-3 shadow-sm">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Select Variant</label>
                            <select class="form-control variantSelect" name="variant[]">
                                <option value="">Select Variant</option>
                                @foreach($variantsData as $variant)
                                    <option {{ $data['variant_id'] == $variant->id ? 'selected' : '' }} value="{{ $variant->id }}">{{ $variant->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">Variant Values</label>
                            <select name="variant_values[{{ $i }}][]" 
                                    class="form-control variantValuesSelect product_select2" 
                                    multiple 
                                    data-selected-values='@json($data['variant_values'])'>
                                <option value="">Select Variant Value</option>
                            </select>
                        </div>

                        @if($i > 0)
                        <div class="col-md-1 d-flex align-items-end" >
                            <button type="button" class="btn btn-danger removeVariant">✕</button>
                        </div>
                        @endif
                      
                    </div>
                </div>
            @endforeach
        @else 
            <div class="variant-card card p-3 mb-3 shadow-sm">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Select Variant</label>
                        <select class="form-control variantSelect" name="variant[]">
                            <option value="">Select Variant</option>
                            @foreach($variantsData as $variant)
                                <option value="{{ $variant->id }}">{{ $variant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label">Variant Values</label>
                        <select name="variant_values[0][]" class="form-control variantValuesSelect product_select2" multiple>
                            <option value="">Select Variant Value</option>
                        </select>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="mb-3 add-more-variant">
        <button type="button" class="btn btn-success" id="addVariant">+ Add Variant</button>
    </div>

    {{-- <div class="mb-3 text-end btn_add_new">
        <button type="button" class="btn btn-primary prevBtn" onclick="onclickPrevious('step1')">Preview</button>
        <button type="button" class="btn btn-primary nextBtn" onclick="submitProductStep2()">Save & Continue</button>
    </div> --}}
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    window.availableVariants = `{!! $variantsData->map(fn($v) => "<option value='{$v->id}'>".e($v->name)."</option>")->implode('') !!}`;
</script>

<script>
    var product_type = {{ $product_type ?? 0 }}
    
$(function () {
    // Skipped step 2 case of simple product
    if(product_type==1){
        $('.nextBtn').trigger('click');
    }

    // // Active step 2 linktab &  stepdiv 
    // $(".tab-pane").removeClass("active");
    // $("#tab2").addClass("active");

    // $(".nav-link").removeClass("active");
    // $('#step2').addClass("active");


    // **FIX 4: Initialize index based on how many variants are already on the page.**
    let index = $('#variantContainer .variant-card').length;
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    function initSelect2() {
        $('.product_select2').select2({ placeholder: "Select Variant Value" });
    }

    function getSelectedVariantIds() {
        return $('.variantSelect').map((_, el) => $(el).val()).get().filter(Boolean);
    }

    function updateVariantSelectOptions() {
        const selectedIds = getSelectedVariantIds();
        $('.variantSelect').each(function () {
            const currentVal = $(this).val();
            $(this).find('option').each(function () {
                const optVal = $(this).val();
                $(this).prop('disabled', optVal && optVal !== currentVal && selectedIds.includes(optVal));
            });
        });
        $('#addVariant').prop('disabled', selectedIds.length >= $('.variantSelect option').length - 1);
    }

    function loadVariantValues(variantSelect) {
        const variantId = $(variantSelect).val();
        const valuesSelect = $(variantSelect).closest('.variant-card').find('.variantValuesSelect');

        valuesSelect.empty().trigger('change');

        if (!variantId) {
            // Return a resolved promise if there's no ID so .done() can still be called
            return $.Deferred().resolve().promise();
        }

        // **FIX 5: Return the AJAX promise so we can chain .done() to it.**
        return $.post("{{ route('admin-product-variant-values') }}", { id: variantId, _token: csrfToken })
            .done(res => {
                if (res.success && res.data.length > 0) {
                    res.data.forEach(v => {
                        valuesSelect.append(new Option(v.name, v.id, false, false));
                    });
                } else {
                    valuesSelect.append(new Option("No values found", ""));
                }
                valuesSelect.trigger('change.select2');
            })
            .fail(() => valuesSelect.append(new Option("Error loading values", "")).trigger('change.select2'));
    }

    function canAddMore() {
        const lastCard = $('#variantContainer .variant-card').last();
        if (!lastCard.length) {
            $('#addVariant').show();
            return;
        }
        const filled = lastCard.find('.variantSelect').val() && lastCard.find('.variantValuesSelect').val()?.length;
        $('#addVariant').toggle(!!filled);
    }

    $('#addVariant').click(function () {
        const variantOptions = `<option value="">Select Variant</option>` + window.availableVariants;
        const newCardHTML = `
        <div class="variant-card card p-3 mb-3 shadow-sm">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Select Variant</label>
                    <select class="form-control variantSelect" name="variant[]">${variantOptions}</select>
                </div>
                <div class="col-md-7">
                    <label class="form-label">Variant Values</label>
                    <select name="variant_values[${index}][]" class="form-control variantValuesSelect product_select2" multiple>
                        <option value="">Select Variant Value</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger removeVariant">✕</button>
                </div>
            </div>
        </div>`;
        $('#variantContainer').append(newCardHTML);
        index++;
        initSelect2();
        updateVariantSelectOptions();
        canAddMore();
    });

    $(document)
        .on('change', '.variantSelect', function () {
            loadVariantValues(this).done(() => {
                 canAddMore();
            });
            updateVariantSelectOptions();
        })
        .on('change', '.variantValuesSelect', canAddMore)
        .on('click', '.removeVariant', function () {
            $(this).closest('.variant-card').remove();
            updateVariantSelectOptions();
            canAddMore();
        });

    // --- INITIALIZATION ON PAGE LOAD ---

    initSelect2();

    // **FIX 6: Loop through pre-rendered cards and load their values.**
    $('#variantContainer .variant-card').each(function() {
        const $card = $(this);
        const $variantSelect = $card.find('.variantSelect');
        const $valuesSelect = $card.find('.variantValuesSelect');
        const selectedValueIds = $valuesSelect.data('selected-values');

        // Check if there is a variant selected and there are values to pre-select
        if ($variantSelect.val() && selectedValueIds && selectedValueIds.length > 0) {
            // Load the values, and in the callback, set the selected options
            loadVariantValues($variantSelect).done(function() {
                // Set the value using the IDs from the data attribute
                $valuesSelect.val(selectedValueIds);
                // Trigger change for Select2 to update its display
                $valuesSelect.trigger('change.select2');
                // Check if we can add more variants now
                canAddMore();
            });
        }
    });

    updateVariantSelectOptions();
    canAddMore();
});


function submitProductStep2() {

    const $btn = $('.nextBtn');
    const originalHtml = $btn.html();
    // The submit function remains the same, it should work correctly.
    const formData = new FormData();
    formData.append('product_id', $('#product_id').val());
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    var variantSelected = false;
    $('.variant-card').each((i, el) => {
        const variantId = $(el).find('.variantSelect').val();
        const values = $(el).find('.variantValuesSelect').val() || [];
        if (variantId) {  
            variantSelected = true;          // Use the correct naming convention for arrays in FormData
            formData.append(`variant[${i}]`, variantId);
            values.forEach(v => formData.append(`variant_values[${i}][]`, v));
        }
    });
    if(product_type == 2 && variantSelected == false){
        // alert('Variant is required for configurable product.');
        Swal.fire('Error','Variant is required for configurable product. Please map the variant with selected collection or category.','error')
        return;
    }
    $.ajax({
        url: "{{ route('admin-product-save.step2') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        // 🔽 BEFORE AJAX SEND
        beforeSend: function () {
            $btn.prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Processing...
            `);
        },
        success: res => {
            if (res.success) {
                if (res.success && res.step==2) {
                    $('#tab2').html(res.varient);
                } else if (res.success && res.step==3) {
                    $('#tab3').html(res.mainView);
                } else if (res.success && res.step==4) {
                    $('#tab4').html(res.seoView);
                } else {
                    alert(res.message || "Something went wrong");
                }

            } else {
                alert(res.message || 'Something went wrong.');
            }


        },
        error: xhr => {
            const msg = xhr.status === 422
                ? Object.values(xhr.responseJSON.errors).map(e => e[0]).join('\n')
                : 'Server error';
            alert(msg);
        },
        complete: function () {
            $btn.prop('disabled', false).html(originalHtml);
        }
    });
}

function onclickPrevious(value) {
    const $btn = $('.prevBtn');
    const originalHtml = $btn.html();
    const formData = new FormData();
    formData.append('product_id', $('#product_id').val());
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    formData.append('step', "step1");
    $.ajax({
        url: "{{ route('admin-product-previousStep') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $btn.prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Processing...
            `);
        },
        success: res => {
            if (res.success) {
                $('#tab2 script').remove();
                $('#formTabs .nav-link').removeClass('active');
                $('#formTabs .nav-link[data-tab="tab1"]').addClass('active');
                $('#tab1').html("");
                $('#tab2').html("").html(res.mainView);
            } else {
                alert(res.message || 'Something went wrong.');
            }
        },
        error: xhr => {
            const msg = xhr.status === 422
                ? Object.values(xhr.responseJSON.errors).map(e => e[0]).join('\n')
                : 'Server error';
            alert(msg);
        },

        complete: function () {
            $btn.prop('disabled', false).html(originalHtml);
        }
    });
}


</script>