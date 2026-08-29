
   
        if (window.preselectedAttributes && Array.isArray(window.preselectedAttributes)) {
          
            window.preselectedAttributes.forEach(attr => {
                addAttributePair(attr); // from your earlier function
            });
        }

    // ==========================
    // 🔧 FUNCTION: Add Attribute-Value Pair Row
    // ==========================

    function updateAddAttributeButton() {
        const button = document.getElementById('add-attribute-button');
        const rows = document.querySelectorAll('.attribute-pair');

        if (rows.length === 0) {
            button.style.display = 'inline-block';
            return;
        }

        const allFilled = Array.from(rows).every(row => {
            const attrSelect = row.querySelector('.attribute-select');
            const valueSelect = row.querySelector('.value-select');

            return attrSelect?.value && valueSelect?.value;
        });

        button.style.display = allFilled ? 'inline-block' : 'none';
    }


    function addAttributePair(preselected = null) {
          console.log(preselected);
        const selectedIds = Array.from(document.querySelectorAll('.attribute-select'))
            .map(select => select.value)
            .filter(val => val !== '');

        const availableAttributes = attributes.filter(attr =>
            !selectedIds.includes(attr.id.toString()) || (preselected && preselected.product_attribute_id == attr.id)
        );

        const attributePairContainer = document.createElement('div');
        attributePairContainer.className = 'row mb-3 attribute-pair';

        const attributeId = preselected ? preselected.attribute_id : '';
        const selectedValueIds = preselected ? preselected.attribute_value_id : [];

        const attributeCol = document.createElement('div');
        attributeCol.className = 'col-4';
        attributeCol.innerHTML = `
            <div class="p-0">
                <div class="mb-3">
                    <label class="form-label">Attribute</label>
                    <select name="attribute_ids[]" class="form-control attribute-select">
                        <option value="">Select Attribute</option>
                        ${availableAttributes.map(attribute => `
                            <option value="${attribute.id}" ${attribute.id == attributeId ? 'selected' : ''}>
                                ${ucfirst(attribute.name)}
                            </option>
                        `).join('')}
                    </select>
                </div>
            </div>`;

        const valueCol = document.createElement('div');
        valueCol.className = 'col-4';
        valueCol.innerHTML = `
            <div class="p-0">
                <div class="mb-3">
                    <label class="form-label">Attribute Value</label>
                    <select class="form-control value-select value-select.select2" name="attribute_value_ids[${attributeId}][]" >
                        <option value="">Loading...</option>
                    </select>
                </div>
            </div>`;

        const removeCol = document.createElement('div');
        removeCol.className = 'col-4 d-flex align-items-center';
        removeCol.innerHTML = `
            <div class="p-0">
                <button class="btn btn-danger remove-attribute-button" type="button">Remove</button>
            </div>`;

        removeCol.querySelector('.remove-attribute-button').addEventListener('click', function () {
            attributePairContainer.remove();
            updateAttributeDropdowns();
            updateAddAttributeButton();
        });

        attributePairContainer.appendChild(attributeCol);
        attributePairContainer.appendChild(valueCol);
        attributePairContainer.appendChild(removeCol);

        document.getElementById('attribute-pairs-container').appendChild(attributePairContainer);

        updateAddAttributeButton();

        const attributeSelect = attributeCol.querySelector('.attribute-select');
        const valueSelect = valueCol.querySelector('.value-select');

        valueSelect.addEventListener('change', function () {
            updateAddAttributeButton();
        });
        attributeSelect.addEventListener('change', function () {
            const selectedId = this.value;
            valueSelect.name = `attribute_value_ids[${selectedId}][]`;
            getAttributeValues(this);
            updateAttributeDropdowns();
            updateAddAttributeButton();
        });

        // Load attribute values via AJAX, then preselect
        if (attributeId) {  
            fetch(`${getAttributesValues}?attribute_id=${attributeId}`)
                .then(res => res.json())
                .then(data => {
                    let options = '<option value="">Select Attribute Value</option>';
                    const selectedIds = Array.isArray(selectedValueIds) ? selectedValueIds : [selectedValueIds];
                    data.attributeValues.forEach(val => {
                        const isSelected = selectedIds.includes(val.id);
                        options += `<option value="${val.id}" ${isSelected ? 'selected' : ''}>${val.name}</option>`;
                    });
                    valueSelect.innerHTML = options;
                    if (selectedIds.length) valueSelect.value = selectedIds[0];

                    updateAddAttributeButton();
                });
        }

        updateAttributeDropdowns();
        updateAddAttributeButton();
    }





    // ==========================
    // 🧠 FUNCTION: Capitalize First Letter
    // ==========================
    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // ==========================
    // 🔄 FUNCTION: Update All Attribute Dropdowns (Hide already selected)
    // ==========================
    
    function updateAttributeDropdowns() {
        const selectedIds = Array.from(document.querySelectorAll('.attribute-select'))
            .map(select => select.value)
            .filter(val => val !== '');

        const totalRows = document.querySelectorAll('.attribute-pair').length;

        // Hide selected options in other dropdowns
        document.querySelectorAll('.attribute-select').forEach(currentSelect => {
            const currentValue = currentSelect.value;

            currentSelect.querySelectorAll('option').forEach(option => {
                if (option.value && option.value !== currentValue && selectedIds.includes(option.value)) {
                    option.style.display = 'none';
                } else {
                    option.style.display = '';
                }
            });
        });

        // ✅ Hide the add button if one more would exceed attribute count
        const addButton = document.getElementById('add-attribute-button');
        if ((totalRows + 1) > attributes.length) {
            addButton.style.display = 'none';
        } else {
            addButton.style.display = 'inline-block'; // or 'block' depending on your layout
        }
    }


    // ==========================
    // 💾 FUNCTION: Save New Attribute & Values via AJAX
    // ==========================
    function saveAttributePair() {
        const attributeId = document.getElementById('attributeName').value.trim();
        const rawValueInput = document.getElementById('attributeValue').value.trim();

        const valuesArray = rawValueInput.split(',')
            .map(val => val.trim())
            .filter(val => val.length > 0);

        if (attributeId && valuesArray.length > 0) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch("{{ route('admin-product-save-attribute') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    attributeId,
                    attributeValues: valuesArray
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('attributeValue').value = '';
                    $('#addAttributeModal').modal('hide');
                    show_message(data.msg, 'success');
                } else {
                    show_message('Something went wrong', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });

        } else {
            show_message('Please enter at least one value.', 'error');
        }
    }


