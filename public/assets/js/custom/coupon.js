$('.select2').select2();
$(".checkbox").click(function () {
    if ($(".checkbox").is(':checked')) {
        $(this).parent().find('option').prop("selected", "selected");
        $(this).parent().find('option').trigger("change");
        $(this).parent().find('option').click();

    } else {
        $("select").val("");
        $(this).parent().find('option').removeAttr("selected", "selected");
        $(this).parent().find('option').trigger("change");
    }
});
$('#coupon_type').change(function () {
    couponType();
})
$(document).ready(function () {
    couponType();
})
function couponType() {
    if ($("#coupon_type").val() == 'private') {
        $('#user_type').trigger('change');
        $('.customer_name').show();
    } else {
        $('.customer_name').hide();
    }
}
$('#is_unlimited').change(function () {
    unlimited();
})

unlimited();
function unlimited() {
    if ($("#is_unlimited").val() == '1') {
        $('.available_coupons').hide();
    } else {
        $('.available_coupons').show();
    }
}

$('#category').change(function () {
    getSubCategory();
})
function getSubCategory() {
    var cat_id = $("#category").val();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    if (cat_id) {
        $.ajax({
            type: "POST",
            url: subCategoryRoute,
            data: { cat_id: cat_id },
            success: function (response) {
                if (response.data.length > 0) {
                    console.log(response.data);
                    $('#sub_category_data').show();
                    $('#sub_category')
                        .html('<option value="0">All</option>')
                        .select2({
                            data: $.map(response.data, function (item) {
                                return {
                                    text: item.name,
                                    id: item.id,
                                }
                            })
                        })
                        .val(subCatId)   // set the selected value
                        .trigger('change');
                    $('#customer_name').trigger('change');
                } else {
                    $('#sub_category_data').hide();
                }
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    } else {

        $('#sub_category_data').hide();

    }
}


$('#user_type').on('change', function () {
    getUserTypeList();
});

function getUserTypeList() {
    var couponCustomers = $("#selectedCustomer").val();
    console.log(couponCustomers);
    var user_type = $("#user_type").val();
    // Set CSRF token (for Laravel-style apps)
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: "POST",
        url: userTypeRoute, // make sure this variable is defined properly
        data: { user_type: user_type },
        success: function (response) {
            // Clear existing options
            $('#customer_name').empty();
            // Add new options
            $.each(response.data, function (index, item) {
                let isActive = item.is_active ? " (A)" : " (Ina)";
                let isSelected = selectedUsers.includes(item.id); // Check if item.id is in the allowed list
                $('#customer_name').append(
                    $('<option>', {
                        value: item.id,
                        text: item.name + isActive,
                        selected: isSelected
                    })
                );
            });
            // ✅ Set selected values
            // if (customerids) {
            //     $('#customer_name').val(customerids).trigger('change');
            // }
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
}



