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
                url: '{{url("master/user_list")}}',
                type: 'GET',
                data:{start_limit,end_limit},
            },
            columns: [
                { data: 'sno' },
                { data: 'id' },
                { data: 'first_name' },
                { data: 'login_id' },
                { data: 'email' },
                { data: 'date_birth' },
                { data: 'created_at', type: 'date' },
                { data: 'status' },
                { data: 'action' }
            ],
            "order": [[1, 'DESC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "dom": 'Bfrtip',
            "buttons": ['copy', 'csv', 'excel', 'pdf', 'print',"colvis"],
            "columnDefs": [{"targets": [0,8],"orderable": false}]
        });
    }

    function check_user_record(where_value,check_type='',id=''){
        if(where_value == ''){
            return false;
        }
        $.ajax({
            type: "POST",
            url: "{{url('master/ajax_user_check_record')}}",
            data: {where_value,check_type,id},
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

    function get_role_reporting(p_id='',html_id=''){
        var selectedOption = $('#role').find('option:selected');
        var rank = selectedOption.data('rank');
        
        var type = 'ajax_list';
        $.ajax({
            type: "POST",
            url: "{{route('get_role_reporting')}}",
            data: {p_id,type,rank},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                $('#'+html_id).html(resp);
            }
        });
    } 

    /* add user */
    $("#submitRegister").on("click",function (e) {
        event.preventDefault();
        $('#first_nameError, #last_nameError, #emailError, #date_birthError, #roleError, #reporting_role_idError, #department_idError, #login_idError, #passwordError, #confirm_passwordError, #countryError, #phone1Error, #stateError, #cityError, #zip_codeError, #address1Error, #address2Error').html('');
        
        var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        var mobilePattern = /^[6789]\d{9}$/;
        var passwordPattern = /^(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*()_+[\]{}|;:'",.<>?/-])[a-zA-Z\d!@#$%^&*()_+[\]{}|;:'",.<>?/-]{6,}$/;

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
        }else{
            if(!emailPattern.test($('#email').val())) {
                var check = 1; 
                $('#emailError').html('Please enter a valid email address');
            }
        }
        if($('#phone1').val() == ''){
            var check = 1;
            $('#phone1Error').html('Phone is required');
        }else{
            if(!mobilePattern.test($('#phone1').val())) {
                var check = 1; 
                $('#phone1Error').html('Please enter a valid phone number');
            }
        }
        if($('#date_birth').val() == ''){
            var check = 1;
            $('#date_birthError').html('Date of birth is required');
        }
        if($('#login_id').val() == ''){
            var check = 1;
            $('#login_idError').html('Login id is required');
        }
        if($('#password').val() == ''){
            var check = 1;
            $('#passwordError').html('Password is required');
        }else{
            if(!passwordPattern.test($('#password').val())) {
                var check = 1;
                $('#confirm_password').val('');
                $('#passwordError').html('Enter vaild password for secure account');
                swal_error('Password should be at least 6 characters long, including a number, special character, and a lowercase letter.');
                return false;
            }
        }
        if($('#confirm_password').val() == ''){
            var check = 1;
            $('#confirm_passwordError').html('Confirm password is required');
        }else{
            if($('#password').val() != $('#confirm_password').val()){
                var check = 1;
                $('#confirm_passwordError').html('Confirm password is wrong');
            }
        }
        if($('#role').val() == ''){
            var check = 1;
            $('#roleError').html('Role is required');
        }
        if($('#reporting_role_id').val() == ''){
            var check = 1;
            $('#reporting_role_idError').html('Reporting role is required');
        }
        if($('#department_id').val() == ''){
            var check = 1;
            $('#department_idError').html('Department is required');
        }
        if($('#country_id').val() == ''){
            var check = 1;
            $('#countryError').html('Country is required');
        }
        if($('#state_id').val() == ''){
            var check = 1;
            $('#stateError').html('State is required');
        }
        if($('#city_id').val() == ''){
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
            $('.show_message').html('Some fields are required');
            return false;
        }
        $('.show_message').html('');
        $('#submitRegister').html('Loading...');
        var formData = new FormData($("#registerFormId")[0]);
        
        $.ajax({
            type: "POST",
            url: "{{url('master/add-user')}}",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function(resp) {
                $('#submitRegister').html('Submit');
                $('.show_message').html(resp.message);
                if(resp.status == 'success'){
                    swal_success(resp.s_msg);
                    window.setTimeout(function(){
                        window.location.href = "{{url('master/user')}}";
                    },3000);
                }else{
                    swal_error(resp.s_msg);
                }
            }
        });
    });  

    /* update user */
    $("#userSubmitButton").on("click",function (e) {
        event.preventDefault();
        $('#first_nameError, #last_nameError, #emailError, #date_birthError, #roleError, #reporting_role_idError, #department_idError, #phone1Error, #countryError, #stateError, #cityError, #zip_codeError, #address1Error, #address2Error').html('');
        
        var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        var mobilePattern = /^[6789]\d{9}$/;

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
        }else{
            if(!emailPattern.test($('#email').val())) {
                var check = 1; 
                $('#emailError').html('Please enter a valid email address');
            }
        }
        if($('#phone1').val() == ''){
            var check = 1;
            $('#phone1Error').html('Phone is required');
        }else{
            if(!mobilePattern.test($('#phone1').val())) {
                var check = 1; 
                $('#phone1Error').html('Please enter a valid phone number');
            }
        }
        if($('#date_birth').val() == ''){
            var check = 1;
            $('#date_birthError').html('Date of birth is required');
        }
        if($('#role').val() == ''){
            var check = 1;
            $('#roleError').html('Role is required');
        }
        if($('#reporting_role_id').val() == ''){
            var check = 1;
            $('#reporting_role_idError').html('Reporting role is required');
        }
        if($('#department_id').val() == ''){
            var check = 1;
            $('#department_idError').html('Department is required');
        }
        if($('#country_id').val() == ''){
            var check = 1;
            $('#countryError').html('Country is required');
        }
        if($('#state_id').val() == ''){
            var check = 1;
            $('#stateError').html('State is required');
        }
        if($('#city_id').val() == ''){
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
            swal_error('Some fields are required');
            return false;
        }
        $('.show_message').html('');
        $('#userSubmitButton').html('Loading...');
        var formData = new FormData($("#formId")[0]);
        
        $.ajax({
            type: "POST",
            url: "{{url('master/update_user')}}",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function(resp) {
                $('#userSubmitButton').html('Submit');
                $('.show_message').html(resp.message);
                if(resp.status == 'success'){
                    swal_success(resp.s_msg);
                    window.setTimeout(function(){
                        window.location.href = "{{url('master/user')}}";
                    },3000);
                }else{
                    swal_error(resp.s_msg);
                }
            }
        });
    });
</script>