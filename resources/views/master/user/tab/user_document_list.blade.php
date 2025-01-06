<style>
    .file-upload-container {
        text-align: center;
        border: 2px dashed #ccc;
        padding: 20px;
        margin: 0 auto;
    }

    .drag-drop-area {
        border: 2px dashed #007bff;
        padding: 10px;
        margin-top: 13px;
        cursor: pointer;
    }

    .drag-drop-area p {
        margin: 0;
        font-size: 16px;
        color: #007bff;
    }

    #userDocumentFileList {
        margin-top: 10px;
        text-align: left;
    }

</style>

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
                    <div class="card-body responsive hideSection">
                        <button type="button" class="btn btn-sm btn-default mb-2" onclick="return add_edit_user_document('','add');"><i class="fa fa-plus"></i> Add New Document</button>

                        <table id="tableList" class="table responsive table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sno</th>
                                    <th class="set_action_width3">Action</th>
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
    <div class="modal fade" id="addEditUserDocumentModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">User Document</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="addEditUserDocumentFormId">
                        @csrf    
                        <div class="row">
                            <input type="hidden" name="p_id" id="p_id">
                            <input type="hidden" name="user_id" value="{{@$data->id}}">
                            <input type="hidden" name="document_type" value="{{@$data->department_type}}">
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header card_header_color"><h3 class="card-title">Add/Edit User Document</h3>
                                    </div>
                                    <div class="show_message"></div>
                                    <div class="card-body row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Document Category<span class="text-danger">*</span></label>
                                                <select class="form-control select2" name="document_id" id="document_id">
                                                    <option value="">Select document category</option>
                                                    @if(!empty($data_document))
                                                        @foreach($data_document as $doc)
                                                            <option value="{{$doc->id}}" data-name="{{$doc->category_name}}">{{$doc->category_name}} {{($doc->parent_category_id==0)?'(Parent)':'(Child)'}}</option>
                                                        @endforeach    
                                                    @endif        
                                                </select>
                                                <p class="text-danger" id="document_idError"></p>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">    
                                            <div class="form-group">
                                                <label>Document Name<span class="text-danger">*</span></label>
                                                <input type="text" name="document_name" id="document_name" class="form-control" required>
                                                <p class="text-danger" id="document_nameError"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Issue Date (Optional)</label>
                                                <div class="input-group date">
                                                    <input type="date" name="issue_date" id="issue_date" class="form-control issue_date">
                                                    <!-- <div class="input-group-text"><i class="fa fa-calendar"></i></div> -->
                                                </div>
                                                <p class="text-danger" id="issue_dateError"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Expiry Date (Optional)</label>
                                                <div class="input-group date">
                                                    <input type="date" name="expiry_date" id="expiry_date" class="form-control">
                                                    <!-- <div class="input-group-text"><i class="fa fa-calendar"></i></div> -->
                                                </div>
                                                <p class="text-danger" id="expiry_dateError"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">    
                                            <div class="form-group">
                                                <label>Description</label>
                                                <input type="text" name="document_description" id="document_description" class="form-control">
                                                <p class="text-danger" id="document_descriptionError"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Is Active<span class="text-danger">*</span></label>
                                                <select class="form-control" name="is_active" id="is_active">
                                                    <option value="1">Active</option>
                                                    <option value="2">In-Active</option>
                                                </select>
                                                <p class="text-danger" id="is_activeError"></p>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <div id="fileUploadContainer" class="file-upload-container">
                                                <input type="file" name="user_document1" id="user_document" hidden accept=".pdf, .csv, .xls, .xlsx"/>
                                                <div id="dragDropArea" class="drag-drop-area">
                                                    <p>Drag files here</p>
                                                </div>
                                                <div id="userDocumentFileList"></div>
                                                <p class="text-danger" id="user_documentError"></p>
                                                <p class="text-green" id="user_document_show"></p>
                                            </div>
                                        </div>

                                        @if(1==2)
                                        <div class="col-md-12">    
                                            <div class="form-group">
                                                <label>Choose Document (pdf,csv,xls,xlsx)<span class="text-danger">*</span></label>
                                                <input type="file" name="user_document" id="user_document" class="form-control" accept=".pdf, .csv, .xls, .xlsx">
                                                <p class="text-danger" id="user_documentError"></p>
                                                <p class="text-green" id="user_document_show"></p>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <button id="addEditUserDocumentSubmit" type="submit" class="btn btn-primary">Submit</button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2();
        user_document_data_table_list();
    });
</script>
@include('script.user_js')
@include('script.comman_js')