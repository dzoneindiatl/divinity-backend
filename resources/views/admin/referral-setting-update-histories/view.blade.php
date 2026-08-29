@extends('admin.layout.master')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}">
@endpush
@section('content')

<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Partner Details</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    Partner Details
                </div>
            </div>
            <div class="card-body">
                <div class="row gx-5">
                    <div class="col-xxl-3 col-xl-12">
                        <div class="row">
                            <div class="col-xxl-12 col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-md-5 mb-3">
                                <p class="fs-15 fw-semibold mb-2">Profile Picture :</p>
                                <img class="img-fluid" src="{{ $userDetails->image }}" alt="img">
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-9 col-xl-12">
                        <div class="row gx-5">
                            <div class="col-xl-8 mt-xxl-0 mt-3">
                                <div class="row">
                                    <div class="col-xl-10">
                                        <div class="table-responsive">
                                            <table class="table table-bordered text-nowrap">
                                                <tbody>
                                                    <tr>
                                                        <th scope="row" class="fw-semibold">
                                                            Name
                                                        </th>
                                                        <td>{{ $userDetails->name ?? "N/A" }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row" class="fw-semibold">
                                                            Email
                                                        </th>
                                                        <td>
                                                            {{ $userDetails->email ?? "N/A" }}
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <th scope="row" class="fw-semibold">
                                                            Phone Number
                                                        </th>
                                                        <td>
                                                            {{ $userDetails->phone_number ?? "N/A" }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row" class="fw-semibold">
                                                            Gender
                                                        </th>
                                                        <td>
                                                            {{ ucfirst($userDetails->gender) ?? "N/A" }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row" class="fw-semibold">
                                                            Date Of Birth
                                                        </th>
                                                        <td>
                                                        {{ isset($userDetails->date_of_birth) ? \Carbon\Carbon::parse($userDetails->date_of_birth)->format('M d, Y') : "N/A" }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row" class="fw-semibold">
                                                            Referral Link
                                                        </th>
                                                        <td>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" id="referralLink" value="{{ env('APP_URL') . 'login?' . http_build_query(['referral_code' => $userDetails->referral_code ?? '']) }}" readonly>
                                                                <button class="btn btn-primary" id="copyReferralLink">
                                                                    <i class="bi bi-files"></i> Copy
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row" class="fw-semibold">
                                                            Status
                                                        </th>
                                                        <td class="{{ $userDetails->is_active ? 'text-success' : 'text-danger' }}">
                                                            {{ $userDetails->is_active ? 'Activated' : 'Deactivated' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row" class="fw-semibold">
                                                            Created On
                                                        </th>
                                                        <td>
                                                            {{ isset($userDetails->created_at) ? \Carbon\Carbon::parse($userDetails->created_at)->format('M d, Y') : "N/A" }}
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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

      <script>
        document.addEventListener('DOMContentLoaded', function () {
            const referralLinkInput = document.getElementById('referralLink');
            const copyReferralLinkButton = document.getElementById('copyReferralLink');

            copyReferralLinkButton.addEventListener('click', function () {
                referralLinkInput.select();
                document.execCommand('copy');
                // Optionally, you can show a tooltip or alert to indicate the link is copied.
                // For example, using Bootstrap's Tooltip:
                // new bootstrap.Tooltip(copyReferralLinkButton, { title: 'Copied!', trigger: 'manual' });
                // bootstrap.Tooltip.getInstance(copyReferralLinkButton).show();
                // setTimeout(() => bootstrap.Tooltip.getInstance(copyReferralLinkButton).hide(), 2000);
            });
        });
    </script>
@endpush