// ==========================
// 🌐 GLOBAL FUNCTION: Load Attribute Values via AJAX
// ==========================
window.getAttributeValues = function (selectElement) {
    const attributeId = selectElement.value;
    const valueSelect = selectElement.closest('.attribute-pair').querySelector('.value-select');

    if (attributeId) {
       fetch(`${getAttributesValues}?attribute_id=${attributeId}`)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">Select Attribute Value</option>';
                data.attributeValues.forEach(function (value) {
                    options += `<option value="${value.id}">${value.name}</option>`;
                });
                valueSelect.innerHTML = options;

                updateAddAttributeButton();
            })
            .catch(error => {
                console.error('Error fetching attribute values:', error);
            });
    } else {
        valueSelect.innerHTML = '<option value="">Select Attribute Value</option>';
    }
};


/* ============================
   🧩 VALUE SELECT FILTER LOGIC
=============================== */
function refreshValueSelectOptions() {
    const selectedValues = [];

    // Collect all selected values
    $('.value-select').each(function () {
        const selected = $(this).val();
        if (selected) selectedValues.push(selected);
    });

    // Disable already selected values in other dropdowns
    $('.value-select').each(function () {
        const currentSelect = $(this);
        const currentSelected = currentSelect.val();

        currentSelect.find('option').each(function () {
            const optionValue = $(this).val();
            if (!optionValue || optionValue === currentSelected) {
                $(this).prop('disabled', false);
            } else {
                $(this).prop('disabled', selectedValues.includes(optionValue));
            }
        });
    });
}


