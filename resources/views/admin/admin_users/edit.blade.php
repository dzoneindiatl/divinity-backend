@extends('admin.layout.master')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.css') }}">
    <link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit User</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->
    <div class="row">
        <div class="col-xl-12">
            <form action="{{ route('admin-admin_users.update', $userDetails->id) }}" method="post"
                enctype="multipart/form-data" id="editUserForm">
                @csrf
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Basic Info
                        </div>
                    </div>
                    <div class="card-body add-products p-0">
                        <div class="p-4">
                            <div class="row gx-5">
                                <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12">
                                    <div class="card custom-card shadow-none mb-0 border-0">
                                        <div class="card-body p-0">
                                            <div class="row gy-3">
                                                <div class="col-xl-6">
                                                    <label for="name" class="form-label"><span class="text-danger">*
                                                        </span>Name</label>
                                                    <input type="text"
                                                        class="form-control @error('name') is-invalid @enderror"
                                                        id="name" name="name"
                                                        value="{{ isset($userDetails->name) ? $userDetails->name : old('name') }}"
                                                        placeholder="Name">
                                                    @if ($errors->has('name'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('name') }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-xl-6">
                                                    <label for="email" class="form-label"><span class="text-danger">*
                                                        </span>Email</label>
                                                    <input type="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        id="email" name="email"
                                                        value="{{ isset($userDetails->email) ? $userDetails->email : old('email') }}"
                                                        placeholder="Email">
                                                    @if ($errors->has('email'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('email') }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-xl-6">
                                                    <label for="phone_number" class="form-label">Phone Number</label>
                                                    <input type="text"
                                                        class="form-control @error('phone_number') is-invalid @enderror"
                                                        id="phone_number" name="phone_number"
                                                        value="{{ isset($userDetails->phone_number) ? $userDetails->phone_number : old('phone_number') }}"
                                                        placeholder="Phone Number">
                                                    @if ($errors->has('phone_number'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('phone_number') }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-xl-6">
                                                    <label for="gender" class="form-label">Gender</label>
                                                    <select class="form-control @error('gender') is-invalid @enderror"
                                                        name="gender" id="gender">
                                                        <option value="" selected>Select Gender</option>
                                                        <option value="male"
                                                            {{ !empty($userDetails->gender) && $userDetails->gender == 'male' ? 'selected' : '' }}>
                                                            Male</option>
                                                        <option value="female"
                                                            {{ !empty($userDetails->gender) && $userDetails->gender == 'female' ? 'selected' : '' }}>
                                                            Female</option>
                                                        <option value="other"
                                                            {{ !empty($userDetails->gender) && $userDetails->gender == 'other' ? 'selected' : '' }}>
                                                            Other</option>
                                                    </select>
                                                    @if ($errors->has('gender'))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('gender') }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-xl-6">
                                                    <label for="image" class="form-label"><span class="text-danger">
                                                        </span>Profile Picture</label>
                                                    <input type="file"
                                                        class="form-control @error('image') is-invalid @enderror"
                                                        id="image" name="image">
                                                    @if (!empty($userDetails->image))
                                                        <img height="50" width="50"
                                                            src="{{ $userDetails->image }}" />
                                                    @endif
                                                </div>

                                                <div class="col-xl-6">
                                                    <label for="date_of_birth" class="form-label">Date Of Birth</label>
                                                    <input type="date"
                                                        class="form-control @error('date_of_birth') is-invalid @enderror"
                                                        id="date_of_birth" name="date_of_birth"
                                                        value="{{ isset($userDetails->date_of_birth) ? $userDetails->date_of_birth : old('date_of_birth') }}"
                                                        placeholder="Date Of Birth">
                                                    @if ($errors->has('date_of_birth'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('date_of_birth') }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-xl-6">
                                                    <label for="role" class="form-label"><span class="text-danger">*
                                                        </span>Role</label>
                                                    <select name="roles" id="roles"
                                                        class="form-control  @error('roles') is-invalid @enderror">
                                                        <option value="">Select Role</option>
                                                        @foreach ($roles as $key => $role)
                                                            <option value="{{ $key }}"
                                                                {{ $userDetails->user_role_id == $key ? 'selected' : '' }}>
                                                                {{ $role }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('roles'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('roles') }}
                                                        </div>
                                                    @endif
                                                </div>


                                                

                                                <div class="col-xl-6">
                                                    <div class="form-group" id="wallet_active">
                                                        <label class="form-label d-block mb-2">Wallet Option</label>
                                                        
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" id="Active" name="wallet_active" value="1"
                                                                {{ intval($userDetails->wallet_active) === 1 ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="Active">Active</label>
                                                        </div>

                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" id="InActive" name="wallet_active" value="0"
                                                                {{ intval($userDetails->wallet_active) === 0 ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="InActive">In Active</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-6">
                                                    <label for="password" class="form-label"><span class="text-danger"> </span>Password</label>
                                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="{{ old('password') }}" placeholder="Password">
                                                    @if ($errors->has('password'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('password') }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-xl-6">
                                                    <label for="confirm_password" class="form-label"><span class="text-danger"> </span>Confirm Password</label>
                                                    <input type="password" class="form-control @error('confirm_password') is-invalid @enderror" id="confirm_password" name="confirm_password" value="{{ old('confirm_password') }}" placeholder="Confirm Password">
                                                    @if ($errors->has('confirm_password'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('confirm_password') }}
                                                        </div>
                                                    @endif
                                                </div>


                                                {{-- <div class="col-xl-6">
                                                <label for="wallet_avl_balance" class="form-label"><span class="text-danger">* </span>wallet Available Balance</label>
                                                <input type="text" class="form-control @error('wallet_avl_balance') is-invalid @enderror" id="wallet_avl_balance" name="wallet_avl_balance" value="{{isset($userDetails->wallet_avl_balance) ? $userDetails->wallet_avl_balance: old('wallet_avl_balance')}}" placeholder="Phone Number">
                                                @if ($errors->has('wallet_avl_balance'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('wallet_avl_balance') }}
                                                    </div>
                                                @endif
                                            </div> --}}

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Select2 Cdn -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugin/tagify/tagify.min.js') }}"></script>

    <!-- Internal Select-2.js -->
    <script src="{{ asset('assets/js/select2.js') }}"></script>

    <script src="{{ asset('assets/libs/dropzone/dropzone-min.js') }}"></script>

    <script src="{{ asset('assets/js/custom/product.js') }}"></script>

    {{-- <script src="{{ asset('assets/js/fileupload.js') }}"></script> --}}
    <script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
    <!-- <script src="{{ asset('assets/js/form-validation.js') }}"></script> -->
@endpush
