@extends('admin.layout.master')

@push('styles')
    <link href="{{ asset('assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
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
                    <li class="breadcrumb-item active" aria-current="page">Edit Seo Page</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->

    <div class="row">
        <div class="col-xl-12">
            <form action="{{ route('admin-' . $model . '.update', base64_encode($emailTemplate->id)) }}" method="POST"
                id="categoryForm" enctype="multipart/form-data">
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
                                                    <label for="name" class="form-label"><span class="text-danger">*
                                                        </span>Name</label>
                                                    <input type="text"
                                                        class="form-control @error('name') is-invalid @enderror"
                                                        id="name" name="name"
                                                        value="{{ isset($emailTemplate->name) ? $emailTemplate->name : old('name') }}"
                                                        placeholder="Name">
                                                    @if ($errors->has('name'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('name') }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-xl-6">
                                                    <label for="subject" class="form-label"><span class="text-danger">*
                                                        </span>Subject</label>
                                                    <input type="text"
                                                        class="form-control @error('subject') is-invalid @enderror"
                                                        id="subject" name="subject"
                                                        value="{{ isset($emailTemplate->subject) ? $emailTemplate->subject : old('subject') }}"
                                                        placeholder="Subject">
                                                    @if ($errors->has('subject'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('subject') }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-xl-6" style="display:none;">
                                                    <label for="action" class="form-label"><span class="text-danger">*
                                                        </span>Action</label>
                                                    <select
                                                        class="js-example-placeholder-single form-control @error('action') is-invalid @enderror"
                                                        name="action" id="action">
                                                        <option value="">{{ $emailTemplate->action }}</option>
                                                    </select>
                                                </div>

                                                <div class="col-xl-6">
                                                    <lable for="Constants">Constants</lable><span class="text-danger"> *
                                                    </span>
                                                    <select name="constants" onchange="constants()" id="constants"
                                                        class="form-control">
                                                        <option value="" selected>Select Action</option>
                                                        @foreach ($action_options as $key => $arr)
                                                            <option value="">{{ $arr->action_option }}</option>
                                                        @endforeach
                                                    </select>
                                                    <a onclick="return InsertHTML()" href="javascript:void(0)"
                                                        class="btn btn-lg btn-success no-ajax pull-right"><i
                                                            class="icon-white "></i>{{ trans('Insert Variable') }} </a>
                                                </div>

                                                <div class="col-xl-12 mt-3">
                                                    <label for="body" class="form-label">Body</label>
                                                    <textarea class="form-control @error('body') is-invalid @enderror" name="body" id="body" cols="30"
                                                        rows="5">{!! isset($emailTemplate->body) ? $emailTemplate->body : old('body') !!}</textarea>
                                                    @if ($errors->has('body'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('body') }}
                                                        </div>
                                                    @endif
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

    {{-- <script src="{{ asset('assets/js/fileupload.js') }}"></script> --}}
    <script src="{{ asset('assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
    <!-- <script src="{{ asset('assets/js/form-validation.js') }}"></script> -->

    <script>
        function InsertHTML() {
            var str = document.getElementById('constants');
            var strUser = str.options[str.selectedIndex].text;

            if (strUser != '') {
                var newStr = '{' + strUser + '}';
                var oEditor = CKEDITOR.instances['body'];
                oEditor.insertHtml(newStr);
            }
        }

        CKEDITOR.replace(<?php echo 'body'; ?>, {
            filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
            enterMode: CKEDITOR.ENTER_BR
        });
        CKEDITOR.config.allowedContent = true;
    </script>
@endpush