/* ============================
   🔢 PRODUCT LIMIT AJAX UPDATE
=============================== */
function UpdateProductVariantLimit() {
    const max_selling_units = $('#maxProduct').val();
    const min_selling_units = $('#minProduct').val();
    const product_id = 1;

    $.ajax({
        type: "POST",
        url: productLimit,
        data: {
            max_selling_units,
            min_selling_units,
            product_id,
            _token: '{{ csrf_token() }}'
        },
        dataType: "json",
        success: function (response) {
            if (response.success == 1) {
                console.log('✅ Product limits updated.');
            } else {
                console.error(response.error);
            }
        }
    });
}


/* ============================
   📦 DOCUMENT READY EVENTS
=============================== */
$(document).ready(function () {
    refreshValueSelectOptions();

    // Re-check uniqueness when value select clicked
    $(document).on('click', '.value-select', function () {
        refreshValueSelectOptions();
    });

    // Handle Max/Min updates
    $('#maxProduct, #minProduct').on('input', function () {
        UpdateProductVariantLimit();
    });

    // Draft button: disable required & submit
    $(document).on('click', '#saveDraft', function () {
        $('#draf').val(1); // Hidden input flag for draft
        $('#finalProductForm').submit();
    });

    // Final submit
    $(document).on('click', '#finalSubmit', function () {
        $('#draf').val(0); // Not draft
        // You can enable loader here if needed
    });
});


/* ============================
🖼️ Start Simple Product IMAGE UPLOAD + PREVIEW LOGIC + Video
=============================== */
/* 🖼️ PREVIEW ON FILE SELECT */
function previewImagesSimple(event, productId) {
    const files = event.target.files;

    if (!window.uploadedImages[productId]) {
        window.uploadedImages[productId] = [];
    }

    Array.from(files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            window.uploadedImages[productId].push({
                url: e.target.result,
                file: file,
                front: false,
                back: false,
                icon:false
            });
            renderPreviewsSimple(productId);
        };
        reader.readAsDataURL(file);
    });
}

/* 🧱 RENDER IMAGES IN MODAL & OUTSIDE */
function renderPreviewsSimple(productId) { 
    const container = document.getElementById('preview_images_' + productId);
    //const groupRow = document.querySelector(`.variant_group_row input[name="main_variant"][value="${productId}"]`)?.closest('.variant_group_row');
    //const thumbs = groupRow?.querySelector('.image-thumbnails');
    const thumbs = document.querySelector('.image-thumbnails');

    if (!container || !thumbs) return;
    
    container.innerHTML = '';
    thumbs.innerHTML = '';

    window.uploadedImages[productId].forEach((imageData, index) => {
        container.appendChild(createImagePreviewBoxSimple(imageData, productId, index, false));
        thumbs.appendChild(createImagePreviewBoxSimple(imageData, productId, index, true));
    });
}

