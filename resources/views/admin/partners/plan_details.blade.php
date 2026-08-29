<h6 class="mb-3">Plan Details</h6>
@if(!empty(($planDetailsData)))
<?php $iterationCount = 0; ?>

<div id="kt_repeater_1" class="ml-7">
    <div class="form-group row" id="kt_repeater_1">
        <div data-repeater-list="planDetailsArr" class="col-lg-12">
            @foreach($planDetailsData as $dataKey => $dataVal)
            @if(!empty($dataVal['sales_from']) && !empty($dataVal['sales_to']) && !empty($dataVal['type']) &&
            !empty($dataVal['amount']) )
            <div data-repeater-item class="form-group row align-items-center mb-0">
                <div class="col-md-2 mb-3">
                    <div class="form-group">
                        <label for="sales_from" class="form-label">Sales From</label><span class="text-danger">
                        </span>
                        <input type="number" name="sales_from"
                            class="form-control form-control-solid form-control-lg  @error('sales_from') is-invalid @enderror"
                            value="{{!empty($dataVal['sales_from']) ? $dataVal['sales_from'] : ''}}">

                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="form-group">
                        <label for="sales_to" class="form-label">Sales To</label><span class="text-danger">
                        </span>
                        <input type="number" name="sales_to"
                            class="form-control form-control-solid form-control-lg  @error('sales_to') is-invalid @enderror"
                            value="{{!empty($dataVal['sales_to']) ? $dataVal['sales_to'] : ''}}">

                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="form-group">
                        <label for="type" class="form-label">Type</label><span class="text-danger">
                        </span>
                        <select class=" form-control" name="type" id="type">
                            <option value="" selected>Select Type</option>
                            <option value="flat"
                                {{(!empty($dataVal['type']) && $dataVal['type'] == 'flat') ? 'selected' : ''}}>Flat
                            </option>
                            <option value="percentage"
                                {{(!empty($dataVal['type']) && $dataVal['type'] == 'percentage') ? 'selected' : ''}}>
                                Percentage</option>

                        </select>

                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="form-group">
                        <label for="amount" class="form-label">Amount</label><span class="text-danger">
                        </span>
                        <input type="number" name="amount"
                            class="form-control form-control-solid form-control-lg  @error('amount') is-invalid @enderror"
                            value="{{!empty($dataVal['amount']) ? $dataVal['amount'] : ''}}">

                    </div>
                </div>



                <div class="col-md-2">
                    @if($iterationCount == 0)
                    <a href="javascript:;" data-repeater-create=""
                        class="btn btn-sm font-weight-bolder btn btn-primary-light btn-border-down">
                        <i class="la la-plus"></i>Add More Details
                    </a>
                    <a href="javascript:;" data-repeater-delete=""
                        class="btn btn-sm font-weight-bolder btn btn-danger-light btn-border-down"
                        style="display:none;">
                        <i class="la la-trash-o"></i>Delete Details
                    </a>
                    @else
                    <a href="javascript:;" data-repeater-create=""
                        class="btn btn-sm font-weight-bolder btn btn-primary-light btn-border-down"
                        style="display:none;">
                        <i class="la la-plus"></i>Add More Details
                    </a>
                    <a href="javascript:;" data-repeater-delete=""
                        class="btn btn-sm font-weight-bolder btn btn-danger-light btn-border-down">
                        <i class="la la-trash-o"></i>Delete Details
                    </a>
                    @endif
                </div>
            </div>
            @endif


            <?php $iterationCount++; ?>
            @endforeach
        </div>
    </div>
</div>

@else
<div id="kt_repeater_1" class="ml-7">
    <div class="form-group row" id="kt_repeater_1">

        <div data-repeater-list="planDetailsArr" class="col-lg-12">
            <div data-repeater-item class="form-group row align-items-center mb-0">
                <div class="col-md-2 mb-3">
                    <div class="form-group">
                        <label for="sales_from" class="form-label">Sales From</label><span class="text-danger">
                        </span>
                        <input type="number" name="sales_from"
                            class="form-control form-control-solid form-control-lg  @error('sales_from') is-invalid @enderror"
                            value="{{!empty($dataVal['sales_from']) ? $dataVal['sales_from'] : ''}}">

                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="form-group">
                        <label for="sales_to" class="form-label">Sales To</label><span class="text-danger">
                        </span>
                        <input type="number" name="sales_to"
                            class="form-control form-control-solid form-control-lg  @error('sales_to') is-invalid @enderror"
                            value="{{!empty($dataVal['sales_to']) ? $dataVal['sales_to'] : ''}}">

                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="form-group">
                        <label for="type" class="form-label">Type</label><span class="text-danger">
                        </span>
                        <select class=" form-control" name="type" id="type">
                            <option value="" selected>Select Type</option>
                            <option value="flat"
                                {{(!empty($dataVal['type']) && $dataVal['type'] == 'flat') ? 'selected' : ''}}>Flat
                            </option>
                            <option value="percentage"
                                {{(!empty($dataVal['type']) && $dataVal['type'] == 'percentage') ? 'selected' : ''}}>
                                Percentage</option>

                        </select>

                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="form-group">
                        <label for="amount" class="form-label">Amount</label><span class="text-danger">
                        </span>
                        <input type="number" name="amount"
                            class="form-control form-control-solid form-control-lg  @error('amount') is-invalid @enderror"
                            value="{{!empty($dataVal['amount']) ? $dataVal['amount'] : ''}}">

                    </div>
                </div>



                <div class="col-md-2">
                    <a href="javascript:;" data-repeater-create=""
                        class="btn btn-sm font-weight-bolder btn btn-primary-light btn-border-down">
                        <i class="la la-plus"></i>Add More Details
                    </a>
                    <a href="javascript:;" data-repeater-delete=""
                        class="btn btn-sm font-weight-bolder btn btn-danger-light btn-border-down"
                        style="display:none;">
                        <i class="la la-trash-o"></i>Delete Details
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
@endif

<script>
var KTFormRepeater = function() {

    var demo1 = function() {


        $('#kt_repeater_1').repeater({
            initEmpty: false,

            defaultValues: {

            },

            show: function() {

                var $item = $(this);
                // Get the counter value (assuming you have a counter element)
                var counter = $item.parent().find('[data-repeater-item]').index($item);
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

jQuery(document).ready(function() {
    KTFormRepeater.init();
});
</script>