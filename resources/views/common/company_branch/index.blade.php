@extends('layouts.head')

@section('title') Company @endsection

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <button type="button" class="btn btn-sm btn-default" onclick="return add_edit_company_branch('','add');"><i class="fa fa-plus" aria-hidden="true"></i> Add</button>
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
                                        <th>Company Name</th>
                                        <th>Branch Code</th>
                                        <th>Branch Name</th>
                                        <th>Country</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>ZIP Code</th>
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
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add/Edit Company Branch</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" id="addFormId">
                            <input type="hidden" name="p_id" id="p_id">
                            @csrf    
                            <div class="card card-primary">
                                <div class="card-header card_header_color">
                                    <h3 class="card-title">Branch Details</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Company Name<span class="text-danger">*</span></label>
                                                <select name="company_id" id="company_id" class="form-control select2">
                                                    <option>Select company</option>
                                                    @if(count($company_data) > 0)
                                                        @foreach($company_data as $cmy)
                                                            <option value="{{$cmy->id}}">{{$cmy->company_name}}</option>
                                                        @endforeach    
                                                    @endif    
                                                </select>
                                                <p class="text-danger remove_error" id="company_idError"></p>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Branch Code<span class="text-danger">*</span></label>
                                                <input type="text" name="branch_code" id="branch_code" class="form-control" placeholder="Enter branch code">
                                                <p class="text-danger remove_error" id="branch_codeError"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Branch Name<span class="text-danger">*</span></label>
                                                <input type="text" name="branch_name" id="branch_name" class="form-control" placeholder="Enter branch name">
                                                <p class="text-danger remove_error" id="branch_nameError"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card card-primary">
                                <div class="card-header card_header_color">
                                    <h3 class="card-title">Address Details</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Country<span class="text-danger">*</span></label>
                                                <input type="text" name="country" id="country" class="form-control" placeholder="Enter country name">
                                                <p class="text-danger remove_error" id="countryError"></p>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Address</label>
                                                <input type="text" name="address" id="address" class="form-control" placeholder="Enter full address">
                                                <p class="text-danger remove_error" id="addressError"></p>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>ZIP Code (Postal Code)<span class="text-danger">*</span></label>
                                                <input type="text" name="zip_code" id="zip_code" class="form-control" placeholder="Enter postal code">
                                                <p class="text-danger remove_error" id="zip_codeError"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card card-primary">
                                <div class="card-header card_header_color">
                                    <h3 class="card-title">Contact Details</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Phone<span class="text-danger">*</span></label>
                                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter phone number">
                                                <p class="text-danger remove_error" id="phoneError"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Email<span class="text-danger">*</span></label>
                                                <input type="text" name="email" id="email" class="form-control" placeholder="Enter email">
                                                <p class="text-danger remove_error" id="emailError"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Website Url</label>
                                                <input type="text" name="website_url" id="website_url" class="form-control" placeholder="Enter website url">
                                                <p class="text-danger remove_error" id="website_urlError"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card card-primary">
                                <div class="card-header card_header_color">
                                    <h3 class="card-title">Branch Logo</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Branch Logo</label>
                                                <input type="file" name="branch_logo" id="branch_logo" class="form-control">
                                                <p class="text-danger remove_error" id="branch_logoError"></p>
                                            </div>
                                            <span id="branch_logo_show"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-div">
                                <div class="form-group">
                                    <button id="addSubmitButton" type="submit" class="btn btn-primary"><i class="fa fa-send-o"></i> Submit</button>
                                    <button type="button" class="close1 btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
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
        company_branch_data_table_list();
    });
</script>
@include('script.comman_js')
@include('common.company_branch.branch_js')

@endsection