<script type="text/javascript">
    function company_branch_data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;

        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route("company_branch_list")}}',
                type: 'POST',
                data:{start_limit,end_limit},
                headers: {
                    'X-CSRF-TOKEN': csrf_token
                },
            },
            columns: [
                { data: 'sno' },
                { data: 'action' },
                { data: 'company_name' },
                { data: 'branch_code' },
                { data: 'branch_name' },
                { data: 'country' },
                { data: 'phone' },
                { data: 'email' },
                { data: 'address' },
                { data: 'zip_code' },
                { data: 'created_at', type: 'date' }
            ],
            "order": [[10, 'DESC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "columnDefs": [{"targets": [0,1,2],"orderable": false}]
        });
    }  

    function add_edit_company_branch(p_id='',type=''){
        $('#addSubmitButton').html('<i class="fa fa-send"></i> Submit');
        $('#addSubmitButton').attr('disabled',false);
        if(type == 'add'){
            $("#addFormId")[0].reset();
            $('#p_id, #description').val('');
            $('#menu_slug').prop('readonly', true);
            $("#addModal").modal();
            return false;
        }
        $('.addEditLoader_'+p_id).html('<i class="fa fa-spinner fa-spin"></i>');
        $('.addEditLoader_'+p_id).attr('disabled',true);

        $.ajax({
            type: "POST",
            url: "{{ route('company_branch_list_edit') }}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                
                if(resp.data != ''){
                    var rep = resp.data;
                    $('#p_id').val(rep.id);
                    $('#company_id').val(rep.company_id).trigger('change');
                    $('#branch_code').val(rep.branch_code);
                    $('#branch_name').val(rep.branch_name);
                    $('#country').val(rep.country);
                    $('#address').val(rep.address);
                    $('#zip_code').val(rep.zip_code);
                    $('#phone').val(rep.phone);
                    $('#email').val(rep.email);
                    $('#website_url').val(rep.website_url);
                    $('#description').val(rep.description);
                    $('#branch_logo_show').html(rep.branch_logo);
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

        if($('#company_id').val() == ''){
            var check = 1;
            $('#company_idError').html('This field is required');
        }
        if($('#branch_code').val() == ''){
            var check = 1;
            $('#branch_codeError').html('This field is required');
        }
        if($('#branch_name').val() == ''){
            var check = 1;
            $('#branch_nameError').html('This field is required');
        }
        if($('#zip_code').val() == ''){
            var check = 1;
            $('#zip_codeError').html('This field is required');
        }
        if($('#phone').val() == ''){
            var check = 1;
            $('#phoneError').html('This field is required');
        }
        if($('#email').val() == ''){
            var check = 1;
            $('#emailError').html('This field is required');
        }
        if(check == 1){
            swal_error('Some fields are required');
            return false;
        }

        $('.show_message').html('');
        $('#addSubmitButton').html('<i class="fa fa-spinner fa-spin"></i> Loading');
        $('#addSubmitButton').attr('disabled',true);
        
        var formData = new FormData($("#addFormId")[0]);
        $.ajax({
            type: "POST",
            url: "{{ route('company-branch.store') }}",
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
                    company_branch_data_table_list();
                    $('#addModal').modal('hide');
                    swal_success(resp.message);
                }else{
                    swal_error(resp.message);
                }
            }
        });
    });
</script>