@extends('admin.layout.master')
@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
@endpush
@section('content')

@include('admin.layout.response_message')

<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <h1 class="page-title fw-semibold fs-18 mb-0">Departments</h1>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Departments</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    Departments
                </div>
                <div class="prism-toggle">
                    <a href="{{route('admin-'.$model.'.create')}}" class="btn btn-primary mb-3">Add
                        New Department</a>
                </div>
            </div>
            <div class="card-body">
                <table id="datatable-basic" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center">Name</th>
                            <th class="text-center">Added On</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!$results->isEmpty())
                        @foreach($results as $result)
                        <tr>
                        <td>{{ $result->name ?? "N/A" }}</td>
                        <td>{{ date("Y-m-d",strtotime($result->created_at)) }}</td>
                        <td>
                            @if($result->is_active == 1)
                            <span class="badge bg-success">Activated</span>
                            @else
                            <span class="badge bg-danger">Deactivated</span>
                            @endif
                        </td>
                        <td>
                        <div class="hstack gap-2 flex-wrap">
                            @if($result->is_active == 1)
                            
                            <a title="Click To Deactivate"
                                href='{{route("admin-".$model.".status",array($result->id,1))}}'
                                class="btn btn-danger" id="deactivate-button"
                                ><i class="ri-close-line"></i>
                                
                            </a>
                            @else
                            <a title="Click To Activate"
                                href='{{route("admin-".$model.".status",array($result->id,0))}}'
                                class="btn btn-success" id="activate-button"
                                ><i class="ri-check-line"></i>
                                
                            </a>
                            @endif

                            
                                <a href="{{route('admin-'.$model.'.edit',base64_encode($result->id))}}"
                                    class="btn btn-info"><i class="ri-edit-line"></i></a>
                                <form method="POST"
                                    action="{{route('admin-'.$model.'.delete',['endepid'=>base64_encode($result->id)])}}">
                                    @csrf
                                    <input name="_method" type="hidden" value="GET">
                                    <button type="submit" class="btn btn-danger" id="confirm-button"><i
                                            class="ri-delete-bin-5-line"></i></button>
                                </form>
                          

                            <div class="dropdown dropdown-inline">
                                <a href="javascript:;" class="btn btn-light"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ri-list-check"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <ul class="nav nav-hoverable flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link"
                                                href="{{route('admin-designations.index',['endesid'=>base64_encode($result->id)])}}">
                                                <span class="nav-text">Designation</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            </div>
                        </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="6" style="text-align:center;"> {{ trans("Record not found.") }}</td>
                        </tr>
                        @endif

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

<script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>

<!-- Internal Datatables JS -->
<script src="{{ asset('assets/js/datatables.js') }}"></script>

<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>
@endpush