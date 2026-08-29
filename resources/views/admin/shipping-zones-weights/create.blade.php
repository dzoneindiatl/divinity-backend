@extends('admin.layout.master')

@push('styles')
<link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
@endpush
@section('content')
@include('admin.layout.response_message')
<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{  route('admin-shipping-zones.index')}}">Shipping Zone</a></li>
                <li class="breadcrumb-item"><a href="{{  route('admin-shipping-zones-weights.index', base64_encode($dep_id))}}">Shipping Weight</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Weight</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="{{ route('admin-shipping-zones-weights.add',base64_encode($dep_id)) }}" method="post" id="shippingAreaForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" class="form-control" id="shipping_zone_id" name="shipping_zone_id" value="{{$dep_id}}">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Create Shipping Weight
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="weight_min" class="form-label"><span class="text-danger">*</span>Weight Minimum</label>
                                    <input type="text" class="form-control @error('weight_min') is-invalid @enderror" id="weight_min" name="weight_min" value="" placeholder="0" required>  
                                    @if ($errors->has('weight_min'))
                                    <div class=" invalid-feedback">
                                        {{ $errors->first('weight_min') }}
                                    </div>
                                    @endif                                  
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="weight_max" class="form-label"><span class="text-danger">*</span>Weight Maximum</label>
                                    <input type="text" class="form-control @error('weight_max') is-invalid @enderror" id="weight_max" name="weight_max" value="" placeholder="100" required>                                    
                                    @if ($errors->has('weight_max'))
                                    <div class=" invalid-feedback">
                                        {{ $errors->first('weight_max') }}
                                    </div>
                                    @endif 
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="weight_type" class="form-label"><span class="text-danger">*</span>Weight Type </label>
                                    <select class="select2-original form-control @error('weight_type') is-invalid @enderror" name="weight_type" id="weight_type" required>
                                    <option value="">Select Type</option>                                        
                                    <option value="grm" {{ old('weight_type') == 'grm' ? 'selected' : '' }}>GRM</option>                                        
                                    <option value="kg" {{ old('weight_type') == 'kg' ? 'selected' : '' }}>KG</option>
                                    </select>
                                    @if ($errors->has('weight_type'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('weight_type') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="amount" class="form-label"><span class="text-danger">*</span>Amount </label>
                                    <input type="text" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="" placeholder="Amount" required>  
                                    @if ($errors->has('amount'))
                                    <div class=" invalid-feedback">
                                        {{ $errors->first('amount') }}
                                    </div>
                                    @endif       
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
                    </div>
                </div>
            </div>

            
           
        </form>
    </div>
</div>


@endsection

@push('scripts')
<script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<!-- Select2 Cdn -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/plugin/tagify/tagify.min.js') }}"></script>
<!-- Internal Select-2.js -->
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>
<script src="{{ asset('assets/js/form-validation.js') }}"></script>
@endpush