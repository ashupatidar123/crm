@extends('layouts.head')

@section('title') Company @endsection

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <button type="button" class="btn btn-sm btn-default" onclick="return add_edit_menu('','add');"><i class="fa fa-plus" aria-hidden="true"></i> Add</button>
                    <button type="button" class="btn btn-sm btn-default" onclick="return referesh_form();"><i class="fa fa-refresh" aria-hidden="true"></i> Refresh</button>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Home</a></li>
                        <li class="breadcrumb-item active">Company</li>
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
                            <h3 class="card-title">All company</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body responsive">
                            <table id="tableList" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sno</th>
                                        <th class="set_action_width3">Action</th>
                                        <th>Menu Name</th>
                                        <th>Parent Menu</th>
                                        <th>Code</th>
                                        <th>Sequence</th>
                                        <th>Link</th>
                                        <th>Icon</th>
                                        <th>Description</th>
                                        <th>Created AT</th>
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
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Menu</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" id="addFormId">
                            @csrf    
                            <div class="row">
                                <input type="hidden" name="p_id" id="p_id">
                                <div class="col-md-12">
                                    <div class="card card-primary">
                                        <div class="card-header card_header_color"><h3 class="card-title">Add/Edit Menu</h3>
                                        </div>
                                        <div class="show_message"></div>
                                        <div class="card-body row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Menu Name<span class="text-danger">*</span></label>
                                                    <input type="text" name="menu_name" id="menu_name" class="form-control" placeholder="Enter menu name" onkeyup="return create_menu_slug(this.value);">
                                                    <p class="text-danger remove_error" id="menu_nameError"></p>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Menu Slug<span class="text-danger">*</span></label>
                                                    <input type="text" name="menu_slug" id="menu_slug" class="form-control" placeholder="Enter menu slug" readonly>
                                                    <p class="text-danger remove_error" id="menu_slugError"></p>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Parent Menu (Optional)</label>
                                                    <select class="form-control select2" name="parent_menu_id" id="parent_menu_id">  
                                                    </select>
                                                    <p class="text-danger remove_error" id="parent_menu_idError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Menu Code</label>
                                                    <input type="text" name="menu_code" id="menu_code" class="form-control" placeholder="Enter menu code">
                                                    <p class="text-danger remove_error" id="menu_codeError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Menu Sequence (1 Top)<span class="text-danger">*</span></label>
                                                    <input type="number" name="menu_sequence" id="menu_sequence" class="form-control" placeholder="Enter menu sequence">
                                                    <p class="text-danger remove_error" id="menu_sequenceError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Menu Link</label>
                                                    <input type="text" name="menu_link" id="menu_link" class="form-control" placeholder="Enter menu link">
                                                    <p class="text-danger remove_error" id="menu_linkError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Menu Icon</label>
                                                    <input type="text" name="menu_icon" id="menu_icon" class="form-control" placeholder="Enter menu fa-fa icon">
                                                    <p class="text-danger remove_error" id="menu_iconError"></p>
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
                                                    <button type="button" class="close1 btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
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
        menu_data_table_list();
    });
</script>
@include('script.comman_js')
@include('common.company.company_js')

@endsection