/* 📦 BUILD IMAGE PREVIEW BOX */
function createImagePreviewBoxSimple(imageData, productId, index, showControls = false) {
    const wrapper = document.createElement('div');
    wrapper.className = 'image-preview-container position-relative';
    wrapper.style.width = '100px';
    wrapper.style.marginRight = '10px';

    // 👁 Image Element
    const img = document.createElement('img');
    img.src = imageData.url;
    img.className = 'rounded border';
    img.style.width = '100px';
    img.style.height = '100px';
    img.style.objectFit = 'cover';

    // ❌ Remove Button
    const removeBtn = document.createElement('button');
    removeBtn.innerHTML = '&times;';
    removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0';
    removeBtn.onclick = () => {
        window.uploadedImages[productId].splice(index, 1);
        renderPreviewsSimple(productId);
    };

    wrapper.appendChild(img);
    wrapper.appendChild(removeBtn);

    // 🔘 FRONT / BACK radio buttons (only in modal)
    if (showControls) {
        const frontSwitch = document.createElement('div');
        frontSwitch.className = 'form-check form-switch d-flex align-items-center justify-content-center mb-1 px-0';
        frontSwitch.innerHTML = `
            <input class="form-check-input" type="radio" name="front_image[${productId}]" 
                value="${productId}-${index}" 
                ${imageData.front ? 'checked' : ''} 
                onchange="setFrontImage(${productId}, ${index})" 
                id="frontSwitch_${productId}_${index}">
            <label class="form-check-label small ms-2" for="frontSwitch_${productId}_${index}">
                Front Image
            </label>`;

        const backSwitch = document.createElement('div');
        backSwitch.className = 'form-check form-switch d-flex align-items-center justify-content-center px-0';
        backSwitch.innerHTML = `
           <input class="form-check-input" type="radio" name="back_image[${productId}]" 
                value="${productId}-${index}" 
                ${imageData.back ? 'checked' : ''} 
                onchange="setBackImage(${productId}, ${index})" 
                id="backSwitch_${productId}_${index}">
            <label class="form-check-label small ms-2" for="backSwitch_${productId}_${index}">
                Back Image
            </label>`;
        
        const variantIconSwitch = document.createElement('div');
        variantIconSwitch.className = 'form-check form-switch d-flex align-items-center justify-content-center px-0';
        variantIconSwitch.innerHTML = `
           <input class="form-check-input" type="radio" name="variant_icon[${productId}]" 
                value="${productId}-${index}" 
                ${imageData.icon ? 'checked' : ''} 
                onchange="setVariantIcon(${productId}, ${index})" 
                id="iconSwitch_${productId}_${index}">
            <label class="form-check-label small ms-2" for="iconSwitch_${productId}_${index}">
                Variant Icon
            </label>`;

        wrapper.appendChild(frontSwitch);
        wrapper.appendChild(backSwitch);
        wrapper.appendChild(variantIconSwitch);
    }
    return wrapper;
}

function previewVideoSimple(event, productId) {
    const file = event.target.files[0];
    if (!file) return;

    const groupRow = event.target.closest('.variant_group_row');
    const preview = document.getElementById('preview_video_' + productId);
    const thumb = groupRow ? groupRow.querySelector('.video-thumbnails') : null;

    preview.innerHTML = '';
    if (thumb) thumb.innerHTML = '';

    const reader = new FileReader();
    reader.onload = function(e) {
        const removeBoth = () => {
            preview.innerHTML = '';
            if (thumb) thumb.innerHTML = '';
        };

        const videoWrapper = document.createElement('div');
        videoWrapper.className = 'image-preview-container position-relative';
        videoWrapper.style.width = '100px';
        videoWrapper.style.marginRight = '10px';

        const video = document.createElement('video');
        video.src = e.target.result;
        video.className = 'rounded border';
        video.style.width = '100px';
        video.style.height = '100px';
        video.style.objectFit = 'cover';
        video.controls = true;

        const removeBtn = document.createElement('button');
        removeBtn.innerHTML = '&times;';
        removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0';
        removeBtn.onclick = removeBoth;

        videoWrapper.appendChild(video);
        videoWrapper.appendChild(removeBtn);
        preview.appendChild(videoWrapper);

        if (thumb) {
            const thumbWrapper = videoWrapper.cloneNode(true);
            const thumbBtn = thumbWrapper.querySelector('button');
            if (thumbBtn) thumbBtn.onclick = removeBoth;
            thumb.appendChild(thumbWrapper);
        }
    };
    reader.readAsDataURL(file);
}

/* ============================
🖼️ End Simple Product IMAGE UPLOAD + PREVIEW LOGIC
=============================== */


/* ============================
🖼️ IMAGE UPLOAD + PREVIEW LOGIC
=============================== */

window.uploadedImages = {};
let cropper;
let currentFile = null;
let currentVariantId = null;

/* 🖼️ PREVIEW ON FILE SELECT */
function previewImages(event, variantId) {
    const files = event.target.files;

    if (!window.uploadedImages[variantId]) {
        window.uploadedImages[variantId] = [];
    }

    Array.from(files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            window.uploadedImages[variantId].push({
                url: e.target.result,
                file: file,
                front: false,
                back: false,
                icon:false
            });
            renderPreviews(variantId);
        };
        reader.readAsDataURL(file);
    });
}

