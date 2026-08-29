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
                <li class="breadcrumb-item active" aria-current="page">Create {{$pageTitle}}</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->

<div class="row">
    <div class="col-xl-12">
        <form action="{{ route('admin-category.store') }}" method="post" id="categoryForm"
            enctype="multipart/form-data">
            @csrf

            {{-- Main Category Info --}}
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Create {{$pageTitle}}</div>
                </div>
                <div class="card-body">
                    @if(isset($pageTitle) && $pageTitle === 'Categories')
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="select_category_type" class="form-label">
                                        <span class="text-danger">*</span>Select Category Type
                                    </label>
                                    <select
                                        class="select2-original form-control @error('select_category_type') is-invalid @enderror"
                                        name="select_category_type" id="select_category_type" required
                                        data-msg-required="Please select a category">
                                        <option value="">Select Type</option>
                                        <option value="1" {{ old('select_category_type') == '1' ? 'selected' : '' }}>
                                            Collection</option>
                                        <option value="2"
                                            {{ old('select_category_type', '2') == '2' ? 'selected' : '' }}>Category
                                        </option>
                                    </select>
                                    @error('select_category_type')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
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
                                    <label for="name" class="form-label">
                                        <span class="text-danger">*</span>Name
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Enter Name" onkeyup="displaySlug($(this))"
                                        required data-msg-required="Please enter a name" value="{{ old('name') }}">
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                                    <div class="card-body p-0">
                                        <div class="mb-3">
                                            <label for="show_on_home" class="form-label">
                                                Show On Home Page
                                            </label>

                                            {{-- ✅ Hidden input to send 0 if unchecked --}}
                                            <input type="hidden" name="show_on_home" value="0">

                                            <input type="checkbox" id="show_on_home" name="show_on_home" value="1"
                                                {{ old('show_on_home', $model->show_on_home ?? 0) == 1 ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                                
                                 <div class="col-xl-6 mb-3">
                            <label for="category" class="form-label">Url</label>
                            <input type="url" class="form-control" name="url" value="{{ old('url') }}">
                        </div>
                        <div class="col-xl-6 mb-3 SlugBox" style="display: none">
                            <label for="category" class="form-label"><span class="text-danger">*</span>Slug</label>
                            <input type="text" class="form-control category-slug" disabled value="{{ old('slug') }}">
                        </div>

                        <input type="hidden"  name="priority" value="{{ old('priority', $nextPriority ?? '') }}">
                       
                        <div class="col-xl-6 mb-3">
                            <label for="image" class="form-label">Banner Image</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                                name="image" accept="image/*">
                            @error('image')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="col-xl-6 mb-3">
                            <label for="thumbnail_image" class="form-label">Thumbnail Image</label>
                            <input type="file" class="form-control @error('thumbnail_image') is-invalid @enderror"
                                id="thumbnail_image" name="thumbnail_image" accept="image/*">
                            @error('thumbnail_image')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="col-xl-6 mb-3">
                            <label for="width" class="form-label">Thumbnail Image Width</label>
                            <input type="text" class="form-control" id="width" name="width" value="{{ old('width') }}">
                        </div>
                        <div class="col-xl-6 mb-3">
                            <label for="height" class="form-label">Thumbnail Image Height</label>
                            <input type="text" class="form-control" id="height" name="height"
                                value="{{ old('height') }}">
                        </div>

                        <div class="col-xl-6 mb-3">
                            <label for="video" class="form-label">Video</label>
                            <input type="file" class="form-control @error('video') is-invalid @enderror" id="video"
                                name="video" accept="video/*">
                            @if (!empty($category->video))
                            <video height="70" controls>
                                <source src="{{ isset($category->video) ? $category->video : '' }}" type="video/mp4">
                            </video>
                            @endif
                            @error('video')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="col-xl-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" name="description"
                                id="description" cols="30"
                                rows="5">{{ old('description', $category->description ?? '') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            <div class="accordion mt-4" id="categoryAccordion">

                @php $selected_size_chart = 1; @endphp
                @if ($selected_size_chart == 1)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSizeChart">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseSizeChart" aria-expanded="true" aria-controls="collapseSizeChart">
                            <h5>Manage Size Chart</h5>
                        </button>
                    </h2>
                    <div id="collapseSizeChart" class="accordion-collapse collapse" aria-labelledby="headingSizeChart"
                        data-bs-parent="#categoryAccordion">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-xl-6 mb-3">
                                    <label for="chart_title" class="form-label">Chart Title</label>
                                    <input type="text" class="form-control" id="chart_title" name="chart_title"
                                        placeholder="Enter Chart Title"
                                        value="{{ old('chart_title', $chart_content->title ?? '') }}">
                                </div>
                                <div class="col-xl-10 mb-3">
                                    <label for="mesurement_type_inch" class="form-label">Inch </label>
                                    <input type="radio" id="mesurement_type_inch" name="mesurement_type" value="inch"
                                        onclick="changeMesurementType('inch')"
                                        {{ old('mesurement_type', 'inch') == 'inch' ? 'checked' : '' }}>
                                    <label for="mesurement_type_cm" class="form-label">CM </label>
                                    <input type="radio" id="mesurement_type_cm" name="mesurement_type" value="cm"
                                        onclick="changeMesurementType('cm')"
                                        {{ old('mesurement_type') == 'cm' ? 'checked' : '' }}>
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
                                                {{-- Dynamic rows will go here. You'll need JavaScript to populate old data for tables --}}
                                            </tbody>
                                        </table>
                                        <button type="button" id="addRowUpper" class="btn btn-primary">Add More</button>
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
                                                {{-- Dynamic rows will go here. You'll need JavaScript to populate old data for tables --}}
                                            </tbody>
                                        </table>
                                        <button type="button" id="addRowBottom" class="btn btn-primary">Add
                                            More</button>
                                    </div>
                                </div>

                                <div class="col-xl-10 mb-3 mesurement_type_cm_div" style="display:none;">
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
                                                {{-- Dynamic rows will go here. You'll need JavaScript to populate old data for tables --}}
                                            </tbody>
                                        </table>
                                        <button type="button" id="addRowUpper" class="btn btn-primary">Add More</button>
                                    </div>
                                </div>

                                <div class="col-xl-10 mb-3 mesurement_type_cm_div" style="display:none;">
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
                                                {{-- Dynamic rows will go here. You'll need JavaScript to populate old data for tables --}}
                                            </tbody>
                                        </table>
                                        <button type="button" id="addRowBottom" class="btn btn-primary">Add
                                            More</button>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="card-body p-0">
                                        <div class="mb-3">
                                            <label for="uppar_chart_title" class="form-label">Chart Upper Title</label>
                                            <input type="text"
                                                class="form-control @error('uppar_chart_title') is-invalid @enderror"
                                                id="uppar_chart_title" name="uppar_chart_title"
                                                placeholder="Enter Chart Title" value="{{ old('uppar_chart_title') }}">
                                            @error('uppar_chart_title')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 mb-3">
                                    <label for="uppar_chart_image" class="form-label">Chart Upper Image</label>
                                    <input type="file"
                                        class="form-control @error('uppar_chart_image') is-invalid @enderror"
                                        id="uppar_chart_image" name="uppar_chart_image" accept="image/*">
                                    @error('uppar_chart_image')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="col-xl-6">
                                    <div class="card-body p-0">
                                        <div class="mb-3">
                                            <label for="bootom_chart_title" class="form-label">Chart Bottom
                                                Title</label>
                                            <input type="text"
                                                class="form-control @error('bootom_chart_title') is-invalid @enderror"
                                                id="bootom_chart_title" name="bootom_chart_title"
                                                placeholder="Enter Bottom Chart Title"
                                                value="{{ old('bootom_chart_title') }}">
                                            @error('bootom_chart_title')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 mb-3">
                                    <label for="bottom_chart_image" class="form-label">Chart Bottom Image</label>
                                    <input type="file"
                                        class="form-control @error('bottom_chart_image') is-invalid @enderror"
                                        id="bottom_chart_image" name="bottom_chart_image" accept="image/*">
                                    @error('bottom_chart_image')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-xl-12 mb-3">
                                    <label for="chart_description" class="form-label">Chart Description</label>
                                    <textarea class="form-control" name="chart_description" id="chart_description"
                                        cols="30"
                                        rows="5">{{ old('chart_description', $chart_content->description ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

               

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingVariants">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseVariants" aria-expanded="false" aria-controls="collapseVariants">
                            <h5>Variants</h5>
                        </button>
                    </h2>
                    <div id="collapseVariants" class="accordion-collapse collapse" aria-labelledby="headingVariants"
                        data-bs-parent="#categoryAccordion">
                        <div class="accordion-body">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12 select2-error">
                                        <label for="variantsSelect" class="form-label">Variants</label>
                                        <select class="js-example-placeholder-single js-states form-control"
                                            multiple="multiple" name="variantsData[]" id="variantsSelect">
                                            @forelse ($variants as $variant)
                                            <option value="{{ $variant->id }}"
                                                {{ in_array($variant->id, old('variantsData', [])) ? 'selected' : '' }}>
                                                {{ $variant->name }}
                                            </option>
                                            @empty
                                            <option value="" selected>No Data found</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

              
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingAttributes">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseAttributes" aria-expanded="false"
                            aria-controls="collapseAttributes">
                            <h5>Attributes</h5>
                        </button>
                    </h2>
                    <div id="collapseAttributes" class="accordion-collapse collapse" aria-labelledby="headingAttributes"
                        data-bs-parent="#categoryAccordion">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-xl-12 select2-error">
                                    <label for="attributesSelect" class="form-label">Attributes</label>
                                    <select class="js-example-placeholder-single js-states form-control"
                                        multiple="multiple" name="attributesData[]" id="attributesSelect">
                                        @forelse ($attributes as $attribute)
                                        <option value="{{ $attribute->id }}"
                                            {{ in_array($attribute->id, old('attributesData', [])) ? 'selected' : '' }}>
                                            {{ $attribute->name }}
                                        </option>
                                        @empty
                                        <option value="" selected>No Data found</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

             
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTaxes">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTaxes" aria-expanded="false" aria-controls="collapseTaxes">
                            <h5>Taxes</h5>
                        </button>
                    </h2>
                    <div id="collapseTaxes" class="accordion-collapse collapse" aria-labelledby="headingTaxes"
                        data-bs-parent="#categoryAccordion">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-xl-12 select2-error">
                                    <label for="taxesSelect" class="form-label">Taxes</label>
                                    <select class="js-example-placeholder-single js-states form-control"
                                        multiple="multiple" name="taxesData[]" id="taxesSelect">
                                        @forelse ($taxes as $tax)
                                        <option value="{{ $tax->id }}"
                                            {{ in_array($tax->id, old('taxesData', [])) ? 'selected' : '' }}>
                                            {{ $tax->name }}
                                        </option>
                                        @empty
                                        <option value="" selected>No Data found</option>
                                        @endforelse
                                    </select>
                                </div>

                                <div id="taxCountFields" style="display: none;">
                                    {{-- You'll need JavaScript to re-populate these based on old('taxesData') and any associated old values for their counts. --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

             

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSEO">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseSEO" aria-expanded="false" aria-controls="collapseSEO">
                            <h5>Advance SEO</h5>
                        </button>
                    </h2>
                    <div id="collapseSEO" class="accordion-collapse collapse" aria-labelledby="headingSEO"
                        data-bs-parent="#categoryAccordion">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-xl-12 mb-3">
                                    <label for="seo_description" class="form-label">Footer Seo Content</label>
                                    <textarea class="form-control @error('seo_data') is-invalid @enderror"
                                        name="seo_data" id="seo_description" cols="30"
                                        rows="5">{{ old('seo_data', $category->seo_data ?? '') }}</textarea>
                                    @error('seo_data')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="col-xl-6 mb-3">
                                    <label for="meta_title" class="form-label">Meta Title</label>
                                    <input type="text" class="form-control" id="meta_title" name="meta_title"
                                        placeholder="Meta Title" value="{{ old('meta_title') }}">
                                </div>

                                <div class="col-xl-6 mb-3">
                                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                    <input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
                                        placeholder="Meta Keywords" value="{{ old('meta_keywords') }}">
                                </div>

                                <div class="col-xl-12 mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea class="form-control" name="meta_description" id="meta_description"
                                        cols="30" rows="5">{{ old('meta_description') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

               

                <div class="px-4 py-3 border-top d-sm-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Save & Continue</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- CKEditor & Validation -->
<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="{{ asset('assets/js/form-validation.js') }}"></script>
<script src="{{ asset('assets/js/custom/category.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tax Selection Handler
    $('#taxesSelect').on('change', function() {
        const selected = $(this).val() || [];
        const $fields = $('#taxCountFields').empty().toggle(selected.length > 0);
        selected.forEach(taxId => {
            const taxName = $(`#taxesSelect option[value="${taxId}"]`).text();
            $fields.append(`
                <div class="mb-3">
                    <label class="form-label">${taxName} Value</label>
                    <input type="text" class="form-control" name="tax_counts[${taxId}]" placeholder="Enter Value">
                </div>`);
        });
    });
});

// CKEditor Init
['description', 'chart_description', 'seo_description'].forEach(id => {
    CKEDITOR.replace(id, {
        filebrowserUploadUrl: '{{ URL()->to("base/uploder") }}',
        enterMode: CKEDITOR.ENTER_BR,
        allowedContent: true
    });
});

const inchToCm = 2.54;

// Convert Inch Table to CM Table
function syncCMTable(inchSelector, cmSelector, nameReplace) {
    const $cmBody = $(cmSelector).find('tbody').empty();
    $(inchSelector).find('tbody tr').each(function() {
        const $row = $('<tr/>');
        $(this).find('input').each(function(i) {
            let val = $(this).val();
            let name = $(this).attr('name').replace(nameReplace.from, nameReplace.to);
            val = i === 0 ? val : (val ? (parseFloat(val) * inchToCm).toFixed(2) : '');
            $row.append(
                `<td><input type="${i === 0 ? 'text' : 'number'}" min="0" step="0.0001" name="${name}" class="form-control" value="${val}"></td>`
                );
        });
        $row.append(
            `<td><button type="button" class="btn btn-danger ${cmSelector.includes('Upper') ? 'removeRowUpperCM' : 'removeRowBottomCM'}">X</button></td>`
            );
        $cmBody.append($row);
    });
}

// Add Row to Table
function addRow(target, typePrefix) {
    const sizes = ['xs', 's', 'm', 'l', 'xl', '2xl'].map(size =>
        `<td><input type="number" name="${typePrefix}_size_${size}[]" min="0" step="0.0001" class="form-control"></td>`
        ).join('');
    const row =
        `<tr><td><input type="text" name="${typePrefix}_type[]" class="form-control" placeholder="e.g., chest, waist"></td>${sizes}<td><button type="button" class="btn btn-danger removeRow${typePrefix === 'upper' ? 'Upper' : 'Bottom'}">X</button></td></tr>`;
    $(`#sizeChartTable${typePrefix.charAt(0).toUpperCase() + typePrefix.slice(1)} tbody`).append(row);
    syncCMTable(`#sizeChartTable${typePrefix.charAt(0).toUpperCase() + typePrefix.slice(1)}`,
        `#sizeChartTable${typePrefix.charAt(0).toUpperCase() + typePrefix.slice(1)}CM`, {
            from: `${typePrefix}_size_`,
            to: `${typePrefix}_size_cm_`
        });
}

// Measurement Type Toggle
function changeMesurementType(type) {
    $('.mesurement_type_inch_div').toggle(type === 'inch');
    $('.mesurement_type_cm_div').toggle(type !== 'inch');
}
window.changeMesurementType = changeMesurementType;

// Event Listeners
$(document)
    .on('input', '.mesurement_type_inch_div input', () => syncCMTable('#sizeChartTableUpper',
    '#sizeChartTableUpperCM', {
        from: 'top_size_',
        to: 'top_size_cm_'
    }))
    .on('input', '#sizeChartTableBottom input', () => syncCMTable('#sizeChartTableBottom', '#sizeChartTableBottomCM', {
        from: 'bottom_size_',
        to: 'bottom_size_cm_'
    }))
    .on('click', '#addRowUpper', () => addRow('Upper', 'upper'))
    .on('click', '#addRowBottom', () => addRow('Bottom', 'bottom'))
    .on('click', '.removeRowUpper, .removeRowBottom', function() {
        $(this).closest('tr').remove();
        const prefix = $(this).hasClass('removeRowUpper') ? 'upper' : 'bottom';
        syncCMTable(`#sizeChartTable${prefix.charAt(0).toUpperCase() + prefix.slice(1)}`,
            `#sizeChartTable${prefix.charAt(0).toUpperCase() + prefix.slice(1)}CM`, {
                from: `${prefix}_size_`,
                to: `${prefix}_size_cm_`
            });
    })
    .on('click', '.removeRowUpperCM, .removeRowBottomCM', function() {
        $(this).closest('tr').remove();
    });
</script>
@endpush