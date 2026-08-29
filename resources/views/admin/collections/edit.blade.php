@extends('admin.layout.master')

@section('content')
@push('styles')
<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
@endpush
<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Collection</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="{{route('admin-collections.update',base64_encode($model->id))}}" method="post" enctype="multipart/form-data"
            id="editStaffForm">
            @method('PUT')
            @csrf
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Basic Info
                    </div>
                </div>
                <div class="card-body add-products p-0">
                    <div class="p-4">
                        <div class="row gx-5">
                            <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12">
                                <div class="card custom-card shadow-none mb-0 border-0">
                                    <div class="card-body p-0">
                                        <div class="row gy-3">

                                            <div class="col-xl-6">
                                                <label for="name" class="form-label"><span class="text-danger">* </span>Title</label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="title" name="title" value="{{isset($model->title) ? $model->title: old('name')}}" placeholder="Title">
                                                @if ($errors->has('name'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('title') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-6 mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control @error('title') is-invalid @enderror" name="description" id="description" cols="30" rows="5">{!! isset($model->description) ? $model->description: old('description') !!}</textarea>
                                                    @if ($errors->has('description'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('description') }}
                                                        </div>
                                                    @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="image" class="form-label"><span class="text-danger">
                                                    </span><span class="text-danger">* </span>Image</label>
                                                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                                                @if (!empty($model->image))
                                                    <img height="50" width="50" src="{{ Config('constant.COLLECTION_IMAGE_URL'). $model->image}}" />
                                                @endif
                                                @if ($errors->has('image'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('image') }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </form>
        @if(!empty($products) && count($products) > 0)
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Image</th>
                    <th scope="col">Name</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 1; @endphp
                    @foreach($products as $product)
                        <tr>
                            <th scope="row">{{ $i++ }}</th>
                            <td>
                            @if(!empty($product->front_image))
                                    <img src="{{ $product->front_image }}" height="70px" width="70px" style="border-radius: 10%"></td>
                                @else if
                                    <img src="https://commons.wikimedia.org/wiki/File:No_Image_Available.jpg" height="70px" width="70px" style="border-radius: 10%"></td>
                                @endif 
                            </td>
                            <td>{{ $product->name }}</td>
                        <td>
                            <form method="POST" action="{{route('admin-collections.removeProduct',[base64_encode($product->id), base64_encode($model->id)])}}">
                                @csrf
                                <input name="_method" type="hidden" value="DELETE">
                                <button type="submit" class="btn btn-danger" id="confirm-button"><i
                                        class="ri-delete-bin-5-line"></i></button>
                            </form>
                        </td>
                            
                        </tr>
                    @endforeach

            </tbody>
        </table>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<!-- Select2 Cdn -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/plugin/tagify/tagify.min.js') }}"></script>

<!-- Internal Select-2.js -->
<script src="{{ asset('assets/js/select2.js') }}"></script>

<script src="{{ asset('assets/libs/dropzone/dropzone-min.js') }}"></script>

<script src="{{ asset('assets/js/custom/product.js') }}"></script>

{{-- <script src="{{ asset('assets/js/fileupload.js') }}"></script> --}}
<script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<!-- <script src="{{ asset('assets/js/form-validation.js') }}"></script> -->

<script>
    CKEDITOR.replace('description', {
        filebrowserUploadUrl: '{{ url('base/uploder') }}',
        enterMode: CKEDITOR.ENTER_BR
    });
</script>
@endpush