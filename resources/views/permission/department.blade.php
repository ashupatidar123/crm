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

<style type="text/css">



/**
 * Framework starts from here ...
 * ------------------------------
 */

.tree, .tree ul {
    /*margin:0 0 0 1em;
    padding:0;
    list-style:none;
    color:#369;
    position:relative;*/
    margin: 0 0 0 1em;
    padding: 8px 0px 12px 1px;
    list-style: none;
    position: relative;
}

.tree ul {margin-left:.5em} /* (indentation/2) */

.tree:before,
.tree ul:before {
  content:"";
  display:block;
  width:0;
  position:absolute;
  top:0;
  bottom:0;
  left:4px;
  border-left:1px dashed;
}

ul.tree:before {
  border-left:none
}

.tree li {
  margin:0;
  padding:0 1.5em; /* indentation + .5em */
  line-height:2em; /* default list item's `line-height` */
  font-weight:bold;
  position:relative;
}

.tree container, .tree folder {
  display: block;
}

.tree container icon, .tree folder icon {
  background-repeat: no-repeat;
  background-position: 4px center;
  padding-left: 24px;
}

.tree folder.selected {
  background-image: linear-gradient(to bottom, #e4eef5, #c0d1db);
  border-radius: 5px;
}

.tree folder:hover {
    background-image: linear-gradient(to bottom, #e9f5e7, #c3d9bf);
    border-radius: 5px;
}

.tree li:before {
  content:"";
  display:block;
  width:20px; /* same with indentation */
  height:0;
  border-top:1px dashed;
  margin-top:-1px; /* border top width */
  position:absolute;
  top:1em; /* (line-height/2) */
  left:4px;
}

ul.tree>li:before {
  border-top:none;
}

.tree li:last-child:before {
  background:white; /* same with body background */
  height:auto;
  top:1em; /* (line-height/2) */
  bottom:0;
}
.fa-child, .fa-baby, .fa-link{
    margin-left: 8px;
}
.fa-child, .fa-baby{
    font-size:16px;
    color:#8f09099e;
}
.fa-user{
    font-size:16px;
    color:#ffc107ad;
    margin-left: 5px;
}
.main_heading{
    color:#df8806
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
                        @if(!empty($all_menu))
                        <form method="POST">
                        <input type="hidden" name="department_id" id="department_id" value="{{@$department_id}}">
                        <input type="hidden" id="department_name" value="{{$department->department_name}}">
                        <!-- /.card-header -->
                        
                        <ul class="tree">
                            @foreach($all_menu as $main_key => $menu)
                            <li>
                                <container>
                                    <i class='fa fa-user'></i>
                                    ({{$main_key+1}}) <strong class="main_heading">{{ucwords(@$menu['menu_name'])}}</strong>
                                </container>
                                <ul>
                                    <!-- 1st level -->
                                    @foreach(@$menu['sub_menu_one'] as $one_menu)
                                    <li>
                                        @if(!empty(@$one_menu['menu_link']))
                                            <folder>
                                                <i class='fa fa-link' style='font-size:16px;color:#0d990bab'></i>
                                                <?php $permission_check = ($one_menu['permission_check']=='yes')?'checked':''; ?>
                                                <input type="checkbox" class="menu_ids add_edit_delete_access_check_{{@$one_menu['id']}}" name="menu_ids[]" value="{{@$one_menu['id']}}" data-name="{{@$one_menu['menu_name']}}" data-add_access="{{($one_menu['add_access']=='yes')?'yes':'no'}}" data-edit_access="{{($one_menu['edit_access']=='yes')?'yes':'no'}}" data-delete_access="{{($one_menu['delete_access']=='yes')?'yes':'no'}}" {{$permission_check}}>
                                                {{ucwords(@$one_menu['menu_name'])}}
                                                || 
                                                <span class="action_div">
                                                <?php $add_access_permission_check = ($one_menu['add_access']=='yes')?'checked':''; 
                                                ?>

                                                add <input type="checkbox" value="{{@$one_menu['id']}}" onclick="return add_edit_delete_access(this,this.value,'add');" {{$add_access_permission_check}}> 

                                                <?php $edit_access_permission_check = ($one_menu['edit_access']=='yes')?'checked':''; 
                                                ?>
                                                edit <input type="checkbox" value="{{@$one_menu['id']}}" onclick="return add_edit_delete_access(this,this.value,'edit');" {{$edit_access_permission_check}}>

                                                <?php $delete_access_permission_check = ($one_menu['delete_access']=='yes')?'checked':''; 
                                                ?>
                                                delete <input type="checkbox" value="{{@$one_menu['id']}}" onclick="return add_edit_delete_access(this,this.value,'delete');" {{$delete_access_permission_check}}>
                                                </span>
                                            </folder>
                                        @else
                                            <container>
                                                <i class='fa fa-child'></i>
                                                {{ucwords(@$one_menu['menu_name'])}}
                                            </container>

                                            <ul>
                                                @if(!empty($one_menu['sub_menus']) && empty(@$one_menu['menu_link']))
                                                @foreach(@$one_menu['sub_menus'] as $sbm1)
                                                <!-- 2nd level -->
                                                <li>
                                                    @if(!empty(@$sbm1['menu_link']))
                                                    
                                                    <folder>
                                                        <?php $permission_check = ($sbm1['permission_check']=='yes')?'checked':''; ?>
                                                        <i class='fa fa-link' style='font-size:16px;color:#0d990bab'></i>
                                                        <input type="checkbox" class="menu_ids add_edit_delete_access_check_{{@$sbm1['id']}}" name="menu_ids[]" value="{{@$sbm1['id']}}" data-name="{{@$sbm1['menu_name']}}" data-add_access="{{($sbm1['add_access']=='yes')?'yes':'no'}}" data-edit_access="{{($sbm1['edit_access']=='yes')?'yes':'no'}}" data-delete_access="{{($sbm1['delete_access']=='yes')?'yes':'no'}}" {{$permission_check}}>

                                                        {{ucwords(@$sbm1['menu_name'])}}

                                                        || 
                                                        <span class="action_div">
                                                            <?php $add_access_permission_check = ($sbm1['add_access']=='yes')?'checked':''; 
                                                            ?>
                                                            add <input type="checkbox" value="{{@$sbm1['id']}}" onclick="return add_edit_delete_access(this,this.value,'add');" {{$add_access_permission_check}}> 

                                                            <?php $edit_access_permission_check = ($sbm1['edit_access']=='yes')?'checked':''; 
                                                            ?>
                                                            edit <input type="checkbox" value="{{@$sbm1['id']}}" onclick="return add_edit_delete_access(this,this.value,'edit');" {{$edit_access_permission_check}}>

                                                            <?php $delete_access_permission_check = ($sbm1['delete_access']=='yes')?'checked':''; 
                                                            ?>
                                                            delete <input type="checkbox" value="{{@$sbm1['id']}}" onclick="return add_edit_delete_access(this,this.value,'delete');" {{$delete_access_permission_check}}>
                                                            </span>
                                                    </folder>
                                                    @else
                                                    <container>
                                                        <i class='fa fa-baby'></i>
                                                        {{ucwords(@$sbm1['menu_name'])}}
                                                    </container>
                                                    <!-- 3rd level -->
                                                    <ul>
                                                    @if(!empty($sbm1['sub_menus']) && empty(@$sbm1['menu_link']))
                                                        @foreach(@$sbm1['sub_menus'] as $sbm2)
                                                    <li>
                                                        @if(!empty(@$sbm2['menu_link']))
                                                        <folder>
                                                            <?php $permission_check = ($sbm2['permission_check']=='yes')?'checked':''; ?>
                                                            <i class='fa fa-link' style='font-size:16px;color:#0d990bab'></i>
                                                            <input type="checkbox" class="menu_ids add_edit_delete_access_check_{{@$sbm2['id']}}" name="menu_ids[]" value="{{@$sbm2['id']}}" data-name="{{@$sbm2['menu_name']}}" data-add_access="{{($sbm2['add_access']=='yes')?'yes':'no'}}" data-edit_access="{{($sbm2['edit_access']=='yes')?'yes':'no'}}" data-delete_access="{{($sbm2['delete_access']=='yes')?'yes':'no'}}" {{$permission_check}}>

                                                            {{ucwords(@$sbm2['menu_name'])}}

                                                            ||
                                                            <span class="action_div">
                                                            <?php $add_access_permission_check = ($sbm2['add_access']=='yes')?'checked':''; 
                                                            ?>

                                                            add <input type="checkbox" value="{{@$sbm2['id']}}" onclick="return add_edit_delete_access(this,this.value,'add');" {{$add_access_permission_check}}> 

                                                            <?php $edit_access_permission_check = ($sbm2['edit_access']=='yes')?'checked':''; 
                                                            ?>
                                                            edit <input type="checkbox" value="{{@$sbm2['id']}}" onclick="return add_edit_delete_access(this,this.value,'edit');" {{$edit_access_permission_check}}>

                                                            <?php $delete_access_permission_check = ($sbm2['delete_access']=='yes')?'checked':''; 
                                                            ?> 
                                                            delete <input type="checkbox" value="{{@$sbm2['id']}}" onclick="return add_edit_delete_access(this,this.value,'delete');" {{$delete_access_permission_check}}>
                                                            </span>
                                                        </folder>
                                                        @else
                                                        <container>
                                                            <i class='fa fa-baby'></i>
                                                            {{ucwords(@$sbm2['menu_name'])}}
                                                        </container>
                                                        <!-- 4th level end-->
                                                        <ul>
                                                        @if(!empty($sbm2['sub_menus']) && empty(@$sbm2['menu_link']))
                                                            @foreach(@$sbm2['sub_menus'] as $sbm3)
                                                        <li>
                                                            @if(!empty(@$sbm3['menu_link']))
                                                            <folder>
                                                                <?php $permission_check = ($sbm3['permission_check']=='yes')?'checked':''; ?>
                                                                <i class='fa fa-link' style='font-size:16px;color:#0d990bab'></i>
                                                                <input type="checkbox" class="menu_ids add_edit_delete_access_check_{{@$sbm3['id']}}" name="menu_ids[]" value="{{@$sbm3['id']}}" data-name="{{@$sbm3['menu_name']}}" data-add_access="{{($sbm3['add_access']=='yes')?'yes':'no'}}" data-edit_access="{{($sbm3['add_access']=='yes')?'yes':'no'}}" data-delete_access="{{($sbm3['add_access']=='yes')?'yes':'no'}}" {{$permission_check}}>

                                                                {{ucwords(@$sbm3['menu_name'])}}
                                                                ||

                                                                <span class="action_div">
                                                                <?php $add_access_permission_check = ($sbm3['add_access']=='yes')?'checked':''; 
                                                                ?>

                                                                add <input type="checkbox" value="{{@$sbm3['id']}}" onclick="return add_edit_delete_access(this,this.value,'add');" {{$add_access_permission_check}}> 

                                                                <?php $edit_access_permission_check = ($sbm3['edit_access']=='yes')?'checked':''; 
                                                                ?>
                                                                edit <input type="checkbox" value="{{@$sbm3['id']}}" onclick="return add_edit_delete_access(this,this.value,'edit');" {{$edit_access_permission_check}}>

                                                                <?php $delete_access_permission_check = ($sbm3['delete_access']=='yes')?'checked':''; 
                                                                ?>
                                                                delete <input type="checkbox" value="{{@$sbm3['id']}}" onclick="return add_edit_delete_access(this,this.value,'delete');" {{$delete_access_permission_check}}>
                                                                </span>
                                                            </folder>
                                                            @else
                                                            <container>
                                                                <i class='fa fa-baby'></i>
                                                                {{ucwords(@$sbm3['menu_name'])}}
                                                            </container>
                                                            @endif
                                                        </li>
                                                        @endforeach
                                                        @endif
                                                        </ul>
                                                        <!-- 4th level end-->
                                                        @endif
                                                    </li>
                                                    @endforeach
                                                    @endif
                                                    </ul>
                                                    <!-- 3rd level end-->
                                                    @endif
                                                </li>
                                                <!-- 2nd level end-->
                                                @endforeach
                                                @endif
                                            </ul>
                                        @endif
                                    </li>
                                    @endforeach
                                    <!-- 1st level end-->
                                </ul>
                            </li>
                            @endforeach
                            <div class="col-md-6 mt-3">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="return department_permission();" id="applyPermissionLoader"><i class="fas fa-user-shield"></i> Apply Permission</button>
                                
                                    <button type="button" class="btn btn-danger btn-sm" onclick="return referesh_form();"><i class="fa fa-refresh" aria-hidden="true"></i> Refresh</button>
                                </div>
                            </div>
                        </ul>
                        </form>
                        @else
                        <div class="text-center alert alert-danger">Oops! No menu record found <a href="{{route('menu.index')}}" class="text-primary">Add Menu</a></div>
                        @endif
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
@include('permission.permission_js')
@endsection