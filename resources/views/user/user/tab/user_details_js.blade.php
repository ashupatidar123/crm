<script type="text/javascript">
    /* user details page document section tab*/
    function user_document_data_table_list(){    
        var user_id = $('#user_id').val();
        
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit   = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;

        var search_document_name = $('#search_document_name').val();
        var search_user_name = $('#search_user_name').val();
        var search_document_category = $('#search_document_category').val();

        var search_issue_date = $('#search_issue_date').val();
        var search_expiry_date = $('#search_expiry_date').val();
        var search_start_date = $('#search_start_date').val();
        var search_end_date = $('#search_end_date').val();
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{url("user/user_document_list_tab")}}',
                type: 'GET',
                data:{user_id,start_limit,end_limit,
                search_document_name,search_user_name,search_document_category,
                search_issue_date,search_expiry_date,search_start_date,search_end_date},
            },
            columns: [
                { data: 'sno' },
                { data: 'action' },
                { data: 'document_user_name' },
                { data: 'document_name' },
                { data: 'category_name' },
                { data: 'document_type' },
                { data: 'issue_date' },
                { data: 'expiry_date' },
                { data: 'user_document' },
                { data: 'created_at', type: 'date' }
                
            ],
            "order": [[9, 'DESC']],
            "lengthMenu": [5,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "dom": 'Bfrtip',
            "buttons": ['copy', 'csv', 'excel', 'pdf', 'print',"colvis"],
            "columnDefs": [{"targets": [0,2,4],"orderable": false}]
        });
    }

    function add_edit_user_document(p_id='',type=''){
        $('#userDocumentFileList').html('');
        
        if(type == 'add'){
            $('#p_id, #document_name, #issue_date, #expiry_date').val('');
            $('#document_id').val('').trigger('change');
            $('#set_user_document, #document_description').html('');
            $("#addEditUserDocumentModal").modal();
            return false;
        }
        $('.addEditLoader_'+p_id).html('<i class="fa fa-spinner fa-spin"></i>');
        $('.addEditLoader_'+p_id).attr('disabled',true);

        $.ajax({
            type: "POST",
            url: "{{ url('user/user_document_edit') }}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                
                if(resp.data != ''){
                    var rep = resp.data;
                    $('#p_id').val(rep.id);
                    $("#addEditUserDocumentModal").modal();
                    $('#document_name').val(rep.document_name);
                    $('#document_id').val(rep.document_id).trigger('change');
                    $('#issue_date').val(rep.issue_date);
                    $('#expiry_date').val(rep.expiry_date);
                    $('#document_type').val(rep.document_type);
                    $('#document_description').val(rep.description);
                    $('#set_user_document').html(rep.user_document);
                    $('#is_active').val(rep.is_active);
                }else{
                    swal_error('Something went wrong');
                    return false;
                }
                $('.addEditLoader_'+p_id).html('<i class="fa fa-edit"></i>');
                $('.addEditLoader_'+p_id).attr('disabled',false);
            }
        });  
    }

    function access_rights_user_document_data_table_list(p_id='',type=''){    
        $('#access_tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit   = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:5;
        var pageLength = (end_limit > 0)?end_limit:5;
        
        $('#access_tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{url("user/access_rights_user_document_list_tab")}}',
                type: 'GET',
                data:{start_limit,end_limit,p_id},
            },
            columns: [
                { data: 'action' },
                { data: 'user_name' },
                { data: 'email' },
                { data: 'created_at', type: 'date' }
                
            ],
            "info": false,
            "order": [[3, 'DESC']],
            "lengthMenu": [5,10,25,50],
            "pageLength": pageLength,
            "responsive": true,
            "columnDefs": [{"targets": [0,1,2,3],"orderable": false}],

            drawCallback: function () {
                $('.accessLoader_' + p_id).html('<i class="fas fa-key"></i>');
                $('.accessLoader_' + p_id).attr('disabled', false);
            }
        });
        
    }

    function access_rights_user_document(p_id='',type=''){
        $('.accessLoader_'+p_id).html('<i class="fa fa-spinner fa-spin"></i>');
        $('.accessLoader_'+p_id).attr('disabled',true);
        access_rights_user_document_data_table_list(p_id,type);
        $("#accessRightDocumentModal").modal();
        return false;  
    }

    function access_rights_user_document_add_ids(user_id='',document_id='',type=''){
        
        $('.access_rights_user').attr('disabled',true);
        //$('#access_rights_user_'+user_id).attr('disabled',true);
        $('#access_rights_user_loader_'+user_id).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            type: "POST",
            url: "{{ url('user/user_document_access_save') }}",
            data: {user_id,document_id},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                if(resp.status = 'success'){
                    swal_success(resp.s_msg);
                }else{
                    swal_error(resp.s_msg);
                    return false;
                }
                $('.access_rights_user').attr('disabled',false);
                //$('#access_rights_user_'+user_id).attr('disabled',false);
                $('#access_rights_user_loader_'+user_id).html('');
            }
        }); 
    }

    /*Drag-and-drop functionality*/
    function save_user_document_data(save_file_name=''){
        $('#addEditUserDocumentSubmit').html('<i class="fa fa-spinner fa-spin"></i> Loading...');
        $('#addEditUserDocumentSubmit').attr('disabled',true);

        var formData = new FormData($("#addEditUserDocumentFormId")[0]);
        formData.append('user_document',save_file_name);
        $.ajax({
            type: "POST",
            url: "{{url('user/add_user_document')}}",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function(resp) {
                $('#addEditUserDocumentSubmit').html('<i class="fa fa-send"></i> Submit');
                $('#addEditUserDocumentSubmit').attr('disabled',false);
                if(resp.status == 'success'){
                    swal_success(resp.s_msg);
                    $("#addEditUserDocumentModal").modal('hide');
                    user_change_tab('document');
                    //user_document_data_table_list();
                    //location.reload();
                }else{
                    swal_error(resp.s_msg);
                }
            }
        });
    }

    function view_document(file_name='',file_type='',type=''){
        if(type == 'user_document' && file_type == 'image'){
            var url = "{{asset('storage/app/public/uploads/document/users')}}";
            var img = '<img src="'+url+'/'+file_name+'" class="img-rounded" width="220px" height="230px">';

            var button = '<a href="'+url+'/'+file_name+'" target="_blank" class="bt btn-default">'+file_name+'</a>';

            $('#document_file_set').html(img);
            $('#document_file_name').html('File Name :-'+button);
            $("#viewUserDocumentModal").modal();
        }
        else if(type == 'user_document' && file_type == 'pdf'){
            var url = "{{asset('storage/app/public/uploads/document/users')}}";
            var img = '<embed id="pdfEmbed" src="'+url+'/'+file_name+'" width="100%" height="500px" type="application/pdf">'
            var button = '<a href="'+url+'/'+file_name+'" target="_blank" class="bt btn-default">'+file_name+'</a>';

            $('#document_file_set').html(img);
            $('#document_file_name').html('File Name :-'+button);
            $("#viewUserDocumentModal").modal();
        }
        else{
            var url = "{{asset('storage/app/public/uploads/document/users')}}";
            var button = '<a href="'+url+'/'+file_name+'" target="_blank" class="bt btn-default">'+file_name+'</a>';
            $('#document_file_set').html('<p>Below Click to download DOC file</p>');
            $('#document_file_name').html('File Name :-'+button);
            $("#viewUserDocumentModal").modal();
        }
        
                    
        return false;
    }

    function other_document_data_table_list(){    
        var user_id = $('#user_id').val();
        
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit   = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;

        var search_document_name = $('#search_document_name').val();
        var search_user_name = $('#search_user_name').val();
        var search_document_category = $('#search_document_category').val();

        var search_issue_date = $('#search_issue_date').val();
        var search_expiry_date = $('#search_expiry_date').val();
        var search_start_date = $('#search_start_date').val();
        var search_end_date = $('#search_end_date').val();
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{url("user/user_other_document_list_tab")}}',
                type: 'GET',
                data:{user_id,start_limit,end_limit,
                search_document_name,search_user_name,search_document_category,
                search_issue_date,search_expiry_date,search_start_date,search_end_date},
            },
            columns: [
                { data: 'sno' },
                { data: 'document_user_name' },
                { data: 'document_name' },
                { data: 'category_name' },
                { data: 'document_type' },
                { data: 'issue_date' },
                { data: 'expiry_date' },
                { data: 'user_document' },
                { data: 'created_at', type: 'date' }
                
            ],
            "order": [[8, 'DESC']],
            "lengthMenu": [5,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "dom": 'Bfrtip',
            "buttons": ['copy', 'csv', 'excel', 'pdf', 'print',"colvis"],
            "columnDefs": [{"targets": [0,1,3],"orderable": false}]
        });
    }

    function user_other_document_search(){
        other_document_data_table_list();
    }
    function user_other_document_search_reset_form(){
        $('#search_issue_date, #search_expiry_date, #search_start_date, #search_end_date').val('');
        $('#userOtherDocumentSearch').trigger("reset");
        other_document_data_table_list();
    }
</script>