@extends('layouts.head')

@section('title') Vessels @endsection

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <button type="button" class="btn btn-sm btn-default" onclick="return add_edit_vessel('','add');"><i class="fa fa-plus" aria-hidden="true"></i> Add</button>
                    <button type="button" class="btn btn-sm btn-default" onclick="return referesh_form();"><i class="fa fa-refresh" aria-hidden="true"></i> Refresh</button>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Home</a></li>
                        <li class="breadcrumb-item active">Vessel</li>
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
                            <h3 class="card-title">All Vessels</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body responsive">
                            <table id="tableList" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sno</th>
                                        <th class="set_action_width4">Action</th>
                                        <th>Vessel Name</th>
                                        <th>Technical Manager</th>
                                        <th>Registered Owner</th>
                                        <th>Vessel Email</th>
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
        <div class="modal fade" id="addEditModal" role="dialog">
            <div class="modal-dialog modal-lg modal-popup-scroll">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Vessel</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" id="addFormId" class="dropzone" enctype="multipart/form-data">
                            @csrf    
                            <div class="row">
                                <input type="hidden" name="p_id" id="p_id">
                                <div class="col-md-12">
                                    <div class="card card-primary">
                                        <div class="card-header card_header_color"><h3 class="card-title">Add/Edit Vessel</h3>
                                        </div>
                                        <div class="show_message"></div>
                                        <div class="card-body row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Vessel Name<span class="text-danger">*</span></label>
                                                    <input type="text" name="vessel_name" id="vessel_name" class="form-control" placeholder="Enter vessel name">
                                                    <p class="remove_text text-danger" id="vessel_nameError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Technical Manager<span class="text-danger">*</span></label>
                                                    <input type="text" name="technical_manager" id="technical_manager" class="form-control" placeholder="Enter technical manager">
                                                    <p class="remove_text text-danger" id="technical_managerError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Registered Owner<span class="text-danger">*</span></label>
                                                    <input type="text" name="registered_owner" id="registered_owner" class="form-control" placeholder="Enter registered owner">
                                                    <p class="remove_text text-danger" id="registered_ownerError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Hull No<span class="text-danger">*</span></label>
                                                    <input type="text" name="hull_no" id="hull_no" class="form-control" placeholder="Enter hull no">
                                                    <p class="remove_text text-danger" id="hull_noError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Master<span class="text-danger">*</span></label>
                                                    <input type="text" name="master" id="master" class="form-control" placeholder="Enter master">
                                                    <p class="remove_text text-danger" id="masterError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Vessel Email<span class="text-danger">*</span></label>
                                                    <input type="text" name="vessel_email" id="vessel_email" class="form-control" placeholder="Enter vessel email">
                                                    <p class="remove_text text-danger" id="vessel_emailError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>IMO No</label>
                                                    <input type="text" name="imo_no" id="imo_no" class="form-control" placeholder="Enter imo no">
                                                    <p class="remove_text text-danger" id="imo_noError"></p>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Category<span class="text-danger">*</span></label>
                                                    <select class="form-control select2" name="category_id" id="category_id" onchange="return get_all_parent_vessel_category('',this.value);"> 
                                                    <option value="">Select</option>
                                                    </select>
                                                    <p class="text-danger remove_text" id="category_idError"></p>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Type (Parent Category)<span class="text-danger">*</span></label>
                                                    <select class="form-control select2" name="parent_category_id" id="parent_category_id">  
                                                    <option value="">Select</option>
                                                    </select>
                                                    <p class="text-danger remove_text" id="parent_category_idError"></p>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Delivery Date</label>
                                                    <input type="text" name="delivery_date" id="delivery_date" class="form-control" placeholder="dd/mm/yyyy" readonly>
                                                    <p class="remove_text text-danger" id="delivery_dateError"></p>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Dead Weight</label>
                                                    <input type="text" name="dead_weight" id="dead_weight" class="form-control" placeholder="Enter dead weight">
                                                    <p class="remove_text text-danger" id="dead_weightError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Main Engine</label>
                                                    <input type="text" name="main_engine" id="main_engine" class="form-control" placeholder="Enter main engine">
                                                    <p class="remove_text text-danger" id="main_engineError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>BHP</label>
                                                    <input type="text" name="bhp" id="bhp" class="form-control" placeholder="Enter bhp">
                                                    <p class="remove_text text-danger" id="bhpError"></p>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Flag<span class="text-danger">*</span></label>
                                                    <input type="text" name="flag" id="flag" class="form-control" placeholder="Enter flag">
                                                    <p class="remove_text text-danger" id="flagError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>GRT</label>
                                                    <input type="text" name="grt" id="grt" class="form-control" placeholder="Enter grt">
                                                    <p class="remove_text text-danger" id="grtError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>NRT</label>
                                                    <input type="text" name="nrt" id="nrt" class="form-control" placeholder="Enter nrt">
                                                    <p class="remove_text text-danger" id="nrtError"></p>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>CY Number</label>
                                                    <input type="text" name="cy_number" id="cy_number" class="form-control" placeholder="Enter cy number">
                                                    <p class="remove_text text-danger" id="cy_numberError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>DE Number</label>
                                                    <input type="text" name="de_number" id="de_number" class="form-control" placeholder="Enter de number">
                                                    <p class="remove_text text-danger" id="de_numberError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>SG Number</label>
                                                    <input type="text" name="sg_number" id="sg_number" class="form-control" placeholder="Enter sg number">
                                                    <p class="remove_text text-danger" id="sg_numberError"></p>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Yard</label>
                                                    <input type="text" name="yard" id="yard" class="form-control" placeholder="Enter yard">
                                                    <p class="remove_text text-danger" id="yardError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>SID<span class="text-danger">*</span></label>
                                                    <input type="text" name="sid" id="sid" class="form-control" placeholder="Enter sid">
                                                    <p class="remove_text text-danger" id="sidError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Is Active<span class="text-danger">*</span></label>
                                                    <select class="form-control" name="is_active" id="is_active" required>
                                                        <option value="1">Active</option>
                                                        <option value="2">In-Active</option>
                                                    </select>
                                                    <p class="remove_text text-danger" id="is_activeError"></p>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <label>Vessel Image<span class="text-danger">*</span></label>
                                                <div id="myDropzone" class="dropzone"></div>
                                                <p id="set_vessel_image"></p>
                                                <p class="remove_text text-danger" id="vessel_imageError"></p>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea name="description" id="description" class="form-control" placeholder="Enter description"></textarea>
                                                    <p class="remove_text text-danger" id="descriptionError"></p>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <button id="addEditSubmitButton" type="submit" class="btn btn-primary"><i class="fa fa-send-o"></i> Submit</button>
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

<!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css"> -->
<script>
    $(document).ready(function() {
        vessel_data_table_list();
    });
</script>
@include('vessel.vessel.vessel_js')
@include('script.comman_js')
@endsection