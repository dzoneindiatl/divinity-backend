@extends('admin.layout.master')

@push('styles')

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">
@endpush
<style>
    #sortableCategories li {
    cursor: move; /* Change cursor to indicate draggable items */
    padding: 10px;
    margin: 5px 0;
    background-color: #f1f1f1;
    border: 1px solid #ccc;
    border-radius: 5px;
}

#sortableCategories li:hover {
    background-color: #e0e0e0;
}

#sortableCategories {
    list-style-type: none;
    padding: 0;
}
</style>
@section('content')

@include('admin.layout.response_message')

<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <h1 class="page-title fw-semibold fs-18 mb-0">Sub Categories</h1>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sub Categories</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card p-4">
    <h4>Manage Sub Categories Priority</h4>
    <form id="priorityForm">
        @csrf
        <ul id="sortableCategories">
            @foreach($subCategories as $category)
                <li class="list-group-item" data-id="{{ $category->id }}">
                    {{ $category->name }}
                </li>
            @endforeach
        </ul>

        <button type="submit" class="btn btn-success mt-3">Update Priority</button>
    </form>
</div>

@endsection