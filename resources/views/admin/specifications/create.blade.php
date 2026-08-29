@extends('admin.layout.master')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
@endpush

@section('content')

@include('admin.layout.response_message')

<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
<a href="{{ route('admin-specifications.index') }}" class="btn btn-dark">
    Back
</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Specification</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->

<div class="row">
    <div class="col-xl-12">
        <form action="{{ route('admin-specifications.store') }}" method="post" id="categoryForm"
            enctype="multipart/form-data">
            @csrf

            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Create Specification</div>
                </div>
                <div class="card-body">
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
                                
                        <div class="col-xl-6 mb-3">
                            <label for="icon" class="form-label">Icon</label>
                            <input type="file" class="form-control @error('icon') is-invalid @enderror" id="icon"
                                name="image" accept="image/*">
                            @error('icon')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="col-xl-6 mb-3 SlugBox" style="display: none">
                            <label for="category" class="form-label"><span class="text-danger">*</span>Slug</label>
                            <input type="text" class="form-control category-slug" disabled value="{{ old('slug') }}">
                        </div>

                        <input type="hidden"  name="priority" value="{{ old('priority', $nextPriority ?? '') }}">
                       
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 border-top d-sm-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')

<script src="{{ asset('assets/js/custom/category.js') }}"></script>

@endpush