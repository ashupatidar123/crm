<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- /.card -->
                <div class="card1 card-primary1">
                    <div class="search-form">
                        <form method="POST" id="userDocumentSearch">
                            @csrf    
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-primary">
                                        <div class="card-body row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <input type="text" name="search_document_name" id="search_document_name" class="form-control" placeholder="Search document name">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <select class="form-control select2" name="search_user_name" id="search_user_name">
                                                        <option value="">Select user</option>
                                                        <option value="all">All</option>
                                                        @if(!empty($data_user))
                                                            @foreach($data_user as $usr)
                                                                <?php
                                                                    $user_selected = ($usr->id==$data->id)?'selected':'';
                                                                ?>

                                                                <option value="{{$usr->id}}" {{$user_selected}}>{{$usr->first_name}}</option>
                                                            @endforeach    
                                                        @endif
                                                    </select>    
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <select class="form-control select2" name="search_document_category" id="search_document_category">
                                                        <option value="">Select category</option>
                                                        @if(!empty($data_document))
                                                            @foreach($data_document as $doc)
                                                                <option value="{{$doc->id}}" data-name="{{$doc->category_name}}">{{$doc->category_name}} {{($doc->parent_category_id==0)?'(Parent)':'(Child)'}}</option>
                                                            @endforeach    
                                                        @endif
                                                    </select>    
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <div class="input-group date">
                                                        <input type="text" name="search_issue_date" id="search_issue_date" class="form-control" placeholder="Issue date" readonly>
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <div class="input-group date">
                                                        <input type="text" name="search_expiry_date" id="search_expiry_date" class="form-control" placeholder="Expiry date" readonly>
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <div class="input-group date">
                                                        <input type="text" name="search_start_date" id="search_start_date" class="form-control" placeholder="Start date" readonly>
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <div class="input-group date">
                                                        <input type="text" name="search_end_date" id="search_end_date" class="form-control" placeholder="End date" readonly>
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-primary" onclick="return user_document_search();"><i class="fa fa-search"></i> Search</button>
                                                    <button type="button" class="btn btn-danger" onclick="return user_document_search_reset_form();"><i class="fa fa-refresh"></i> Reset</button>
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
                        <button type="button" class="btn btn-sm btn-default mb-2" onclick="return add_edit_user_document('','add');"><i class="fa fa-plus"></i> Add New Document</button>
                        <table id="tableList" class="table responsive table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sno</th>
                                    <th class="set_action_width4">Action</th>
                                    <th>User Name</th>
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
        <div class="modal-dialog modal-lg">
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
                                                <input type="text" name="document_name" id="document_name" class="form-control" placeholder="Enter document name" required>
                                                <p class="text-danger" id="document_nameError"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Issue Date (Optional)</label>
                                                <input type="text" name="issue_date" id="issue_date" class="form-control" placeholder="DD/MM/YY" readonly>
                                                <p class="text-danger" id="issue_dateError"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Expiry Date (Optional)</label>
                                                <input type="text" name="expiry_date" id="expiry_date" class="form-control" placeholder="DD/MM/YY" readonly>
                                                <p class="text-danger" id="expiry_dateError"></p>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
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
                                            <label>User Document<span class="text-danger">*</span></label>
                                            <div id="myDropzone" class="dropzone"></div>
                                            <p id="set_user_document"></p>
                                            <p class="remove_text text-danger" id="user_documentError"></p>
                                        </div>

                                        <div class="col-md-12">    
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea name="document_description" id="document_description" class="form-control" placeholder="Enter document description..."></textarea>
                                                <p class="text-danger" id="document_descriptionError"></p>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <button id="addEditUserDocumentSubmit" type="submit" class="btn btn-primary">Submit</button>
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

<!-- Show document Modal -->
<section class="content">
    <div class="modal fade" id="viewUserDocumentModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">User Document</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <div class="card-body row">
                                    <div class="col-md-12">    
                                        <div class="form-group">
                                            <p class="text-success" id="document_file_set"></p>
                                            <p class="text-success" id="document_file_name"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Access right document Modal -->
<section class="content">
    <div class="modal fade" id="accessRightDocumentModal" role="dialog">
        <div class="modal-dialog modal-lg responsive">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">User Document Access right</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card-body row responsive">
                                <table id="access_tableList" class="table responsive table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Access</th>
                                            <th class="set_action_width3">User Name</th>
                                            <th>Email</th>
                                            <th>Created AT</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
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
        $('.select2').select2();
        user_document_data_table_list();
    
        /* file upload section */
        Dropzone.autoDiscover = false;
        var myDropzone = new Dropzone("#myDropzone", {
            url: "{{route('dropzone_file_upload')}}",
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            autoProcessQueue: false,
            addRemoveLinks: true,
            paramName: "user_document",
            dictDefaultMessage: "Drag files here or click to upload",
            maxFiles: 1,
            maxFilesize: 2,
            acceptedFiles: ".jpg,.jpeg,.png,.gif,.pdf,.xls,.csv",
            init: function() {
                var myDropzone = this;
                this.on("success", function(vessel_image, response) {
                    var save_file_name = response;
                    save_user_document_data(save_file_name);
                });
            }
        });
    
        $("#addEditUserDocumentSubmit").on("click",function (event) {
            event.preventDefault();
            $('.remove_text').html('');
            var check = 0;

            var check = 0;
            if($('#document_name').val() == ''){
                var check = 1;
                $('#document_nameError').html('This field is required');
            }
            if($('#document_id').val() == ''){
                var check = 1;
                $('#document_idError').html('This field is required');
            }
            
            if(check == 1){
                swal_error('Some fields are required');
                return false;
            }

            if(myDropzone.files != ''){
                if(myDropzone.files.length > 0) {
                    myDropzone.processQueue(); 
                }
            }
            else if($('#p_id').val() < 1){
                $('#user_documentError').html('This field is required');
                swal_error("User document is required");
                return false;
            }
            else{
                save_user_document_data('');
                return false;
            }
            return false;
        });
    });
</script>
@include('script.user_js')
@include('script.comman_js')
@include('user.user.tab.user_details_js')