@extends('admin.layout.master')

@section('content')
<!-- Start::page-header -->

<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <div>
        <p class="fw-semibold fs-18 mb-0">Welcome back, {{ ucfirst(auth()->user()->name) }}</p>
        <span class="fs-semibold text-muted">Track your activities here.</span>
    </div>
    {{-- <div class="btn-list mt-md-0 mt-2">
        <button type="button" class="btn btn-primary btn-wave">
            <i class="ri-filter-3-fill me-2 align-middle d-inline-block"></i>Filters
        </button>
        <button type="button" class="btn btn-outline-secondary btn-wave">
            <i class="ri-upload-cloud-line me-2 align-middle d-inline-block"></i>Export
        </button>
    </div> --}}
</div>

<!-- End::page-header -->

<!-- Start::row-1 -->
@if (Auth::user()->user_role_id != 4)
<div class="row">
    <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-top">
                    {{-- <div class="me-3">
                        <span class="avatar avatar-sm shadow-sm avatar-rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" class="svg-info" viewBox="0 0 128 128"><path d="M64 128C28.7 128 0 99.3 0 64S28.7 0 64 0s64 28.7 64 64-28.7 64-64 64z"/><path fill="#fff" d="M94.3 71.4c-.5-3.6-2.3-6.4-5.4-8.2-1.4-.8-3.1-1.4-4.7-2.1.2-.1.3-.3.4-.3 7.9-4.1 7.2-16.3 1.5-20.5-2.3-1.7-4.9-2.8-7.7-3.4-2-.4-3.9-.7-6-1.1 0-3.5.1-8.5.1-12.4H65c0 3.8-.1 8.7-.1 12.1H59c0-3.5 0-8.3.1-12.1h-7.4c0 4-.1 8.8-.1 12.3-5.1 0-10-.1-15-.1 0 2.7 0 5.3-.1 7.9h3c.9 0 1.9 0 2.8.1 2.6.2 3.9 1.6 3.9 4.2l-.3 31.9c0 2.3-1 3.3-3.3 3.3h-5c-.5 3-1 5.9-1.6 8.9 5 .1 10 .1 15 .2 0 3.8 0 8.7-.1 12.7h7.4c0-4 .1-8.7.1-12.5 2.1.1 4 .1 5.9.2 0 3.8-.1 8.4-.1 12.3h7.4c0-4 .1-8.7.1-12.4.3 0 .5-.1.6-.1 3.5-.6 7.2-.9 10.6-1.7 4.5-1.1 8.1-3.7 9.9-8.2 1.7-3.6 2-7.2 1.5-11zM59 44.3c4.6 0 9.1-.4 13.4 1.5 2.8 1.2 4.2 3.5 4 6.3-.2 2.9-1.9 5-4.8 6-4.1 1.3-8.3 1.3-12.7 1.1 0-5 .1-9.8.1-14.9zm16.2 37.1c-4.1 1.8-8.5 1.8-12.8 1.9-1.2 0-2.4-.1-3.8-.1.1-5.5.1-10.9.2-16.4 5.6 0 11.2-.4 16.5 1.9 2.7 1.2 4.3 3.3 4.3 6.4 0 3.1-1.6 5.1-4.4 6.3z"/></svg>
                        </span>
                    </div> --}}
                    <a href="{{ route('admin-admin_users.index') }}">
                    <div class="flex-fill">
                        <div class="d-flex flex-wrap align-items-center justify-content-between fs-14 mb-2">
                            <span class="flex-fill">Total Users</span>
                        </div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <h5 class="fw-semibold mb-0">{{ $usersCount ?? 0 }}</h5>
                            <div id="btcCoin"></div>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-top">
                    <div class="me-3">
                        <span class="avatar avatar-sm shadow-sm avatar-rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" class="svg-info" viewBox="0 0 128 128"><path d="M64 128C28.7 128 0 99.3 0 64S28.7 0 64 0s64 28.7 64 64-28.7 64-64 64z"/><path fill="#fff" d="M94.3 71.4c-.5-3.6-2.3-6.4-5.4-8.2-1.4-.8-3.1-1.4-4.7-2.1.2-.1.3-.3.4-.3 7.9-4.1 7.2-16.3 1.5-20.5-2.3-1.7-4.9-2.8-7.7-3.4-2-.4-3.9-.7-6-1.1 0-3.5.1-8.5.1-12.4H65c0 3.8-.1 8.7-.1 12.1H59c0-3.5 0-8.3.1-12.1h-7.4c0 4-.1 8.8-.1 12.3-5.1 0-10-.1-15-.1 0 2.7 0 5.3-.1 7.9h3c.9 0 1.9 0 2.8.1 2.6.2 3.9 1.6 3.9 4.2l-.3 31.9c0 2.3-1 3.3-3.3 3.3h-5c-.5 3-1 5.9-1.6 8.9 5 .1 10 .1 15 .2 0 3.8 0 8.7-.1 12.7h7.4c0-4 .1-8.7.1-12.5 2.1.1 4 .1 5.9.2 0 3.8-.1 8.4-.1 12.3h7.4c0-4 .1-8.7.1-12.4.3 0 .5-.1.6-.1 3.5-.6 7.2-.9 10.6-1.7 4.5-1.1 8.1-3.7 9.9-8.2 1.7-3.6 2-7.2 1.5-11zM59 44.3c4.6 0 9.1-.4 13.4 1.5 2.8 1.2 4.2 3.5 4 6.3-.2 2.9-1.9 5-4.8 6-4.1 1.3-8.3 1.3-12.7 1.1 0-5 .1-9.8.1-14.9zm16.2 37.1c-4.1 1.8-8.5 1.8-12.8 1.9-1.2 0-2.4-.1-3.8-.1.1-5.5.1-10.9.2-16.4 5.6 0 11.2-.4 16.5 1.9 2.7 1.2 4.3 3.3 4.3 6.4 0 3.1-1.6 5.1-4.4 6.3z"/></svg>
                        </span>
                    </div>
                    <a href="{{ route('admin-category.index') }}">
                    <div class="flex-fill">
                        <div class="d-flex flex-wrap align-items-center justify-content-between fs-14 mb-2">
                            <span class="flex-fill">Total Categories</span>
                        </div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <h5 class="fw-semibold mb-0">{{ $categoriesCount ?? 0 }}</h5>
                            <div id="btcCoin"></div>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-top">
                    <div class="me-3">
                        <span class="avatar avatar-sm shadow-sm avatar-rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" class="svg-success" viewBox="0 0 128 128"><path d="M64 128C28.7 128 0 99.3 0 64S28.7 0 64 0s64 28.7 64 64-28.7 64-64 64z"/><path fill="#fff" d="M65.2 87.2v22.7l28.1-39.5zM92.3 63.1l-27.1-45v32.7zM65.2 53.3v28l26.9-15.7zM35.8 63.1l27-45v32.7zM62.8 53.3v28L35.9 65.6zM62.8 87.2v22.7L34.7 70.4z"/></svg>
                        </span>
                    </div>
                    <div class="flex-fill">
                        <div class="d-flex flex-wrap align-items-center justify-content-between fs-14 mb-2">
                            <span class="flex-fill">Sotal Sub Categories</span>
                        </div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <h5 class="fw-semibold mb-0"></h5>
                            <div id="ethCoin"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-top">
                    <div class="me-3">
                        <span class="avatar avatar-sm shadow-sm avatar-rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" class="svg-danger" viewBox="0 0 128 128"><path d="M64 128C28.7 128 0 99.3 0 64S28.7 0 64 0s64 28.7 64 64-28.7 64-64 64z"/><path fill="#fff" d="M20.8 89.3c1.4-4.1 2.7-7.9 4.1-11.8.1-.4.9-.7 1.3-.7 2.3-.1 4.7 0 7 0H79c1.2 0 1.8-.4 2.1-1.5 2.5-7.5 5.2-15 7.8-22.5.1-.4.2-.8.4-1.4H33.8c1.4-4.2 2.8-8.1 4.1-12 .1-.3.7-.6 1.1-.6 1.4-.1 2.8 0 4.2 0 18.8 0 37.6.1 56.3-.1 5.6-.1 11.6 4.3 9.2 12.5-1.8 6.1-4.1 12.1-6.2 18.2-.7 2.1-1.4 4.1-2.1 6.2-2.6 7.2-7.9 11.6-15.2 13.6-.7.2-1.4.2-2.1.2H22.3c-.4-.1-.8-.1-1.5-.1z"/><path fill="#fff" d="M55.9 58.1c-1.4 4-2.8 7.7-4.2 11.4-.1.3-.7.5-1 .5H19.2c-.1 0-.3-.1-.6-.2 1.3-3.7 2.6-7.3 4-10.9.1-.3.7-.7 1-.7 10.7-.1 21.4-.1 32.3-.1z"/></svg>
                        </span>
                    </div> 
                    <div class="flex-fill">
                        <div class="d-flex flex-wrap align-items-center justify-content-between fs-14 mb-2">
                            <span class="flex-fill">Total Child Category</span>
                        </div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <h5 class="fw-semibold mb-0"></h5>
                            <div id="dshCoin"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-top">
                    {{-- <div class="me-3">
                        <span class="avatar avatar-sm shadow-sm avatar-rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" class="svg-warning" viewBox="0 0 128 128"><path d="M64 128C28.7 128 0 99.3 0 64S28.7 0 64 0s64 28.7 64 64-28.7 64-64 64z"/><path fill="#fff" d="M63.8 22.6c3.7-.1 7.2.6 10.2 2.7 1.4.9 2.4.8 3.6-.5 2.4-2.6 5-5 7.5-7.6.9-.9 1.8-1.5 2.9-.2 1.1 1.1.8 2-.2 2.9-2.6 2.6-5 5.2-7.6 7.8-1.1 1.1-1.3 1.9-.5 3.3 5.8 10.4 2.9 22.5-8.6 28.1-1.1.5-2.3.8-3.5 1-1.4.3-1.9 1-1.9 2.5.1 3 .1 6 0 8.9 0 1.6.5 2.3 2.1 2.7 6.2 1.5 10.6 5.2 13.3 10.9 2.6 5.5 1.9 15.4-3.8 21.3-4.8 4.9-13.2 6.9-19.5 4.4-7.5-3-12.1-9.6-12.2-17.5-.1-9.6 5.1-16.4 14.3-19 1.4-.4 2.2-1 2.1-2.5-.1-3.4 0-6.8-.2-10.2 0-.6-1.1-1.4-1.8-1.6-6-1.6-10.4-5.2-12.9-10.8-5.6-13 4.3-27.4 16.7-26.6zM79 92.8c0-7.9-6.9-15.1-14.5-15.1-8-.1-15.3 7-15.4 15-.1 7.9 7.3 15.6 14.9 15.5 8.1-.1 15-7.2 15-15.4zM49.3 41.1c0 8.6 6.6 15.6 14.9 15.6 7.7 0 14.8-7.1 14.8-15.1 0-7.4-5.5-15.2-14.7-15-8.4.1-14.9 6.3-15 14.5z"/></svg>
                        </span>
                    </div> --}}
                    <div class="flex-fill">
                        <div class="d-flex flex-wrap align-items-center justify-content-between fs-14 mb-2">
                            <span class="flex-fill">Total Products</span>
                        </div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <h5 class="fw-semibold mb-0">{{ $productsCount ?? 0 }}</h5>
                            <div id="glmCoin"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@else
<div class="row justify-content-center align-items-center" style="height: 100vh;">
    <div class="col text-center">
        <h1 style="color: #f542cb;">Welcome In Partner Panel</h1>
    </div>
</div>


@endif
<!-- End::row-1 -->
@endsection