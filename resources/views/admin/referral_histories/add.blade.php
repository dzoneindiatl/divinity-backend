@extends('admin.layouts.layout')
@section('content')
<style>
    .invalid-feedback {
        display: inline;
    }
</style>
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        Add Referral </h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($model.'.index')}}" class="text-muted">{{Config('constants.REFERRAL.REFERRAL_TITLE')}}s</a>
                        </li>
                    </ul>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>
    {{-- @php 
    echo "<pre>"; print_r($errors); die;
    @endphp --}}
    <div class="d-flex flex-column-fluid">
        <div class=" container ">
            <form action="{{route('referral_histories.save')}}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-1"></div>
                            <div class="col-xl-10">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="referral_by">Referral By Email Address</label><span class="text-danger"> * </span>
                                            <input type="email" name="referral_by" class="form-control form-control-solid form-control-lg  @error('referral_by') is-invalid @enderror" value="{{old('referral_by')}}">
                                            @if ($errors->has('referral_by'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('referral_by') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="referral_to">Referral To Email Address</label><span class="text-danger"> *</span>
                                            <input type="email" name="referral_to" class="form-control form-control-solid form-control-lg  @error('referral_to') is-invalid @enderror" value="{{old('referral_to')}}">
                                            @if ($errors->has('referral_to'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('referral_to') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="d-flex justify-content-between border-top mt-5 pt-10">
                                    <div>
                                        <button button type="submit" onclick="submit_form();" class="btn btn-success adimnBtnStyle1 font-weight-bold text-uppercase px-9 py-4">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                                   

                            </div>
                        </div>





                               
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    $(document).ready(function() {
        initialize();
    });

    
    $("#phone_number_country").intlTelInput({
                                        allowDropdown:true,
                                        preferredCountries:[],
                                        initialCountry: 'in',
                                        separateDialCode:true
                                    });
                                    
    $("#phone_number_country").on('countrychange', function (e, countryData) {
        console.log(countryData);
        $("#dial_code").val($(".iti__selected-dial-code").html());
        $("#country_code").val($("#phone_number_country").intlTelInput("getSelectedCountryData").iso2);
    });

    function doIt(e) {
        var e = e || event;
        if (e.keyCode == 32) return false;
    }
    window.onload = function() {
        var inp = document.getElementById("zip_code");

        inp.onkeydown = doIt;
    };
    function submit_form() {
        $(".mws-form").submit();
    }
</script>
@stop