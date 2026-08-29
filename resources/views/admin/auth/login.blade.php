@extends('admin.layout.master')

@section('guest_content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="container">
    <div class="row justify-content-center align-items-center authentication authentication-basic h-100">
        <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-6 col-sm-8 col-12">
            <div class="my-5 d-flex justify-content-center">
                <a href="#">
                    <img src="{{ env('WEBSITE_URL').'/public/assets/front/8thniqjewells/assets/img/logo/logo.svg' }}" alt="logo"
                        class="desktop-logo" style="height: 4rem;line-height: 4rem;">
                    <img src="{{ env('WEBSITE_URL').'/public/assets/front/8thniqjewells/assets/img/logo/logo.svg' }}" alt="logo"
                        class="desktop-dark" style="height: 4rem;line-height: 4rem;">
                </a>
            </div>
            <div class="card custom-card">
                <div class="card-body p-5">
                    <p class="h5 fw-semibold mb-2 text-center">LOGIN</p>
                    {{-- <p class="mb-4 text-muted op-7 fw-normal text-center">Welcome back Jhon !</p> --}}
                    <form class="form-horizontal" method="post" action="{{ route('admin-verify-login') }}">
                        @csrf
                        <div class="row gy-3">
                            <div class="col-xl-12">
                                <label for="email" class="form-label text-default">Email</label>
                                <input type="text" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter Email" value="{{old('email')}}">
                                @if ($errors->has('email'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </div>
                                @endif
                            </div>
                            <div class="col-xl-12 mb-2">
                                <label for="password" class="form-label text-default d-block">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" name="password" placeholder="password" value="" autocomplete="false">
                                    <button class="btn btn-light" type="button" onclick="createpassword('password', this)" id="button-addon2">
                                        <i id="eye-icon" class="ri-eye-off-line align-middle"></i>
                                    </button>
                                    @if ($errors->has('password'))
                                    <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                                    @endif
                                </div>
                                {{-- <div class="mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                                        <label class="form-check-label text-muted fw-normal" for="defaultCheck1">
                                            Remember password ?
                                        </label>
                                    </div>
                                </div> --}}
                                <div class="mt-2">
                                    <div class="form-check" style="float: right;"> 
                                        <a class="btn btn-link" href="#">
                                        {{ __('Forgot Password?') }}
                                    </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12 d-grid mt-2">
                                <button class="btn btn-lg btn-primary" type="submit">Login <i class="fa fa-sign-in"></i></button>
                            </div>
                        </div>
                    </form>
                    {{-- <div class="text-center">
                        <p class="fs-12 text-muted mt-3">Dont have an account? <a href="sign-up-basic.html"
                                class="text-primary">Sign Up</a></p>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <!-- Custom-Switcher JS -->
    <script src="{{ asset('assets/js/custom-switcher.min.js') }}"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Show Password JS -->
    <script src="{{ asset('assets/js/show-password.js') }}"></script>
@endpush