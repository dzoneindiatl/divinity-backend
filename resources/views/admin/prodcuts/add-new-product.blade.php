@extends('admin.layout.master')
@section('content')
<div class="card-header">
    <div class="card-title">
        <h5>Add Product </h5>
    </div>
</div>
<div class="tab-container">
    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif



    <form id="finalProductForm" method="post" action="{{ route('admin-product-save-product') }}"
        enctype="multipart/form-data">
        @csrf

        <input type="hidden" value="" name="draf" id="draf">
       
        <ul class="nav nav-tabs mb-4" id="formTabs">
            <li class="nav-item category-tab">
                <a class="nav-link active" data-tab="tab1" id="step1" href="javascript:void(0)" onclick="onclickPrevious('step1')">Select Category</a>
            </li>
            @php
            $hideClass='';
            if(@$product->product_type==1){
                $hideClass = 'style="display:none"';
            }
            @endphp
            <li class="nav-item variant-tab" <?php echo $hideClass; ?> >
                <?php if(isset($product->id) && !empty($product->id)) { ?>
                <a class="nav-link" data-tab="tab2" id="step2" href="javascript:void(0)" onclick="onclickPrevious('step2')">Select Variant</a>
                <?php } else { ?>
                <a class="nav-link" data-tab="tab2" id="step2" href="javascript:void(0)">Select Variant</a>
                <?php } ?>
            </li>
            
            <li class="nav-item feature-tab">
                <?php if(isset($product->id) && !empty($product->id)) { ?>
                <a class="nav-link" data-tab="tab3" id="step3" href="javascript:void(0)" onclick="onclickPrevious('step3')">Advance Feature</a>
                <?php } else { ?>
                <a class="nav-link" data-tab="tab3" id="step3" href="javascript:void(0)">Advance Feature</a>
                <?php } ?>

            </li>
            <li class="nav-item seo-tab">
                <?php if(isset($product->id) && !empty($product->id)) { ?>
                <a class="nav-link" data-tab="tab4" id="step4" href="javascript:void(0)" onclick="onclickPrevious('step4')">SEO Feature</a>
                <?php } else { ?>
                <a class="nav-link" data-tab="tab4" id="step4" href="javascript:void(0)">SEO Feature</a>
                <?php } ?>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab1">
                @include('modals.products.create_product_combined', ['categories' =>$categories,'product'=>$product])
            </div>
            <div class="tab-pane" id="tab2">
            </div>
            <div class="tab-pane" id="tab3">
            </div>
            <div class="tab-pane" id="tab4">
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
<script>
let $form = $("#finalProductForm");
$form.validate({
    errorClass: 'error',
    highlight: function(element) {
        $(element).addClass("is-invalid");
    },
    unhighlight: function(element) {
        $(element).removeClass("is-invalid");
    },
    errorPlacement: function(error, element) {
        error.insertAfter(element);
    }
});

$(".nextBtn").click(function() {
    let $currentTab = $(this).closest(".tab-pane");
    let $fields = $currentTab.find("input, select, textarea");

    let valid = true;
    $fields.each(function() {
        if (!$(this).valid()) {
            valid = false;
        }
    });

    if (valid) {
        let currentTabId = $currentTab.attr("id");
        let nextTab = $currentTab.next(".tab-pane").attr("id");
        // Check if moving from Tab2 to Tab3 (variant -> advance feature)
        
            // Normal tab switch
            switchTab(nextTab);
        
    }
});

$(".prevBtn").click(function() {
    let $currentTab = $(this).closest(".tab-pane");
    let prevTab = $currentTab.prev(".tab-pane").attr("id");
    if (prevTab) {
        switchTab(prevTab);
    }
});

function switchTab(targetId) {
    $(".tab-pane").removeClass("active");
    $("#" + targetId).addClass("active");

    $(".nav-link").removeClass("active");
    $('.nav-link[data-tab="' + targetId + '"]').addClass("active");
}

$(document).on('change', '.product_type_selectbox', function() {
    var product_type = $(this).val();
    if(product_type==1){
        $('.variant-tab').hide();
    } else {
        $('.variant-tab').show();
    }
});

function onclickPrevious(value) {
    const $btn = $('.prevBtn');
    const originalHtml = $btn.html();
    const formData = new FormData();
    formData.append('product_id', $('#product_id').val());
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    formData.append('step', value);  
    $.ajax({
        url: "{{ route('admin-product-previousStep') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $btn.prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Processing...
            `);
        },
        success: res => {
            if (res.success) {
                if (res.success && res.step==1) {
                    $('#tab1').html(res.mainView);
                } else if (res.success && res.step==2) {
                    $('#tab2').html(res.varient);
                } else if (res.success && res.step==3) {
                    $('#tab3').html(res.mainView);
                } else if (res.success && res.step==4) {
                    $('#tab4').html(res.seoView);
                } else {
                    alert(res.message || "Something went wrong");
                }
            } else {
                alert(res.message || 'Something went wrong.');
            }


        },
        error: xhr => {
            const msg = xhr.status === 422
                ? Object.values(xhr.responseJSON.errors).map(e => e[0]).join('\n')
                : 'Server error';
            alert(msg);
        },

        complete: function () {
            $btn.prop('disabled', false).html(originalHtml);
        }
    });
}
</script>
@endpush