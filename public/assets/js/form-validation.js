$("#categoryForm").validate({
    rules: {
        name: "required",
    },
    
    errorPlacement: function (label, element) {
        label.addClass("mt-1 text-danger");
        // label.appendTo(element.parents("div.level-error-show"));
        label.insertAfter(element);
    }
});

$("#editCategoryForm").validate({
    rules: {
        name: "required",
    },
    errorPlacement: function (label, element) {
        label.addClass("mt-1 text-danger");
        // label.appendTo(element.parents("div.level-error-show"));
        label.insertAfter(element);
    }
});

$("#subCategoryForm").validate({
    rules: {
        category_id: "required",
        sub_category: "required",
    },
    errorPlacement: function (label, element) {
        label.addClass("mt-1 text-danger");
        if (element.is("select")) {
            label.appendTo(element.parents("div.select2-error"));
        } else {
            label.insertAfter(element);
        }
    }
});

$("#editsubCategoryForm").validate({
    rules: {
        category_id: "required",
        sub_category: "required",
    },
    errorPlacement: function (label, element) {
        label.addClass("mt-1 text-danger");
        if (element.is("select")) {
            label.appendTo(element.parents("div.select2-error"));
        } else {
            label.insertAfter(element);
        }
    }
});


$("#childCateogryForm").validate({
    rules: {
        category_id: "required",
        sub_category_id: "required",
        child_category: "required",
    },
    errorPlacement: function (label, element) {
        label.addClass("mt-1 text-danger");
        if (element.is("select")) {
            label.appendTo(element.parents("div.select2-error"));
        } else {
            label.insertAfter(element);
        }
    }
});

$("#editChildCateogryForm").validate({
    rules: {
        category_id: "required",
        sub_category_id: "required",
        child_category: "required",
    },
    errorPlacement: function (label, element) {
        label.addClass("mt-1 text-danger");
        if (element.is("select")) {
            label.appendTo(element.parents("div.select2-error"));
        } else {
            label.insertAfter(element);
        }
    }
});

$("#createProductForm").validate({
    rules: {
        category_id: "required",
        name: "required",
        short_description: {
            required: true,
            maxlength: 150,
        },
        description: "required",
        front_image: "required",
        back_image: "required",
        "product_images[]": "required",
        "product_videos[]": "required",
        price: "required",
    },
    messages: {
        short_description: {
            maxlength: "Maximum 150 characters allowed" // Display a custom message
        }
    },
    errorPlacement: function (label, element) {
        label.addClass("mt-1 text-danger");
        if (element.is("select")) {
            label.appendTo(element.parents("div.select2-error"));
        } else {
            label.insertAfter(element);
        }
    }
});

$("#editProductForm").validate({
    rules: {
        category_id: "required",
        name: "required",
        description: "required",
        price: "required",
    },
    errorPlacement: function (label, element) {
        label.addClass("mt-1 text-danger");
        if (element.is("select")) {
            label.appendTo(element.parents("div.select2-error"));
        } else {
            label.insertAfter(element);
        }
    }
});

$("#productOptionsForm").validate({
    rules: {
        name: "required",
    },
    errorPlacement: function (label, element) {
        label.addClass("mt-1 text-danger");
        // label.appendTo(element.parents("div.level-error-show"));
        label.insertAfter(element);
    }
});

$("#editProductOptionsForm").validate({
    rules: {
        name: "required",
    },
    errorPlacement: function (label, element) {
        label.addClass("mt-1 text-danger");
        // label.appendTo(element.parents("div.level-error-show"));
        label.insertAfter(element);
    }
});


$("#brandForm").validate({
    rules: {
        name: "required",
    },
    
    errorPlacement: function (label, element) {
        label.addClass("mt-1 text-danger");
        // label.appendTo(element.parents("div.level-error-show"));
        label.insertAfter(element);
    }
});