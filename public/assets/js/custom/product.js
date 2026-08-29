// // Dropzone for reimburse receipt files
// let reimburseReceiptsGeneralConfig = {},
//     reimburseReceiptsDropzoneConfig = {},
//     reimburseReceiptsSessionStorageConfig = {};

// reimburseReceiptsGeneralConfig.display_existing_files = false;
// reimburseReceiptsDropzoneConfig.max_files = 5;
// reimburseReceiptsDropzoneConfig.dict_max_files_exceeded_message = "You can not uploade more than 5 file";
// reimburseReceiptsDropzoneConfig.accepted_files = ".png,.jpg,.jpeg,.bmp";

// reimburseReceiptsSessionStorageConfig.session_key_datatype = Array;

// let $receiptsElement = $('.products_images'),
//     uploadedFilesArray = $receiptsElement.attr('data-uploaded-files-array') ?? JSON.stringify([]),
//     uploadFileUrl = $receiptsElement.attr('data-file-upload-action') ?? '',
//     removeFileUrl = $receiptsElement.attr('data-file-remove-action') ?? '',
//     uploadFilePath = $receiptsElement.attr('data-file-upload-path') ?? '';
//     // console.log(uploadFileUrl,removeFileUrl);

// uploadFileUrl += "?path=" + uploadFilePath;
// existingFiles = JSON.parse(uploadedFilesArray);
// // console.log(uploadFileUrl);

// reimburseReceiptsDropzoneConfig.upload_file_url = uploadFileUrl;
// reimburseReceiptsDropzoneConfig.remove_file_url = removeFileUrl;


// reimburseReceiptsGeneralConfig.dropzone_init_element = $('#product_images_upload');
// reimburseReceiptsSessionStorageConfig.session_key = 'productImagesFiles';

// // console.log(reimburseReceiptsGeneralConfig,reimburseReceiptsDropzoneConfig,reimburseReceiptsSessionStorageConfig);

// if ($('#product_images_upload').length) {
//     dropzoneInit(reimburseReceiptsGeneralConfig, reimburseReceiptsDropzoneConfig, reimburseReceiptsSessionStorageConfig);
// }

// Dropzone.autoDiscover = false;



// var dropzone = new Dropzone('#image-upload', {

//     thumbnailWidth: 200,

//     maxFilesize: 5,

//     acceptedFiles: ".jpeg,.jpg,.png,.gif"

// });


$('[name=meta_keywords]').tagify();

//Sub categories list
$('#category_id').on('change', function (e) {
    let $this = $(this),
        url = $this.attr('data-action'),
        categoryId = $this.val() ?? "";

    // if (categoryId == "") {
        $('#sub_category_id').html('<option value="">None</option>');
        $('#child_category_id').html('<option value="">None</option>');
    // }
    $.ajax({
        url: url + "?category_id=" + categoryId,
        method: "GET",
        success: function (response) {
            if(response.subCategories && response.subCategories.length > 0) {
                $('#subcategory-filter').show();
                $('#sub_category_id').html('<option value="">None</option>')
                .select2({
                    data: $.map(response.subCategories, function (item) {
                        return {
                            text: item.name,
                            id: item.id,
                        }
                    }),
                });

                if(selectedSubcategory){
                    $('#sub_category_id').val(selectedSubcategory).trigger('change');
                }
            } else {
                $('#subcategory-filter').hide();
            }

        },
        error: function (jqXHR, exception) {
            console.log("error");
        }
    });
});

//Child categories list
$('#sub_category_id').on('change', function (e) {
    let $this = $(this),
        url = $this.attr('data-action')
        subCategoryId = $this.val() ?? "";

    $.ajax({
        url: url + "?sub_category_id=" + subCategoryId,
        method: "GET",
        success: function (response) {
            if(response.childCategories && response.childCategories.length > 0) {
                $('#child-category-filter').show();
                $('#child_category_id').html('<option value="">None</option>')
                .select2({
                    data: $.map(response.childCategories, function (item) {
                        return {
                            text: item.name,
                            id: item.id,
                        }
                    }),
                });
                if(selectedChildcategory){

                    $('#child_category_id').val(selectedChildcategory).trigger('change');
                }
            } else {
                $('#child-category-filter').hide();
            }

        },
        error: function (jqXHR, exception) {
            console.log("error");
        }
    });
});

//Sub categories list
$('#edit_category_id').on('change', function (e) {
    let $this = $(this),
        url = $this.attr('data-action'),
        categoryId = $this.val() ?? "";

        $('#edit_sub_category_id').html('<option value="">None</option>');
        $('#edit_child_category_id').html('<option value="">None</option>');

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

//Child categories list
$('#edit_sub_category_id').on('change', function (e) {
    let $this = $(this),
        url = $this.attr('data-action')
        subCategoryId = $this.val() ?? "";

    $.ajax({
        url: url + "?sub_category_id=" + subCategoryId,
        method: "GET",
        success: function (response) {
            $('#edit_child_category_id').html('<option value="">None</option>')
                .select2({
                    data: $.map(response.childCategories, function (item) {
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



function displaySlug(ele) {
    let $this = ele;
        product = $this.val(),
        lowercaseString = product.toLowerCase(),
        replacedString = lowercaseString.replace(/ /g, '-');

        $('.product-slug').text('Slug : https://jaipurjewelleryhouse.com/' + replacedString);
}

function editDisplaySlug(ele) {
    let $this = ele;
        editProduct = $this.val(),
        editLowercaseString = editProduct.toLowerCase(),
        editReplacedString = editLowercaseString.replace(/ /g, '-');

        $('.edit-product-slug').text('Slug : https://jaipurjewelleryhouse.com/' + editReplacedString);
}