@if(!empty($variantsDataArr))
@foreach($variantsDataArr as $variantKey => $variantVal)
@if(($variantKey > 0 && count($variantsDataArr) > 1) || ($variantKey == 0 && count($variantsDataArr) == 1))
@if(!empty($variantVal['variant_values_names']))
<td>
    <div class="table-responsive">
        <table class="table text-nowrap table-bordered">
            <thead>
                <tr>
                    <th>Variant</th>
                    <th>Buying Price<span class="text-danger">* </span></th>
                    <th>Selling Price<span class="text-danger">* </span></th>
                    <th>Height<span class="text-danger">  </span></th>
                    <th>Weight<span class="text-danger"> </span></th>
                    <th>Width<span class="text-danger"> </span></th>
                    <th>Length<span class="text-danger"> </span></th>
                    <th>DC<span class="text-danger"> </span></th>
                    <th>Bar Code<span class="text-danger"> </span></th>
                </tr>
            </thead>
            <tbody>
                @if(count($variantsDataArr) > 1)
                @foreach($variantVal['variant_values_names'] as $variantValName2Key => $variantValName2)
                
                
                <tr>
                    <td>{{$variantValName2 ?? ''}}</td>
                   
                    <input type="hidden" class="form-control"  name="variantCombinationArr[{{$variantValueNameKey}}][variant_value_ids][{{$variantValName2Key}}][value_id]" value="{{!empty($variantsDataArr[1]['variant_values'][$variantValName2Key]) ? $variantsDataArr[1]['variant_values'][$variantValName2Key] : '' }}"
                                 >
                       
                    <td>
                        <div class="col-xl-12">

                            <input type="text" class="form-control"  name="variantCombinationArr[{{$variantValueNameKey}}][variant_value_ids][{{$variantValName2Key}}][buying_price]" value="{{!empty($variantsDataArr[$variantValueNameKey]['variant_values_data'][$variantValName2Key]['buying_price']) ? $variantsDataArr[$variantValueNameKey]['variant_values_data'][$variantValName2Key]['buying_price'] : '' }}"
                                placeholder="Enter Buying Price" required>
                        </div>
                    </td>
                    <td>
                        <div class="col-xl-12">

                            <input type="text" class="form-control" name="variantCombinationArr[{{$variantValueNameKey}}][variant_value_ids][{{$variantValName2Key}}][selling_price]" value="{{!empty($variantsDataArr[$variantValueNameKey]['variant_values_data'][$variantValName2Key]['selling_price']) ? $variantsDataArr[$variantValueNameKey]['variant_values_data'][$variantValName2Key]['selling_price'] : '' }}"
                                placeholder="Enter Selling Price" required>
                        </div>
                    </td>
                    <td>
                        <div class="col-xl-12">

                            <input type="text" class="form-control" name="variantCombinationArr[{{$variantValueNameKey}}][variant_value_ids][{{$variantValName2Key}}][height]" placeholder="Height" value="{{!empty($variantsDataArr[$variantValueNameKey]['variant_values_data'][$variantValName2Key]['height']) ? $variantsDataArr[$variantValueNameKey]['variant_values_data'][$variantValName2Key]['height'] : '' }}" required>
                        </div>
                    </td>
                    <td>
                        <div class="col-xl-12">

                            <input type="text" class="form-control" name="variantCombinationArr[{{$variantValueNameKey}}][variant_value_ids][{{$variantValName2Key}}][weight]" placeholder="Weight" value="{{!empty($variantsDataArr[$variantValueNameKey]['variant_values_data'][$variantValName2Key]['weight']) ? $variantsDataArr[$variantValueNameKey]['variant_values_data'][$variantValName2Key]['weight'] : '' }}" required>
                        </div>
                    </td>
                    <td>
                        <div class="col-xl-12">

                            <input type="text" class="form-control" name="variantCombinationArr[{{$variantValueNameKey}}][variant_value_ids][{{$variantValName2Key}}][width]" placeholder="Width" value="{{!empty($variantsDataArr[$variantValueNameKey]['variant_values_data'][$variantValName2Key]['width']) ? $variantsDataArr[$variantValueNameKey]['variant_values_data'][$variantValName2Key]['width'] : '' }}" required>
                        </div>
                    </td>
                    <td>
                        <div class="col-xl-12">

                            <input type="text" class="form-control" name="variantCombinationArr[{{$variantValueNameKey}}][variant_value_ids][{{$variantValName2Key}}][length]" placeholder="Length" value="{{!empty($variantsDataArr[$variantValueNameKey]['variant_values_data'][$variantValName2Key]['length']) ? $variantsDataArr[$variantValueNameKey]['variant_values_data'][$variantValName2Key]['length'] : '' }}" required>
                        </div>
                    </td>
                    <td>
                        <div class="col-xl-12">

                            <input type="text" class="form-control" name="variantCombinationArr[{{$variantValueNameKey}}][variant_value_ids][{{$variantValName2Key}}][dc]" placeholder="DC" value="{{!empty($variantsDataArr[$variantValueNameKey]['variant_values_data'][$variantValName2Key]['dc']) ? $variantsDataArr[$variantValueNameKey]['variant_values_data'][$variantValName2Key]['dc'] : '' }}" required>
                        </div>
                    </td>
                    <td>
                        <div class="col-xl-12">

                            <input type="text" class="form-control" name="variantCombinationArr[{{$variantValueNameKey}}][variant_value_ids][{{$variantValName2Key}}][bar_code]"
                                placeholder="Enter Bar Code" value="{{!empty($variantsDataArr[$variantValueNameKey]['variant_values_data'][$variantValName2Key]['bar_code']) ? $variantsDataArr[$variantValueNameKey]['variant_values_data'][$variantValName2Key]['bar_code'] : '' }}" required>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td>{{!empty($variantsDataArr[0]['variant_values_names'][$variantValueNameKey]) ? $variantsDataArr[0]['variant_values_names'][$variantValueNameKey] :  ''}}</td>
                   
                    <td>
                        <div class="col-xl-12">

                            <input type="text" class="form-control"  name="variantCombinationArr[{{$variantValueNameKey}}][variant_value_ids][0][buying_price]"
                                placeholder="Enter Buying Price" value="{{!empty($variantsDataArr[0]['variant_values_data'][$variantValueNameKey]['buying_price']) ? $variantsDataArr[0]['variant_values_data'][$variantValueNameKey]['buying_price'] : '' }}" required>
                        </div>
                    </td>
                    <td>
                        <div class="col-xl-12">

                            <input type="text" class="form-control" name="variantCombinationArr[{{$variantValueNameKey}}][variant_value_ids][0][selling_price]"
                                placeholder="Enter Selling Price" value="{{!empty($variantsDataArr[0]['variant_values_data'][$variantValueNameKey]['selling_price']) ? $variantsDataArr[0]['variant_values_data'][$variantValueNameKey]['selling_price'] : '' }}" required>
                        </div>
                    </td>
                    <td>
                        <div class="col-xl-12">

                            <input type="text" class="form-control" name="variantCombinationArr[{{$variantValueNameKey}}][variant_value_ids][0][height]" placeholder="Height" value="{{!empty($variantsDataArr[0]['variant_values_data'][$variantValueNameKey]['height']) ? $variantsDataArr[0]['variant_values_data'][$variantValueNameKey]['height'] : '' }}" required>
                        </div>
                    </td>
                    <td>
                        <div class="col-xl-12">

                            <input type="text" class="form-control" name="variantCombinationArr[{{$variantValueNameKey}}][variant_value_ids][0][weight]" placeholder="Weight" value="{{!empty($variantsDataArr[0]['variant_values_data'][$variantValueNameKey]['weight']) ? $variantsDataArr[0]['variant_values_data'][$variantValueNameKey]['weight'] : '' }}" required>
                        </div>
                    </td>
                    <td>
                        <div class="col-xl-12">

                            <input type="text" class="form-control" name="variantCombinationArr[{{$variantValueNameKey}}][variant_value_ids][0][width]" placeholder="Width" value="{{!empty($variantsDataArr[0]['variant_values_data'][$variantValueNameKey]['width']) ? $variantsDataArr[0]['variant_values_data'][$variantValueNameKey]['width'] : '' }}" required>
                        </div>
                    </td>
                    <td>
                        <div class="col-xl-12">

                            <input type="text" class="form-control" name="variantCombinationArr[{{$variantValueNameKey}}][variant_value_ids][0][length]" placeholder="Length" value="{{!empty($variantsDataArr[0]['variant_values_data'][$variantValueNameKey]['length']) ? $variantsDataArr[0]['variant_values_data'][$variantValueNameKey]['length'] : '' }}" required>
                        </div>
                    </td>
                    <td>
                        <div class="col-xl-12">

                            <input type="text" class="form-control" name="variantCombinationArr[{{$variantValueNameKey}}][variant_value_ids][0][dc]" placeholder="DC" value="{{!empty($variantsDataArr[0]['variant_values_data'][$variantValueNameKey]['dc']) ? $variantsDataArr[0]['variant_values_data'][$variantValueNameKey]['dc'] : '' }}" required>
                        </div>
                    </td>
                    <td>
                        <div class="col-xl-12">

                            <input type="text" class="form-control" name="variantCombinationArr[{{$variantValueNameKey}}][variant_value_ids][0][bar_code]"
                                placeholder="Enter Bar Code" value="{{!empty($variantsDataArr[0]['variant_values_data'][$variantValueNameKey]['bar_code']) ? $variantsDataArr[0]['variant_values_data'][$variantValueNameKey]['bar_code'] : '' }}" required>
                        </div>
                    </td>
                </tr>
                @endif
                
                
            </tbody>
        </table>
    </div>
</td>
@endif
@endif
@endforeach
@endif