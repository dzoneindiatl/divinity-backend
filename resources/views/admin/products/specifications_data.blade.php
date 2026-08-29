@if(!empty(($specificationsData)))
<div id="kt_repeater_specifications" class="ml-7">
    <div class="form-group row" id="kt_repeater_specifications">
        <div data-repeater-list="specificationDataArr" class="col-lg-12">
            @foreach($specificationsData as $dataKey => $dataVal)
            @if(!empty($dataVal['name']) && !empty($dataVal['id']) )
            <div data-repeater-item class="form-group row align-items-center mb-3">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="name" class="form-label">Specification</label><span class="text-danger">
                        </span>
                        <input type="text" name="name"
                            class="form-control form-control-solid form-control-lg  @error('name') is-invalid @enderror"
                            value="{{!empty($dataVal['name']) ? $dataVal['name'] : ''}}" readonly>
                        <input type="hidden" name="specification_id"
                            value="{{!empty($dataVal['id']) ? $dataVal['id'] : ''}}">

                    </div>
                </div>
                <div class="col-md-5 select2-error">
                    <label for="specification_values" class="form-label"><span class="text-danger">
                        </span>Values</label>
                    <select class="js-example-placeholder-single js-states form-control specificationValuesSelect" multiple="multiple"
                        name="specification_values" >
                        <!-- <option value="" selected>None</option> -->
                        @forelse ($dataVal['specification_values'] as $value)
                        <option value="{{ $value['id'] }}" {{!empty($productSpecifications) && in_array($value['id'],$productSpecifications) ? 'selected' : ''}}>{{ $value['name'] }}</option>
                        @empty
                        <option value="" selected>No Data found</option>
                        @endforelse
                    </select>

                </div>

                <div class="col-md-2">
                    <a href="javascript:;" data-repeater-delete=""
                        class="btn btn-sm font-weight-bolder btn-danger-light">
                        <i class="la la-trash-o"></i>
                    </a>
                </div>
            </div>
            @endif



            @endforeach
        </div>
    </div>
</div>
@endif

<script>
var KTFormRepeater = function() {

    var demo1 = function() {

        $('#kt_repeater_specifications').repeater({
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

$('.specificationValuesSelect').select2({
        // tags: true,
        placeholder: 'Select Values',
        tokenSeparators: [',', ' '], // Separate tags by comma or space
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