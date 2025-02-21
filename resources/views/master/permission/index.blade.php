@extends('layouts.head')

@section('title') Department Menu Permission @endsection

@section('content')
<style>
    .main_menu_heading {
        font-size: 20px;
        color: #563be9;
    }
    .sub_menu_heading {
        font-size: 16px;
        color: #a1094f;
    }
    .sub_menu_sub_heading {
        font-size: 15px;
        color: #64C5B4;
    }
    .fa_arrow{
        font-size: 12px;
    }
    .card-title{
        margin-left: -12px; 
    }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <a href="{{url('master/department')}}" class="btn btn-sm btn-default" title="All departments"><i class="fa fa-list"></i> List</a>
                    <button type="button" class="btn btn-sm btn-default" onclick="return referesh_form();"><i class="fa fa-refresh" aria-hidden="true"></i> Refresh</button>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Home</a></li>
                        <li class="breadcrumb-item active">Department Permission</li>
                    </ol>
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
                    <div class="card card-primary">
                        <div class="card-header card_header_color">
                            <h3 class="card-title"><strong>{{strtoupper($department->department_name)}}</strong> ({{strtolower($department->department_type)}}) Department Menu Permission</h3>
                        </div>
                        <form method="POST">
                        <input type="hidden" name="department_id" id="department_id" value="{{@$department_id}}">
                        <input type="hidden" id="department_name" value="{{$department->department_name}}">
                        <!-- /.card-header -->
                        <?php //printr($all_menu,'p'); ?>
                        @if(!empty($all_menu))
                            @foreach($all_menu as $menu)
                                <div class="col-md-12">
                                    <!-- 1nd level -->
                                    <div class="main_menu_heading">
                                        {{ucwords(@$menu['menu_name'])}} <i class='fas fa-level-down-alt'></i>
                                        @if(!empty(@$menu['menu_link']))
                                            <?php $permission_check = ($menu['permission_check']=='yes')?'checked':''; ?>
                                            <input type="checkbox" class="menu_ids" name="menu_ids[]" value="{{@$menu['id']}}" data-name="{{@$menu['menu_name']}}"{{$permission_check}}>
                                        @endif
                                    </div>
                                    <!-- 1nd level end-->
                                    @foreach(@$menu['sub_menu_one'] as $one_menu)
                                        <div class="ml-4">
                                            <!-- 2nd level -->
                                            <div class="sub_menu_heading">
                                                <i class='fas fa-arrow-right fa_arrow'></i> {{ucwords(@$one_menu['menu_name'])}}
                                                @if(!empty(@$one_menu['menu_link']))
                                                    <?php $permission_check = ($one_menu['permission_check']=='yes')?'checked':''; ?>
                                                    <input type="checkbox" class="menu_ids" name="menu_ids[]" value="{{@$one_menu['id']}}" data-name="{{@$one_menu['menu_name']}}" {{$permission_check}}>
                                                @endif
                                            </div>
                                            <!-- 2nd level end-->
                                            
                                            @if(!empty($one_menu['sub_menus']) && empty(@$one_menu['menu_link']))
                                                @foreach(@$one_menu['sub_menus'] as $sbm1)
                                                    <div class="ml-4">
                                                        <!--3rd level -->
                                                        <div class="sub_menu_heading">
                                                            <i class='fas fa-arrow-right fa_arrow'></i> {{ucwords(@$sbm1['menu_name'])}}
                                                        @if(!empty(@$sbm1['menu_link']))
                                                            <?php $permission_check = ($sbm1['permission_check']=='yes')?'checked':''; ?>
                                                            <input type="checkbox" class="menu_ids" name="menu_ids[]" value="{{@$sbm1['id']}}" data-name="{{@$sbm1['menu_name']}}" {{$permission_check}}>
                                                        @endif
                                                        </div>
                                                        <!-- 3rd level end-->
                                                        
                                                        @if(!empty($sbm1['sub_menus']) && empty(@$sbm1['menu_link']))
                                                            @foreach(@$sbm1['sub_menus'] as $sbm2)
                                                        <div class="ml-4">
                                                            <!--4th level -->
                                                            <div class="sub_menu_heading">
                                                            <i class='fas fa-arrow-right fa_arrow'></i> {{ucwords(@$sbm2['menu_name'])}}
                                                            @if(!empty(@$sbm2['menu_link']))
                                                                <?php $permission_check = ($sbm2['permission_check']=='yes')?'checked':''; ?>
                                                                <input type="checkbox" class="menu_ids" name="menu_ids[]" value="{{@$sbm2['id']}}" data-name="{{@$sbm2['menu_name']}}" {{$permission_check}}>
                                                            @endif

                                                            </div>
                                                            <!--4th level end-->
                                                            
                                                            @if(!empty($sbm2['sub_menus']) && empty(@$sbm2['menu_link']))
                                                            @foreach(@$sbm2['sub_menus'] as $sbm3)
                                                                <div class="ml-4">
                                                                <!--5th level -->
                                                                <div class="sub_menu_heading">
                                                                    <i class='fas fa-arrow-right fa_arrow'></i> {{ucwords(@$sbm3['menu_name'])}}
                                                                @if(!empty(@$sbm3['menu_link']))
                                                                    <?php $permission_check = ($sbm3['permission_check']=='yes')?'checked':''; ?>
                                                                    <input type="checkbox" class="menu_ids" name="menu_ids[]" value="{{@$sbm3['id']}}" data-name="{{@$sbm3['menu_name']}}" {{$permission_check}}>
                                                                @endif
                                                                </div>
                                                                <!--5th level end -->
                                                            </div>
                                                            @endforeach
                                                            @endif
                                                        </div>
                                                        @endforeach
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    @endforeach    
                                </div>
                            @endforeach    
                            <div class="col-md-6 mt-3">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="return department_permission();" id="applyPermissionLoader"><i class="fas fa-user-shield"></i> Apply Permission</button>
                                
                                    <button type="button" class="btn btn-danger btn-sm" onclick="return referesh_form();"><i class="fa fa-refresh" aria-hidden="true"></i> Refresh</button>
                                </div>
                            </div>
                        @endif
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

@include('script.comman_js')
@include('master.permission.permission_js')
@endsection