$('#cropImageBtn').on('click', function () {

    if (!cropper) {
        return;
    }

    const canvas = cropper.getCroppedCanvas({
        width: 800,
        height: 800
    });

    canvas.toBlob(function (blob) {

        const croppedFile = new File(
            [blob],
            currentFile.name,
            {
                type: 'image/jpeg'
            }
        );

        const reader = new FileReader();

        reader.onload = function (e) {

            if (!window.uploadedImages[currentVariantId]) {
                window.uploadedImages[currentVariantId] = [];
            }

            window.uploadedImages[currentVariantId].push({
                url: e.target.result,
                file: croppedFile,
                front: false,
                back: false,
                icon: false
            });

            renderPreviews(currentVariantId);

            const modal = bootstrap.Modal.getInstance(
                document.getElementById('cropperModal')
            );

            modal.hide();
            $('.cropers-images').val('');

        };

        reader.readAsDataURL(croppedFile);

    }, 'image/jpeg', 0.9);

});

function openCropper(event, variantId)
{
    let file = event.target.files[0];

    if(!file) return;

    // ADD THESE
    currentFile = file;
    currentVariantId = variantId;

    let reader = new FileReader();

    reader.onload = function(e)
    {
        $('#cropperImage').attr('src', e.target.result);

        let modal = new bootstrap.Modal(
            document.getElementById('cropperModal')
        );

        modal.show();

        setTimeout(() => {

            if(cropper){
                cropper.destroy();
            }

            cropper = new Cropper(
                document.getElementById('cropperImage'),
                {
                    aspectRatio: 1,
                    viewMode: 1,
                    responsive: true,
                    autoCropArea: 1,

                    ready() {
                        cropper.setCropBoxData({
                            width: 800,
                            height: 800
                        });
                    }
                }
            );

        },300);
    };

    reader.readAsDataURL(file);
}



/* 🧱 RENDER IMAGES IN MODAL & OUTSIDE */
function renderPreviews(variantId) {
    const container = document.getElementById('preview_images_' + variantId);
    const groupRow = document.querySelector(`.variant_group_row input[name="main_variant"][value="${variantId}"]`)?.closest('.variant_group_row');
    const thumbs = document.querySelector('.image-thumbnails-'+ variantId);

    if (!container || !thumbs) return;

    container.innerHTML = '';
    thumbs.innerHTML = '';

    window.uploadedImages[variantId].forEach((imageData, index) => {
        //container.appendChild(createImagePreviewBox(imageData, variantId, index, false));
        thumbs.appendChild(createImagePreviewBox(imageData, variantId, index, true));
    });
}

/* 📦 BUILD IMAGE PREVIEW BOX */
function createImagePreviewBox(imageData, variantId, index, showControls = false) {
    const wrapper = document.createElement('div');
    wrapper.className = 'image-preview-container position-relative';
    wrapper.style.width = '100px';
    wrapper.style.marginRight = '10px';

    // 👁 Image Element
    const img = document.createElement('img');
    img.src = imageData.url;
    img.className = 'rounded border';
    img.style.width = '100px';
    img.style.height = '100px';
    img.style.objectFit = 'cover';

    // ❌ Remove Button
    const removeBtn = document.createElement('button');
    removeBtn.innerHTML = '&times;';
    removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0';
    removeBtn.onclick = () => {
        window.uploadedImages[variantId].splice(index, 1);
        renderPreviews(variantId);
    };

    wrapper.appendChild(img);
    wrapper.appendChild(removeBtn);

    // 🔘 FRONT / BACK radio buttons (only in modal)
    if (showControls) {
        const frontSwitch = document.createElement('div');
        frontSwitch.className = 'form-check form-switch d-flex align-items-center justify-content-center mb-1 px-0';
        frontSwitch.innerHTML = `
            <input class="form-check-input" type="radio" name="front_image[${variantId}]" 
                value="${variantId}-${index}" 
                ${imageData.front ? 'checked' : ''} 
                onchange="setFrontImage(${variantId}, ${index})" 
                id="frontSwitch_${variantId}_${index}"><input type="hidden" name="product_crop_images[${variantId}][]" value="${imageData.url}">
            <label class="form-check-label small ms-2" for="frontSwitch_${variantId}_${index}">
                Front Image
            </label>`;

        const backSwitch = document.createElement('div');
        backSwitch.className = 'form-check form-switch d-flex align-items-center justify-content-center px-0';
        backSwitch.innerHTML = `
           <input class="form-check-input" type="radio" name="back_image[${variantId}]" 
                value="${variantId}-${index}" 
                ${imageData.back ? 'checked' : ''} 
                onchange="setBackImage(${variantId}, ${index})" 
                id="backSwitch_${variantId}_${index}">
            <label class="form-check-label small ms-2" for="backSwitch_${variantId}_${index}">
                Back Image
            </label>`;
        
        const variantIconSwitch = document.createElement('div');
        variantIconSwitch.className = 'form-check form-switch d-flex align-items-center justify-content-center px-0';
        variantIconSwitch.innerHTML = `
           <input class="form-check-input" type="radio" name="variant_icon[${variantId}]" 
                value="${variantId}-${index}" 
                ${imageData.icon ? 'checked' : ''} 
                onchange="setVariantIcon(${variantId}, ${index})" 
                id="iconSwitch_${variantId}_${index}">
            <label class="form-check-label small ms-2" for="iconSwitch_${variantId}_${index}">
                Variant Icon
            </label>`;

        wrapper.appendChild(frontSwitch);
        wrapper.appendChild(backSwitch);
        wrapper.appendChild(variantIconSwitch);
    }

    return wrapper;
}

