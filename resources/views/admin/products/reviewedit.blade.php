@extends('admin.layout.master')

@push('styles')
    <link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
@endpush
@section('content')
    @include('admin.layout.response_message')
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <a class="btn btn-dark" href="{{ route('admin-product-review') }}">Review List</a>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Review</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->

    <div class="row">
        <div class="col-xl-12">
            <form
                action="{{ route('admin-product-reviewupdate', ['reviewId' => base64_encode($review->id), 'productId' => base64_encode($review->product_id)]) }}"
                method="post" enctype="multipart/form-data">
                @csrf
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Edit Review
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-xl-6 mb-3 oldSlug">
                                <label for="category" class="form-label">Rating<span class="text-danger">*
                                    </span></label>
                                <input type="text" class="form-control" name="rating" value="{{ $review->rating }}"
                                    required>
                            </div>

                            <div class="col-xl-6 mb-3 oldSlug">
                                <label for="title" class="form-label">Title<span class="text-danger">*
                                    </span></label>
                                <input type="text" class="form-control" name="title" value="{{ $review->title }}"
                                    required>
                            </div>

                            <div class="col-xl-12 mb-3">
                                <label for="review" class="form-label">Review<span class="text-danger">*
                                    </span></label>
                                <textarea class="form-control @error('title') is-invalid @enderror" name="review" cols="30" rows="5"
                                    required>{!! isset($review->review) ? $review->review : old('review') !!}</textarea>
                                @if ($errors->has('review'))
                                    <div class=" invalid-feedback">
                                        {{ $errors->first('review') }}
                                    </div>
                                @endif
                            </div>


                            <div class="col-xl-6 mb-3">
                                <label for="image" class="form-label">Upload Images</label>
                                <input type="file" name="image[]" id="image" multiple accept="image/*"
                                    class="form-control">

                                {{-- Preview Area --}}
                                <div id="preview-area" style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;">

                                    {{-- Existing Images --}}
                                    @php
                                        $imagesArr = json_decode($review->image, true);
                                    @endphp
                                    @if ($review->image != '')
                                        @foreach ($imagesArr as $image)
                                            <div class="position-relative d-inline-block old-image-wrapper">
                                                <img src="{{ config('constant.REVIEW_IMAGE_URL') . $image }}"
                                                    data-image="{{ $image }}" class="border rounded existing-image"
                                                    width="70" height="70" style="object-fit:cover;">
                                                <button type="button"
                                                    class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0 delete-image"
                                                    data-image="{{ $image }}"
                                                    style="width:22px; height:22px; line-height:1;">×</button>
                                            </div>
                                        @endforeach
                                    @endif

                                </div>

                                {{-- Hidden input to store remaining old images --}}
                                <input type="hidden" name="old_images" id="old_images"
                                    value="{{ json_encode($imagesArr) }}">
                            </div>


                        </div>
                    </div>


                    <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <!-- Select2 Cdn -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Internal Select-2.js -->
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>
    <script src="{{ asset('assets/js/form-validation.js') }}"></script>
    <script src="{{ asset('assets/plugin/tagify/tagify.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom/category.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add an event listener to the taxesSelect dropdown
            $('#taxesSelect').on('change', function() {

                var selectedTaxes = $(this).val();
                $('.taxContainers').hide();
                $('.taxContainers').find('input').prop('disabled', true);
                // Check if any taxes are selected
                if (selectedTaxes && selectedTaxes.length > 0) {

                    // Create a text field for each selected tax
                    selectedTaxes.forEach(function(taxId) {
                        $('.taxDiv' + taxId).show();
                        $('.taxDiv' + taxId).find('input').prop('disabled', false);
                    });
                } else {
                    // Hide the tax count fields div if no taxes are selected
                    $('#taxCountFields').hide();
                }
            });

        });


        $(function() {

            const $preview = $('#preview-area');
            const $oldImages = $('#old_images');
            const reviewId = "{{ $review->id }}";
            const csrf = "{{ csrf_token() }}";
            $('#image').on('change', function(e) {
                for (const file of e.target.files) {
                    const reader = new FileReader();
                    reader.onload = ev => {
                        $preview.append(`
                    <div class="position-relative d-inline-block new-image-wrapper">
                        <img src="${ev.target.result}" width="70" height="70" class="border rounded" style="object-fit:cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0 remove-new"
                            style="width:22px; height:22px; line-height:1;">×</button>
                    </div>
                `);
                    };
                    reader.readAsDataURL(file);
                }
            });
            $(document).on('click', '.delete-image', function() {
                const $btn = $(this);
                const image = $btn.data('image');
                if (!confirm('Delete this image?')) return;

                $.post({
                    url: "{{ route('admin-reviews.image.delete') }}",
                    data: {
                        _token: csrf,
                        image,
                        review_id: reviewId
                    },
                    success: res => {
                        if (res.success) {
                            $btn.closest('.old-image-wrapper').remove();
                            const remaining = $('.existing-image').map((_, el) => $(el).data(
                                'image')).get();
                            $oldImages.val(JSON.stringify(remaining));
                        } else {
                            alert(res.message || 'Failed to delete image');
                        }
                    },
                    error: () => alert('Error deleting image')
                });
            });
            $preview.on('click', '.remove-new', function() {
                $(this).closest('.new-image-wrapper').remove();
            });

        });
    </script>

    <script>
        CKEDITOR.replace(<?php echo 'description'; ?>, {
            filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
            enterMode: CKEDITOR.ENTER_BR
        });
        CKEDITOR.config.allowedContent = true;
        CKEDITOR.replace('seo_data', {
            filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
            enterMode: CKEDITOR.ENTER_BR
        });
        CKEDITOR.config.allowedContent = true;
    </script>
@endpush
