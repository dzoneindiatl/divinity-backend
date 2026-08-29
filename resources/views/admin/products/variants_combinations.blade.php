<div class="variantSecondStepDiv table-responsive">
    <table class="table text-nowrap table-bordered">
        <thead>
            <tr>
                <th scope="col">Image</th>
                @if(!empty($variantsDataArr))
                @foreach($variantsDataArr as $variant)
                <th scope="col">{{$variant['variant_name'] ?? ''}}</th>
                @endforeach
                @endif


            </tr>
        </thead>
        <tbody>
            @if(!empty($variantsDataArr[0]['variant_values_names']))
            @foreach($variantsDataArr[0]['variant_values_names'] as $variantValueNameKey => $variantValueName)
            <tr>
                <th scope="row">
                    @if($productImages->isNotEmpty())
                    @foreach($productImages as $image)
                    <div class="image-checkbox">
                        <label>
                            <input type="checkbox" name="variantCombinationArr[{{$variantValueNameKey}}][image_ids][]" onchange="toggleBorder(this)" value="{{$image->id ?? ''}}">
                            <img src="{{$image->image ?? ''}}" height="100"  width="100"
                                alt="{{$image->image ?? ''}}">
                        </label>
                    </div>
                    @endforeach
                    @endif
                </th>
                @if(count($variantsDataArr) > 1)
                <td>{{$variantValueName ?? ''}}</td>
                <input type="hidden" class="form-control"  name="variantCombinationArr[{{$variantValueNameKey}}][main_variant_id]" value="{{!empty($variantsDataArr[0]['variant_values'][$variantValueNameKey]) ? $variantsDataArr[0]['variant_values'][$variantValueNameKey] : '' }}"
                                 >
                @include('admin.products.variants_meta_data', ['variantsDataArr' => $variantsDataArr,'variantValueNameKey' => $variantValueNameKey])
                @else
                
                <input type="hidden" class="form-control"  name="variantCombinationArr[{{$variantValueNameKey}}][main_variant_id]" value="{{!empty($variantsDataArr[0]['variant_values'][$variantValueNameKey]) ? $variantsDataArr[0]['variant_values'][$variantValueNameKey] : '' }}">
                @include('admin.products.variants_meta_data', ['variantsDataArr' => $variantsDataArr,'variantValueNameKey' => $variantValueNameKey])
                @endif
                
                
            </tr>
            @endforeach
            @endif

        </tbody>
    </table>
</div>

<script>
function toggleBorder(checkbox) {
    const image = checkbox.nextElementSibling; // Get the image element

    // Toggle the CSS class based on checkbox state
    if (checkbox.checked) {
        image.classList.add('image-bordered'); // Add border when checked
    } else {
        image.classList.remove('image-bordered'); // Remove border when unchecked
    }
}
</script>