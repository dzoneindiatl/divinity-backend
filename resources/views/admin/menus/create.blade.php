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
                <li class="breadcrumb-item active" aria-current="page">Create Menu</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="{{ route('admin-frontend-menus.store') }}" method="post" enctype="multipart/form-data"
            id="createUserForm">
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
                                                <label for="url" class="form-label"><span class="text-danger">* </span>Parent</label>
                                                <select name="parent_id" class="form-control">
                                                                <option value="0">Select Parent</option>
                                                                @foreach($parentMenus as $parent)
                                                                   <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                                                                @endforeach
                                                </select>                       
                                                @if ($errors->has('url'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('url') }}
                                                        </div>
                                                    @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="title" class="form-label"><span class="text-danger">* </span>Title</label>
                                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" required value="{{isset($userDetails->name) ? $userDetails->name: old('title')}}" placeholder="Title">
                                                @if ($errors->has('title'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('title') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="url" class="form-label"><span class="text-danger">* </span>Menu URL</label>
                                                <input type="text" class="form-control @error('url') is-invalid @enderror" id="title" name="url" value="{{isset($userDetails->name) ? $userDetails->name: old('url')}}" placeholder="Menu URL">
                                                @if ($errors->has('url'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('url') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="order" class="form-label"><span class="text-danger">* </span>Oder</label>
                                                <input type="text" class="form-control @error('url') is-invalid @enderror" id="title" name="order" value="{{isset($userDetails->name) ? $userDetails->name: old('url')}}" placeholder="Order">
                                                @if ($errors->has('url'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('url') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-xl-6">
                                                <label for="url" class="form-label"><span class="text-danger">* </span>Status</label>
                                                <select name="is_active" class="form-control">
                                                                <option value="1">Active</option>
                                                                <option value="0">Inactive</option>
                                                </select>                       
                                                @if ($errors->has('url'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('url') }}
                                                        </div>
                                                    @endif
                                            </div>
                                            
                                        </div>
                                        {{-- <button type="button" class="btn btn-primary add-child" data-parent="0">Add Child</button> --}}
                                         <div id="child-menus-container"></div>
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
    </div>
</div>
<!-- Child Menu Template -->
<div id="child-template" class="d-none">
    <div class="child-menu-section border p-3 my-3">
        <h5>Child Menu</h5>
        <div class="form-group">
            <label for="title">Menu Title</label>
            <input type="text" name="menus[__INDEX__][title]" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="url">Menu URL</label>
            <input type="text" name="menus[__INDEX__][url]" class="form-control">
        </div>
        <div class="form-group">
            <label for="order">Display Order</label>
            <input type="number" name="menus[__INDEX__][order]" class="form-control" value="0" required>
        </div>
        <div class="form-group">
            <label for="is_active">Status</label>
            <select name="menus[__INDEX__][is_active]" class="form-control">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
        <div class="form-group">
            <label for="parent_id">Parent Menu</label>
            <input type="hidden" name="menus[__INDEX__][parent_id]" value="__PARENT_ID__">
        </div>
        <button type="button" class="btn btn-primary add-child" data-parent="__INDEX__">Add Sub-Child</button>
        <button type="button" class="btn btn-danger remove-menu">Remove</button>

        <!-- Sub-Children Container -->
        <div class="sub-children-container mt-3"></div>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let menuIndex = 1;

        // Add Child Menu
        document.querySelectorAll('.add-child').forEach(button => {
            button.addEventListener('click', function () {
                const parentIndex = this.dataset.parent;
                const template = document.querySelector('#child-template').innerHTML
                    .replace(/__INDEX__/g, menuIndex)
                    .replace(/__PARENT_ID__/g, parentIndex);

                const container = parentIndex == "0" 
                    ? document.getElementById('child-menus-container')
                    : this.parentElement.querySelector('.sub-children-container');

                const childElement = document.createElement('div');
                childElement.innerHTML = template;
                container.appendChild(childElement);

                // Increment index for next menu
                menuIndex++;
            });
        });

        // Remove Menu
        document.body.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-menu')) {
                e.target.closest('.child-menu-section').remove();
            }
        });
    });
</script>
@endpush