<script type="text/javascript">
    function vessel_check_in_out_data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;

        var search_vessel_id = $('#search_vessel_id').val();
        var search_user_id = $('#search_user_id').val();
        var search_start_check_in_date = $('#search_start_check_in_date').val();
        var search_end_check_in_date = $('#search_end_check_in_date').val();
        var search_start_check_out_date = $('#search_start_check_out_date').val();
        var search_end_check_out_date = $('#search_end_check_out_date').val();
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route("check_in_out_list")}}',
                type: 'POST',
                data:{start_limit,end_limit,search_vessel_id,search_user_id,search_start_check_in_date,search_end_check_in_date,search_start_check_out_date,search_end_check_out_date},
                headers: {
                    'X-CSRF-TOKEN': csrf_token
                },
            },
            columns: [
                { data: 'sno' },
                { data: 'action' },
                { data: 'user_name' },
                { data: 'vessel_name' },
                { data: 'check_in_date' },
                { data: 'check_out_date' },
                { data: 'description' },
                { data: 'check_out_description' },
                { data: 'created_at', type: 'date' }
            ],
            "order": [[8, 'DESC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "searching": false,
            //"lengthChange": false,
            "columnDefs": [{"targets": [0,2,3],"orderable": false}]
        });
    }  

    function add_edit_vessel_check_in_out(p_id='',type=''){
        $('#addSubmitButton').html('<i class="fa fa-send"></i> Submit');
        $('#addSubmitButton').attr('disabled',false);
        $('.remove_error').html('');
        if(type == 'add'){
            $('#p_id, #check_in_date, #check_in_out, #description').val('');
            $("#addModal").modal();
            return false;
        }
        $('.addEditLoader_'+p_id).html('<i class="fa fa-spinner fa-spin"></i>');
        $('.addEditLoader_'+p_id).attr('disabled',true);

        $.ajax({
            type: "POST",
            url: "{{ route('check_in_out_edit') }}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                
                if(resp.data != ''){
                    var rep = resp.data;
                    $('#p_id').val(rep.id);
                    $('#vessel_id').val(rep.vessel_id).trigger('change');
                    $('#user_id').val(rep.user_id).trigger('change');
                    $('#check_in_date').val(rep.check_in_date);
                    $('#description').val(rep.description);
                    $('#is_active').val(rep.is_active);
                    $("#addModal").modal();
                }else{
                    swal_error('Something went wrong');
                    return false;
                }
                $('.addEditLoader_'+p_id).html('<i class="fa fa-edit"></i>');
                $('.addEditLoader_'+p_id).attr('disabled',false);
            }
        });  
    }

    $("#addSubmitButton").on("click",function (event) {
        event.preventDefault();
        $('.remove_error').html('');
        var check = 0;

        if($('#user_id').val() == ''){
            var check = 1;
            $('#user_idError').html('This field is required');
        }
        if($('#vessel_id').val() == ''){
            var check = 1;
            $('#vessel_idError').html('This field is required');
        }
        if($('#check_in_date').val() == ''){
            var check = 1;
            $('#check_in_dateError').html('This field is required');
        }
        if($('#is_active').val() == ''){
            var check = 1;
            $('#is_activeError').html('This field is required');
        }
        if(check == 1){
            return false;
        }

        $('#addSubmitButton').html('<i class="fa fa-spinner fa-spin"></i> Loading');
        $('#addSubmitButton').attr('disabled',true);
        
        var formData = new FormData($("#addFormId")[0]);
        $.ajax({
            type: "POST",
            url: "{{ route('check-in-out.store') }}",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                $('#addSubmitButton').html('Submit');
                $('#addSubmitButton').attr('disabled',false);
                if(resp.status == 'success'){
                    vessel_check_in_out_data_table_list();
                    $('#addModal').modal('hide');
                    swal_success(resp.message);
                }else{
                    swal_error(resp.message);
                }
            }
        });
    });

    function vessel_check_out_popup(p_id='',type='',db_check_in_date=''){
        $('#addCheckOutSubmitButton').html('<i class="fa fa-send"></i> Submit');
        $('#addCheckOutSubmitButton').attr('disabled',false);
        $('.remove_error, #check_out_description').html('');
        $('#check_out_date, #check_out_description').val('');
        $('#check_out_p_id').val(p_id);
        $('#db_check_in_date').val(db_check_in_date);
        $("#addCheckOutModal").modal();
        return false; 
    }

    $("#addCheckOutSubmitButton").on("click",function (event) {
        event.preventDefault();
        $('.remove_error').html('');
        var check = 0;

        if($('#check_out_date').val() == ''){
            var check = 1;
            $('#check_out_dateError').html('This field is required');
        }
        if($('#check_out_description').val() == ''){
            var check = 1;
            $('#check_out_descriptionError').html('This field is required');
        }
        if(check == 1){
            return false;
        }

        $('#addCheckOutSubmitButton').html('<i class="fa fa-spinner fa-spin"></i> Loading');
        $('#addCheckOutSubmitButton').attr('disabled',true);
        
        var formData = new FormData($("#addCheckOutFormId")[0]);
        $.ajax({
            type: "POST",
            url: "{{ route('check_out') }}",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                $('#addCheckOutSubmitButton').html('Submit');
                $('#addCheckOutSubmitButton').attr('disabled',false);
                if(resp.status == 'success'){
                    vessel_check_in_out_data_table_list();
                    $('#addCheckOutModal').modal('hide');
                    swal_success(resp.message);
                }else{
                    swal_error(resp.message);
                }
            }
        });
    });

    function check_in_out_advance_search(){
        vessel_check_in_out_data_table_list();
    }
    function search_reset_form(){
        $('#search_start_check_in_date, #search_end_check_in_date, #search_start_check_out_date, #search_end_check_out_date').val('');
        $('#advanceSearch').trigger("reset");
        vessel_check_in_out_data_table_list();
    }

    $('#check_in_date').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        minDate: 1
    });
    
    $('#check_out_date').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        minDate: 0
    });

    $('#search_start_check_in_date, #search_end_check_in_date, #search_start_check_out_date, #search_end_check_out_date').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
    });
</script>