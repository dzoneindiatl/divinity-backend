<form id="seoForm" action="{{ route('admin-product-save.step4') }}" method="POST">
    @csrf
    <div class="card-body">
        <div class="row">
            <div class="col">
              
				 <input type="hidden"  name="product_id"  value="{{$product->id}}" />
                <div class="form-group">
                    <label for="meta_title">Meta Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="meta_title" id="meta_title" value="{{$product->meta_title}}" />
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="meta_keywords">Meta Keywords</label>
                    <input type="text" class="form-control" name="meta_keywords" id="meta_keywords" value="{{$product->meta_keywords}}"  />
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="meta_description">Meta Description </label>
                    <textarea class="form-control" name="meta_description" id="meta_description" cols="30" rows="3">{{$product->meta_description }}</textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="seo_content">Web SEO Content </label>
                    <textarea class="form-control" name="seo_content" id="seo_content" cols="30" rows="3">{{$product->seo_content }}</textarea>
                </div>
            </div>
        </div>

    </div>

    
    <div class="row text-end">
        <div class="mb-3 mt-3 text-first btn_add">
            <button type="button" class="btn btn-primary prevBtn" onclick="onclickPrevious('step3')">Preview</button>
            <button type="button" class="btn btn-info" onclick="submitSeoForm()">Save & Continue</button>
        </div>
        
    </div>
</form>

<!-- CKEditor Script -->
<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>

<script>
    $(function () {
        // Active step 4 linktab &  stepdiv 
        $(".tab-pane").removeClass("active");
        $("#tab4").addClass("active");

        $(".nav-link").removeClass("active");
        $('#step4').addClass("active"); 
    });

    // Initialize CKEditor
    CKEDITOR.replace('seo_content');
    CKEDITOR.replace('meta_description');

    function submitSeoForm() {
        // Update hidden field
        // for (instance in CKEDITOR.instances) {
        //     CKEDITOR.instances[instance].updateElement();
        // }

        // Submit the form
        document.getElementById('seoForm').submit();
    }


    // function onclickPrevious(value) {
    //     const $btn = $('.prevBtn');
    //     const originalHtml = $btn.html();
        
    //     const formData = new FormData();
    //     formData.append('product_id', $('#product_id').val());
    //     formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    //     formData.append('step', "step3");
    //     $.ajax({
    //         url: "{{ route('admin-product-previousStep') }}",
    //         type: "POST",
    //         data: formData,
    //         contentType: false,
    //         processData: false,
    //         beforeSend: function () {
    //             $btn.prop('disabled', true).html(`
    //                 <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
    //                 Processing...
    //             `);
    //         },
    //         success: res => {
    //             if (res.success) {
    //                 $('#tab2 script').remove();
    //                 $('#tab2 script').each(function() {
    //                     eval($(this).html()); // Caution: Only if trusted
    //                 });
    //                 $('#formTabs .nav-link').removeClass('active');
    //                 $('#formTabs .nav-link[data-tab="tab2"]').addClass('active');
                
    //                 $('#tab2').html("").html(res.mainView);
    //             } else {
    //                 alert(res.message || 'Something went wrong.');
    //             }


    //         },
    //         error: xhr => {
    //             const msg = xhr.status === 422
    //                 ? Object.values(xhr.responseJSON.errors).map(e => e[0]).join('\n')
    //                 : 'Server error';
    //             alert(msg);
    //         },
    //         complete: function () {
    //             $btn.prop('disabled', false).html(originalHtml);
    //         }
    //     });
    // }


</script>
