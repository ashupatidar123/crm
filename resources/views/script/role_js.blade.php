<script type="text/javascript">
    function role_data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route("role.list")}}',
                type: 'GET',
                data:{start_limit,end_limit},
            },
            columns: [
                { data: 'sno' },
                { data: 'id' },
                { data: 'role_name' },
                { data: 'rank' },
                { data: 'description' },
                { data: 'created_at', type: 'date' },
                { data: 'status' },
                { data: 'action' }
            ],
            "order": [[3, 'ASC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "columnDefs": [{"targets": [0,7],"orderable": false}]
        });
    }  

    function add_edit_role(p_id='',type=''){
        $('#addSubmitButton').html('<i class="fa fa-send"></i> Submit');
        $('#addSubmitButton').attr('disabled',false);
        if(type == 'add'){
            $('#p_id, #role_name, #rank, #description').val('');
            $("#addModal").modal();
            return false;
        }
        $.ajax({
            type: "POST",
            url: "{{ route('role.edit') }}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                if(resp.data != ''){
                    var rep = resp.data;
                    $('#p_id').val(rep.id);
                    $('#role_name').val(rep.role_name);
                    $('#rank').val(rep.rank);
                    $('#description').val(rep.description);
                    $('#is_active').val(rep.is_active);
                    $("#addModal").modal();
                }else{
                    swal_error('Something went wrong');
                    return false;
                }
            }
        });  
    }

    $("#addSubmitButton").on("click",function (event) {
        event.preventDefault();
        $('#role_nameError, #rankError, #role_nameError').html('');
        var check = 0;

        $('#role_nameError').html('');
        var check = 0;
        if($('#role_name').val() == ''){
            var check = 1;
            $('#role_nameError').html('This field is required');
        }
        if($('#rank').val() == ''){
            var check = 1;
            $('#rankError').html('This field is required');
        }
        if($('#is_active').val() == ''){
            var check = 1;
            $('#is_activeError').html('This field is required');
        }
        if(check == 1){
            return false;
        }

        $('.show_message').html('');
        $('#addSubmitButton').html('Loading...');
        $('#addSubmitButton').attr('disabled',true);
        
        var formData = new FormData($("#addFormId")[0]);
        $.ajax({
            type: "POST",
            url: "{{ route('role.store') }}",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                $('#addSubmitButton').html('<i class="fa fa-send"></i> Submit');
                $('#addSubmitButton').attr('disabled',false);
                if(resp.status == 'success'){
                    role_data_table_list();
                    $('#addModal').modal('hide');
                    swal_success(resp.message);
                }else{
                    swal_error(resp.message);
                }
            }
        });
    });
</script>