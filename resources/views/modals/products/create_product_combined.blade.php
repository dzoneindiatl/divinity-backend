<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-body">
            @if (!empty($product->id))
                <input type="hidden" id="product_id" value="{{ $product->id }}">
            @endif

            {{-- Product Type --}}
            <div class="mb-3">
                <label class="form-label">Product Type <span class="text-danger">*</span></label>
                <select class="form-control select2 @error('product_type') is-invalid @enderror product_type_selectbox"
                        name="product_type" id="product_type" required>
                    <option value="">Select</option>
                    <option value="1" {{ old('product_type', $product->product_type ?? '') == '1' ? 'selected' : '' }}>Simple Product</option>
                    <option value="2" {{ old('product_type', $product->product_type ?? '') == '2' ? 'selected' : '' }}>Configured Product</option>
                </select>
                @error('product_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Type<span class="text-danger">*</span></label>
                <select class="form-control select2 @error('category_collection') is-invalid @enderror product_type_selectbox"
                        name="category_collection" id="category_collection" required>
                    <option value="">Select</option>
                    <option value="1" {{ old('category_collection', @$product->category_collection ?? '') == '1' ? 'selected' : '' }}>Category</option>
                    <option value="2" {{ old('category_collection', @$product->category_collection ?? '') == '2' ? 'selected' : '' }}>Collection</option>
                </select>
                @error('product_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Main Category --}}
            <div class="mb-3 category-div" style="display:none">
                @php
                //prx($categories->toArray());
                $grouped1 = $categories->groupBy('category_type_id');
                $cats1 = $grouped1?->toArray();
                $categories1 = !empty($cats1[2])?$cats1[2]:[];
                $collections1 = !empty($cats1[1])?$cats1[1]:[];
                @endphp
                <label class="form-label">Collection / Category <span class="text-danger">*</span></label>
                <select class="form-control select2 @error('main_category_id') is-invalid @enderror category-input"
                        name="main_category_id" id="prdct_category_id"
                        onchange="loadSubCategories()" required>
                    <option value="">Select Category</option>
                    <optgroup label="Category">
                        @foreach ($categories1 as $category)
                            <option value="{{ $category['id'] }}"
                                {{ old('main_category_id', $product->main_category_id ?? '') == $category['id'] ? 'selected' : '' }}>
                                {{ $category['name'] }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
                @error('main_category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 collection-div" style="display:none">
                @php
                //prx($categories->toArray());
                $grouped2= $categories->groupBy('category_type_id');
                $cats2 = $grouped2?->toArray();
                $categories2 = !empty($cats2[2])?$cats2[2]:[];
                $collections2 = !empty($cats2[1])?$cats2[1]:[];
                @endphp
                <label class="form-label">Collection / Category <span class="text-danger">*</span></label>
                <select class="form-control select2 @error('main_category_id') is-invalid @enderror collection-input"
                        name="main_category_id"  id="prdct_collection_id"
                        required>
                    <option value="">Select Collection</option>
                    <optgroup label="Collection">
                        @foreach ($collections2 as $collection)
                            <option value="{{ $collection['id'] }}"
                                {{ old('main_category_id', $product->main_category_id ?? '') == $collection['id'] ? 'selected' : '' }}>
                                {{ $collection['name'] }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
                @error('main_category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Subcategory --}}
            <div class="mb-3 subCategorieHide d-none">
                <label class="form-label">SubCategory <span class="text-danger">*</span></label>
                <select name="main_sub_category_id" id="prdct_sub_category_id"
                        class="form-control select2" onchange="loadChildCategories()">
                    <option value="">Select Subcategory</option>
                </select>
            </div>

            {{-- Child Category --}}
            <div class="mb-3 childCategoryHide d-none">
                <label class="form-label">Child Category <span class="text-danger">*</span></label>
                <select name="main_child_cate_id" id="prdct_child_category_id"
                        class="form-control select2">
                    <option value="">Select Child Category</option>
                </select>
            </div>

            <div class="mb-3 text-end">
                <button type="button" class="btn btn-primary nextBtn" id="nextBtn" onclick="submitProduct()">
                    <span class="btn-text">Save & Continue</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container {
        width: 100% !important;
    }
    .modal .select2-container {
        z-index: 9999;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

{{-- Safe JS block --}}
<script>
(function () {
    // Active step 1 linktab &  stepdiv 
    $(".tab-pane").removeClass("active");
    $("#tab1").addClass("active");

    $(".nav-link").removeClass("active");
    $('#step1').addClass("active");

    // Guard against redeclaration
    window.selectorsData = window.selectorsData || {
        main: '#prdct_category_id',
        sub: '#prdct_sub_category_id',
        child: '#prdct_child_category_id',
    };

    window.preselected = window.preselected || {
        sub: @json($product['main_sub_category_id'] ?? null),
        child: @json($product['main_child_category_id'] ?? null)
    };

    window.initSelect2 = function(force = false) {
        $('.select2').each(function () {
            const $el = $(this);

            if (force && $el.data('select2')) {
                try {
                    $el.select2('destroy');
                } catch (e) {
                    console.warn('Select2 destroy failed for', this, e);
                }
            }

            if (!$el.data('select2')) {
                $el.select2({ width: '100%', placeholder: "Select" });
            }
        });
    };


    window.ajaxCall = function(url, data, onSuccess) {
        $.ajax({
            type: 'GET',
            url,
            data,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: onSuccess,
            error: (xhr, status, error) => console.error(`AJAX Error: ${status}: ${error}`)
        });
    }

    window.populateSelect = function(selector, items, placeholder = "Select", selectedId = null) {
        const $el = $(selector);

        // 1. Destroy Select2 if initialized
        if ($el.data('select2')) {
            $el.select2('destroy');
        }

    
        let html = `<option value="">${placeholder}</option>`;

        
        items.forEach(item => {
            if (item.id && item.name) { // ✅ Only include valid options
                const selected = (parseInt(selectedId) === parseInt(item.id)) ? 'selected' : '';
                html += `<option value="${item.id}" ${selected}>${item.name}</option>`;
            }
        });

     
        
        // 3. Set options and reinitialize Select2
        $el.html(html);
        $el.select2({ width: '100%', placeholder });
    };


    window.loadSubCategories = function() {
        const catId = $(selectorsData.main).val();


        ajaxCall("{{ route('admin-product-ajax-getrelatedsubcategories') }}", { category_ids: catId }, res => {
           
            if (res.success && res.subcategories.length) {
               
                populateSelect(selectorsData.sub, res.subcategories, "Subcategory", preselected.sub);
                $('.subCategorieHide').removeClass('d-none');

                populateSelect(selectorsData.child, [], "Child Category");
                $('.childCategoryHide').addClass('d-none');

                if (preselected.sub) {
                    loadChildCategories(); // load child if sub is preselected
                }
            } else {
                $('.subCategorieHide, .childCategoryHide').addClass('d-none');
                populateSelect(selectorsData.sub, [], "Subcategory");
                populateSelect(selectorsData.child, [], "Child Category");
            }
        });
    }

    window.loadChildCategories = function() {
        const subCatId = $(selectorsData.sub).val();

        ajaxCall("{{ route('admin-product-ajax-getchildcategory') }}", { subctgids: subCatId }, res => {
            console.log((res.childcat));
            if (res.success && res.childcat.length) {
                populateSelect(selectorsData.child, res.childcat, "Child Category", preselected.child);
                $('.childCategoryHide').removeClass('d-none');
            } else {
                $('.childCategoryHide').addClass('d-none');
                populateSelect(selectorsData.child, [], "Child Category");
            }
        });
    }

    window.submitProduct = function() {
        const $btn = $('.nextBtn');
        const originalHtml = $btn.html();

        if($('#prdct_category_id').val() !=''){
            var main_category_id = $('#prdct_category_id').val();
        } else {
            var main_category_id = $('#prdct_collection_id').val();
        }

        const formData = {
            _token: '{{ csrf_token() }}',
            product_type: $('#product_type').val(),
            category_collection: $('#category_collection').val(),
            main_category_id: main_category_id,
            main_sub_category_id: $('#prdct_sub_category_id').val(),
            main_child_cate_id: $('#prdct_child_category_id').val(),
        };

        const productId = $('#product_id').val();
        if (productId) {
            formData.product_id = productId;
        }

        $.ajax({
            url: '{{ route("admin-product-save.step1") }}',
            method: 'POST',
            data: formData,

            // 🔽 BEFORE AJAX SEND
            beforeSend: function () {
                $btn.prop('disabled', true).html(`
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Processing...
                `);
            },

            // 🔽 ON SUCCESS
            success: function (res) {
                if (res.success && res.step==2) {
                    $('#tab2').html(res.varient);
                } else if (res.success && res.step==3) {
                    $('#tab3').html(res.mainView);
                } else if (res.success && res.step==4) {
                    $('#tab4').html(res.seoView);
                } else {
                    alert(res.message || "Something went wrong");
                }
            },

            // 🔽 ON ERROR
            error: function (xhr) {
                console.error(xhr.responseText);
                alert("Validation error or server error");
            },

            // 🔽 ALWAYS RUN (AFTER success/error)
            complete: function () {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    }

    

    $(document).ready(() => {
        initSelect2(true);

        console.log($(selectorsData.main).val());
        if ($(selectorsData.main).val()) {
            loadSubCategories();
        }

        $(document).on('change', 'select', function () {
            const id = $(this).attr('id');
            $(`label.error[for="${id}"]`).remove();
            $(this).removeClass('is-invalid');
        });

        $(document).on('change', '#category_collection', function () {
            let category_collection = $(this).val();
            $('.category-div').hide();
            $('.collection-div').hide();
            $('.category-input').prop('disabled', true);
            $('.collection-input').prop('disabled', true);
            $('.subCategorieHide').addClass('d-none');
            $('.childCategoryHide').addClass('d-none');
            $('.prdct_category_id').val('');
            $('.prdct_collection_id').val('');

            
            if(category_collection == 1){
                $('.category-div').show();
                $('.category-input').prop('disabled', false);
            } else {
                $('.collection-div').show();
                $('.collection-input').prop('disabled', false);
            }
        });

        

       $('.modal').on('shown.bs.modal', function () {
        setTimeout(() => {
            initSelect2(true);
            if ($(selectorsData.main).val()) {
                loadSubCategories(); // Also run on modal open
            }
        }, 100);
    });
    });
})();
</script>
