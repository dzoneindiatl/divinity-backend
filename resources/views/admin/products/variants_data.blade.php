@if(!empty(($variantsData)))
<div id="kt_repeater_2" class="ml-7">
    <div class="form-group row" id="kt_repeater_2">
        <div data-repeater-list="variantsDataArr" class="col-lg-12">

            <div data-repeater-item class="form-group row align-items-center mb-3">
                <div class="col-md-5 select2-error">
                    <label for="variant_id" class="form-label"><span class="text-danger">*
                        </span>Main Variant</label>
                    <select class="js-example-placeholder-single js-states form-control variantSelect"
                        name="variant_id" data-action="{{ route('admin-product-variant-values-list') }}">
                        <option value="" selected>Select Variant</option>
                        @forelse ($variantsData as $value)
                        <option value="{{ $value['id'] }}" {{(!empty($productVariants[0]) && $productVariants[0] == $value['id']) ? 'selected' : ''}}>{{ $value['name'] }}</option>
                        @empty
                        <option value="" selected>No Data found</option>
                        @endforelse
                    </select>

                </div>
                <div class="col-md-5 select2-error">
                    <label for="variant_values" class="form-label"><span class="text-danger">*
                        </span>Values</label>
                    <select class="js-example-placeholder-single js-states form-control variantValuesSelect"
                        multiple="multiple" name="variant_values">


                    </select>

                </div>

                <div class="col-md-2">
                    <a href="javascript:;" data-repeater-delete=""
                        class="btn btn-sm font-weight-bolder btn-danger-light">
                        <i class="la la-trash-o"></i>
                    </a>
                </div>
            </div>

            <div data-repeater-item class="form-group row align-items-center mb-3">
                <div class="col-md-5 select2-error">
                    <label for="variant_id" class="form-label"><span class="text-danger">
                        </span>Second Variant</label>
                    <select class="js-example-placeholder-single js-states form-control variantSelect"
                        name="variant_id" data-action="{{ route('admin-product-variant-values-list') }}">
                        <option value="" selected>Select Variant</option>
                        @forelse ($variantsData as $value)
                        <option value="{{ $value['id'] }}" {{(!empty($productVariants[1]) && $productVariants[1] == $value['id']) ? 'selected' : ''}}>{{ $value['name'] }}</option>
                        @empty
                        <option value="" selected>No Data found</option>
                        @endforelse
                    </select>

                </div>
                <div class="col-md-5 select2-error">
                    <label for="variant_values" class="form-label"><span class="text-danger">
                        </span>Values</label>
                    <select class="js-example-placeholder-single js-states form-control variantValuesSelect"
                        multiple="multiple" name="variant_values">


                    </select>

                </div>

                <div class="col-md-2">
                    <a href="javascript:;" data-repeater-delete=""
                        class="btn btn-sm font-weight-bolder btn-danger-light">
                        <i class="la la-trash-o"></i>
                    </a>
                </div>
            </div>

            

            <button class="btn btn-primary mb-3 createVariantBtn" data-action="next" data-current-tab="variantsTab" data-current-action="first_step"
                data-tab-action="createVariant">Create Variant</button>
        </div>
    </div>
</div>
@endif

@if(!empty($productVariants))
<script>
    jQuery(document).ready(function() {
    setTimeout(() => {
        
        $('.variantSelect').trigger('change');

    }, 200); 
});
</script>
@if(!empty($hasProductVariantCombinationsOtherThanMainProductOnEditPage))
<script>
    jQuery(document).ready(function() {
    setTimeout(() => {
        
        $('.createVariantBtn').trigger('click');
        
    }, 1200);
});
</script>
@endif
@endif
<script>
var KTFormRepeater = function() {

    var demo1 = function() {

        $('#kt_repeater_2').repeater({
            initEmpty: false,

            defaultValues: {

            },

            show: function() {
                $elem = $(this).slideDown();
                $elem.find('.btn-primary-light').remove();
                $elem.find('.btn-danger-light').show();

            },

            hide: function(deleteElement) {
                $(this).slideUp(deleteElement);
            },
            isFirstItemUndeletable: true
        });


    }

    return {
        // public functions
        init: function() {
            demo1();
        }
    };
}();
$('.variantSelect').on('change', function (e) {
    let $this = $(this),
        url = $this.attr('data-action'),
        variantId = $this.val() ?? "";

 
    $.ajax({
        url: url + "?variant_id=" + variantId,
        method: "GET",
        success: function (response) {
            if(response.variantValues && response.variantValues.length > 0) {
                $this.parent('.col-md-5').parent('.form-group').find('.variantValuesSelect').html('<option value="">Select Values</option>')
                .select2({
                    data: $.map(response.variantValues, function (item) {
                        return {
                            text: item.name,
                            id: item.id,
                        }
                    }),
                });

                if (response.productVariantValues && response.productVariantValues.length > 0) {
                    $this.parent('.col-md-5').parent('.form-group').find('.variantValuesSelect').val(response.productVariantValues).trigger('change');
                }
                
            } 
           
        },
        error: function (jqXHR, exception) {
            console.log("error");
        }
    });
});

$('.variantSelect').select2({
    // tags: true,
    placeholder: 'Select Variant',
    tokenSeparators: [',', ' '], 
});

$('.variantValuesSelect').select2({
    // tags: true,
    placeholder: 'Select Values',
    tokenSeparators: [',', ' '], 
    createTag: function(params) {
        return {
            id: params.term,
            text: params.term,
            newTag: true
        };
    }
});

jQuery(document).ready(function() {
    KTFormRepeater.init();


});
</script>