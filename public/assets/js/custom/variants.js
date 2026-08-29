var KTFormRepeater = function() {

    var demo1 = function() {

        $('#kt_repeater_1').repeater({
            initEmpty: false,

            defaultValues: {

            },

            show: function() {
                $elem = $(this).slideDown();
                var $item = $(this);
                // Get the counter value (assuming you have a counter element)
                var counter = $item.parent().find('[data-repeater-item]').index($item);
                if($("#name").val().toLowerCase() === 'color'){
                    $elem.find('.colorPickerIn').show();
                    intializeColorPicker(counter);
                }else{
                    $elem.find('.colorPickerIn').hide();
                }
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
    $('.colorCodeInputHidden').each(function(index,html) {
        intializeColorPicker(index);
    });

});

function intializeColorPicker(index = 0){
    const colorPickerElement = document.querySelector('input[name="dataArr['+index+'][color_code]"]');
    const colorPickerElementHidden = document.querySelector('input[name="dataArr['+index+'][color_code_hidden]"]');
    console.log(colorPickerElement)
    const pickr = Pickr.create({
        el: colorPickerElement,
        theme: 'classic', // or 'monolith', or 'nano'

        swatches: [
            'rgba(244, 67, 54, 1)',
            'rgba(233, 30, 99, 0.95)',
            'rgba(156, 39, 176, 0.9)',
            'rgba(103, 58, 183, 0.85)',
            'rgba(63, 81, 181, 0.8)',
            'rgba(33, 150, 243, 0.75)',
            'rgba(3, 169, 244, 0.7)',
            'rgba(0, 188, 212, 0.7)',
            'rgba(0, 150, 136, 0.75)',
            'rgba(76, 175, 80, 0.8)',
            'rgba(139, 195, 74, 0.85)',
            'rgba(205, 220, 57, 0.9)',
            'rgba(255, 235, 59, 0.95)',
            'rgba(255, 193, 7, 1)'
        ],

        components: {

            // Main components
            preview: true,
            opacity: true,
            hue: true,

            // Input / output Options
            interaction: {
                hex: true,
                rgba: false,
                hsla: false,
                hsva: false,
                cmyk: false,
                input: true,
                clear: true,
                save: true
            }
        }
    });
    pickr.on('save', (color,instance) => {
        colorPickerElementHidden.value = color.toHEXA().toString();
    });
    pickr.on('init', instance => {
        instance.setColor(colorPickerElementHidden.value);
        
    })
}