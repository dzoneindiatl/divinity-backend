@extends('admin.layout.master')
@section('content')

<link href="{{ asset('./public/assets/css/jquery-treetable.css')}}" rel="stylesheet" type="text/css" />
<script src="{{ asset('./public/assets/js/jquery-treetable.js')}}"></script>
<script>
    $(function() {
        $("table").treetable();
    });
</script>

<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        Referral Histories
                    </h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin-admin.dashboard')}}" class="text-muted">Dashboard</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <div class="d-flex flex-column-fluid">
        <div class=" container ">
            <div class="row">
                <div class="col-12">
                    <div class="card card-custom card-stretch card-shadowless">
                        <div class="card-body">
                            <div class="dataTables_wrapper ">
                                <div class="table-responsive">
                                    <table
                                        class="table dataTable table-head-custom table-head-bg table-borderless table-vertical-center"
                                        id="taskTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>referral_by</th>
                                                <th>referral_to</th>
                                                <th style="width:400px">Graph here</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i=1;?>
                                                @if(!empty($referral_histories))
                                                    <tr>
                                                        <th scope="row">{{$i}}</th>
                                                        <td>Mark</td>
                                                        <td>Otto</td>
                                                        <td>
                                                            <div class="tt" data-tt-id="294">294</div>
                                                        </td>
                                                    </tr>

                                                    @foreach($referral_histories as $referral_histories_v)
                                                    <tr>
                                                        <th scope="row">{{$i}}</th>
                                                        <td>Mark</td>
                                                        <td>Otto</td>
                                                        <td>
                                                            <div class="tt" data-tt-id="{{$referral_histories_v->referral_to}}" data-tt-parent="{{$referral_histories_v->referral_by}}">{{$referral_histories_v->referral_to}}</div>
                                                        </td>
                                                    </tr>
                                                    <?php $i++;?>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop