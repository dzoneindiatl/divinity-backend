<div class="row p-3 text-center">
    <?php $is_front = $is_back = 0; ?>
    @if(!empty($images))
        @foreach($images as $key => $image)
        <?php
            $is_show = 0 ;
            if(!empty($colorIds)){
                if($colorIds && $image['color_id']==$colorIds){
                    $is_show = 1  ;
                }else{
                    $is_show = 0 ; 
                }
            }
          
        ?>
        {{-- @if(!empty($is_show))         --}}
        <div class="col-auto productPicMainContainer">
            <div class="img-wrap rounded-2 mb-3 portfolioPicContainer">
                 @if(in_array($image['ext'], ['mp4', 'wmv', 'avi', 'mov', 'mkv']))
                    <video width="100" height="100" controls>
                        <source src="{{ isset($image) ? Config('constant.PRODUCT_IMAGE_PATH').$image['path'] : '' }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                @else              
                    <img src="{{ isset($image) ? Config('constant.PRODUCT_IMAGE_URL').$image['path'] : '' }}" class="card-img rounded-2 portfolioPicContainerImg" />
                @endif
                <i class="removeProductImage bi bi-x-circle close-icon" data-url="{{ route('admin-product-delete-image-new').'?name='.$image['path'] }}"  data-nam="{{$image['path']}}"></i>
            </div>
            <div class="form-check form-switch d-flex align-items-center justify-content-center px-0">
                <input class="form-check-input statusCheckboxProductPicture m-0 portfolioImgAciveInput"
                    id="mySwitch{{$key}}front"
                    data-url="{{ route('admin-product-update-image-data').'?id='.base64_encode($key).'&type=front' }}"
                    type="radio" name="frontImage" value="front" {{($image['is_front'] == 1)? 'checked' : ''}}>
                <label class="form-check-label portfolioImgAciveLabel" for="mySwitch{{$key}}front"
                    style="margin-left:0.5em;">Front Image</label>
            </div>
            <div class="form-check form-switch d-flex align-items-center justify-content-center px-0">
                <input class="form-check-input statusCheckboxProductPicture m-0 portfolioImgAciveInput"
                    id="mySwitch{{$key}}back"
                    data-url="{{ route('admin-product-update-image-data').'?id='.base64_encode($key).'&type=back' }}"
                    type="radio" name="backImage" value="back" {{($image['is_back'] == 1)? 'checked' : ''}}>
                <label class="form-check-label portfolioImgAciveLabel" for="mySwitch{{$key}}back"
                    style="margin-left:0.5em;">Back Image</label>
            </div>
        </div>
        {{-- @endif --}}
        @endforeach
    @else
        <small>Portfolio Pictures will be shown here.</small>
    @endif
</div>
