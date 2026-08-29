@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        View {{Config('constants.BLOGS.BLOG_TITLE')}}
                    </h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route(dashboard)}}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($model.'.index')}}" class="text-muted">Blogs</a>
                        </li>
                    </ul>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <div class=" container ">
            <div class="card card-custom gutter-b">
                <div class="card-header card-header-tabs-line">
                    <div class="card-toolbar">
                        <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-bold nav-tabs-line-3x"
                            role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active hide_me" data-toggle="tab"
                                    href="#kt_apps_contacts_view_tab_1">
                                    <span class="nav-text">
                                        Blog Information
                                    </span>
                                </a>
                            </li>
                            
                        </ul>
                    </div>
                </div>
                <div class="card-body px-0">
                    <div class="tab-content px-10">
                        <div class="tab-pane active" id="kt_apps_contacts_view_tab_1" role="tabpanel">
                            <div class="form-group row my-2">
                                <label class="col-4 col-form-label">Image:</label>
                                <div class="col-8">
                                    @if (!empty($userDetails->image))
                                        <a class="fancybox-buttons" data-fancybox-group="button" href="{{isset($userDetails->image)? $userDetails->image:''}}"><img height="50" width="50" src="{{isset($userDetails->image)? $userDetails->image:''}}" />
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row my-2">
                                <label class="col-4 col-form-label">Title:</label>
                                <div class="col-8">
                                    <span>{{$userDetails->title?? 'N/A'}}</span>
                                </div>
                            </div>
                            <div class="form-group row my-2">
                                <label class="col-4 col-form-label">Category:</label>
                                <div class="col-8">
                                    <span>{{$userDetails->category_name?? 'N/A'}}</span>
                                </div>
                            </div>
                            <div class="form-group row my-2">
                                <label class="col-4 col-form-label">Author:</label>
                                <div class="col-8">
                                    <span>{{$userDetails->posted_by?? 'N/A'}}</span>
                                </div>
                            </div>
                           
                            <div class="form-group row my-2">
                                <label class="col-4 col-form-label">Description:</label>
                                <div class="col-8">
                                    <span>{!!$userDetails->description?? 'N/A'!!}</span>
                                </div>
                            </div>
                
                             
                            <div class="form-group row my-2">
                                <label class="col-4 col-form-label">Posted On:</label>
                                <div class="col-8">
                                    <span>{{ date(config("Reading.date_format"),strtotime($userDetails->posted_on)) }}</span>
                                </div>
                            </div>
                            <div class="form-group row my-2">
                                <label class="col-4 col-form-label">Status:</label>
                                <div class="col-8">
                                    <span class="form-control-plaintext font-weight-bolder">
                                        @if($userDetails->is_active == 1)
                                        <span class="label label-lg label-light-success label-inline">Activated</span>
                                        @else
                                        <span class="label label-lg label-light-danger label-inline">Deactivated</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                       
                      
                    
                


            </div>
        </div>
    </div>
</div>
</div>
</div>
<script>
$('#upgradediv').hide();
$(document).ready(function() {
    $('input[type="radio"]').click(function() {
        if ($(this).attr('id') == 'upgrade') {
            $('#upgradediv').show();
            $('#changediv').hide();
        } else {
            $('#upgradediv').hide();
            $('#changediv').show();
        }
    });


});

// $("#show_info").click(function(){
//    $('.medical_info').show();
// });


$(document).ready(function() {
    $(".hide_me").click(function() {
        $(".medical_info").hide();
    });
    $("#show_info").click(function() {
        $('.medical_info').show();
    });
});
</script>
<script>
$(document).ready(function() {
    $('#datepickerfrom').datetimepicker({
        format: 'YYYY-MM-DD'
    });
});
</script>


@stop