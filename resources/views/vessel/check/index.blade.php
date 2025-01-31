@extends('layouts.head')

@section('title') Signing Vessel @endsection

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <button type="button" class="btn btn-sm btn-default" onclick="return add_edit_vessel_check_in_out('','add');"><i class="fa fa-plus" aria-hidden="true"></i> Add</button>
                    <button type="button" class="btn btn-sm btn-default" onclick="return referesh_form();"><i class="fa fa-refresh" aria-hidden="true"></i> Refresh</button>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Home</a></li>
                        <li class="breadcrumb-item active">Signing Vessel</li>
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
                    <!-- /.card -->
                    <div class="card card-primary">
                        <div class="card-header card_header_color">
                            <h3 class="card-title">All Signing Vessel</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body responsive">
                            <table id="tableList" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sno</th>
                                        <th class="set_action_width4">Action</th>
                                        <th>User Name</th>
                                        <th>Vessel Name</th>
                                        <th>Signing Date</th>
                                        <th>SignOut Date</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
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

    <!-- Modal -->
    <section class="content">
        <div class="modal fade" id="addModal" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Vessel Signing</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" id="addFormId">
                            @csrf    
                            <div class="row">
                                <input type="hidden" name="p_id" id="p_id">
                                <div class="col-md-12">
                                    <div class="card card-primary">
                                        <div class="card-header card_header_color"><h3 class="card-title">Add/Edit Vessel Signing</h3>
                                        </div>
                                        <div class="show_message"></div>
                                        <div class="card-body row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Vessel<span class="text-danger">*</span></label>
                                                    <select class="form-control select2" name="vessel_id" id="vessel_id">  
                                                        <option value="">Select vessel</option>
                                                        @if(!empty($vessel))
                                                            @foreach($vessel as $vsl)
                                                                <option  value="{{$vsl->id}}">{{$vsl->vessel_name}} ({{$vsl->vessel_email}})</option>
                                                            @endforeach    
                                                        @endif
                                                    </select>
                                                    <p class="text-danger remove_error" id="vessel_idError"></p>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Vessel user<span class="text-danger">*</span></label>
                                                    <select class="form-control select2" name="user_id" id="user_id">  
                                                        <option value="">Select vessel user</option>
                                                        @if(!empty($vessel_user))
                                                            @foreach($vessel_user as $u_vsl)
                                                                <option value="{{$u_vsl->id}}">{{$u_vsl->name_title}} {{$u_vsl->first_name}} {{$u_vsl->last_name}} ({{$u_vsl->email}})</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <p class="text-danger remove_error" id="user_idError"></p>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Signing Date<span class="text-danger">*</span></label>
                                                    <input type="text" name="check_in_date" id="check_in_date" class="form-control" placeholder="dd/mm/yyyy" readonly>
                                                    <p class="remove_error text-danger" id="check_in_dateError"></p>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Is Active<span class="text-danger">*</span></label>
                                                    <select class="form-control select2" name="is_active" id="is_active" required>
                                                        <option value="1">Active</option>
                                                        <option value="2">In-Active</option>
                                                    </select>
                                                    <p class="text-danger remove_error" id="is_activeError"></p>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea name="description" id="description" class="form-control" placeholder="Enter description" required></textarea>
                                                    <p class="text-danger remove_error" id="descriptionError"></p>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <button id="addSubmitButton" type="submit" class="btn btn-primary"><i class="fa fa-send-o"></i> Submit</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal SignOut -->
    <section class="content">
        <div class="modal fade" id="addCheckOutModal" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Vessel SignOut</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" id="addCheckOutFormId">
                            @csrf    
                            <div class="row">
                                <input type="hidden" name="check_out_p_id" id="check_out_p_id">
                                <div class="col-md-12">
                                    <div class="card card-primary">
                                        <div class="card-header card_header_color"><h3 class="card-title">Vessel SignOut</h3>
                                        </div>
                                        <div class="show_message"></div>
                                        <div class="card-body row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Signing Date</label>
                                                    <input type="text" id="db_check_in_date" class="form-control" readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>SignOut Date<span class="text-danger">*</span></label>
                                                    <input type="text" name="check_out_date" id="check_out_date" class="form-control" placeholder="dd/mm/yyyy" readonly>
                                                    <p class="remove_error text-danger" id="check_out_dateError"></p>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>SignOut Description<span class="text-danger">*</span></label>
                                                    <textarea name="check_out_description" id="check_out_description" class="form-control" placeholder="Enter check out description" required></textarea>
                                                    <p class="text-danger remove_error" id="check_out_descriptionError"></p>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <button id="addCheckOutSubmitButton" type="submit" class="btn btn-primary"><i class="fa fa-send-o"></i> Submit</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        vessel_check_in_out_data_table_list();
    });
</script>
@include('script.comman_js')
@include('vessel.check.check_in_out_js')

@endsection