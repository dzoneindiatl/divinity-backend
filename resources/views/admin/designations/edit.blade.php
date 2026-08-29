@extends('admin.layout.master')

@push('styles')
<link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
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
                <li class="breadcrumb-item"><a href="{{  route('admin-departments.index')}}">Departments</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Designation</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->

<div class="card custom-card">
    <div class="card-header">
        <div class="card-title">
            Edit Designation
        </div>
    </div>
    <form action="" method="post" id="designationForm" autocomplete="off" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-xl-6">
                    <div class="card-body p-0">

                        <div class="mb-3">
                            <label for="name" class="form-label"><span class="text-danger">* </span>Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" placeholder="Enter Name" value="{{ $modell->name ?? '' }}">
                            @if ($errors->has('name'))
                            <div class=" invalid-feedback">
                                {{ $errors->first('name') }}
                            </div>
                            @endif
                        </div>


                    </div>


                </div>
                <div class="col-xl-10">
                    @if (!empty($aclModules))
                    <h5 class="mt-8 mb-8">Designation Permissions</h5>
                    <label class="font-size-lg font-weight-bold checkbox ">
                        <input type="checkbox" class="checkAll" />
                        <span class="mr-2"></span>
                        Check All
                    </label>
                    <div id="accordion" role="tablist" class="accordion accordion-toggle-arrow">
                        <?php $counter	=	0; ?>
                        @foreach ($aclModules as $aclModule)
                        <div class="card mb-4 border-bottom">
                            <div class="card-header d-flex align-items-center" role="tab">
                                <div class="ml-5 d-flex align-items-center w-100">
                                    <label class="checkbox mb-0 mr-3">
                                        <input type="checkbox" name="data[{{$counter}}][value]" value=1 class="parent"
                                            id="{{$aclModule->id}}" {{ ($aclModule->active == 1) ? 'checked' : '' }}>
                                        <input type="hidden" name="data[{{$counter}}][designation_id]"
                                            value="{{$aclModule->id}}">
                                        <span class="mr-2"></span>
                                    </label>
                                    <a class="text-dark px-2 py-4 w-100" role="button" data-bs-toggle="collapse"
                                        data-bs-parent="#accordion" href="#collapse{{$counter}}" aria-expanded="true"
                                        aria-controls="collapse{{$counter}}">
                                        <i class="more-less glyphicon glyphicon-plus"></i>
                                        {{strtoupper($aclModule->title ?? '')}}
                                    </a>
                                </div>

                            </div>
                            <div id="collapse{{$counter}}" class="collapse" data-bs-parent="#accordion">
                                @if (!empty($aclModule['sub_module']))
                                <div class="card-body">
                                    <div class="">
                                        <?php $module_counter =	0;	?>
                                        @foreach ($aclModule['sub_module'] as $subModule)
                                        <div class="font-size-lg font-weight-bold mb-3">
                                            {{strtoupper($subModule->title ?? '')}}
                                        </div>
                                        <div class="row">
                                            @if (!$subModule['module']->isEmpty())
                                            <?php $count	=	0; 	?>
                                            @foreach ($subModule['module'] as $module)
                                            <?php $count++;	?>
                                            <div class="col-auto mb-5">
                                                <label class="checkbox">
                                                    <input type="checkbox"
                                                        name="data[{{$counter}}][module][{{$module_counter}}][value]"
                                                        value=1 id="{{$aclModule->id}}"
                                                        class="children child.{{$aclModule->id}}"
                                                        {{ ($module->active == 1) ? 'checked' : '' }}>
                                                    <input type="hidden"
                                                        name="data[{{$counter}}][module][{{$module_counter}}][id]"
                                                        value="{{$module->id}}">
                                                    <input type="hidden"
                                                        name="data[{{$counter}}][module][{{$module_counter}}][designation_module_id]"
                                                        value="{{$subModule->id}}">
                                                    <span class="mr-2"></span>
                                                    {{$module->name}}
                                                </label>
                                            </div>
                                            <?php $module_counter++; ?>
                                            @endforeach
                                            <td colspan="6-{{$count}}"></td>
                                            @else
                                            <td colspan="6"></td>
                                            @endif
                                        </div>
                                        @endforeach
                                        @if (!empty($aclModule['extModule']))
                                        <?php $count	=	0;
													foreach ($aclModule['extModule'] as $subModule) {
														$count++;
													?>
                                        <div class="font-size-lg font-weight-bold mb-3">
                                            {{strtoupper($subModule->title ?? '')}}
                                        </div>
                                        <div class="row">
                                            @if (!$subModule['module']->isEmpty())
                                            @foreach ($subModule['module'] as $module)
                                            <div class="col-auto mb-5">
                                                <label class="checkbox">
                                                    <input type="checkbox"
                                                        name="data[{{$counter}}][module][{{$module_counter}}][value]"
                                                        value=1 id="{{$aclModule->id}}"
                                                        class="children child.{{$aclModule->id}}"
                                                        {{ ($module->active == 1) ?  'checked' : '' }}>
                                                    <input type="hidden"
                                                        name="data[{{$counter}}][module][{{$module_counter}}][id]"
                                                        value="{{$module->id}}">
                                                    <input type="hidden"
                                                        name="data[{{$counter}}][module][{{$module_counter}}][designation_module_id]"
                                                        value="{{$subModule->id}}">
                                                    <span class="mr-2"></span>
                                                    {{$module->name}}
                                                </label>
                                            </div>
                                            <?php $module_counter++; ?>
                                            @endforeach
                                            <td colspan="6-{{$count}}"></td>
                                            @else
                                            <td colspan="5"></td>
                                            @endif
                                        </div>
                                        <?php
													}
													?>
                                        @endif
                                    </div>
                                    @endif
                                    @if (isset($aclModule['parent_module_action']) &&
                                    (!$aclModule['parent_module_action']->isEmpty()))
                                    <div class="font-size-lg font-weight-bold mb-3">
                                        {{$aclModule->title}}
                                    </div>
                                    <div class="row">
                                        @foreach ($aclModule['parent_module_action'] as $parentModule)
                                        <div class="card mb-5 border-0 col-auto">
                                            <label class="checkbox">
                                                <input id="{{$aclModule->id}}" type="checkbox"
                                                    name="data[{{$counter}}][module][{{$module_counter}}][value]"
                                                    value=1 class="children child.{{$aclModule->id}}"
                                                    {{ ($parentModule->active == 1) ?  'checked' : '' }}>
                                                <input type="hidden"
                                                    name="data[{{$counter}}][module][{{$module_counter}}][id]"
                                                    value="{{$parentModule->id}}">
                                                <input type="hidden"
                                                    name="data[{{$counter}}][module][{{$module_counter}}][designation_module_id]"
                                                    value="{{$aclModule->id}}">
                                                <span class="mr-2"></span>

                                                {{$parentModule->name}}
                                            </label>
                                        </div>
                                        <?php
													$counter++;	?>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <?php $counter++; ?>
                        @endforeach
                    </div>
                    @endif
                </div>

            </div>
        </div>
        <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>

@endsection
@push('scripts')
<script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>
<!-- <script src="{{ asset('assets/js/form-validation.js') }}"></script> -->
<script src="{{ asset('assets/plugin/tagify/tagify.min.js') }}"></script>
<script src="{{ asset('assets/js/custom/designation.js') }}"></script>

@endpush