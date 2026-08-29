@extends('admin.layout.master')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
@endpush
@section('content')
@include('admin.layout.response_message')

@php
    $pageTitle = match(request()->query('type')) {
        'sub-category' => 'Sub Categories',
        'child-category' => 'Child Categories',
        default => 'Categories',
    };

    $query = http_build_query([
        'type' => request()->query('type'),
        'endesid' => request()->query('endesid'),
    ]);
@endphp
<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
<a href="{{ route('admin-category.index') }}?{{ $query }}" class="btn btn-dark">
  
    Back
</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit {{$pageTitle}}</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->

<div class="row">
    <div class="col-xl-12">
        <form action="{{ route('admin-' . $model . '.update', base64_encode($category->id)) }}" method="post"
            id="categoryForm" enctype="multipart/form-data">
            @csrf
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Edit {{$pageTitle}}
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($pageTitle) && $pageTitle == 'Categories')
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="select_category_type" class="form-label"><span
                                            class="text-danger">*</span>Select Category Type</label>
                                    <select
                                        class="select2-original form-control @error('select_category_type') is-invalid @enderror"
                                        name="select_category_type" id="select_category_type">
                                        <option value="">Select Type</option>
                                        <option value="1" {{ $category->category_type_id == 1 ? 'selected' : '' }}>
                                            Collection</option>
                                        <option value="2" {{ $category->category_type_id == 2 ? 'selected' : '' }}>
                                            Category</option>
                                    </select>
                                    @if ($errors->has('select_category_type'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('select_category_type') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                
                    @if(isset($pageTitle) && $pageTitle != 'Categories')
                        <input type="hidden" name="parent_id" value="{{ request()->query('endesid') }}">
                        <input type="hidden" name="type" value="{{ request()->query('type') }}">
                    @endif
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card-body p-0">

                                <div class="mb-3">
                                    <label for="name" class="form-label"><span class="text-danger">*
                                        </span>Name</label>
                                    <input type="text" class="form-control" id="edit_name" name="name"
                                        placeholder="Enter Name" onkeyup="editDisplaySlug($(this))"
                                        value="{{ $category->name }}">
                                    {{-- <h6 class="edit-category-slug mt-2"></h6> --}}
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                                    <div class="card-body p-0">
                                        <div class="mb-3">
                                            <label for="show_on_home" class="form-label">
                                                Show On Home Page
                                            </label>

                                            <input type="checkbox" id="show_on_home" name="show_on_home" value="1"
                                                {{ old('show_on_home', $category->show_on_home ?? 0) == 1 ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 mb-3">
                            <label for="category" class="form-label">Url</label>
                            <input type="url" class="form-control"name="url" value="{{ $category->url }}">
                        </div>
                        <div class="col-xl-6 mb-3 oldSlug">
                            <label for="category" class="form-label"><span class="text-danger">*
                                </span>Slug</label>
                            <input type="text" class="form-control edit-category" disabled
                                value="{{ $category->slug }}">
                        </div>
                        <div class="col-xl-6 mb-3 newSlug" style="display: none">
                            <label for="category" class="form-label"><span class="text-danger">*
                                </span>Slug</label>
                            <input type="text" class="form-control edit-category-slug" disabled value="">
                        </div>
                        <div class="col-xl-6 mb-3">
                            <label for="image" class="form-label"><span class="text-danger">
                                </span>Banner Image</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                                name="image" accept="image/*">
                                
                            @if (!empty($category->image))
                                <img src="{{ asset($category->image) }}" width="50" height="50" alt="Category Image">
                            @endif
                            @if ($errors->has('image'))
                            <div class="invalid-feedback">
                                {{ $errors->first('image') }}
                            </div>
                            @endif
                        </div>
                        <div class="col-xl-6 mb-3">
                            <label for="thumbnail_image" class="form-label"><span class="text-danger">
                                </span>Thumbnail Image</label>
                            <input type="file" class="form-control @error('thumbnail_image') is-invalid @enderror"
                                id="thumbnail_image" name="thumbnail_image" accept="image/*">
                            @if (!empty($category->thumbnail_image))
                            <img height="50" width="50"
                                src="{{ isset($category->thumbnail_image) ? $category->thumbnail_image : '' }}" />
                            @endif
                            @if ($errors->has('thumbnail_image'))
                            <div class="invalid-feedback">
                                {{ $errors->first('thumbnail_image') }}
                            </div>
                            @endif
                        </div>
                        <div class="col-xl-6 mb-3">
                            <label for="thumbnail_image" class="form-label">Thumbnail Image Width</label>
                            <input type="text" class="form-control " id="width" name="width"
                                value="{{ $category->width }}">

                        </div>
                        <div class="col-xl-6 mb-3">
                            <label for="thumbnail_image" class="form-label">Thumbnail Image Height</label>
                            <input type="text" class="form-control " id="height" name="height"
                                value="{{ $category->height }}">

                        </div>
                        <div class="col-xl-6 mb-3">
                            <label for="video" class="form-label"><span class="text-danger"> </span>Video</label>
                            <input type="file" class="form-control @error('video') is-invalid @enderror" id="video"
                                name="video" accept="video/*">
                            @if (!empty($category->video))
                            <video height="70" controls>
                                <source src="{{ isset($category->video) ? $category->video : '' }}" type="video/mp4">
                            </video>
                            @endif
                            @if ($errors->has('video'))
                            <div class="invalid-feedback">
                                {{ $errors->first('video') }}
                            </div>
                            @endif
                        </div>
                        <div class="col-xl-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('title') is-invalid @enderror" name="description"
                                id="description" cols="30"
                                rows="5">{!! isset($category->description) ? $category->description : old('description') !!}</textarea>
                            @if ($errors->has('description'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('description') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            @php
            $selected_size_chart = 1; // this value can be dynamic in future
            @endphp
            @if ($selected_size_chart == 1)
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Manage Size Chart
                    </div>
                </div>

          
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6 mb-3">
                            <label for="name" class="form-label"><span class="text-danger">
                                </span>Chart Title</label>
                            <input type="text" class="form-control" id="chart_title"  name="chart_title"
                                placeholder="Enter Chart  Title" value="{{ $category->chart_title ?? '' }}">
                        </div>
                        <div class="col-xl-10 mb-3">
                            <label for="mesurement_type_inch" class="form-label"><span class="text-danger">
                                </span>Inch </label>
                            <input type="radio" checked id="mesurement_type_inch" name="mesurement_type" value="inch"
                                onclick="changeMesurementType('inch')">
                            <label for="mesurement_type_cm" class="form-label"><span class="text-danger">
                                </span>CM </label>
                            <input type="radio" id="mesurement_type_cm" name="mesurement_type" value="cm"
                                onclick="changeMesurementType('cm')">
                        </div>
                        <div class="col-xl-10 mb-3 mesurement_type_inch_div" id="">

                            <div class="table-responsive">
                                <table id="sizeChartTableUpper" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Upper</th>
                                            <th>XS</th>
                                            <th>S</th>
                                            <th>M</th>
                                            <th>L</th>
                                            <th>XL</th>
                                            <th>2XL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $measurement_inch = '';
                                        if (
                                        $chart_measurement &&
                                        !empty($chart_measurement->measurement_inch)
                                        ) {
                                        $measurement_inch = json_decode(
                                        $chart_measurement->measurement_inch,
                                        true,
                                        );
                                        }

                                        @endphp
                                        @if (!empty($measurement_inch) && is_array($measurement_inch))
                                        @foreach ($measurement_inch['upper'] as $u_type => $inch)
                                        <tr>
                                            <td><input type="text" name="upper_type[]" required class="form-control"
                                                    placeholder="e.g., chest, hip" value="{{ $u_type }}">
                                            </td>
                                            <td><input type="number" name="top_size_xs[]" step="0.0001"
                                                    class="form-control" value="{{ $inch['xs'] }}">
                                            </td>
                                            <td><input type="number" name="top_size_s[]" step="0.0001"
                                                    class="form-control" value="{{ $inch['s'] }}">
                                            </td>
                                            <td><input type="number" name="top_size_m[]" step="0.0001"
                                                    class="form-control" value="{{ $inch['m'] }}">
                                            </td>
                                            <td><input type="number" name="top_size_l[]" step="0.0001"
                                                    class="form-control" value="{{ $inch['l'] }}">
                                            </td>
                                            <td><input type="number" name="top_size_xl[]" step="0.0001"
                                                    class="form-control" value="{{ $inch['xl'] }}">
                                            </td>
                                            <td><input type="number" name="top_size_2xl[]" step="0.0001"
                                                    class="form-control" value="{{ $inch['2xl'] }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger removeRowUpper">X</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                       
                                                                                                                 
                                        @endif
                                    </tbody>
                                </table>
                                <button type="button" id="addRowUpper" class="btn btn-primary">Add
                                    More</button>
                            </div>
                        </div>

                        <div class="col-xl-10 mb-3 mesurement_type_inch_div">
                            <div class="table-responsive">
                                <table id="sizeChartTableBottom" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Bottom</th>
                                            <th>XS</th>
                                            <th>S</th>
                                            <th>M</th>
                                            <th>L</th>
                                            <th>XL</th>
                                            <th>2XL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($measurement_inch) && is_array($measurement_inch))
                                        @foreach ($measurement_inch['bottom'] as $u_type => $inch)
                                        <tr>
                                            <td><input type="text" name="bottom_type[]" required class="form-control"
                                                    placeholder="e.g., chest, hip" value="{{ $u_type }}"></td>
                                            <td><input type="number" name="bottom_size_xs[]" step="0.0001"
                                                    class="form-control" value="{{ $inch['xs'] }}"></td>
                                            <td><input type="number" name="bottom_size_s[]" step="0.0001"
                                                    class="form-control" value="{{ $inch['s'] }}"></td>
                                            <td><input type="number" name="bottom_size_m[]" step="0.0001"
                                                    class="form-control" value="{{ $inch['m'] }}"></td>
                                            <td><input type="number" name="bottom_size_l[]" step="0.0001"
                                                    class="form-control" value="{{ $inch['l'] }}"></td>
                                            <td><input type="number" name="bottom_size_xl[]" step="0.0001"
                                                    class="form-control" value="{{ $inch['xl'] }}"></td>
                                            <td><input type="number" name="bottom_size_2xl[]" step="0.0001"
                                                    class="form-control" value="{{ $inch['2xl'] }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger removeRowBottom">X</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        
                                        @endif
                                    </tbody>
                                </table>
                                <button type="button" id="addRowBottom" class="btn btn-primary">Add
                                    More</button>
                            </div>
                        </div>

                        <div class="col-xl-10 mb-3 mesurement_type_cm_div" style="display:none;">

                            <div class="table-responsive">
                                <table id="sizeChartTableUpperCM" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Upper</th>
                                            <th>XS</th>
                                            <th>S</th>
                                            <th>M</th>
                                            <th>L</th>
                                            <th>XL</th>
                                            <th>2XL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $measurement_cm = '';
                                        if (
                                        $chart_measurement &&
                                        !empty($chart_measurement->measurement_cm)
                                        ) {
                                        $measurement_cm = json_decode(
                                        $chart_measurement->measurement_cm,
                                        true,
                                        );
                                        }

                                        @endphp
                                        @if (!empty($measurement_cm) && is_array($measurement_cm))
                                        @foreach ($measurement_cm['upper'] as $u_type => $cm)
                                        <tr>
                                            <td><input type="text" name="upper_type_cm[]" required class="form-control"
                                                    placeholder="e.g., chest, hip" value="{{ $u_type }}">
                                            </td>
                                            <td><input type="number" name="top_size_cm_xs[]" step="0.0001"
                                                    class="form-control" value="{{ $cm['xs'] }}"></td>

                                            <td><input type="number" name="top_size_cm_s[]" step="0.0001"
                                                    class="form-control" value="{{ $cm['s'] }}"></td>

                                            <td><input type="number" name="top_size_cm_m[]" step="0.0001"
                                                    class="form-control" value="{{ $cm['m'] }}"></td>

                                            <td><input type="number" name="top_size_cm_l[]" step="0.0001"
                                                    class="form-control" value="{{ $cm['l'] }}"></td>

                                            <td><input type="number" name="top_size_cm_xl[]" step="0.0001"
                                                    class="form-control" value="{{ $cm['xl'] }}"></td>

                                            <td><input type="number" name="top_size_cm_2xl[]" step="0.0001"
                                                    class="form-control" value="{{ $cm['2xl'] }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger removeRowUpperCM">X</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                      
                                        @endif
                                    </tbody>
                                </table>
                                <button type="button" id="addRowUpperCM" class="btn btn-primary">Add
                                    More</button>
                            </div>
                        </div>

                        <div class="col-xl-10 mb-3 mesurement_type_cm_div" style="display:none;">
                            <div class="table-responsive">
                                <table id="sizeChartTableBottomCM" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Bottom</th>
                                            <th>XS</th>
                                            <th>S</th>
                                            <th>M</th>
                                            <th>L</th>
                                            <th>XL</th>
                                            <th>2XL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($measurement_cm) && is_array($measurement_cm))
                                        @foreach ($measurement_cm['bottom'] as $u_type => $cm)
                                        <tr>
                                            <td><input type="text" name="bottom_type_cm[]" required class="form-control"
                                                    placeholder="e.g., chest, hip" value="{{ $u_type }}"></td>
                                            <td><input type="number" name="bottom_size_cm_xs[]" step="0.0001"
                                                    class="form-control" value="{{ $cm['xs'] }}"></td>
                                            <td><input type="number" name="bottom_size_cm_s[]" step="0.0001"
                                                    class="form-control" value="{{ $cm['s'] }}"></td>
                                            <td><input type="number" name="bottom_size_cm_m[]" step="0.0001"
                                                    class="form-control" value="{{ $cm['m'] }}"></td>
                                            <td><input type="number" name="bottom_size_cm_l[]" step="0.0001"
                                                    class="form-control" value="{{ $cm['l'] }}"></td>
                                            <td><input type="number" name="bottom_size_cm_xl[]" step="0.0001"
                                                    class="form-control" value="{{ $cm['xl'] }}"></td>
                                            <td><input type="number" name="bottom_size_cm_2xl[]" step="0.0001"
                                                    class="form-control" value="{{ $cm['2xl'] }}">
                                            </td>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-danger removeRowBottomCM">X</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                       
                                        @endif
                                    </tbody>
                                </table>
                                <button type="button" id="addRowBottomCM" class="btn btn-primary">Add
                                    More</button>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Chart Uppar Title</label>
                                    <input type="text"
                                        class="form-control @error('uppar_chart_title') is-invalid @enderror"
                                        id="uppar_chart_title" name="uppar_chart_title" placeholder="Enter Chart Title"
                                        value="{{ $category->uppar_chart_title }}">
                                    {{-- <h6 class="category-slug mt-2"></h6> --}}
                                    @if ($errors->has('uppar_chart_title'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('uppar_chart_title') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 mb-3">
                            <label for="image" class="form-label">Chart Uppar Image</label>
                            <input type="file" class="form-control @error('uppar_chart_image') is-invalid @enderror"
                                id="uppar_chart_image" name="uppar_chart_image" accept="image/*">
                            @if ($errors->has('uppar_chart_image'))
                            <div class="invalid-feedback">
                                {{ $errors->first('uppar_chart_image') }}
                            </div>
                            @endif
                        </div>

                        <div class="col-xl-6">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Chart Bottom Title</label>
                                    <input type="text"
                                        class="form-control @error('bootom_chart_title') is-invalid @enderror"
                                        id="bootom_chart_title" name="bootom_chart_title"
                                        placeholder="Enter Bottom Chart Title"
                                        value="{{ $category->bootom_chart_title }}">
                                    @if ($errors->has('bootom_chart_title'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('bootom_chart_title') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 mb-3">
                            <label for="image" class="form-label">Chart Bottom Image</label>
                            <input type="file" class="form-control @error('bottom_chart_image') is-invalid @enderror"
                                id="bottom_chart_image" name="bottom_chart_image" accept="image/*">
                            @if ($errors->has('bottom_chart_image'))
                            <div class="invalid-feedback">
                                {{ $errors->first('bottom_chart_image') }}
                            </div>
                            @endif
                        </div>
                        <div class="col-xl-12 mb-3">
                            <label for="seo_data" class="form-label">Chart Description</label>
                            <textarea class="form-control" name="chart_description" id="chart_description" cols="30"
                                rows="5">{{ $category->description ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Variants
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-12 select2-error">
                            <label for="category_id" class="form-label"><span class="text-danger">
                                </span>Variants</label>
                            <select class="js-example-placeholder-single js-states form-control" multiple="multiple"
                                name="variantsData[]" id="variantsSelect">
                               
                                @forelse ($variants as $variant)
                                <option value="{{ $variant->id }}"
                                    {{ in_array($variant->id, $categoryVariants) ? 'selected' : '' }}>
                                    {{ $variant->name }}</option>
                                @empty
                                <option value="" selected>No Data found</option>
                                @endforelse
                            </select>

                        </div>
                    </div>
                </div>
            </div>


            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Attributes
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-12 select2-error">
                            <label for="category_id" class="form-label"><span class="text-danger">
                                </span>Attributes</label>
                            <select class="js-example-placeholder-single js-states form-control" multiple="multiple"
                                name="attributesData[]" id="attributesSelect">
                                <!-- <option value="" selected>None</option> -->
                                @forelse ($attributes as $attribute)
                                <option value="{{ $attribute->id }}"
                                    {{ in_array($attribute->id, $categoryAttribute) ? 'selected' : '' }}>
                                    {{ $attribute->name }}</option>
                                @empty
                                <option value="" selected>No Data found</option>
                                @endforelse
                            </select>

                        </div>
                    </div>
                </div>
            </div>

            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Taxes
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-12 select2-error">
                            <label for="category_id" class="form-label"><span class="text-danger">
                                </span>Taxes</label>
                            <select class="js-example-placeholder-single js-states form-control" multiple="multiple"
                                name="taxesData[]" id="taxesSelect">
                                @forelse ($taxes as $tax)
                                <option value="{{ $tax->id }}"
                                    {{ in_array($tax->id, $categoryTaxes) ? 'selected' : '' }}>{{ $tax->name }}
                                </option>
                                @empty
                                <option value="" selected>No Data found</option>
                                @endforelse
                            </select>

                        </div>
                        <input type="hidden" id="taxValues" value="{{ json_encode($categoryTaxesValues) }}">

                        <div id="taxCountFields">

                       
                            @forelse($taxes as $tax)
                           <div class="mb-3 taxDiv{{ $tax->id }} taxContainers"
                                style="{{ isset($categoryTaxesValues[$tax->id]) ? '' : 'display:none' }}">
                                <label for="tax_counts[{{ $tax->id }}]" class="form-label">
                                    {{ $tax->name ?? '' }}
                                </label>
                                <input type="text"
                                    class="form-control"
                                    id="tax_counts[{{ $tax->id }}]"
                                    name="tax_counts[{{ $tax->id }}]"
                                    placeholder="Enter Value"
                                    value="{{ $categoryTaxesValues[$tax->id] ?? '' }}">
                            </div>
                            @empty
                            @endforelse



                        </div>
                    </div>
                </div>
            </div>
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Footer SEO data
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-12 mb-3">
                            <label for="seo_data" class="form-label">Seo Data</label>
                            <textarea class="form-control" name="seo_data" id="seo_data" cols="30"
                                rows="5">{{ $category->value }}</textarea>
                        </div>

                        <div class="col-xl-6 mb-3">
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" class="form-control" id="meta_title" name="meta_title"
                                placeholder="Meta TItle" value="{{ $category->meta_title }}">
                        </div>
                        <div class="col-xl-6 mb-3">
                            <label for="meta_keywords" class="form-label">Meta Keywords</label>
                            <input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
                                placeholder="Meta Keywords" value="{{ $category->meta_keywords }}">
                        </div>
                        <div class="col-xl-12 mb-3">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea class="form-control" name="meta_description" id="meta_description" cols="30"
                                rows="5">{{ $category->meta_description }}</textarea>
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
<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="{{ asset('assets/js/form-validation.js') }}"></script>
<script src="{{ asset('assets/js/custom/category.js') }}"></script>
<script>

const inchToCm = 2.54;

$(function() {
    // Tax field toggle
    $('#taxesSelect').on('change', function() {
        const selectedTaxes = $(this).val() || [];
        $('.taxContainers').hide().find('input').prop('disabled', true);
        selectedTaxes.forEach(id => $(`.taxDiv${id}`).show().find('input').prop('disabled', false));
    });
 
    // CKEditor init
    ['description', 'seo_data', 'chart_description'].forEach(id => {
    
      
        CKEDITOR.replace(id, {
            filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
            enterMode: CKEDITOR.ENTER_BR
        });
        CKEDITOR.config.allowedContent = true;
    });

    const updateCMTable = (sourceSelector, targetSelector, inputNameMap) => {
        const $rows = $(`${sourceSelector} tbody tr`);
        const $cmBody = $(`${targetSelector} tbody`).empty();
        $rows.each(function() {
            const $inputs = $(this).find('input');
            const $cmRow = $('<tr>');
            $inputs.each(function(i) {
                const val = $(this).val();
                const name = $(this).attr('name')
                    .replace(inputNameMap.from, inputNameMap.to)
                    .replace(inputNameMap.typeFrom, inputNameMap.typeTo);
                const cmVal = i === 0 ? val : (val ? (parseFloat(val) * inchToCm).toFixed(
                    2) : '');
                $cmRow.append(
                    `<td><input type="${i === 0 ? 'text' : 'number'}" step="0.0001" name="${name}" class="form-control" value="${cmVal}"></td>`
                    );
            });
            $cmRow.append(
                `<td><button type="button" class="btn btn-danger ${inputNameMap.removeClass}">X</button></td>`
                );
            $cmBody.append($cmRow);
        });
    };

    const bindSizeChartEvents = (section) => {
        const upper = section === 'Upper';
        const source = `#sizeChartTable${section}`;
        const target = `#sizeChartTable${section}CM`;
        const prefix = upper ? 'top' : 'bottom';

        $(document).on('input', `${source} input`, () => {
            updateCMTable(source, target, {
                from: `${prefix}_size_`,
                to: `${prefix}_size_cm_`,
                typeFrom: `${prefix}_type`,
                typeTo: `${prefix}_type_cm`,
                removeClass: `removeRow${section}CM`
            });
        });

        $(`#addRow${section}`).on('click', () => {
            const row = `
                    <tr>
                        <td><input type="text" name="${prefix}_type[]" class="form-control" placeholder="e.g., chest"></td>
                        ${['xs','s','m','l','xl','2xl'].map(size => `<td><input type="number" name="${prefix}_size_${size}[]" step="0.0001" class="form-control"></td>`).join('')}
                        <td><button type="button" class="btn btn-danger removeRow${section}">X</button></td>
                    </tr>`;
            $(`${source} tbody`).append(row);
            updateCMTable(source, target, {
                from: `${prefix}_size_`,
                to: `${prefix}_size_cm_`,
                typeFrom: `${prefix}_type`,
                typeTo: `${prefix}_type_cm`,
                removeClass: `removeRow${section}CM`
            });
        });

        $(document).on('click', `.removeRow${section}`, function() {
            $(this).closest('tr').remove();
            updateCMTable(source, target, {
                from: `${prefix}_size_`,
                to: `${prefix}_size_cm_`,
                typeFrom: `${prefix}_type`,
                typeTo: `${prefix}_type_cm`,
                removeClass: `removeRow${section}CM`
            });
        });

        $(document).on('click', `.removeRow${section}CM`, function() {
            $(this).closest('tr').remove();
        });

        updateCMTable(source, target, {
            from: `${prefix}_size_`,
            to: `${prefix}_size_cm_`,
            typeFrom: `${prefix}_type`,
            typeTo: `${prefix}_type_cm`,
            removeClass: `removeRow${section}CM`
        });
    };

    bindSizeChartEvents('Upper');
    bindSizeChartEvents('Bottom');

    // Measurement toggle
    window.changeMesurementType = type => {
        $('.mesurement_type_inch_div').toggle(type === 'inch');
        $('.mesurement_type_cm_div').toggle(type !== 'inch');
    };
});
</script>
@endpush