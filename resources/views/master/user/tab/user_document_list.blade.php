<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- /.card -->
                <div class="card1 card-primary1">
                    <!-- <div class="card-header card_header_color">
                        <h3 class="card-title">All Users</h3>
                    </div> -->

                    <!-- /.card-header -->
                    <div class="card-body responsive">
                        <table id="tableList" class="table responsive table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sno</th>
                                    <th>Action</th>
                                    <th>Document Name</th>
                                    <th>Category Name</th>
                                    <th>Document Type</th>
                                    <th>Issue Date</th>
                                    <th>Expiry Date</th>
                                    <th>User Document</th>
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
    <div class="modal fade" id="userViewModal" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">View user</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="p_id" id="p_id">
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>First name</th>
                                                <td class="view_tbl_first_name"></td>
                                            </tr>
                                            <tr>
                                                <th>Middle name</th>
                                                <td class="view_tbl_middle_name"></td>
                                            </tr>
                                            <tr>
                                                <th>Last name</th>
                                                <td class="view_tbl_last_name"></td>
                                            </tr>
                                            <tr>
                                                <th>Phone</th>
                                                <td class="view_tbl_phone"></td>
                                            </tr>
                                            <tr>
                                                <th>Date Of Birth</th>
                                                <td class="view_tbl_date_birth"></td>
                                            </tr>
                                            <tr>
                                                <th>Updated date</th>
                                                <td class="view_tbl_update_at"></td>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<script type="text/javascript">
    $(document).ready(function() {
        user_document_data_table_list();
    });
</script>
@include('script.user_js')
@include('script.comman_js')