@extends('admin.layout.master')

@section('content')
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Instagram Sliders</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->
    <div class="row">
        <div class="col-xl-12">
            <form action="{{ route('admin-InstagramSliders.update', $userDetails->id) }}" method="post" enctype="multipart/form-data"
                id="createBannerForm">
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
                                            <input type="hidden" name="type" value="full_image">
                                            <div class="row gy-3 mt-2" id="imageField">
                                                <div class="col-xl-3">
                                                    <label for="image" class="form-label"><span class="text-danger">
                                                        </span>Instagram Sliders Image</label>
                                                    <input type="file"
                                                        class="form-control @error('profile_image') is-invalid @enderror"
                                                        id="image" name="image" accept="image/*" >
                                                    @if (!empty($userDetails->media_url))
                                                        <img height="50" width="50"
                                                            src="{{ isset($userDetails->media_url) ? $userDetails->media_url : '' }}" />
                                                    @endif
                                                    @if ($errors->has('image'))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('image') }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-xl-3">
                                                    <label for="title" class="form-label">Title</label>
                                                    <input type="text"
                                                        class="form-control @error('title') is-invalid @enderror"
                                                        name="title" id="title" value="{!! isset($userDetails->title) ? $userDetails->title : old('title') !!}">

                                                    @if ($errors->has('title'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('title') }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-xl-4">
                                                    <label for="redirection_url" class="form-label"><span class="text-danger">
                                                        </span>URL</label>
                                                    <input type="url"
                                                        class="form-control @error('redirection_url') is-invalid @enderror"
                                                        id="redirection_url" name="redirection_url"
                                                        value="{{ isset($userDetails->redirection_url) ? $userDetails->redirection_url : old('redirection_url') }}"
                                                        placeholder="URL">
                                                    @if ($errors->has('redirection_url'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('redirection_url') }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-xl-2">
                                                    <label for="like_count" class="form-label"><span class="text-danger">
                                                        </span>Like Count</label>
                                                    <input type="text"
                                                        class="form-control @error('like_count') is-invalid @enderror"
                                                        id="like_count" name="like_count"
                                                        value="{{ isset($userDetails->like_count) ? $userDetails->like_count : old('like_count') }}"
                                                        placeholder="Like Count">
                                                    @if ($errors->has('like_count'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('like_count') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            
                                                <!-- <div class="col-xl-3">
                                                    <label for="height" class="form-label"><span class="text-danger">
                                                        </span>Height</label>
                                                    <input type="number"
                                                        class="form-control @error('height') is-invalid @enderror"
                                                        id="height" name="height"
                                                        value="{{ isset($userDetails->height) ? $userDetails->height : old('height') }}"
                                                        placeholder="Height">
                                                    @if ($errors->has('height'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('height') }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-xl-3">
                                                    <label for="width" class="form-label"><span class="text-danger">
                                                        </span>Width</label>
                                                    <input type="number"
                                                        class="form-control @error('width') is-invalid @enderror"
                                                        id="width" name="width"
                                                        value="{{ isset($userDetails->width) ? $userDetails->width : old('width') }}"
                                                        placeholder="Width">
                                                    @if ($errors->has('width'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('width') }}
                                                        </div>
                                                    @endif
                                                </div> -->
                                            </div>
                                            <div class="row gy-3 mt-2" id="descriptionField">                                                
                                                <div class="col-xl-6">
                                                    <label for="description" class="form-label">Description</label>
                                                    <textarea class="form-control @error('title') is-invalid @enderror" name="description" id="description"
                                                        rows="2">{!! isset($userDetails->description) ? $userDetails->description : old('description') !!}</textarea>
                                                    @if ($errors->has('description'))
                                                        <div class=" invalid-feedback">
                                                            {{ $errors->first('description') }}
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
