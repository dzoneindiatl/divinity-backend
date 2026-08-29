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
                <li class="breadcrumb-item active" aria-current="page">Edit Staff</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="{{route('admin-'.$model.'.update',base64_encode($modell->id))}}" method="post" enctype="multipart/form-data"
            id="editStaffForm">
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
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{isset($modell->name) ? $modell->name: old('name')}}" placeholder="Name">
                                                @if ($errors->has('name'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('name') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="email" class="form-label"><span class="text-danger">* </span>Email</label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{isset($modell->email) ? $modell->email: old('email')}}" placeholder="Email">
                                                @if ($errors->has('email'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('email') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="phone_number" class="form-label"><span class="text-danger">* </span>Phone Number</label>
                                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{isset($modell->phone_number) ? $modell->phone_number: old('phone_number')}}" placeholder="Phone Number">
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
                                                @if (!empty($modell->image))
                                                    <img height="50" width="50" src="{{isset($modell->image)? $modell->image:''}}" />
                                                @endif
                                                @if ($errors->has('image'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('image') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="password" class="form-label"><span class="text-danger">* </span>Password</label>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="{{ old('password') }}" placeholder="Password">
                                                @if ($errors->has('password'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('password') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="confirm_password" class="form-label"><span class="text-danger">* </span>Confirm Password</label>
                                                <input type="password" class="form-control @error('confirm_password') is-invalid @enderror" id="confirm_password" name="confirm_password" value="{{old('confirm_password') }}" placeholder="Confirm Password">
                                                @if ($errors->has('confirm_password'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('confirm_password') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="department_id" class="form-label">Department</label><span class="text-danger"> * </span>
                                                <select name="department_id" class="DepartmentList form-control @error('department_id') is-invalid @enderror">
                                                    <option value="" selected="true" disabled="disabled">Select Department</option>
                                                    @foreach($departments as $departments)
                                                    <option value="{{$departments->id}}" {{ $departments->id == $modell->department_id ? 'selected' : '' }}> {{$departments->name}}</option>
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
                                                <div class="staffPermission">
                                                    <?php
                                                    if (!empty($aclModules)) {
                                                    ?>
                                                        <div class="card-body">
                                                        <h3 class="mt-8 mb-8">Staff Permissions</h3>
                                                        <label class="font-size-lg font-weight-bold checkbox mb-2">
                                                            <input type="checkbox" class="checkAll" />
                                                            <span class="mr-4"></span>
                                                            Check All
                                                        </label>
                                                        <div id="accordion" role="tablist" class="accordion accordion-toggle-arrow">
                                                            <?php
                                                            $counter    =    0;
                                                            foreach ($aclModules as $aclModule) {
                                                            ?>
                                                                <div class="card mb-5 border-bottom">
                                                                    <div class="card-header d-flex align-items-center" role="tab">
                                                                        <div class="ml-5">
                                                                            <label class="checkbox">
                                                                                <input type="checkbox" name="data[{{$counter}}][value]" value=1 class="parent" id="{{$aclModule->id}}" {{ ($aclModule->active == 1) ? 'checked' : '' }}>
                                                                                <input type="hidden" name="data[{{$counter}}][department_id]" value="{{$aclModule->id}}">
                                                                                <span class="mr-2"></span>

                                                                            </label>
                                                                            <a class="text-dark px-2 py-4 w-100" role="button" data-bs-toggle="collapse" data-bs-parent="#accordion" href="#collapse{{$counter}}" aria-expanded="true" aria-controls="collapse{{$counter}}">
                                                                            <i class="more-less glyphicon glyphicon-plus"></i>
                                                                            {{strtoupper($aclModule->title ?? '')}}
                                                                        </a>
                                                                        </div>
                                                                        
                                                                    </div>
                                                                    <div id="collapse{{$counter}}" class="collapse" data-bs-parent="#accordion">
                                                                        <?php
                                                                        if (!empty($aclModule['sub_module'])) {
                                                                        ?>
                                                                            <div class="card-body ">
                                                                                <div class="">
                                                                                    <?php
                                                                                    $module_counter        =    0;
                                                                                    foreach ($aclModule['sub_module'] as $subModule) {
                                                                                    ?>
                                                                                        <div class="font-size-lg font-weight-bold mb-3">{{!empty($subModule->title)?strtoupper($subModule->title):''}}</div>
                                                                                        <div class="row">
                                                                                            <?php
                                                                                            $count    =    0;
                                                                                            if (!$subModule['module']->isEmpty()) {
                                                                                                foreach ($subModule['module'] as $module) {
                                                                                                    $count++;
                                                                                            ?>
                                                                                                    <div class="col-auto mb-5">
                                                                                                        <label class="checkbox">
                                                                                                            <input type="checkbox" id="{{ $aclModule->id }}" name="data[{{$counter}}][module][{{$module_counter}}][value]" value=1 class="children child{{$aclModule->id}}" {{ ($module->active == 1) ? 'checked' : '' }}>
                                                                                                            <input type="hidden" name="data[{{$counter}}][module][{{$module_counter}}][id]" value="{{$module->id}}">
                                                                                                            <input type="hidden" name="data[{{$counter}}][module][{{$module_counter}}][department_module_id]" value="{{$subModule->id}}">
                                                                                                            <span class="mr-2"></span>
                                                                                                            {{$module->name}}
                                                                                                        </label>
                                                                                                    </div>
                                                                                                <?php
                                                                                                    $module_counter++;
                                                                                                }
                                                                                                ?>
                                                                                                <td colspan="6-{{$count}}"></td>
                                                                                            <?php
                                                                                            } else {
                                                                                            ?>
                                                                                                <td colspan="6"></td>
                                                                                            <?php
                                                                                            }
                                                                                            ?>
                                                                                        </div>
                                                                                        <?php
                                                                                    }
                                                                                    if (!empty($aclModule['extModule'])) {
                                                                                        $count    =    0;
                                                                                        foreach ($aclModule['extModule'] as $subModule) {
                                                                                            $count++;
                                                                                        ?>
                                                                                            <div class="font-size-lg font-weight-bold mb-3">
                                                                                                {{strtoupper($subModule->title ?? '')}}
                                                                                            </div>
                                                                                            <div class="row">
                                                                                                <?php
                                                                                                if (!$subModule['module']->isEmpty()) {
                                                                                                    foreach ($subModule['module'] as $module) {
                                                                                                ?>
                                                                                                        <div class="col-auto mb-5">
                                                                                                            <label class="checkbox">
                                                                                                                <label class="checkbox">
                                                                                                                    <input type="checkbox" id="{{ $aclModule->id }}" name="data[{{$counter}}][module][{{$module_counter}}][value]" value=1 class="children child{{$aclModule->id}}" {{ ($module->active == 1) ?  'checked' : '' }}>

                                                                                                                    <input type="hidden"  name="data[{{$counter}}][module][{{$module_counter}}][id]" value="{{$module->id}}">

                                                                                                                    <input type="hidden" name="data[{{$counter}}][module][{{$module_counter}}][department_module_id]" value="{{$subModule->id}}">
                                                                                                                    <span class="mr-2"></span>
                                                                                                                    {{$module->name}}
                                                                                                                </label>
                                                                                                        </div>
                                                                                                    <?php
                                                                                                        $module_counter++;
                                                                                                    }
                                                                                                    ?>
                                                                                                    <td colspan="6-{{$count}}"></td>
                                                                                                <?php
                                                                                                } else {
                                                                                                ?>
                                                                                                    <td colspan="5"></td>
                                                                                                <?php
                                                                                                }
                                                                                                ?>
                                                                                            </div>
                                                                                    <?php
                                                                                        }
                                                                                    }
                                                                                    ?>
                                                                                </div>
                                                                            <?php
                                                                        }
                                                                            ?>
                                                                            <?php
                                                                            if (isset($aclModule['parent_module_action'])  && (!$aclModule['parent_module_action']->isEmpty())) {
                                                                            ?>
                                                                                <div class="font-size-lg font-weight-bold mb-3"> {{$aclModule->title}} </div>
                                                                                <div class="row">
                                                                                    <?php
                                                                                    foreach ($aclModule['parent_module_action'] as $parentModule) {
                                                                                    ?>
                                                                                        <div class="col-auto mb-5">
                                                                                            <label class="checkbox">
                                                                                                <input type="checkbox" id="{{ $aclModule->id }}" name="data[{{$counter}}][module][{{$module_counter}}][value]" value=1 class="children child{{$aclModule->id}}" {{ ($parentModule->active == 1) ?  'checked' : '' }}>
                                                                                                <input type="hidden" id="{{ $aclModule->id }}" name="data[{{$counter}}][module][{{$module_counter}}][id]" value="{{$parentModule->id}}">
                                                                                                <input type="hidden" id="{{ $aclModule->id }}" name="data[{{$counter}}][module][{{$module_counter}}][department_module_id]" value="{{$aclModule->id}}">
                                                                                                <span class="mr-2"></span>
                                                                                                {{$parentModule->name}}
                                                                                            </label>
                                                                                        </div>
                                                                                    <?php
                                                                                        $counter++;
                                                                                    }
                                                                                    ?>
                                                                                </div>
                                                                            <?php
                                                                            }
                                                                            ?>
                                                                            </div>
                                                                    </div>
                                                                </div>
                                                            <?php
                                                                $counter++;
                                                            }
                                                            ?>
                                                        </div>
                                                        </div>
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
                                <button type="button" id="editstaffformbutton" class="btn btn-primary">Submit</button>
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
                'departmentid': departmentid
            },
            success: function(response) {
                $(".designation_iddrop").html(response);
                setTimeout(() => {
                    
                    $("#designation_id").val(parseInt('{{$modell->designation_id}}'))
                }, 100);
            }
        });
    }
    $(function() {
        $(".DepartmentList").change(function() {
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
                    $('.staffPermission').html('');
                    $('.staffPermission').html(r);
                } else {
                    show_message(r.message, 'error');
                }

            }
        });
    });

    $("#editstaffformbutton").click(function() {
            $("#editStaffForm").submit();
        });

</script>

@endpush