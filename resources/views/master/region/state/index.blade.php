@extends('layouts.head')

@section('title') States @endsection

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <!-- <h1>DataTables</h1> -->
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Home</a></li>
                        <li class="breadcrumb-item active">State</li>
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
                            <h3 class="card-title">All State List</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body responsive">
                            <table id="tableList" class="table responsive table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sno</th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Country Name</th>
                                        <th>ISO 2</th>
                                        <th>Country Code</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th>Sno</th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Country Name</th>
                                        <th>ISO 2</th>
                                        <th>Country Code</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
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
        <div class="modal fade" id="stateModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">State</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" id="stateFormId">
                            @csrf    
                            <div class="row">
                                <input type="hidden" name="p_id" id="p_id">
                                <div class="col-md-12">
                                    <div class="card card-primary">
                                        <div class="card-header card_header_color"><h3 class="card-title">Update State</h3>
                                        </div>
                                        <div class="show_message"></div>
                                        <div class="card-body row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Name<span class="text-danger">*</span></label>
                                                    <input type="text" name="name" id="name" class="form-control"  placeholder="Enter name">
                                                    <p class="text-danger" id="nameError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Country Name<span class="text-danger">*</span></label>
                                                    <select class="form-control select2" name="country_id" id="country_id">
                                                        <option value="">Select country</option>
                                                    </select>
                                                    <p class="text-danger" id="country_idError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Country Code<span class="text-danger">*</span></label>
                                                    <input type="text" name="country_code" id="country_code" class="form-control" placeholder="Enter country code">
                                                    <p class="text-danger" id="country_codeError"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>ISO 2<span class="text-danger">*</span></label>
                                                    <input type="text" name="iso2" id="iso2" class="form-control" placeholder="Enter iso2">
                                                    <p class="text-danger" id="iso2Error"></p>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <button id="update_state" type="submit" class="btn btn-primary">Submit</button>
                                                    <button type="button" class="btn btn-danger" onclick="return referesh_form();">Refresh</button>
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

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script>
    $(document).ready(function() {
        state_data_table_list();
        $('.select2').select2();
    });
</script>
@include('script.comman_js')
@include('script.country_state_city_js')

@endsection