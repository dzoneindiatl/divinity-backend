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
                <li class="breadcrumb-item active" aria-current="page">Create Staff</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="{{ route('admin-staff.store') }}" method="post" enctype="multipart/form-data"
            id="createUserForm">
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
                                                <label for="name" class="form-label"><span class="text-danger">* </span>Name</label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{isset($userDetails->name) ? $userDetails->name: old('name')}}" placeholder="Name">
                                                @if ($errors->has('name'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('name') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="email" class="form-label"><span class="text-danger">* </span>Email</label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{isset($userDetails->email) ? $userDetails->email: old('email')}}" placeholder="Email">
                                                @if ($errors->has('email'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('email') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="phone_number" class="form-label"><span class="text-danger">* </span>Phone Number</label>
                                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{isset($userDetails->phone_number) ? $userDetails->phone_number: old('phone_number')}}" placeholder="Phone Number">
                                                @if ($errors->has('phone_number'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('phone_number') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="image" class="form-label"><span class="text-danger">
                                                    </span>Image</label>
                                                <input type="file" class="form-control @error('profile_image') is-invalid @enderror" id="image" name="image">
                                                @if (!empty($userDetails->image))
                                                    <img height="50" width="50" src="{{isset($userDetails->image)? $userDetails->image:''}}" />
                                                @endif
                                                @if ($errors->has('image'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('image') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="password" class="form-label"><span class="text-danger">* </span>Password</label>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="{{isset($userDetails->password) ? $userDetails->password: old('password')}}" placeholder="Password">
                                                @if ($errors->has('password'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('password') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="confirm_password" class="form-label"><span class="text-danger">* </span>Confirm Password</label>
                                                <input type="password" class="form-control @error('confirm_password') is-invalid @enderror" id="confirm_password" name="confirm_password" value="{{isset($userDetails->confirm_password) ? $userDetails->confirm_password: old('confirm_password')}}" placeholder="Confirm Password">
                                                @if ($errors->has('confirm_password'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('confirm_password') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="department_id" class="form-label">Department</label><span class="text-danger"> * </span>
                                                <select name="department_id" class="DepartmentList form-control @error('department_id') is-invalid @enderror">
                                                    <option value="">Select Department</option>
                                                    @foreach($departments as $departments)
                                                    <option value="{{$departments->id}}"> {{$departments->name}}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('department_id'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('department_id') }}
                                                </div>
                                                @endif
                                            </div>
                                            <div class="col-xl-6">
                                                <label for="designation_id" class="form-label">Designation</label><span class="text-danger"> * </span>
                                                <select name="designation_id" id="designation_id" class="designation_iddrop form-control chosenselect_designation_id @error('designation_id') is-invalid @enderror">
                                                </select>
                                                @if ($errors->has('designation_id'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('designation_id') }}
                                                </div>
                                                @endif
                                            </div>

                                        </div>
                                        <div class="row gy-3">
                                            <div class="col-xl-12">
                                                <div class="staffPermission"></div>
                                            </div>
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

<script type="text/javascript">
    $(document).ready(function() {
        if ($(".parent:input").val() == 1) {
            var parentid = $(".parent:input:checked").attr('id');
            $('.child' + parentid).attr('checked', true);
        }
        $(".checkAll").click(function() {
            $(".parent:input").not(this).prop('checked', this.checked);
            $(".children:input").not(this).prop('checked', this.checked);
        });
        $(".parent:input").click(function() {
            var parentid = $(this).attr('id');
            $('.child' + parentid).not(this).prop('checked', this.checked);
        });

        $(".children").click(function() {
            var childid = $(this).attr('id');
            $('#' + childid).not(this).prop('checked', this.checked);
        });
    });
</script>
<script>
   
    function DepartmentList() {
        departmentid = ($(".DepartmentList").val() != "") ? $(".DepartmentList").val() : 0;
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            url: '{{route("admin-"."$model.getDesignations")}}',
            type: "POST",
            data: {
                'departmentid': departmentid,
                "selctedid": "0"
            },
            success: function(response) {
                $(".designation_iddrop").html(response);
            }
        });
    }
    $(function() {
        $(".DepartmentList").change(function() {
            console.log('asdad')
            DepartmentList();
        });
        DepartmentList();
    });

    $('#designation_id').on('change', function() {
        var id = $(this).val();
        $.ajax({
            url: '{{route("admin-"."$model.getStaffPermission")}}',
            type: 'POST',
            data: {
                'designation_id': id
            }, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(r) {
                if (r != '') {
                    $('.staffPermission').html(r);
                } else {
                    show_message(r.message, 'error');
                }

            }
        });
    });

</script>

@endpush