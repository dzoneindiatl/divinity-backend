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
                <li class="breadcrumb-item active" aria-current="page">Referral Histories</li>
            </ol>
        </nav>
    </div>
</div>
@if(!$referralHistoryto->isEmpty())
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                Referral By 
                </div>                
            </div>
            <div class="card-body">
                <div class="table-container">
                    @php $i = 1; @endphp 
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Registration date</th>
                                        <th>Referral By </th>
                                        <th>Referral By Amount</th> 
                                        <th>Referral To Amount</th> 
                                                                                                                      
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($referralHistoryto as $referralhistoryto)
                                    <?php  $userdetails = DB::table('users')->select('*')->where('id', $referralhistoryto->referral_by)->first();    ?>
                                        <tr>
                                            <td>{{ $referralhistoryto->updated_at->format('d-m-Y') }}</td>
                                            <td>{{ $userdetails ? $userdetails->name : 'N/A' }}</td>
                                            <td>₹ {{ $referralhistoryto->referral_by_amount }}</td>
                                            <td>₹ {{ $referralhistoryto->referral_to_amount }}</td>                                     
                                                                                 
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        
                    </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- /********************************************************************************************************************************************** */ -->

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                Referral To Histories
                </div>
                <div class="container col-12 d-flex justify-content-end align-items-center">
                    <button type="button" class="btn btn-outline-primary">
                        Total Referral:
                        <span class="badge ms-2 totalDataCount">{{ $totalReferrals ?? 0 }}</span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-container">
                    @php $i = 1; @endphp
                    @if($referralHistory->isEmpty())
                            <p>No Referral Histories available for this user.</p>
                        @else
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Registration date</th>
                                        <th>Referral To </th>
                                        <th>Referral To Amount</th> 
                                        <th>Referral By Amount</th>
                                                                                
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($referralHistory as $history)
                                    <?php  $userdetails = DB::table('users')->select('*')->where('id', $history->referral_to)->first();    ?>
                                        <tr>
                                            <td>{{ $history->updated_at->format('d-m-Y') }}</td>
                                            <td>{{ $userdetails ? $userdetails->name : 'N/A' }}</td>
                                            <td>₹ {{ $history->referral_to_amount }}</td>
                                            <td>₹ {{ $history->referral_by_amount }}</td>
                                                                                         
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