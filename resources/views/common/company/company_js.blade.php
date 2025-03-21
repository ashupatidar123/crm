<script type="text/javascript">
    function company_data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;

        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route("company_list")}}',
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
                { data: 'currency' },
                { data: 'phone' },
                { data: 'email' },
                { data: 'website_url' },
                { data: 'gst_no' },
                { data: 'address' },
                { data: 'zip_code' },
                { data: 'company_logo' },
                { data: 'created_at', type: 'date' }
            ],
            "order": [[10, 'DESC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "columnDefs": [{"targets": [0,1,6],"orderable": false}]
        });
    }  

    function add_edit_company(p_id='',type=''){
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
            url: "{{ route('company_list_edit') }}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                
                if(resp.data != ''){
                    var rep = resp.data;
                    $('#p_id').val(rep.id);
                    $('#company_name').val(rep.company_name);
                    $('#currency').val(rep.currency);
                    $('#phone').val(rep.phone);
                    $('#email').val(rep.email);
                    $('#website_url').val(rep.website_url);
                    $('#fax').val(rep.fax);
                    $('#gst_no').val(rep.gst_no);
                    $('#address').val(rep.address);
                    $('#zip_code').val(rep.zip_code);
                    $('#description').val(rep.description);
                    $('#company_logo_show').html(rep.company_logo);
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

        if($('#company_name').val() == ''){
            var check = 1;
            $('#company_nameError').html('This field is required');
        }
        if($('#currency').val() == ''){
            var check = 1;
            $('#currencyError').html('This field is required');
        }
        if($('#address').val() == ''){
            var check = 1;
            $('#addressError').html('This field is required');
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
        if($('#gst_no').val() == ''){
            var check = 1;
            $('#gst_noError').html('This field is required');
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
            url: "{{ route('company-profile.store') }}",
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
                    company_data_table_list();
                    $('#addModal').modal('hide');
                    swal_success(resp.message);
                }else{
                    swal_error(resp.message);
                }
            }
        });
    });
</script>