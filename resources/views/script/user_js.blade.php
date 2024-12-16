<script type="text/javascript">
    function user_data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit   = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{url("user_list")}}',
                type: 'GET',
                data:{start_limit,end_limit},
            },
            columns: [
                { data: 'sno' },
                { data: 'id' },
                { data: 'first_name' },
                { data: 'last_name' },
                { data: 'email' },
                { data: 'date_birth' },
                { data: 'created_at', type: 'date' },
                { data: 'action' }
            ],
            "order": [[1, 'DESC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "dom": 'Bfrtip',
            "buttons": ['copy', 'csv', 'excel', 'pdf', 'print',"colvis"],
            "columnDefs": [{"targets": [0,7],"orderable": false}]
        });
    }

    function user_delete(p_id){
        Swal.fire({
            title: "Are you sure to delete?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if(result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{url('master/user_delete')}}",
                    data: {p_id},
                    headers: {
                        'X-CSRF-TOKEN': csrf_token
                    },
                    dataType:"JSON",
                    success: function (resp) {
                        if(resp.status == 'success'){
                            data_table_list();
                            swal_success(resp.message,1800);
                        }else{
                            swal_error(resp.message,1800); 
                        }
                    }
                });
            }
        });        
    }

    function check_user_record(where_value,check_type=''){
        if(where_value == ''){
            return false;
        }
        $.ajax({
            type: "POST",
            url: "{{url('ajax_user_check_record')}}",
            data: {where_value,check_type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                if(check_type == 'username_login_id'){
                    if(resp > 0){
                        $('#login_idError').html('Login id is already taken');
                        return false;
                    }else{
                        $('#login_idError').html('');
                    }
                }
                else if(check_type == 'email'){
                    if(resp > 0){
                        $('#emailError').html('Email id is already taken');
                        return false;
                    }else{
                        $('#emailError').html('');
                    }
                }
            }
        });
    } 

    $("#submitRegister").on("click",function (e) {
        event.preventDefault();
        $('#first_nameError, #last_nameError, #emailError, #date_birthError, #roleError, #login_idError, #passwordError, #countryError, #phone1Error, #stateError, #cityError, #zip_codeError, #address1Error, #address2Error').html('');
        var check = 0;
        if($('#first_name').val() == ''){
            var check = 1;
            $('#first_nameError').html('First name is required');
        }
        if($('#last_name').val() == ''){
            var check = 1;
            $('#last_nameError').html('Last name is required');
        }
        if($('#email').val() == ''){
            var check = 1;
            $('#emailError').html('Email is required');
        }
        if($('#phone1').val() == ''){
            var check = 1;
            $('#phone1Error').html('Phone is required');
        }
        if($('#date_birth').val() == ''){
            var check = 1;
            $('#date_birthError').html('Date of birth is required');
        }
        if($('#role').val() == ''){
            var check = 1;
            $('#roleError').html('Role is required');
        }
        if($('#login_id').val() == ''){
            var check = 1;
            $('#login_idError').html('Login id is required');
        }
        if($('#password').val() == ''){
            var check = 1;
            $('#passwordError').html('Password id is required');
        }
        if($('#country').val() == ''){
            var check = 1;
            $('#countryError').html('Country is required');
        }
        if($('#state').val() == ''){
            var check = 1;
            $('#stateError').html('State is required');
        }
        if($('#city').val() == ''){
            var check = 1;
            $('#cityError').html('City is required');
        }
        if($('#city').val() == ''){
            var check = 1;
            $('#cityError').html('City is required');
        }
        if($('#zip_code').val() == ''){
            var check = 1;
            $('#zip_codeError').html('ZIP code is required');
        }
        if($('#address1').val() == ''){
            var check = 1;
            $('#address1Error').html('Address line 1 is required');
        }
        if($('#address2').val() == ''){
            var check = 1;
            $('#address2Error').html('Address line 2 is required');
        }
        if(check == 1){
            return false;
        }
        $('.show_message').html('');
        $('#submitRegister').html('Loading...');
        var formData = new FormData($("#registerFormId")[0]);
        
        $.ajax({
            type: "POST",
            url: "{{url('register')}}",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                
                $('#submitRegister').html('Submit');
                $('.show_message').html(resp.message);
                if(resp.status == 'success'){
                    swal_success(resp.s_msg);
                    window.setTimeout(function(){
                        window.location.href = "{{url('users')}}";
                    },5000);
                }else{
                    swal_error(resp.s_msg);
                }
            }
        });
    });  
</script>