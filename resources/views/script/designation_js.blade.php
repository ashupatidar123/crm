<script type="text/javascript">
    function designation_data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route("designation.list")}}',
                type: 'GET',
                data:{start_limit,end_limit},
            },
            columns: [
                { data: 'sno' },
                { data: 'id' },
                { data: 'designation_name' },
                { data: 'department_name' },
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
            "columnDefs": [{"targets": [0,3,8],"orderable": false}]
        });
    }  

    function add_edit_designation(p_id='',type=''){
        $('#addSubmitButton').html('Submit');
        $('#addSubmitButton').attr('disabled',false);
        if(type == 'add'){
            $('#p_id, #designation_name, #rank, #description').val('');
            $("#addModal").modal();
            return false;
        }
        $.ajax({
            type: "POST",
            url: "{{ route('designation.edit') }}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                
                if(resp.data != ''){
                    var rep = resp.data;
                    $('#p_id').val(rep.id);
                    $('#designation_name').val(rep.designation_name);
                    $('#department_id').val(rep.department_id).trigger('change');
                    $('#department_type').val(rep.department_type);
                    $('#rank').val(rep.rank);
                    $('#description').val(rep.description);
                    $('#is_active').val(rep.is_active);
                    $("#addModal").modal();
                    get_department_record(rep.department_id,'department_id');
                }else{
                    swal_error('Something went wrong');
                    return false;
                }
            }
        });  
    }

    $("#addSubmitButton").on("click",function (event) {
        event.preventDefault();
        $('#designation_nameError, #department_idError, #rankError, #descriptionError').html('');
        var check = 0;

        if($('#designation_name').val() == ''){
            var check = 1;
            $('#designation_nameError').html('This field is required');
        }
        if($('#department_id').val() == ''){
            var check = 1;
            $('#department_idError').html('This field is required');
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
            url: "{{ route('designation.store') }}",
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
                    designation_data_table_list();
                    $('#addModal').modal('hide');
                    swal_success(resp.message);
                }else{
                    swal_error(resp.message);
                }
            }
        });
    });

    function get_department_record(p_id='',html_id=''){
        var selectedOption = $('#department_type').find('option:selected');
        var department_type = selectedOption.val();
        
        var type = 'ajax_list';
        $.ajax({
            type: "POST",
            url: "{{route('get_department_record')}}",
            data: {p_id,type,department_type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                $('#'+html_id).html(resp);
            }
        });
    }
</script>