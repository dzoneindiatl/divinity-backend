@extends('admin.layout.master')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')

@include('admin.layout.response_message')

<?php 
    $createPermission   = functionCheckPermission("StaffController@create");
    $editPermission   = functionCheckPermission("StaffController@edit");
    $viewPermission     = functionCheckPermission("StaffController@show");
    $deletePermission   = functionCheckPermission("StaffController@destroy");
    $statusPermission   = functionCheckPermission("StaffController@changeStatus");

?>
<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <h1 class="page-title fw-semibold fs-18 mb-0">Staffs</h1>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Staffs</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    Staffs
                </div>
                <div class="prism-toggle">
                    <a href='{{route("admin-"."$model.create")}}' class="btn btn-primary mb-3" style="float: right">Add
                    Staff</a>
                </div>
            </div>
            <div class="card-body">
                <table id="datatable-basic" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $result)
                        <tr>
                            <td>
                                @if (!empty($result->image))
                                    <img height="50" width="50" src="{{isset($result->image)? $result->image:''}}" />
                                @endif
                            </td>
                            
                            <td>{{ $result->name ?? "N/A" }}</td>
                            <td>
                                {{ $result->email ?? "N/A" }}
                            </td>
                            <td>
                                {{ $result->phone_number ?? "N/A" }}
                            </td>
                            <td>
                                @if($result->is_active == 1)
                                <span class="label label-lg label-light-success label-inline">Activated</span>
                                @else
                                <span class="label label-lg label-light-danger label-inline">Deactivated</span>
                                @endif
                            </td>

                            <td>
                                <div class="hstack gap-2 flex-wrap">
                                    @if($statusPermission == 1)
                                        @if($result->is_active == 1)
                                            <a href='{{route("admin-staff.status",array($result->id,0))}}'
                                            class="btn btn-danger" id="deactivate-button"><i class="ri-close-line"></i></a>
                                        @else
                                            <a href='{{route("admin-staff.status",array($result->id,1))}}'
                                            class="btn btn-success" id="activate-button"><i class="ri-check-line"></i></a>
                                        @endif
                                        
                                    @endif
                                    <!-- @if($viewPermission == 1)
                                        <a href="{{route('admin-staff.show',base64_encode($result->id))}}"
                                            class="btn btn-info"><i class="ri-eye-line"></i></a>
                                    @endif -->
                                    @if($editPermission == 1)
                                        <a href="{{route('admin-staff.edit',base64_encode($result->id))}}"
                                        class="btn btn-info"><i class="ri-edit-line"></i></a>
                                    @endif
                                    @if($deletePermission == 1)
                                        <form method="GET"
                                            action="{{route('admin-staff.delete',base64_encode($result->id))}}">
                                            @csrf
                                            <input name="_method" type="hidden" value="DELETE">
                                            <button type="submit" class="btn btn-danger" id="confirm-button"><i
                                                    class="ri-delete-bin-5-line"></i></button>
                                        </form>
                                    @endif
                                    
                                </div>
                            </td>
                        </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Datatables Cdn -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.6/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- Internal Datatables JS -->
<script src="{{ asset('assets/js/datatables.js') }}"></script>

<!-- Sweetalerts JS -->
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>

@endpush