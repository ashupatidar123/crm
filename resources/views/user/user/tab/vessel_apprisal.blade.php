<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- /.card -->
                <div class="mb-2">
                    <button type="button" class="btn btn-sm btn-default" onclick="return add_edit_apprisal('','add');"><i class="fa fa-plus" aria-hidden="true"></i> Add</button>
                </div>

                <div class="card1 card-primary1">    
                    <div class="search-form">
                        <form method="POST" id="vesselApprisalAdvanceSearch">
                            @csrf    
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-primary">
                                        <div class="card-body row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <div class="input-group date">
                                                        <input type="text" id="search_start_apprisal_date" class="form-control dtpk" placeholder="Search start apprisal date" readonly>
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <div class="input-group date">
                                                        <input type="text" id="search_end_apprisal_date" class="form-control dtpk" placeholder="Search end apprisal date" readonly>
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <select class="form-control" id="search_rating">
                                                        <option value="" hidden="">Select rating</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-primary" onclick="return vessel_apprisal_data_table_list_tab();"><i class="fa fa-search"></i> Search</button>
                                                    <button type="button" class="btn btn-danger" onclick="return vessel_apprisal_search_reset_form_tab();"><i class="fa fa-refresh"></i> Reset</button>
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
                                    <th class="set_action_width3">Action</th>
                                    <th>Vessel User</th>
                                    <th>Assign User</th>
                                    <th>Apprisal Date</th>
                                    <th>Rating</th>
                                    <th>Specific Strength</th>
                                    <th>Area of Improvemnt</th>
                                    <th>Additional Notes</th>
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
@include('user.model.vessel_apprisal_model')

<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2();
        vessel_apprisal_data_table_list_tab();
    });

    $('.dtpk').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
    });
</script>
@include('script.comman_js')
@include('user.user.tab.user_details_js')