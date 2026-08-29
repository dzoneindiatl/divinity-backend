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
    <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Size Chart</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->

<div class="row">
    <div class="col-xl-12">
        <form action="{{route('admin-'.$model.'.update',base64_encode($size_charts->id))}}" method="post" id="shippingcompanyForm"
            enctype="multipart/form-data">
            @csrf
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Edit Size Chart
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card-body p-0">

                                <div class="mb-3">
                                    <label for="name" class="form-label"><span class="text-danger">* </span>Name</label>
                                    <input type="text" class="form-control" id="edit_name" name="name"
                                        placeholder="Enter Name" value="{{ $size_charts->name }}">
                                </div>
                                @if ($errors->has('name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('name') }}
                                </div>
                                @endif

                            </div>
                        </div>
                        <div class="col-xl-6">
                            <label for="file" class="form-label"><span class="text-danger">
                                </span>File/Image</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file">
                            @if (!empty($size_charts->file))
                                <img height="50" width="50" src="{{isset($size_charts->file)? $size_charts->file:''}}" />
                            @endif
                            @if ($errors->has('file'))
                            <div class="invalid-feedback">
                                {{ $errors->first('file') }}
                            </div>
                            @endif
                        </div>
                        <div class="col-xl-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('title') is-invalid @enderror" name="description" id="description" cols="30" rows="5">{!! isset($size_charts->description) ? $size_charts->description: old('description') !!}</textarea>
                            @if ($errors->has('description'))
                                <div class=" invalid-feedback">
                                    {{ $errors->first('description') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Assign To
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6" id="categoryDropdown">
                            <label for="reference" class="form-label">Categories</label>
                            <select class="js-example-placeholder-single js-states form-control" multiple="multiple" name="categoryData[]">
                                @forelse ($categories as $category)
                                <option value="{{ $category['id'] }}" {{in_array($category['id'],$category_assigned) ? 'selected' : ''}}>{{ $category['name'] }}</option>
                                @empty
                                <option value="" selected>No Data found</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="col-xl-6" id="productDropdown">
                            <label for="reference" class="form-label">Products</label>
                            <select class="js-example-placeholder-single js-states form-control" multiple="multiple" name="productData[]">
                                @forelse ($products as $product)
                                <option value="{{ $product->id }}" {{in_array($product->id, $product_assigned) ? 'selected' : ''}}>{{ $product->name }}</option>
                                @empty
                                <option value="" selected>No Data found</option>
                                @endforelse
                            </select>
                        </div>
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
    CKEDITOR.replace(<?php echo 'description'; ?>, {
        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
        enterMode: CKEDITOR.ENTER_BR
    });
    CKEDITOR.config.allowedContent = true;
</script>
@endpush