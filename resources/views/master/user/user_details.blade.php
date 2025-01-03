@extends('layouts.head')
@section('title') User details @endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <a href="{{url('master/user')}}" class="btn btn-sm btn-default" title="All users"><i class="fa fa-list"></i> List</a>
                        <button type="button" class="btn btn-sm btn-default" onclick="return referesh_form();"><i class="fa fa-refresh" aria-hidden="true"></i> Refresh</button>
                    </div>
                </div>
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                
                <div class="row">
                    <div class="col-12">
                        <h4>All user details section <small>card-tabs / card-outline-tabs</small></h4>
                    </div>
                </div>
                <!-- ./row -->
                <div class="row">
                    <div class="col-12 col-sm-12">
                        <div class="card card-primary card-tabs">
                            <div class="card-header p-0 pt-1 card_header_color">
                                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="true" onclick="return user_change_tab('profile');">Profile</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-one-document-tab" data-toggle="pill" href="#custom-tabs-one-document" role="tab" aria-controls="custom-tabs-one-document" aria-selected="false" onclick="return user_change_tab('document');">Document</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-one-messages-tab" data-toggle="pill" href="#custom-tabs-one-messages" role="tab" aria-controls="custom-tabs-one-messages" aria-selected="false">Messages</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-one-settings-tab" data-toggle="pill" href="#custom-tabs-one-settings" role="tab" aria-controls="custom-tabs-one-settings" aria-selected="false">Settings</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-one-tabContent">
                                    <div class="tab-pane fade show active" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                                        <div class="setTabLoaderDiv"></div>
                                        <div id="setProfileDiv"></div>
                                    </div>
                                    <div class="tab-pane fade" id="custom-tabs-one-document" role="tabpanel" aria-labelledby="custom-tabs-one-document-tab">
                                        <div class="setTabLoaderDiv"></div>
                                        <div id="setDocumentDiv"></div>
                                    </div>
                                    <div class="tab-pane fade" id="custom-tabs-one-messages" role="tabpanel" aria-labelledby="custom-tabs-one-messages-tab">
                                        <div class="setTabLoaderDiv"></div>
                                        <div id="setMessageDiv"></div>
                                    </div>
                                    <div class="tab-pane fade" id="custom-tabs-one-settings" role="tabpanel" aria-labelledby="custom-tabs-one-settings-tab">
                                        <div class="setTabLoaderDiv"></div>
                                        <div id="setSettingDiv"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <script>
        $(document).ready(function(){
            user_change_tab('profile');
            get_ajax_country('{{@$address->country_id}}','country_id');
            get_ajax_state('{{@$address->country_id}}','state_id','{{@$address->state_id}}');
            get_ajax_city('{{@$address->state_id}}','city_id','{{@$address->city_id}}');

            //get_role_reporting('{{$data->reporting_role_id}}','reporting_role_id');

            get_department_record('{{$data->department_id}}','department_id');
            get_designation_record('{{$data->department_designation_id}}','department_designation_id','department_id','{{$data->department_id}}')
        });

        function user_change_tab(page_type=''){
            var id = "{{$data->id}}";
            $('.setTabLoaderDiv').html('<i class="fa fa-spinner fa-spin"></i> Loading...');
            $.ajax({
                method: 'POST',
                url: "{{route('user_tab_detail')}}",
                data: {id,page_type},
                headers: {
                    'X-CSRF-TOKEN': csrf_token
                },
                success: function(response) {
                    $('.setTabLoaderDiv').html('');
                    if(page_type == 'profile'){
                        $('#setProfileDiv').html(response);
                    }
                    else if(page_type == 'document'){
                        $('#setDocumentDiv').html(response);
                    }
                }
            });
        }
    </script>
    @include('script.comman_js')
    @include('script.country_state_city_js')
    @include('script.user_js')
@endsection    
            
        