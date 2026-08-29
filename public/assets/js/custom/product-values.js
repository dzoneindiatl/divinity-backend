function productValues(ele) {
    let $this = ele;
    optionValue = $this.val();

    if (optionValue == 1) {
        $('.product-color-value').removeClass('d-none');
        $('.product-size-value').addClass('d-none');
    } else if (optionValue == 2) {
        $('.product-color-value').addClass('d-none');
        $('.product-size-value').removeClass('d-none');
    } else {
        $('.product-color-value').addClass('d-none');
        $('.product-size-value').addClass('d-none');
    }
}

function editProductValues(ele) {
    let $this = ele;
    optionValue = $this.find('option:selected').val();

    if (optionValue == 1) {
        $('.product-color-value').removeClass('d-none');
        $('.product-size-value').addClass('d-none');
    } else if (optionValue == 2) {
        $('.product-color-value').addClass('d-none');
        $('.product-size-value').removeClass('d-none');
    } else {
        $('.product-color-value').addClass('d-none');
        $('.product-size-value').addClass('d-none');
    }
}