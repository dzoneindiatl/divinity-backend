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
                    <li class="breadcrumb-item active" aria-current="page">Poduct Reviews</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">
                        Product Reviews
                    </div>
                    <div class="container col-12 d-flex justify-content-end align-items-center">
                        <button type="button" class="btn btn-outline-primary">
                            Total Products Reviews:
                            <span class="badge ms-2 totalDataCount">{{ $totalResults ?? 0 }}</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        @php $i = 1; @endphp
                        @if ($productReviews->isEmpty())
                            <p>No reviews available for this product.</p>
                        @else
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Rating</th>
                                        <th>Review</th>
                                        <th>Image</th>
                                        <th>Created At</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($productReviews as $review)
                                        <tr>
                                            <td>{{ $review->product->name }}</td>
                                            <td>{{ $review->rating }}</td>
                                            <td>{{ $review->review }}</td>
                                            <td>
                                                <?php 
                                                $reviewImages = json_decode($review->image);
                                                ?>
                                                @if (!empty($reviewImages))
                                                    @foreach($reviewImages as $review_img)
                                                    <img height="70" width="70" src="{{ config('constant.REVIEW_IMAGE_URL') . $review_img }}" />
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>{{ $review->created_at->format('d-m-Y H:i') }}</td>
                                            <td>
                                                @if ($review->is_active == 1)
                                                    <span class="badge bg-success">Activated</span>
                                                @else
                                                    <span class="badge bg-danger">Deactivated</span>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="hstack gap-2 flex-wrap">
                                                    @if ($review->is_active == 1)
                                                        <a href='{{ route('admin-admin_users-reviewstatus', [$review->id, 0]) }}'
                                                            class="btn btn-danger" id="deactivate-button"><i
                                                                class="ri-close-line"></i></a>
                                                    @else
                                                        <a href='{{ route('admin-admin_users-reviewstatus', [$review->id, 1]) }}'
                                                            class="btn btn-success" id="activate-button"><i
                                                                class="ri-check-line"></i></a>
                                                    @endif
                                                    <a href="{{ route('admin-admin_users-reviewedit', base64_encode($review->id)) }}"
                                                        class="btn btn-info"><i class="ri-edit-line"></i></a>
                                                    <form method="GET"
                                                        action="{{ route('admin-admin_users-reviewdelete', ['reviewId' => base64_encode($review->id), 'userId' => base64_encode($review->user_id)]) }}">
                                                        @csrf
                                                        <input name="_method" type="hidden" value="DELETE">
                                                        <button type="submit" class="btn btn-danger" id="confirm-button"><i
                                                                class="ri-delete-bin-5-line"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
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