/* 🏷️ SET FRONT IMAGE FLAG */
function setFrontImage(variantId, index) {
    window.uploadedImages[variantId].forEach((img, i) => img.front = i === index);
    renderPreviews(variantId);
}

/* 🏷️ SET BACK IMAGE FLAG */
function setBackImage(variantId, index) {
    window.uploadedImages[variantId].forEach((img, i) => img.back = i === index);
    renderPreviews(variantId);
}

/* 🏷️ SET variant icon IMAGE FLAG */
function setVariantIcon(variantId, index) {
    window.uploadedImages[variantId].forEach((img, i) => img.icon = i === index);
    renderPreviews(variantId);
}

// ===================== Video Preview =====================
function previewVideo(event, variantId) {
    const file = event.target.files[0];
    if (!file) return;

    const groupRow = event.target.closest('.variant_group_row');
    const preview = document.getElementById('preview_video_' + variantId);
    const thumb = groupRow ? groupRow.querySelector('.video-thumbnails') : null;

    preview.innerHTML = '';
    if (thumb) thumb.innerHTML = '';

    const reader = new FileReader();
    reader.onload = function(e) {
        const removeBoth = () => {
            preview.innerHTML = '';
            if (thumb) thumb.innerHTML = '';
        };

        const videoWrapper = document.createElement('div');
        videoWrapper.className = 'image-preview-container position-relative';
        videoWrapper.style.width = '100px';
        videoWrapper.style.marginRight = '10px';

        const video = document.createElement('video');
        video.src = e.target.result;
        video.className = 'rounded border';
        video.style.width = '100px';
        video.style.height = '100px';
        video.style.objectFit = 'cover';
        video.controls = true;

        const removeBtn = document.createElement('button');
        removeBtn.innerHTML = '&times;';
        removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0';
        removeBtn.onclick = removeBoth;

        videoWrapper.appendChild(video);
        videoWrapper.appendChild(removeBtn);
        preview.appendChild(videoWrapper);

        if (thumb) {
            const thumbWrapper = videoWrapper.cloneNode(true);
            const thumbBtn = thumbWrapper.querySelector('button');
            if (thumbBtn) thumbBtn.onclick = removeBoth;
            thumb.appendChild(thumbWrapper);
        }
    };
    reader.readAsDataURL(file);
}


