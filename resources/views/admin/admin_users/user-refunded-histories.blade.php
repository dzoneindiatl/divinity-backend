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
    <!-- <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a> -->
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin-admin_users.index') }}">Users</a></li>
                <li class="breadcrumb-item active" aria-current="page">Refunded Histories</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                Refunded Histories
                </div>
                <div class="container col-12 d-flex justify-content-end align-items-center">
                    <button type="button" class="btn btn-outline-primary">
                        Total Refunded:
                        <span class="badge ms-2 totalDataCount">{{ $totalRefunded ?? 0 }}</span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-container">
                    @php $i = 1; @endphp
                    @if($refundedHistory->isEmpty())
                            <p>No Refunded Histories available for this user.</p>
                        @else
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Registration date</th>
                                        <th>Order Id </th>
                                        <th>Amount</th> 
                                        <th>Amount</th> 
                                        <th>Action</th>                       
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($refundedHistory as $history)
                                    <?php  //$userdetails = DB::table('users')->select('*')->where('id', $history->referral_to)->first();    ?>
                                        <tr>
                                            <td>{{ $history->updated_at->format('d-m-Y') }}</td>                                            
                                            <td> <a href="{{route('admin-orders.view',base64_encode($history->order_id))}}" target="_blank"><b style="color:red;"><u>{{ $history->order_id }}</u></b></a></td>
                                            <td>₹ {{ $history->amount }}</td> 
                                            <td>
                                                @if($history->is_active == 1)
                                                <span class="badge bg-success">Approved</span>
                                                @else
                                                <span class="badge bg-danger">Approvel Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="hstack gap-2 flex-wrap">
                                                @if($history->is_active == 1)
                                                <!-- <a href='{{route("admin-admin_users-refundedapprovalstatus",array($history->id,0))}}' class="btn btn-danger"
                                                    id="deactivate-button"><i class="ri-close-line"></i></a> -->
                                                @else
                                                <a href='{{route("admin-admin_users-refundedapprovalstatus",array($history->id,1))}}' class="btn btn-success"
                                                    id="activate-button"><i class="ri-check-line"></i></a>
                                                @endif 
                                                @if($history->is_active == 0)
                                                <a href="{{route('admin-admin_users-refundedapprovaledit',base64_encode($history->id))}}" class="btn btn-info"><i
                                                class="ri-edit-line"></i></a>
                                                @endif 
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