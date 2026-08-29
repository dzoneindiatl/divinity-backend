@extends('admin.layout.master')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}">
@endpush

<style>
* {
  box-sizing: border-box;
}
.row-images {
  display: flex;
}
/* Create three equal columns that sits next to each other */
.column {
  flex: 33.33%;
  padding: 5px;
}
</style>
@section('content')

<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Poduct Variants</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    Product Variants
                </div>
                <div class="container col-12 d-flex justify-content-end align-items-center">
                    <button type="button" class="btn btn-outline-primary">
                        Total Products Variants:
                        <span class="badge ms-2 totalDataCount">{{ $totalResults ?? 0 }}</span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-container">
                    @php $i = 1; @endphp
                    @foreach($result as $value)
                        <br><span> {{' Variant '.$value['color'] }} </span>
                        <table class="table table-bordered text-nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th style="color:#428bca">SKU</th>
                                <th style="color:#428bca">Colors </th>
                                <th style="color:#428bca">Sizes </th>
                                <th style="color:#428bca">Price (MRP) </th>
                                <th style="color:#428bca">Selling Price</th>
                                <th style="color:#428bca">Discount</th>
                                <th style="color:#428bca">Total Units</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ 'rtyuin001' }}</td>
                                <td>{{ $value['color'] }} </td>
                                <td>{{ 'XL,S,M,L,XL,XXL' }}</td>
                                <td>INR {{ $value['mrp']}} .00</td>
                                <td>INR {{ $value['selling_price'] }}</td>
                                <td>{{ $value['discount'] }}</td>
                                <td>{{ $value['units'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
      <!-- Custom-Switcher JS -->
      <script src="{{ asset('assets/js/custom-switcher.min.js') }}"></script>

      <!-- Swiper JS -->
      <script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>

      <script src="{{ asset('assets/js/product-details.js') }}"></script>
@endpush