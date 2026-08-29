<div class="row p-3 text-center">
    <?php $is_front = $is_back = 0; ?>
    @if (!empty($images) || !empty($videos))
        @foreach ($images as $key => $image)
            <div class="col-auto productPicMainContainer">
                <div class="img-wrap rounded-2 mb-3 portfolioPicContainer">
                    {{-- Check if the file is a video --}}
                    @if (in_array($image['ext'], ['mp4', 'wmv', 'avi', 'mov', 'mkv']))
                        <video width="100" height="100" controls>
                            <source
                                src="{{ isset($image) ? Config('constant.PRODUCT_MEDIA_PATH') . $image['path'] : '' }}"
                                type="video/{{ $image['ext'] }}">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        {{-- Display image --}}
                        <img src="{{ isset($image) ? Config('constant.PRODUCT_IMAGE_URL') . $image['path'] : '' }}"
                            class="card-img rounded-2 portfolioPicContainerImg" />
                    @endif
                    {{-- Remove Button --}}
                    <i class="removeProductImage bi bi-x-circle close-icon"
                        data-url="{{ route('admin-product-delete-image-new') . '?name=' . $image['path'] }}"
                        data-nam="{{ $image['path'] }}">
                    </i>
                </div>
                {{-- Front/Back Switch for Images --}}
                @if (!in_array($image['ext'], ['mp4', 'wmv', 'avi', 'mov', 'mkv']))
                    <div class="form-check form-switch d-flex align-items-center justify-content-center px-0">
                        <input class="form-check-input statusCheckboxProductPicture m-0 portfolioImgAciveInput"
                            id="mySwitch{{ $key }}front"
                            data-url="{{ route('admin-product-update-image-data') . '?id=' . base64_encode($key) . '&type=front' }}"
                            type="radio" name="frontImage" value="front"
                            {{ $image['is_front'] == 1 ? 'checked' : '' }}>
                        <label class="form-check-label portfolioImgAciveLabel" for="mySwitch{{ $key }}front"
                            style="margin-left:0.5em;">
                            Front Image
                        </label>
                    </div>
                    <div class="form-check form-switch d-flex align-items-center justify-content-center px-0">
                        <input class="form-check-input statusCheckboxProductPicture m-0 portfolioImgAciveInput"
                            id="mySwitch{{ $key }}back"
                            data-url="{{ route('admin-product-update-image-data') . '?id=' . base64_encode($key) . '&type=back' }}"
                            type="radio" name="backImage" value="back"
                            {{ $image['is_back'] == 1 ? 'checked' : '' }}>
                        <label class="form-check-label portfolioImgAciveLabel" for="mySwitch{{ $key }}back"
                            style="margin-left:0.5em;">
                            Back Image
                        </label>
                    </div>
                @endif
            </div>
        @endforeach
        @if (!empty($videos))
            @foreach ($videos as $key => $image)
                <div class="col-auto productPicMainContainer">
                    <div class="img-wrap rounded-2 mb-3 portfolioPicContainer">
                        @if (in_array($image['ext'], ['mp4', 'wmv', 'avi', 'mov', 'mkv']))
                            <video width="100" height="100" controls>
                                <source
                                    src="{{ isset($image) ? Config('constant.PRODUCT_MEDIA_PATH') . $image['path'] : '' }}"
                                    type="video/{{ $image['ext'] }}">
                                Your browser does not support the video tag.
                            </video>
                            <i class="removeProductImage bi bi-x-circle close-icon"
                                data-url="{{ route('admin-product-delete-image-new') . '?name=' . $image['path'] }}"
                                data-nam="{{ $image['path'] }}">
                            </i>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    @else
        <small>Portfolio Pictures will be shown here.</small>
    @endif
</div>
