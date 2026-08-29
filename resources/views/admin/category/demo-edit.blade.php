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
                    <li class="breadcrumb-item active" aria-current="page">Edit Category</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->

    <div class="row">
        <div class="col-xl-12">
            <form action="{{ route('admin-' . $model . '.updateDemo', base64_encode($category->id)) }}" method="post"
                id="categoryForm" enctype="multipart/form-data">
                @csrf
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Edit Category
                        </div>
                    </div>
                    <div class="card-body">
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
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                    id="image" name="image">
                                @if (!empty($category->image))
                                    <img height="50" width="50"
                                        src="{{ isset($category->image) ? $category->image : '' }}" />
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
                                    id="thumbnail_image" name="thumbnail_image">
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
                                <label for="thumbnail_image" class="form-label"><span class="text-danger">
                                    </span>Thumbnail Image Width</label>
                                <input type="text" class="form-control @error('width') is-invalid @enderror"
                                    id="width" name="width" value="{{ $category->width }}">
                                @if ($errors->has('width'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('width') }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-xl-6 mb-3">
                                <label for="thumbnail_image" class="form-label"><span class="text-danger">
                                    </span>Thumbnail Image Height</label>
                                <input type="text" class="form-control @error('height') is-invalid @enderror"
                                    id="height" name="height" value="{{ $category->height }}">
                                @if ($errors->has('height'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('height') }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-xl-6 mb-3">
                                <label for="video" class="form-label"><span class="text-danger"> </span>Video</label>
                                <input type="file" class="form-control @error('video') is-invalid @enderror"
                                    id="video" name="video">
                                @if (!empty($category->video))
                                    <video height="70" controls>
                                        <source src="{{ isset($category->video) ? $category->video : '' }}"
                                            type="video/mp4">
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
                                <textarea class="form-control @error('title') is-invalid @enderror" name="description" id="description"
                                    cols="30" rows="5">{!! isset($category->description) ? $category->description : old('description') !!}</textarea>
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
                                    <label for="name" class="form-label"><span class="text-danger">*
                                        </span>Chart Title</label>
                                    <input type="text" class="form-control" id="chart_title" required
                                        name="chart_title" placeholder="Enter Chart  Title"
                                        value="{{ $chart_content->title }}">
                                </div>
                                <div class="col-xl-10 mb-3">
                                    <label for="mesurement_type_inch" class="form-label"><span class="text-danger">
                                        </span>Inch </label>
                                    <input type="radio" checked id="mesurement_type_inch" name="mesurement_type"
                                        value="inch" onclick="changeMesurementType('inch')">
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
                                                    if ($chart_measurement->measurement_inch != '') {
                                                        $measurement_inch = json_decode(
                                                            $chart_measurement->measurement_inch,
                                                            true,
                                                        );
                                                    }

                                                @endphp
                                                @if (!empty($measurement_inch) && is_array($measurement_inch))
                                                    @foreach ($measurement_inch['upper'] as $u_type => $inch)
                                                        <tr>
                                                            <td><input type="text" name="upper_type[]" required
                                                                    class="form-control" placeholder="e.g., chest, hip"
                                                                    value="{{ $u_type }}">
                                                            </td>
                                                            <td><input type="number" name="top_size_xs[]" step="0.1"
                                                                    class="form-control" value="{{ $inch['xs'] }}">
                                                            </td>
                                                            <td><input type="number" name="top_size_s[]" step="0.1"
                                                                    class="form-control" value="{{ $inch['s'] }}">
                                                            </td>
                                                            <td><input type="number" name="top_size_m[]" step="0.1"
                                                                    class="form-control" value="{{ $inch['m'] }}">
                                                            </td>
                                                            <td><input type="number" name="top_size_l[]" step="0.1"
                                                                    class="form-control" value="{{ $inch['l'] }}">
                                                            </td>
                                                            <td><input type="number" name="top_size_xl[]" step="0.1"
                                                                    class="form-control" value="{{ $inch['xl'] }}">
                                                            </td>
                                                            <td><input type="number" name="top_size_2xl[]"
                                                                    step="0.1" class="form-control"
                                                                    value="{{ $inch['2xl'] }}">
                                                            </td>
                                                            <td>
                                                                <button type="button"
                                                                    class="btn btn-danger removeRowUpper">X</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td><input type="text" name="upper_type[]" required
                                                                class="form-control" placeholder="e.g., chest, hip">
                                                        </td>
                                                        <td><input type="number" name="top_size_xs[]" step="0.1"
                                                                class="form-control"></td>
                                                        <td><input type="number" name="top_size_s[]" step="0.1"
                                                                class="form-control"></td>
                                                        <td><input type="number" name="top_size_m[]" step="0.1"
                                                                class="form-control"></td>
                                                        <td><input type="number" name="top_size_l[]" step="0.1"
                                                                class="form-control"></td>
                                                        <td><input type="number" name="top_size_xl[]" step="0.1"
                                                                class="form-control"></td>
                                                        <td><input type="number" name="top_size_2xl[]" step="0.1"
                                                                class="form-control">
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-danger removeRowUpper">X</button>
                                                        </td>
                                                    </tr>
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
                                                            <td><input type="text" name="bottom_type[]" required
                                                                    class="form-control" placeholder="e.g., chest, hip"
                                                                    value="{{ $u_type }}"></td>
                                                            <td><input type="number" name="bottom_size_xs[]"
                                                                    step="0.1" class="form-control"
                                                                    value="{{ $inch['xs'] }}"></td>
                                                            <td><input type="number" name="bottom_size_s[]"
                                                                    step="0.1" class="form-control"
                                                                    value="{{ $inch['s'] }}"></td>
                                                            <td><input type="number" name="bottom_size_m[]"
                                                                    step="0.1" class="form-control"
                                                                    value="{{ $inch['m'] }}"></td>
                                                            <td><input type="number" name="bottom_size_l[]"
                                                                    step="0.1" class="form-control"
                                                                    value="{{ $inch['l'] }}"></td>
                                                            <td><input type="number" name="bottom_size_xl[]"
                                                                    step="0.1" class="form-control"
                                                                    value="{{ $inch['xl'] }}"></td>
                                                            <td><input type="number" name="bottom_size_2xl[]"
                                                                    step="0.1" class="form-control"
                                                                    value="{{ $inch['2xl'] }}">
                                                            </td>
                                                            <td>
                                                                <button type="button"
                                                                    class="btn btn-danger removeRowBottom">X</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td><input type="text" name="bottom_type[]" required
                                                                class="form-control" placeholder="e.g., chest, hip"></td>
                                                        <td><input type="number" name="bottom_size_xs[]" step="0.1"
                                                                class="form-control"></td>
                                                        <td><input type="number" name="bottom_size_s[]" step="0.1"
                                                                class="form-control"></td>
                                                        <td><input type="number" name="bottom_size_m[]" step="0.1"
                                                                class="form-control"></td>
                                                        <td><input type="number" name="bottom_size_l[]" step="0.1"
                                                                class="form-control"></td>
                                                        <td><input type="number" name="bottom_size_xl[]" step="0.1"
                                                                class="form-control"></td>
                                                        <td><input type="number" name="bottom_size_2xl[]" step="0.1"
                                                                class="form-control">
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-danger removeRowBottom">X</button>
                                                        </td>
                                                    </tr>
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
                                                    if ($chart_measurement->measurement_cm != '') {
                                                        $measurement_cm = json_decode(
                                                            $chart_measurement->measurement_cm,
                                                            true,
                                                        );
                                                    }

                                                @endphp
                                                @if (!empty($measurement_cm) && is_array($measurement_cm))
                                                    @foreach ($measurement_cm['upper'] as $u_type => $cm)
                                                        <tr>
                                                            <td><input type="text" name="upper_type_cm[]" required
                                                                    class="form-control" placeholder="e.g., chest, hip"
                                                                    value="{{ $u_type }}">
                                                            </td>
                                                            <td><input type="number" name="top_size_cm_xs[]"
                                                                    step="0.1" class="form-control"
                                                                    value="{{ $cm['xs'] }}"></td>

                                                            <td><input type="number" name="top_size_cm_s[]"
                                                                    step="0.1" class="form-control"
                                                                    value="{{ $cm['s'] }}"></td>

                                                            <td><input type="number" name="top_size_cm_m[]"
                                                                    step="0.1" class="form-control"
                                                                    value="{{ $cm['m'] }}"></td>

                                                            <td><input type="number" name="top_size_cm_l[]"
                                                                    step="0.1" class="form-control"
                                                                    value="{{ $cm['l'] }}"></td>

                                                            <td><input type="number" name="top_size_cm_xl[]"
                                                                    step="0.1" class="form-control"
                                                                    value="{{ $cm['xl'] }}"></td>

                                                            <td><input type="number" name="top_size_cm_2xl[]"
                                                                    step="0.1" class="form-control"
                                                                    value="{{ $cm['2xl'] }}">
                                                            </td>
                                                            <td>
                                                                <button type="button"
                                                                    class="btn btn-danger removeRowUpperCM">X</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td><input type="text" name="upper_type_cm[]" required
                                                                class="form-control" placeholder="e.g., chest, hip">
                                                        </td>
                                                        <td><input type="number" name="top_size_cm_xs[]" step="0.1"
                                                                class="form-control"></td>
                                                        <td><input type="number" name="top_size_cm_s[]" step="0.1"
                                                                class="form-control"></td>
                                                        <td><input type="number" name="top_size_cm_m[]" step="0.1"
                                                                class="form-control"></td>
                                                        <td><input type="number" name="top_size_cm_l[]" step="0.1"
                                                                class="form-control"></td>
                                                        <td><input type="number" name="top_size_cm_xl[]" step="0.1"
                                                                class="form-control"></td>
                                                        <td><input type="number" name="top_size_cm_2xl[]" step="0.1"
                                                                class="form-control">
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-danger removeRowUpperCM">X</button>
                                                        </td>
                                                    </tr>
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
                                                            <td><input type="text" name="bottom_type_cm[]" required
                                                                    class="form-control" placeholder="e.g., chest, hip"
                                                                    value="{{ $u_type }}"></td>
                                                            <td><input type="number" name="bottom_size_cm_xs[]"
                                                                    step="0.1" class="form-control"
                                                                    value="{{ $cm['xs'] }}"></td>
                                                            <td><input type="number" name="bottom_size_cm_s[]"
                                                                    step="0.1" class="form-control"
                                                                    value="{{ $cm['s'] }}"></td>
                                                            <td><input type="number" name="bottom_size_cm_m[]"
                                                                    step="0.1" class="form-control"
                                                                    value="{{ $cm['m'] }}"></td>
                                                            <td><input type="number" name="bottom_size_cm_l[]"
                                                                    step="0.1" class="form-control"
                                                                    value="{{ $cm['l'] }}"></td>
                                                            <td><input type="number" name="bottom_size_cm_xl[]"
                                                                    step="0.1" class="form-control"
                                                                    value="{{ $cm['xl'] }}"></td>
                                                            <td><input type="number" name="bottom_size_cm_2xl[]"
                                                                    step="0.1" class="form-control"
                                                                    value="{{ $cm['2xl'] }}">
                                                            </td>
                                                            <td>
                                                                <button type="button"
                                                                    class="btn btn-danger removeRowBottomCM">X</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td><input type="text" name="bottom_type_cm[]" required
                                                                class="form-control" placeholder="e.g., chest, hip"></td>
                                                        <td><input type="number" name="bottom_size_cm_xs[]"
                                                                step="0.1" class="form-control"></td>
                                                        <td><input type="number" name="bottom_size_cm_s[]"
                                                                step="0.1" class="form-control"></td>
                                                        <td><input type="number" name="bottom_size_cm_m[]"
                                                                step="0.1" class="form-control"></td>
                                                        <td><input type="number" name="bottom_size_cm_l[]"
                                                                step="0.1" class="form-control"></td>
                                                        <td><input type="number" name="bottom_size_cm_xl[]"
                                                                step="0.1" class="form-control"></td>
                                                        <td><input type="number" name="bottom_size_cm_2xl[]"
                                                                step="0.1" class="form-control">
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-danger removeRowBottomCM">X</button>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                        <button type="button" id="addRowBottomCM" class="btn btn-primary">Add
                                            More</button>
                                    </div>
                                </div>

                                <div class="col-xl-12 mb-3">
                                    <label for="seo_data" class="form-label">Chart Description</label>
                                    <textarea class="form-control" name="chart_description" id="chart_description" cols="30" rows="5">{{ $chart_content->description }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


                <div class="card custom-card" style="display: none">
                    <div class="card-header">
                        <div class="card-title">
                            Specifications
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 select2-error">
                                <label for="category_id" class="form-label"><span class="text-danger">
                                    </span>Specifications</label>
                                <select class="js-example-placeholder-single js-states form-control" multiple="multiple"
                                    name="specificationsData[]" id="specificationsSelect">
                                    <!-- <option value="" selected>None</option> -->
                                    @forelse ($specifications as $specification)
                                        <option value="{{ $specification->id }}"
                                            {{ in_array($specification->id, $categorySpecifications) ? 'selected' : '' }}>
                                            {{ $specification->name }}</option>
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
                                    <!-- <option value="" selected>None</option> -->
                                    @forelse ($taxes as $tax)
                                        <option value="{{ $tax->id }}"
                                            {{ in_array($tax->id, $categoryTaxes) ? 'selected' : '' }}>
                                            {{ $tax->name }}
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
                                        style="{{ ($key = array_search($tax->id, $categoryTaxesValues)) ? '' : 'display:none' }}">
                                        <label for="tax_counts[{{ $tax->id }}]"
                                            class="form-label">{{ $tax->name ?? '' }}</label><input type="text"
                                            class="form-control" id="tax_counts[{{ $tax->id }}]"
                                            name="tax_counts[{{ $tax->id }}]" placeholder="Enter Value"
                                            value="{{ ($key = array_search($tax->id, $categoryTaxesValues)) ? $key : '' }}"
                                            fdprocessedid="fdaby">
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
                                <textarea class="form-control" name="meta_description" id="meta_description" cols="30" rows="5">{{ $category->meta_description }}</textarea>
                            </div>
                            <div class="col-xl-12 mb-3">
                                <label for="seo_data" class="form-label">Seo Data</label>
                                <textarea class="form-control" name="seo_data" id="seo_data" cols="30" rows="5">{{ $category->value }}</textarea>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Add an event listener to the taxesSelect dropdown
            $('#taxesSelect').on('change', function() {

                var selectedTaxes = $(this).val();
                $('.taxContainers').hide();
                $('.taxContainers').find('input').prop('disabled', true);
                // Check if any taxes are selected
                if (selectedTaxes && selectedTaxes.length > 0) {

                    // Create a text field for each selected tax
                    selectedTaxes.forEach(function(taxId) {
                        $('.taxDiv' + taxId).show();
                        $('.taxDiv' + taxId).find('input').prop('disabled', false);
                    });
                } else {
                    // Hide the tax count fields div if no taxes are selected
                    $('#taxCountFields').hide();
                }
            });

        });
    </script>

    <script>
        CKEDITOR.replace(<?php echo 'description'; ?>, {
            filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
            enterMode: CKEDITOR.ENTER_BR
        });
        CKEDITOR.config.allowedContent = true;
        CKEDITOR.replace('seo_data', {
            filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
            enterMode: CKEDITOR.ENTER_BR
        });
        CKEDITOR.config.allowedContent = true;

        // Size Chart Section Start
        CKEDITOR.replace('chart_description', {
            filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
            enterMode: CKEDITOR.ENTER_BR
        });
        CKEDITOR.config.allowedContent = true;


        document.getElementById("addRowUpper").addEventListener("click", function() {
            let table = document.getElementById("sizeChartTableUpper").getElementsByTagName('tbody')[0];
            let newRow = table.insertRow();

            newRow.innerHTML = `<td><input type="text" name="upper_type[]" required class="form-control" placeholder="e.g. chest, shoulder"></td>
        <td><input type="number" name="top_size_xs[]" step="0.1" class="form-control"></td>
        <td><input type="number" name="top_size_s[]" required step="0.1" class="form-control"></td>
        <td><input type="number" name="top_size_m[]" required step="0.1" class="form-control"></td>
        <td><input type="number" name="top_size_l[]" step="0.1" class="form-control"></td>
        <td><input type="number" name="top_size_xl[]" step="0.1" class="form-control"></td>
        <td><input type="number" name="top_size_2xl[]" step="0.1" class="form-control"></td><td><button type="button" class="btn btn-danger removeRowUpper">X</button></td>
    `;

            addRemoveEventUpper();
        });

        function addRemoveEventUpper() {
            document.querySelectorAll(".removeRowUpper").forEach(button => {
                button.addEventListener("click", function() {
                    this.closest("tr").remove();
                });
            });
        }

        addRemoveEventUpper();

        document.getElementById("addRowBottom").addEventListener("click", function() {
            let table = document.getElementById("sizeChartTableBottom").getElementsByTagName('tbody')[0];
            let newRow = table.insertRow();

            newRow.innerHTML = `<td><input type="text" name="bottom_type[]" required class="form-control" placeholder="e.g. chest, shoulder"></td>
        <td><input type="number" name="bottom_size_xs[]" step="0.1" class="form-control"></td>
        <td><input type="number" name="bottom_size_s[]" required step="0.1" class="form-control"></td>
        <td><input type="number" name="bottom_size_m[]" required step="0.1" class="form-control"></td>
        <td><input type="number" name="bottom_size_l[]" step="0.1" class="form-control"></td>
        <td><input type="number" name="bottom_size_xl[]" step="0.1" class="form-control"></td>
        <td><input type="number" name="bottom_size_2xl[]" step="0.1" class="form-control"></td><td><button type="button" class="btn btn-danger removeRowBottom">X</button></td>
    `;

            addRemoveEventBottom();
        });

        function addRemoveEventBottom() {
            document.querySelectorAll(".removeRowBottom").forEach(button => {
                button.addEventListener("click", function() {
                    this.closest("tr").remove();
                });
            });
        }

        addRemoveEventBottom();

        // CM

        document.getElementById("addRowUpperCM").addEventListener("click", function() {
            let table = document.getElementById("sizeChartTableUpperCM").getElementsByTagName('tbody')[0];
            let newRow = table.insertRow();

            newRow.innerHTML = `<td><input type="text" name="upper_type_cm[]" required class="form-control" placeholder="e.g. chest, shoulder"></td>
        <td><input type="number" name="top_size_cm_xs[]" step="0.1" class="form-control"></td>
        <td><input type="number" name="top_size_cm_s[]" required step="0.1" class="form-control"></td>
        <td><input type="number" name="top_size_cm_m[]" required step="0.1" class="form-control"></td>
        <td><input type="number" name="top_size_cm_l[]" step="0.1" class="form-control"></td>
        <td><input type="number" name="top_size_cm_xl[]" step="0.1" class="form-control"></td>
        <td><input type="number" name="top_size_cm_2xl[]" step="0.1" class="form-control"></td><td><button type="button" class="btn btn-danger removeRowUpperCM">X</button></td>
    `;

            addRemoveEventUpperCM();
        });

        function addRemoveEventUpperCM() {
            document.querySelectorAll(".removeRowUpperCM").forEach(button => {
                button.addEventListener("click", function() {
                    this.closest("tr").remove();
                });
            });
        }

        addRemoveEventUpperCM();

        document.getElementById("addRowBottomCM").addEventListener("click", function() {
            let table = document.getElementById("sizeChartTableBottomCM").getElementsByTagName('tbody')[0];
            let newRow = table.insertRow();

            newRow.innerHTML = `<td><input type="text" name="bottom_type_cm[]" required class="form-control" placeholder="e.g. chest, shoulder"></td>
        <td><input type="number" name="bottom_size_cm_xs[]" step="0.1" class="form-control"></td>
        <td><input type="number" name="bottom_size_cm_s[]" required step="0.1" class="form-control"></td>
        <td><input type="number" name="bottom_size_cm_m[]" required step="0.1" class="form-control"></td>
        <td><input type="number" name="bottom_size_cm_l[]" step="0.1" class="form-control"></td>
        <td><input type="number" name="bottom_size_cm_xl[]" step="0.1" class="form-control"></td>
        <td><input type="number" name="bottom_size_cm_2xl[]" step="0.1" class="form-control"></td><td><button type="button" class="btn btn-danger removeRowBottomCM">X</button></td>
    `;

            addRemoveEventBottomCM();
        });

        function addRemoveEventBottomCM() {
            document.querySelectorAll(".removeRowBottomCM").forEach(button => {
                button.addEventListener("click", function() {
                    this.closest("tr").remove();
                });
            });
        }

        addRemoveEventBottomCM();
        //end CM

        function changeMesurementType(m_type) {
            if (m_type == 'inch') {
                $('.mesurement_type_cm_div').hide();
                $('.mesurement_type_inch_div').show();
            }
            if (m_type == 'cm') {
                $('.mesurement_type_inch_div').hide();
                $('.mesurement_type_cm_div').show();

            }
        }
        // Size Chart Section end
    </script>
@endpush
