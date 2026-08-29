$('[name=meta_keywords]').tagify();

function displaySlug(ele) {
    let $this = ele;
        category = $this.val(),
        lowercaseString = category.toLowerCase(),
        replacedString = lowercaseString.replace(/ /g, '-');
        slug_data = replacedString;
        // $('.category-slug').text('Slug : https://jaipurjewelleryhouse.com/' + replacedString);
        $('.category-slug').val(slug_data);
        $('.SlugBox').show();

}

function editDisplaySlug(ele) {

    let $this = ele;
        editCategory = $this.val(),

        editLowercaseString = editCategory.toLowerCase(),
        editReplacedString = editLowercaseString.replace(/ /g, '-');
        slug_data = editReplacedString;
        // $('.edit-category-slug').text('Slug : https://jaipurjewelleryhouse.com/' + editReplacedString);
        $('.edit-category-slug').val(slug_data);
        $('.oldSlug').hide();
        $('.newSlug').show();
}

function changeTaxType(taxType) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: "POST",
        url: getCategoryTaxRateListRoute,
        data: { tax_type: taxType, tax_option: taxOption },
        success: function (response) {
            $('#tax_rate').html('<option value="0" disabled>Select Tax Rate</option>')
            .select2({
                data: $.map(response.data, function (item) {
                    return {
                        text: item.tax_rate,
                        id: item.id,
                    }
                }),
            });
            if(taxType == "flat"){
                $('#flat').trigger('change');
            }else if(taxType == "floating"){
                $('#floating').trigger('change');
            }
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
}