
    @php
        $primaryId =0;
        $images = \App\Models\ProductGraphics::where('product_id', $product_id)->where('variant_id',$primaryId)->where('graphic_type','image')->get();
        $videos = \App\Models\ProductGraphics::where('product_id', $product_id)->where('variant_id',$primaryId)->where('graphic_type','video')->get();
        $imagePath = config('constant.PRODUCT_IMAGE_PATH');        
    @endphp
    <div class="card mb-4 shadow-sm variant_group_row" id="variant_0">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <input class="d-none" type="radio" name="main_variant" value="0" id="variant_radio_0" checked>
                Product Images & Videos
            </div>
        </div>

        <div class="row align-items-start mb-4">
            <div class="col-auto m-3">
                <button type="button" class="btn btn-outline-secondary image_upload_button"
                        data-bs-toggle="modal" data-bs-target="#uploadModal_0">
                    <div class="text-center">
                        <div class="fs-2 fw-bold">+</div>
                        <div class="small">Add Images & Video</div>
                    </div>
                </button>
            </div>
            @php
            //prx($images,0);
            @endphp
            <div class="col">
                {{-- Image Previews --}}
                <div class="image-thumbnails-1 d-flex flex-wrap gap-2 mb-2 mt-2">
                    @foreach($images as $k=>$image)
                    @php
                        $frontCheck = !empty($image['is_front'])? "checked":"";
                        $backCheck = !empty($image['is_back'])?"checked":"";
                        $variantIconCheck = !empty($image['is_variant_icon'])?"checked":"";
                    @endphp
                        <div class="image-preview-container position-relative" style="width: 100px; height: 150px;">
                            <img src="{{ $imagePath . $image->graphic }}" alt="Product Image" class="rounded border w-100 " style="object-fit: cover;height:100px"> 
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0 delete-image" data-id="{{ $image->id }}" style="width: 22px; height: 22px; line-height: 1;">×</button>
                            <div class="form-check form-switch d-flex align-items-center justify-content-center mb-0 px-0">
                                <input class="form-check-input updateFrontBackIcon" type="radio" name="front_image[0]" data-vid="0" data-id="{{ $image['id'] }}" data-type="front" value="0-{{ $image['id'] }}" {{ $frontCheck }} id="frontSwitch_0_{{ $image['id'] }}">
                                <label class="form-check-label small" for="frontSwitch_0_{{ $image['id'] }}">
                                    Front Image
                                </label>
                            </div>
                            <div class="form-check form-switch d-flex align-items-center justify-content-center px-0">
                                <input class="form-check-input updateFrontBackIcon" type="radio" name="back_image[0]" data-vid="0" data-id="{{ $image['id'] }}" data-type="back" value="0-{{ $image['id'] }}" {{ $backCheck }} id="backSwitch_0_{{ $image['id'] }}">
                                <label class="form-check-label small" for="backSwitch_0_{{ $image['id'] }}">
                                    Back Image
                                </label>
                            </div>
                            <!-- <div class="form-check form-switch d-flex align-items-center justify-content-center px-0">
                                <input class="form-check-input updateFrontBackIcon" type="radio" name="variant_icon[0]" data-vid="0" data-id="{{ $image['id'] }}" data-type="icon" value="0-{{ $image['id'] }}" {{ $variantIconCheck }} id="iconSwitch_0_{{ $image['id'] }}">
                                <label class="form-check-label small" for="iconSwitch_0_{{ $image['id'] }}">
                                Variant Icon
                                </label>
                            </div>  -->   
                        </div>
                    @endforeach
                </div>

                <div class="image-thumbnails d-flex flex-wrap gap-2 mb-2 mt-2">
                </div>

                {{-- Video Previews --}}
                <div class="video-thumbnails-1 d-flex flex-wrap gap-2 mt-2">
                    @foreach($videos as $video)
                        <div class="image-preview-container position-relative" style="width: 100px; height: 100px;">
                            <video class="rounded border w-100 h-100" style="object-fit: cover;" controls>
                                <source src="{{ asset($imagePath . $video->graphic) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                             <button type="button"
                class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0 delete-image"
                data-id="{{ $video->id }}"
                style="width: 22px; height: 22px; line-height: 1;">×</button>
                        </div>
                    @endforeach
                </div>
                <div class="video-thumbnails d-flex flex-wrap gap-2 mt-2">
                </div>
            </div>
            
        </div>

        <!-- Upload Modal -->
        <div class="modal fade" id="uploadModal_0" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content p-3">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Files for Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Images</label>
                            <input type="file" 
                                name="variant_images[0][]" 
                                accept="image/*" 
                                multiple
                                onchange="previewImages(event, 0)" 
                                class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Video</label>
                            <input type="file" 
                                name="variant_video[0]" 
                                accept="video/*"
                                onchange="previewVideo(event, 0)" 
                                class="form-control">
                        </div>
                        <hr/>
                        <h6 class="fw-bold mb-2">Image Preview</h6>
                        <div id="preview_images_0" class="d-flex flex-wrap gap-2"></div>

                        <h6 class="fw-bold mt-4 mb-2">Video Preview</h6>
                        <div id="preview_video_0" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
            </div>
        </div>


        

        
    </div>


<script>
window.currentPrimaryId = '';

$(document).off('change', '.updateFrontBackIcon').on('change', '.updateFrontBackIcon', function () {
    let vid = $(this).data('vid');
    let id = $(this).data('id');
    let type = $(this).data('type');
    console.log(vid,id,type);
    if(id && type) {
        $.ajax({
            url:"{{ route('admin-product-updateFrontBackIcon') }}",
            data:{vid:vid,id:id,type:type},
            dataType:"json",
            method:"post",
            success:function(resp){
                //console.log(resp);
            },
            error:function(){

            }
        });
    }
});

</script>

<script>
    // $(document).on('click', '.delete-image', function () {
    $(document).off('click', '.delete-image').on('click', '.delete-image', function () {
        const imageId = $(this).data('id');
        const container = $(this).closest('.image-preview-container');

        if (!confirm("Are you sure you want to delete this image?")) return;

        $.ajax({
            url: "{{ route('admin-product-fileDelete') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: imageId
            },
            success: function (response) {
                if (response.success) {
                    container.remove();
                } else {
                    alert(response.message || 'Failed to delete image.');
                }
            },
            error: function () {
                alert('Server error. Please try again.');
            }
        });
    });
</script>