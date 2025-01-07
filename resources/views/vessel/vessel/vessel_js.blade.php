<script type="text/javascript">
    function vessel_data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route("vessel.list")}}',
                type: 'GET',
                data:{start_limit,end_limit},
            },
            columns: [
                { data: 'sno' },
                { data: 'action' },
                { data: 'vessel_name' },
                { data: 'technical_manager' },
                { data: 'registered_owner' },
                { data: 'vessel_email' },
                { data: 'created_at', type: 'date' }
            ],
            "order": [[6, 'DESC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "columnDefs": [{"targets": [0],"orderable": false}]
        });
    }  

    function add_edit_department(p_id='',type=''){
        $('#addEditSubmitButton').html('Submit');
        $('#addEditSubmitButton').attr('disabled',false);
        if(type == 'add'){
            $('#p_id, #vessel_name, #technical_manager').val('');
            $("#addEditModal").modal();
            return false;
        }
        $.ajax({
            type: "POST",
            url: "{{ route('department.edit') }}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                
                if(resp.data != ''){
                    var rep = resp.data;
                    $('#p_id').val(rep.id);
                    $('#department_name').val(rep.department_name);
                    $('#department_type').val(rep.department_type);
                    $('#description').val(rep.description);
                    $('#is_active').val(rep.is_active);
                    $("#addEditModal").modal();
                }else{
                    swal_error('Something went wrong');
                    return false;
                }
            }
        });  
    }

    $("#addEditSubmitButton").on("click",function (event) {
        event.preventDefault();
        $('.remove_text').html('');
        var check = 0;

        if($('#vessel_name').val() == ''){
            var check = 1;
            $('#vessel_nameError').html('This field is required');
        }
        if($('#technical_manager').val() == ''){
            var check = 1;
            $('#technical_managerError').html('This field is required');
        }
        if($('#registered_owner').val() == ''){
            var check = 1;
            $('#registered_ownerError').html('This field is required');
        }
        if($('#hull_no').val() == ''){
            var check = 1;
            $('#hull_noError').html('This field is required');
        }
        if($('#master').val() == ''){
            var check = 1;
            $('#masterError').html('This field is required');
        }
        if($('#vessel_email').val() == ''){
            var check = 1;
            $('#vessel_emailError').html('This field is required');
        }
        if($('#category').val() == ''){
            var check = 1;
            $('#categoryError').html('This field is required');
        }
        if($('#type').val() == ''){
            var check = 1;
            $('#typeError').html('This field is required');
        }
        if($('#flag').val() == ''){
            var check = 1;
            $('#flagError').html('This field is required');
        }
        if($('#sid').val() == ''){
            var check = 1;
            $('#sidError').html('This field is required');
        }
        if($('#is_active').val() == ''){
            var check = 1;
            $('#is_activeError').html('This field is required');
        }
        if(check == 1){
            return false;
        }

        $('.show_message').html('');
        $('#addEditSubmitButton').html('Loading...');
        $('#addEditSubmitButton').attr('disabled',true);
        
        var formData = new FormData($("#addFormId")[0]);
        $.ajax({
            type: "POST",
            url: "{{ route('vessel.store') }}",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                $('#addEditSubmitButton').html('Submit');
                $('#addEditSubmitButton').attr('disabled',false);
                if(resp.status == 'success'){
                    vessel_data_table_list();
                    $('#addEditModal').modal('hide');
                    swal_success(resp.message);
                }else{
                    swal_error(resp.message);
                }
            }
        });
    });
</script>