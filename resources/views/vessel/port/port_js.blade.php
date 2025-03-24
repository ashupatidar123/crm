<script type="text/javascript">
    function port_data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route("port_list")}}',
                type: 'POST',
                data:{start_limit,end_limit},
                headers: {
                    'X-CSRF-TOKEN': csrf_token
                },
            },
            columns: [
                { data: 'sno' },
                { data: 'action' },
                { data: 'port_name' },
                { data: 'phone' },
                { data: 'email' },
                { data: 'country' },
                { data: 'state' },
                { data: 'address' },
                { data: 'zip_code' },
                { data: 'created_at', type: 'date' }
            ],
            "order": [[9, 'DESC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "columnDefs": [{"targets": [0,3],"orderable": false}]
        });
    }  

    function add_edit_port(p_id='',type=''){
        $('#addSubmitButton').html('<i class="fa fa-send"></i> Submit');
        $('#addSubmitButton').attr('disabled',false);
        if(type == 'add'){
            $('#p_id, #port_name, #phone, #email, #country, #state, #address, #zip_code').val('');
            $("#addModal").modal();
            return false;
        }
        $('.addEditLoader_'+p_id).html('<i class="fa fa-spinner fa-spin"></i>');
        $('.addEditLoader_'+p_id).attr('disabled',true);

        $.ajax({
            type: "POST",
            url: "{{ route('port_edit') }}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                
                if(resp.data != ''){
                    var rep = resp.data;
                    $('#p_id').val(rep.id);
                    $('#port_name').val(rep.port_name);
                    $('#phone').val(rep.phone);
                    $('#email').val(rep.email);
                    $('#country').val(rep.country);
                    $('#state').val(rep.state);
                    $('#address').val(rep.address);
                    $('#zip_code').val(rep.zip_code);
                    $('#description').val(rep.description);
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

        if($('#port_name').val() == ''){
            var check = 1;
            $('#port_nameError').html('This field is required');
        }
        if($('#phone').val() == ''){
            var check = 1;
            $('#phoneError').html('This field is required');
        }
        if($('#address').val() == ''){
            var check = 1;
            $('#addressError').html('This field is required');
        }
        if($('#zip_code').val() == ''){
            var check = 1;
            $('#zip_codeError').html('This field is required');
        }
        if(check == 1){
            return false;
        }

        $('.show_message').html('');
        $('#addSubmitButton').html('<i class="fa fa-spinner fa-spin"></i> Loading');
        $('#addSubmitButton').attr('disabled',true);
        
        var formData = new FormData($("#addFormId")[0]);
        $.ajax({
            type: "POST",
            url: "{{ route('port-management.store') }}",
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
                    port_data_table_list();
                    $('#addModal').modal('hide');
                    swal_success(resp.message);
                }else{
                    swal_error(resp.message);
                }
            }
        });
    });

    function get_parent_vessel_category(p_id=''){
        var type = 'ajax_list';
        $.ajax({
            type: "POST",
            url: "{{route('get_parent_vessel_category')}}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                $('#parent_category_id').html(resp);
            }
        });
    }
</script>