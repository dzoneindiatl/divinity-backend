@extends('admin.layout.master')

@section('content')
<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Role</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="{{route('admin-roles.update',base64_encode($role->id))}}" method="post" enctype="multipart/form-data"
            id="editStaffForm">
            @method('PUT')
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
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{isset($role->name) ? $role->name: old('name')}}" placeholder="Name">
                                                @if ($errors->has('name'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('name') }}
                                                    </div>
                                                @endif
                                                @if ($errors->has('permissions'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('permissions') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                
                                <table class="table">
                                    <thead>
                                        <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Page</th>
                                        <th scope="col"><input type="checkbox" name="all_select" value="1" class="checkAll" /> Select All</th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1; @endphp
                                            @foreach($menus as $menu)
                                                <tr>
                                                    <th scope="row">{{ $i++ }}</th>
                                                    <td><input id="{{$menu->id}}" type="checkbox" name="is_checked" value="1" class="parent" /> {{ $menu->menu_name }}</td>
                                                    @php
                                                    $allPermission =  $permissions->where('menu_name', $menu->menu_name);
                                                    @endphp
                                                    @foreach($allPermission as $permission)
                                                        <td>
                                                            <input type="checkbox" id="{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}" class="children child{{$menu->id}}" @if(in_array($permission->id, $rolePermissions)) @checked(true) @endif /> {{ ucfirst($permission->name) }}
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach

                                    </tbody>
                                </table>
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
   
    

    {{--$('#designation_id').on('change', function() {
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
    }); --}}

</script>

@endpush