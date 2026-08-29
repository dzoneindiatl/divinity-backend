$('[name=meta_keywords]').tagify();

function displaySlug(ele) {
    let $this = ele;
        childCategory = $this.val(),
        lowercaseString = childCategory.toLowerCase(),
        replacedString = lowercaseString.replace(/ /g, '-');

        $('.child-category-slug').text('Slug : /' + replacedString);
}

function editDisplaySlug(ele) {
    let $this = ele;
        editChildCategory = $this.val(),
        editLowercaseString = editChildCategory.toLowerCase(),
        editReplacedString = editLowercaseString.replace(/ /g, '-');

        $('.edit-child-category-slug').text('Slug : /' + editReplacedString);
}

//Sub categories list
$('#category_id').on('change', function (e) {
    let $this = $(this),
        url = $this.attr('data-action'),
        categoryId = $this.val() ?? "";

    // if (categoryId == "") {
        $('#sub_category_id').html('<option value="">None</option>');
    // }
    $.ajax({
        url: url + "?category_id=" + categoryId,
        method: "GET",
        success: function (response) {
            $('#sub_category_id').html('<option value="">None</option>')
                .select2({
                    data: $.map(response.subCategories, function (item) {
                        return {
                            text: item.name,
                            id: item.id,
                        }
                    }),
                });
        },
        error: function (jqXHR, exception) {
            console.log("error");
        }
    });
});

$('#edit_category_id').on('change', function (e) {
    let $this = $(this),
        url = $this.attr('data-action'),
        categoryId = $this.val() ?? "";

    // if (categoryId == "") {
        $('#edit_sub_category_id').html('<option value="">None</option>');
    // }
    $.ajax({
        url: url + "?category_id=" + categoryId,
        method: "GET",
        success: function (response) {
            $('#edit_sub_category_id').html('<option value="">None</option>')
                .select2({
                    data: $.map(response.subCategories, function (item) {
                        return {
                            text: item.name,
                            id: item.id,
                        }
                    }),
                });
        },
        error: function (jqXHR, exception) {
            console.log("error");
        }
    });
});