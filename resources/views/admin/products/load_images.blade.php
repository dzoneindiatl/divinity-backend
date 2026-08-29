<div class="row p-3 text-center">
    @if($getData->isNotEmpty())
    @foreach($getData as $dataKey => $dataVal)
    <div class="col-auto productPicMainContainer">
        <div class="img-wrap rounded-2 mb-3 portfolioPicContainer">
           
            <img src="{{ isset($dataVal->image) ? $dataVal->image : '' }}"
                class="card-img rounded-2 portfolioPicContainerImg" />
            <i class="removeProductImage bi bi-x-circle close-icon" data-url="{{ route('admin-product-delete-image').'?id='.base64_encode($dataVal->id) }}"></i>
        </div>
        <div class="form-check form-switch d-flex align-items-center justify-content-center px-0">
            <input class="form-check-input statusCheckboxProductPicture m-0 portfolioImgAciveInput"
                id="mySwitch{{$dataVal->id}}"
                data-url="{{ route('admin-product-update-image-meta-values').'?id='.base64_encode($dataVal->id).'&type=front' }}"
                type="radio" name="frontImage" value="front" {{($dataVal->is_front == 1)? 'checked' : ''}}>
            <label class="form-check-label portfolioImgAciveLabel" for="mySwitch{{$dataVal->id}}"
                style="margin-left:0.5em;">Front Image</label>
        </div>
        <div class="form-check form-switch d-flex align-items-center justify-content-center px-0">
            <input class="form-check-input statusCheckboxProductPicture m-0 portfolioImgAciveInput"
                id="mySwitch{{$dataVal->id}}"
                data-url="{{ route('admin-product-update-image-meta-values').'?id='.base64_encode($dataVal->id).'&type=front' }}"
                type="radio" name="backImage" value="front" {{($dataVal->is_back == 1)? 'checked' : ''}}>
            <label class="form-check-label portfolioImgAciveLabel" for="mySwitch{{$dataVal->id}}"
                style="margin-left:0.5em;">Back Image</label>
        </div>
    </div>
    @endforeach
    @else
    <small>Portfolio Pictures will be shown here.</small>
    @endif
</div>
