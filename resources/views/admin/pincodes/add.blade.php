@extends('admin.layout.master')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <a class="btn btn-dark" href="{{ route('admin-pincodes.index') }}">Back</a>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Create Couriers</li>
                </ol>
            </nav>
        </div>
    </div>

    @include('admin.pincodes._form')
@endsection

@push('scripts')
    <script>
        // Temporarily disable validation to test if form submits
        // $('#countryForm').validate();
    </script>
@endpush
