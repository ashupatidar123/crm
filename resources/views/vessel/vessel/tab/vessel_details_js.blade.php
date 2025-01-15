<script type="text/javascript">
    /*vessel details tab section*/
    function vessel_change_tab(page_type=''){
        var id = $('#vessel_id').val();
        
        $('.hideSection').hide();
        $('.setTabLoaderDiv').html('<i class="fa fa-spinner fa-spin"></i> Loading...');
        $.ajax({
            method: 'POST',
            url: "{{url('vessel/vessel_tab_detail')}}",
            data: {id,page_type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function(response) {
                $('.setTabLoaderDiv').html('');
                if(page_type == 'profile'){
                    $('#setProfileDiv').html(response);
                }
                else if(page_type == 'document'){
                    $('#setDocumentDiv').html(response);
                }
                $('.hideSection').show();
            }
        });
    }

    function vessel_document_data_table_list(){    
        var vessel_id = $('#vessel_id').val();
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit   = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;

        var search_document_name = $('#search_document_name').val();
        var search_vessel_name = $('#search_vessel_name').val();
        var search_document_category = $('#search_document_category').val();

        var search_issue_date = $('#search_issue_date').val();
        var search_expiry_date = $('#search_expiry_date').val();
        var search_start_date = $('#search_start_date').val();
        var search_end_date = $('#search_end_date').val();
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{url("vessel/vessel_document_list_tab")}}',
                type: 'GET',
                data:{vessel_id,start_limit,end_limit,
                search_document_name,search_vessel_name,search_document_category,
                search_issue_date,search_expiry_date,search_start_date,search_end_date},
            },
            columns: [
                { data: 'sno' },
                { data: 'action' },
                { data: 'vessel_name' },
                { data: 'document_name' },
                { data: 'category_name' },
                { data: 'issue_date' },
                { data: 'expiry_date' },
                { data: 'vessel_document' },
                { data: 'created_at', type: 'date' }
                
            ],
            "order": [[8, 'DESC']],
            "lengthMenu": [5,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "dom": 'Bfrtip',
            "buttons": ['copy', 'csv', 'excel', 'pdf', 'print',"colvis"],
            "columnDefs": [{"targets": [0,1,2,4,7],"orderable": false}]
        });
    }

    function add_edit_vessel_document(p_id='',type=''){
        $('#addEditVesselDocumentSubmit').html('<i class="fa fa-send"></i> Submit');
        $('#addEditVesselDocumentSubmit').attr('disabled',false);
        
        $('.remove_text').html('');
        if(type == 'add'){
            $('#p_id, #document_name, #issue_date, #expiry_date').val('');
            $('#set_vessel_document, document_description').html('');
            $('#document_id').val('').trigger('change');
            $("#addEditVesselDocumentModal").modal();
            return false;
        }
        $('.addEditLoader_'+p_id).html('<i class="fa fa-spinner fa-spin"></i>');
        $('.addEditLoader_'+p_id).attr('disabled',true);
        $.ajax({
            type: "POST",
            url: "{{ url('vessel/vessel_document_edit') }}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                
                if(resp.data != ''){
                    var rep = resp.data;
                    $('#p_id').val(rep.id);
                    $("#addEditVesselDocumentModal").modal();
                    $('#document_name').val(rep.document_name);
                    $('#document_id').val(rep.document_id).trigger('change');
                    $('#issue_date').val(rep.issue_date);
                    $('#expiry_date').val(rep.expiry_date);
                    $('#document_description').val(rep.description);
                    $('#set_vessel_document').html(rep.vessel_document);
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

    function save_vessel_document_data(save_file_name=''){
        $('#addEditVesselDocumentSubmit').html('<i class="fa fa-spinner fa-spin"></i> Loading...');
        $('#addEditVesselDocumentSubmit').attr('disabled',true);

        var formData = new FormData($("#addEditVesselDocumentFormId")[0]);
        formData.append('vessel_document',save_file_name);

        $.ajax({
            type: "POST",
            url: "{{url('vessel/add_vessel_document')}}",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function(resp) {
                $('#addEditVesselDocumentSubmit').html('<i class="fa fa-send"></i> Submit');
                $('#addEditVesselDocumentSubmit').attr('disabled',false);
                
                if(resp.status == 'success'){
                    swal_success(resp.s_msg);
                    $("#addEditVesselDocumentModal").modal('hide');
                    vessel_change_tab('document');
                    //vessel_document_data_table_list();
                }else{
                    swal_error(resp.s_msg);
                }
            }
        });
    }

    function vessel_document_search(){
        vessel_document_data_table_list();
    }
    function vessel_document_search_reset_form(){
        $('#search_issue_date, #search_expiry_date, #search_start_date, #search_end_date').val('');
        $('#vesselDocumentSearch').trigger("reset");
        vessel_document_data_table_list();
    }

    function view_document(file_name='',file_type='',type=''){
        if(type == 'vessel_document' && file_type == 'image'){
            var url = "{{asset('storage/app/public/uploads/document/vessels')}}";
            var img = '<img src="'+url+'/'+file_name+'" class="img-rounded" width="220px" height="230px">';

            var button = '<a href="'+url+'/'+file_name+'" target="_blank" class="bt btn-default">'+file_name+'</a>';

            $('#document_file_set').html(img);
            $('#document_file_name').html('File Name :-'+button);
            $("#viewVesselDocumentModal").modal();
        }
        else if(type == 'vessel_document' && file_type == 'pdf'){
            var url = "{{asset('storage/app/public/uploads/document/vessels')}}";
            var img = '<embed id="pdfEmbed" src="'+url+'/'+file_name+'" width="100%" height="500px" type="application/pdf">'
            var button = '<a href="'+url+'/'+file_name+'" target="_blank" class="bt btn-default">'+file_name+'</a>';

            $('#document_file_set').html(img);
            $('#document_file_name').html('File Name :-'+button);
            $("#viewVesselDocumentModal").modal();
        }
        else{
            var url = "{{asset('storage/app/public/uploads/document/vessels')}}";
            var button = '<a href="'+url+'/'+file_name+'" target="_blank" class="bt btn-default">'+file_name+'</a>';
            $('#document_file_set').html('<p>Below Click to download DOC file</p>');
            $('#document_file_name').html('File Name :-'+button);
            $("#viewVesselDocumentModal").modal();
        }        
        return false;
    }

    $('#search_start_date, #search_end_date').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        maxDate: 0,
    });

    $('.input_date_picker').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
    });
</script>