// ===================== Subcategory & Product Fetch =====================
function getsubcategory(category_id) {
    $.ajax({
        type: "GET",
        url: getSubCategory,
        data: { catid: category_id },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            let html = '<option value="">Select Subcategory</option>';
            const preselectedSubcat = $('#preselected_subcategory').val();

            if (response.success) {
                response.subcategories.forEach(subcat => {
                    const selected = subcat.id == preselectedSubcat ? 'selected' : '';
                    html += `<option value="${subcat.id}" ${selected}>${subcat.name}</option>`;
                });
            } else {
                html += '<option value="">No Subcategories Available</option>';
            }

            $("#subcategory_id").html(html);

            if (preselectedSubcat) {
                getproduct(preselectedSubcat); // Trigger product fetch
            } else {
                $('#Productid').html("");
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ' + status + error);
            $("#subcategory_id").html('<option value="">Error fetching subcategories</option>');
        }
    });
}


function getproduct(subcategory_id) {
    $.ajax({
        type: "GET",
        url: getProduct,
        data: { subcatid: subcategory_id },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            let preselectedProductIds = JSON.parse($('#preselected_products').val() || '[]').map(Number);
            let options = '';

            console.log(preselectedProductIds);
            if (response.success) {
                response.subproducts.forEach(item => {

                    const selected = preselectedProductIds.includes(item.id) ? 'selected' : '';
                    options += `<option value="${item.id}" ${selected}>${item.name}</option>`;
                });

                $('#Productid').html(options).select2({ placeholder: "Choose item", width: "100%" });
            } else {
                $('#Productid').html('<option value="">No Product Available</option>').select2({ placeholder: "Choose item", width: "100%" });
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ' + status + ' ' + error);
            $('#Productid').html('<option value="">Error fetching Product</option>').select2({ placeholder: "Choose item", width: "100%" });
        }
    });
}


    window.selectedCategory = $('#categorys_id').val();
     window.preselectedSubcategory = $('#preselected_subcategory').val();

    if (window.selectedCategory && window.preselectedSubcategory) {
        getsubcategory(window.selectedCategory);
    }

    $('#Productid').select2({ placeholder: "Choose item", width: "100%" });

// ===================== Global Helpers =====================
function show_message(message, type) {
    Swal.fire({
        icon: type,
        title: message,
        showConfirmButton: true,
    });
}

// ===================== SKU Updater =====================
$('#sku').on('input', function () {
    const prefix = $(this).val().trim();
    $('.dynamic-sku-prefix').text(prefix ? prefix + '_' : 'SKU_');
});

// ===================== Price Calculation =====================
$(document).ready(function () {
    function calculateSellingPrice() {
        const buyingPrice = parseFloat($('#buying_price').val()) || 0;
        const discount = parseFloat($('#discount').val()) || 0;
        const discountType = $('#discount_type').val();
        let sellingPrice = buyingPrice;

        if (discountType === 'flat') sellingPrice -= discount;
        else if (discountType === 'percentage') sellingPrice -=  (buyingPrice * discount / 100).toFixed(0);

        if (sellingPrice < 0) sellingPrice = 0;

        $('#selling_price').val(sellingPrice.toFixed(2));

        $('.v_input_price').val(buyingPrice.toFixed(2));
        $('.v_input_sale_price').val(sellingPrice.toFixed(2));

        $('.discount_popup').val(discount);
        $('.discount_type_popup').val(discountType);
          
       
    }
    $('#buying_price, #discount').on('input', calculateSellingPrice);
    $('#discount_type').on('change', calculateSellingPrice);
});

// ===================== Quantity Update =====================
$('#qty').on('input', function () {
    const qty = $(this).val();
    $('.v_input_quantity').val(qty);
    updateTotalQtyPerGroup();
    updateGrandTotalQty();
});

function updateTotalQtyPerGroup() {
    $('.variant_group_row').each(function () {
        let total = 0;
        $(this).find('.v_input_quantity').each(function () {
            total += parseInt($(this).val()) || 0;
        });
        $(this).find('.card-header .text-muted').text('Total Qty: ' + total);
    });
}

function updateGrandTotalQty() {
    let grandTotal = 0;
    $('.v_input_quantity').each(function () {
        grandTotal += parseInt($(this).val()) || 0;
    });
    $('#t-qty').text(grandTotal);
}

