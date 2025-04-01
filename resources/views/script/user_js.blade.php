<script type="text/javascript">
    function user_data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit   = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;

        var search_name = $('#search_name').val();
        var search_email = $('#search_email').val();
        var search_department_type = $('#search_department_type').val();
        var search_department_name = $('#search_department_name').val();
        var search_designation_name = $('#search_designation_name').val();
        var search_start_date = $('#search_start_date').val();
        var search_end_date = $('#search_end_date').val();
        var summernote = $('#summernote').val();
        var user_department_type = $('#user_department_type').val();

        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{url("user/user_list")}}',
                type: 'POST',
                data:{start_limit,end_limit,search_name,search_email,search_department_type,search_department_name,search_designation_name,search_start_date,search_end_date,summernote,user_department_type},
                headers: {
                    'X-CSRF-TOKEN': csrf_token
                },
            },
            columns: [
                { data: 'sno' },
                { data: 'action' },
                { data: 'first_name' },
                { data: 'login_id' },
                { data: 'email' },
                { data: 'created_at', type: 'date' },
                { data: 'department_name' },
                { data: 'designation_name' }
            ],
            "order": [[5, 'DESC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "dom": 'Bfrtip',
            "buttons": ['copy', 'csv', 'excel', 'pdf', 'print',"colvis"],
            "columnDefs": [{"targets": [0,1,6,7],"orderable": false}]
        });
    }

    function check_user_record(where_value,check_type='',id=''){
        if(where_value == ''){
            return false;
        }
        $.ajax({
            type: "POST",
            url: "{{url('user/ajax_user_check_record')}}",
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

    function get_department_record(p_id='',html_id=''){
        var selectedOption = $('#department_type').find('option:selected');
        var department_type = selectedOption.val();
        if(department_type == '' || department_type == undefined){
            var department_type = $('#search_department_type').val();
        }
        
        var user_department_type = '';
        if($('#user_department_type').val() != undefined){
            var user_department_type = $('#user_department_type').val();
        }
        
        var type = 'ajax_list';
        $.ajax({
            type: "POST",
            url: "{{route('get_department_record')}}",
            data: {p_id,type,department_type,user_department_type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                $('#'+html_id).html(resp);
            }
        });
    }

    function get_designation_record(p_id='',html_id='',get_id_val='department_id',department_id=''){
        if(department_id < 1){
            var selectedOption = $('#'+get_id_val).find('option:selected');
            var department_id = selectedOption.val();
        }
        
        var type = 'ajax_list';
        $.ajax({
            type: "POST",
            url: "{{route('get_designation_record')}}",
            data: {p_id,type,department_id},
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
        $('#first_nameError, #last_nameError, #emailError, #date_birthError, #department_typeError, #department_idError, #department_designation_idError, #login_idError, #passwordError, #confirm_passwordError, #countryError, #phone1Error, #stateError, #cityError, #zip_codeError, #address1Error, #address2Error').html('');
        
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
        if($('#department_type').val() == ''){
            var check = 1;
            $('#department_typeError').html('Department type is required');
        }
        if($('#department_id').val() == ''){
            var check = 1;
            $('#department_idError').html('Department is required');
        }
        if($('#department_designation_id').val() == ''){
            var check = 1;
            $('#department_designation_idError').html('Designation is required');
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
        if($('#address_type').val() == ''){
            var check = 1;
            $('#address_typeError').html('Address type is required');
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
            url: "{{url('user/add-user')}}",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function(resp) {
                var department_type = $('#department_type').val();
                if(department_type == 'office'){
                    url = "{{url('user/user/office')}}";
                }else{
                    url = "{{url('user/user/vessel')}}";
                }
                $('#submitRegister').html('<i class="fa fa-send"></i> Submit');
                $('.show_message').html(resp.message);
                if(resp.status == 'success'){
                    swal_success(resp.s_msg);
                    window.setTimeout(function(){
                        window.location.href = url;
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
        $('#first_nameError, #last_nameError, #emailError, #date_birthError, #department_typeError, #department_idError, #department_designation_idError, #phone1Error').html('');
        
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
        if($('#department_type').val() == ''){
            var check = 1;
            $('#department_typeError').html('Department type is required');
        }
        if($('#department_id').val() == ''){
            var check = 1;
            $('#department_idError').html('Department is required');
        }
        if($('#department_designation_id').val() == ''){
            var check = 1;
            $('#department_designation_idError').html('Designation is required');
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
            url: "{{url('user/update_user')}}",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function(resp) {
                var department_type = $('#department_type').val();
                if(department_type == 'office'){
                    url = "{{url('user/user/office')}}";
                }else{
                    url = "{{url('user/user/vessel')}}";
                }

                $('#userSubmitButton').html('<i class="fa fa-send"></i> Submit');
                $('.show_message').html(resp.message);
                if(resp.status == 'success'){
                    swal_success(resp.s_msg);
                    window.setTimeout(function(){
                        window.location.href = url;
                    },3000);
                }else{
                    swal_error(resp.s_msg);
                }
            }
        });
    });

    function show_user_view(rep){
        $('.view_tbl_first_name').html(rep.name_title+' '+rep.first_name);
        $('.view_tbl_middle_name').html(rep.middle_name);
        $('.view_tbl_last_name').html(rep.last_name);
        $('.view_tbl_phone').html(rep.phone);
        $('.view_tbl_date_birth').html(rep.date_birth);
        $('.view_tbl_update_at').html(rep.update_at);
    }

    /* update user */
    $("#profileSubmitButton").on("click",function (e) {
        event.preventDefault();
        $('#first_nameError, #last_nameError, #emailError, #date_birthError, #phone1Error, #countryError, #stateError, #cityError, #zip_codeError, #address1Error, #address2Error').html('');
        
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
        if($('#department_type').val() == ''){
            var check = 1;
            $('#department_typeError').html('Department type is required');
        }
        if($('#department_id').val() == ''){
            var check = 1;
            $('#department_idError').html('Department is required');
        }
        if($('#department_designation_id').val() == ''){
            var check = 1;
            $('#department_designation_idError').html('Designation is required');
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
        $('#profileSubmitButton').html('Loading...');
        var formData = new FormData($("#profileFormId")[0]);
        
        $.ajax({
            type: "POST",
            url: "{{url('profile')}}",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function(resp) {
                $('#profileSubmitButton').html('<i class="fa fa-send"></i> Submit');
                $('.show_message').html(resp.message);
                if(resp.status == 'success'){
                    swal_success(resp.s_msg);
                    window.setTimeout(function(){
                        window.location.href = "{{url('profile')}}";
                    },3000);
                }else{
                    swal_error(resp.s_msg);
                }
            }
        });
    });

    $('#user_image').change(function(event) {
        var file = event.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#show_user_image').attr('src', e.target.result);
                $('#show_user_image').show();
            };
            reader.readAsDataURL(file);
        }
    });

    function search_reset_form(){
        $('#search_start_date, #search_end_date').val('');
        $('#userSearch').trigger("reset");
        user_data_table_list();
    }

    function user_document_search(){
        user_document_data_table_list();
    }
    function user_document_search_reset_form(){
        $('#search_issue_date, #search_expiry_date, #search_start_date, #search_end_date').val('');
        $('#userDocumentSearch').trigger("reset");
        user_document_data_table_list();
    }

    $('#search_start_date, #search_end_date').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        maxDate: 0,
    });
    $('#issue_date, #expiry_date, #search_issue_date, #search_expiry_date').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
    });

    /* user address section*/
    function user_address_data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:3;
        var pageLength = (end_limit > 0)?end_limit:3;
        //alert(user_id); return false; 
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{url("user/user_address_list")}}',
                type: 'POST',
                data:{start_limit,end_limit,user_id},
                headers: {
                    'X-CSRF-TOKEN': csrf_token
                },
            },
            columns: [
                { data: 'sno' },
                { data: 'action' },
                { data: 'address_type' },
                { data: 'address1' },
                { data: 'address2' },
                { data: 'address3' },
                { data: 'zip_code' },
                { data: 'country_id' },
                { data: 'state_id' },
                { data: 'city_id' },
                { data: 'created_at', type: 'date' }
            ],
            "lengthMenu": [2,3,5,10],
            "pageLength": pageLength,
            "responsive": true,
            "columnDefs": [{"targets": [0,3,6,7],"orderable": false}]
        });
    }

    function add_edit_address(p_id='',type=''){
        $('#addAddressSubmitButton').html('<i class="fa fa-send"></i> Submit');
        $('#addAddressSubmitButton').attr('disabled',false);
        if(type == 'add'){
            $('#state_id').val('').trigger('change');
            $('#city_id').val('').trigger('change');
            $('#p_id, #address_type, #zip_code, #address1, #address2, #address3').val('');
            get_ajax_country('country_id','country_id');    
            $("#addAddressModal").modal();
            return false;
        }
        $('.addEditLoader_'+p_id).html('<i class="fa fa-spinner fa-spin"></i>');
        $('.addEditLoader_'+p_id).attr('disabled',true);

        $.ajax({
            type: "POST",
            url: "{{ url('user/address_edit') }}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                if(resp.data != ''){
                    var rep = resp.data;
                    $('#p_id').val(rep.id);
                    $('#address_type').val(rep.address_type).trigger('change');
                    $('#zip_code').val(rep.zip_code);
                    $('#address1').val(rep.address1);
                    $('#address2').val(rep.address2);
                    $('#address3').val(rep.address3);
                    $("#addAddressModal").modal();
                    get_ajax_country(rep.country_id,'country_id');
                    get_ajax_state(rep.country_id,'state_id',rep.state_id);
                    get_ajax_city(rep.state_id,'city_id',rep.city_id);
                }else{
                    swal_error('Something went wrong');
                    return false;
                }
                $('.addEditLoader_'+p_id).html('<i class="fa fa-edit"></i>');
                $('.addEditLoader_'+p_id).attr('disabled',false);
            }
        });  
    }

    $("#addAddressSubmitButton").on("click",function (event) {
        event.preventDefault();
        $('.remove_error').html('');
        var check = 0;

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
        if($('#address_type').val() == ''){
            var check = 1;
            $('#address_typeError').html('Address type is required');
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
        $('#addAddressSubmitButton').html('<i class="fa fa-spinner fa-spin"></i> Loading');
        $('#addAddressSubmitButton').attr('disabled',true);
        
        var formData = new FormData($("#addAddressFormId")[0]);
        $.ajax({
            type: "POST",
            url: "{{ url('user/add_edit_address_save') }}",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                $('#addAddressSubmitButton').html('Submit');
                $('#addAddressSubmitButton').attr('disabled',false);
                if(resp.status == 'success'){
                    $('#addAddressModal').modal('hide');
                    user_address_data_table_list();
                    swal_success(resp.message);
                }else{
                    swal_error(resp.message);
                }
            }
        });
    });
</script>