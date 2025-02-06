<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- /.card -->
                <div class="card1 card-primary1">
                    <div class="search-form">
                        <form method="POST" id="vesselAdvanceSearch">
                            @csrf    
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-primary">
                                        <div class="card-body row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <div class="input-group date">
                                                        <input type="text" id="search_start_check_in_date" class="form-control" placeholder="Search start signing date" readonly>
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <div class="input-group date">
                                                        <input type="text" id="search_end_check_in_date" class="form-control" placeholder="Search end signing date" readonly>
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <div class="input-group date">
                                                        <input type="text" id="search_start_check_out_date" class="form-control" placeholder="Search start signout date" readonly>
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <div class="input-group date">
                                                        <input type="text" id="search_end_check_out_date" class="form-control" placeholder="Search end signout date" readonly>
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <select class="form-control select2" id="search_vessel_id">
                                                        <option value="" hidden="">Select vessel</option>
                                                        @if(!empty($vessel))
                                                            @foreach($vessel as $vsl)
                                                                <option  value="{{$vsl->id}}">{{$vsl->vessel_name}} ({{$vsl->vessel_email}})</option>
                                                            @endforeach    
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-primary" onclick="return vessel_check_in_out_advance_search_tab();"><i class="fa fa-search"></i> Search</button>
                                                    <button type="button" class="btn btn-danger" onclick="return vessel_search_reset_form_tab();"><i class="fa fa-refresh"></i> Reset</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body responsive hideSection">
                        <table id="tableList" class="table responsive table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sno</th>
                                    <th>User Name</th>
                                    <th>Vessel Name</th>
                                    <th>Signing Date</th>
                                    <th>SignOut Date</th>
                                    <th>Signing Remark</th>
                                    <th>SignOut Remark</th>
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


<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2();
        vessel_check_in_out_data_table_list_tab();
    });

    $('#search_start_check_in_date, #search_end_check_in_date, #search_start_check_out_date, #search_end_check_out_date').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
    });
</script>
@include('script.comman_js')
@include('user.user.tab.user_details_js')