// ===================== Variant Modal Edit =====================
function open_modal(el) {
    const comboId = el.getAttribute('data-id'); // e.g. "3_8"
    const comboElement = document.getElementById('variant_combo_' + comboId);
    
    if (!comboElement) return;

    // Get the parent group row (e.g. #variant_3)
    const groupRow = comboElement.closest('.variant_group_row');
    if (!groupRow) return;

    // Find the table body inside this group and count <tr> elements
    const tbody = groupRow.querySelector('table tbody');
    const allRows = tbody.querySelectorAll('tr');

    console.log('Variant group:', groupRow.id, 'Row count:', allRows.length);

    if (allRows.length > 1) {
        comboElement.remove();
    } else {
        alert("At least one variant combination is required.");
    }
}

function open_modal_edit(el) {
    const comboId = $(el).data('id');
    const row = $('#variant_combo_' + comboId);

    $('#v_id').val(comboId);
    $('#v_name').val(row.find('.v_input_name').val());
    $('#v_sku').val(row.find('.v_input_sku').val());
    $('#v_price').val(row.find('.v_input_price').val());
    $('#v_sprice').val(row.find('.v_input_sale_price').val());
    $('#v_quantity').val(row.find('.v_input_quantity').val());
    $('#discount_popup').val(row.find('.v_input_discount').val());
    $('#discount_type_popup').val(row.find('.v_input_discount_type').val());

    $('#editVariantModal').modal('show');
}

function submit_form(formId) {
    const comboId = $('#v_id').val();
    const row = $('#variant_combo_' + comboId);

    row.find('.v_name').text($('#v_name').val());
    row.find('.v_sku').text($('#v_sku').val());
    row.find('.v_price .s-price').text($('#v_price').val());
    row.find('.v_sale_price .s-sale_price').text($('#v_sprice').val());
    row.find('.v_discount_type .s-discount_type').text($('#discount_type_popup').val());
    row.find('.v_discount .s-discount').text($('#discount_popup').val());
    row.find('.v_quantity .s-quantity').text($('#v_quantity').val());

    row.find('.v_input_name').val($('#v_name').val());
    row.find('.v_input_sku').val($('#v_sku').val());
    row.find('.v_input_price').val($('#v_price').val());
    row.find('.v_input_sale_price').val($('#v_sprice').val());
    row.find('.v_input_discount_type').val($('#discount_type_popup').val());
    row.find('.v_input_discount').val($('#discount_popup').val());
    row.find('.v_input_quantity').val($('#v_quantity').val());

    updateTotalQtyPerGroup();
    updateGrandTotalQty();
    $('#editVariantModal').modal('hide');
}

function close_container(id) {
    $('.' + id).hide();
}

// ===================== CKEditor Config =====================
['specification', 'description', 'product_details', 'others', 'wash_care', 'seo_content'].forEach(field => {
    CKEDITOR.replace(field, {
        filebrowserUploadUrl: 'http://127.0.0.1:8000/base/uploder',
        enterMode: CKEDITOR.ENTER_BR
    });
});




document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.main-cat-checkbox:checked').forEach(cb => {
        toggleSubCategories(cb.value);
    });

    document.querySelectorAll('.sub-cat-checkbox:checked').forEach(cb => {
        toggleChildCategories(cb.value);
    });
});

function toggleSubCategories(categoryId) {
    const subDiv = document.getElementById('subcategories_' + categoryId);
    if (document.getElementById('main_cat_' + categoryId).checked) {
        subDiv.classList.remove('d-none');
    } else {
        subDiv.classList.add('d-none');
        subDiv.querySelectorAll('.sub-cat-checkbox').forEach(input => {
            input.checked = false;
            document.getElementById('childcategories_' + input.value)?.classList.add('d-none');
            document.querySelectorAll('#childcategories_' + input.value + ' .child-cat-checkbox').forEach(child => {
                child.checked = false;
            });
        });
    }
}

function toggleChildCategories(subCategoryId) {
    const childDiv = document.getElementById('childcategories_' + subCategoryId);
    if (document.getElementById('sub_cat_' + subCategoryId).checked) {
        childDiv.classList.remove('d-none');
    } else {
        childDiv.classList.add('d-none');
        childDiv.querySelectorAll('.child-cat-checkbox').forEach(input => input.checked = false);
    }
}