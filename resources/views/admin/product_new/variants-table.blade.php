<?php //print_r($variant_values); die; 
?>
@if(isset($type) && $type == 'save_attribute')
<div class="card-header">
    <div class="card-title">
        Attributes
    </div>
</div>
<div class="card-body">
    <div class="row">
        <div class="col-md-8">
            <table id="datatable-basic" class="table text-nowrap" cellspacing="0" width="100%">
                <thead>
                    <tr id="tableHeaders">
                        <th>Name</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($variant_values as $key => $val)
                    <tr>
                        <td>{{ $val['name'] }}</td>
                        <td>
                            {{ $val['value']['name'] }}
                            <input type="hidden" name="attribute[{{$key}}][id]" value="{{ $val['id'] }}" />
                            <input type="hidden" name="attribute[{{$key}}][name]" value="{{ $val['name'] }}" />
                            <input type="hidden" name="attribute[{{$key}}][value][id]" value="{{ $val['value']['id'] }}" />
                            <input type="hidden" name="attribute[{{$key}}][value][name]" value="{{ $val['value']['name'] }}" />
                        </td>
                        <td><button type="button" class="btn btn-danger btn-delete-item" data-url="{{route('admin-product-delete-item').'?name='.$key.'&type=attributes'}}">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
@if(!empty($variant_values))
@foreach($variant_values as $k => $v)
 <div class="card-header">
    <div class="card-title">
        {{ $v['name'] }}
        <input type="hidden" name="variant[{{$k}}][id]" value="{{ $v['id'] }}" />
        <input type="hidden" name="variant[{{$k}}][name]" value="{{ $v['name'] }}" />
    </div>
</div>


<div class="card-header">
    <div class="card-title">
        Gallery Images
    </div>
</div>
<div class="card-body">
    <div class="row">
        <div class="col-md-8">
            <table id="datatable-basic" class="table text-nowrap" cellspacing="0" width="100%">
                <thead>
                    <tr id="tableHeaders">
                        <th>Variant</th>
                        <th>Gallery Images</th>
                        <th>Main Images</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($v['value'] as $key => $val)
                    <tr>
                        @if(!strcasecmp($v['name'],'color'))
                        <td style="color: {{ $val['code'] }}">{{ $val['name'] }}</td>
                        @else
                        <td>{{ $val['name'] }}</td>
                        @endif
                        <td>
                            <input type="file" id="gallery_images_{{$val['id']}}" class="form-control variants-price" name="gallery_images[{{$val['id']}}][]" multiple />
                            @if(isset($colorimages[$val['id']]))
                            @foreach($colorimages[$val['id']]['images'] as $image)
                            @if($image->is_front != 1)
                            <a href="javascript:void(0);" onclick="deleteImage('{{ $val['id'] }}', '{{ $image->id }}', 'gallery')" class="delete-image-link">

                                <i class="fa-solid fa-delete-left"></i></a>
                            <img src="{{ asset('storage/' . $image->image) }}" alt="Gallery Image" width="100" height="100" style="margin: 5px;">
                            @endif
                            @endforeach
                            @endif
                        </td>
                        <td><input type="file" id="Main_images_{{$val['id']}}" class="form-control variants-price" name="Main_images[{{$val['id']}}]" />
                            @if(isset($colorimages[$val['id']]))
                            @foreach($colorimages[$val['id']]['images'] as $image)
                            @if($image->is_front == 1)
                            <a href="javascript:void(0);" onclick="deleteImage('{{ $val['id'] }}', '{{ $image->id }}', 'main')" class="delete-image-link"> <i class="fa-solid fa-delete-left"></i></a>
                            <img src="{{ asset('storage/' . $image->image) }}" alt="Gallery Image" width="100" height="100" style="margin: 5px;">
                            @endif
                            @endforeach
                            @endif
                        </td>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach
@endif
@endif