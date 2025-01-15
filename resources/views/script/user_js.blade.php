<script type="text/javascript">
    function user_data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit   = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;

        var search_name = $('#search_name').val();
        var search_email = $('#search_email').val();
        var search_department_name = $('#search_department_name').val();
        var search_designation_name = $('#search_designation_name').val();
        var search_start_date = $('#search_start_date').val();
        var search_end_date = $('#search_end_date').val();
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{url("master/user_list")}}',
                type: 'GET',
                data:{start_limit,end_limit,search_name,search_email,search_department_name,search_designation_name,search_start_date,search_end_date},
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
                $('#submitRegister').html('<i class="fa fa-send"></i> Submit');
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
        $('#first_nameError, #last_nameError, #emailError, #date_birthError, #department_typeError, #department_idError, #department_designation_idError, #phone1Error, #countryError, #stateError, #cityError, #zip_codeError, #address1Error, #address2Error').html('');
        
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
                $('#userSubmitButton').html('<i class="fa fa-send"></i> Submit');
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

    /* user details page document section tab*/

    function user_document_data_table_list(){    
        var user_id = $('#user_id').val();
        
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit   = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;

        var search_document_name = $('#search_document_name').val();
        var search_user_name = $('#search_user_name').val();
        var search_document_category = $('#search_document_category').val();

        var search_issue_date = $('#search_issue_date').val();
        var search_expiry_date = $('#search_expiry_date').val();
        var search_start_date = $('#search_start_date').val();
        var search_end_date = $('#search_end_date').val();
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{url("master/user_document_list_tab")}}',
                type: 'GET',
                data:{user_id,start_limit,end_limit,
                search_document_name,search_user_name,search_document_category,
                search_issue_date,search_expiry_date,search_start_date},
            },
            columns: [
                { data: 'sno' },
                { data: 'action' },
                { data: 'document_user_name' },
                { data: 'document_name' },
                { data: 'category_name' },
                { data: 'document_type' },
                { data: 'issue_date' },
                { data: 'expiry_date' },
                { data: 'user_document' },
                { data: 'created_at', type: 'date' }
                
            ],
            "order": [[9, 'DESC']],
            "lengthMenu": [5,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "dom": 'Bfrtip',
            "buttons": ['copy', 'csv', 'excel', 'pdf', 'print',"colvis"],
            "columnDefs": [{"targets": [0,2,4],"orderable": false}]
        });
    }

    function add_edit_user_document(p_id='',type=''){
        $('#addEditUserDocumentSubmit').html('<i class="fa fa-send"></i> Submit');
        $('#addEditUserDocumentSubmit').attr('disabled',false);
        $('#userDocumentFileList').html('');
        if(type == 'add'){
            $('#p_id, #document_name, #issue_date, #expiry_date').val('');
            $('#document_id').val('').trigger('change');
            $('#set_user_document, document_description').html('');
            $("#addEditUserDocumentModal").modal();
            return false;
        }
        $.ajax({
            type: "POST",
            url: "{{ url('master/user_document_edit') }}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                
                if(resp.data != ''){
                    var rep = resp.data;
                    $('#p_id').val(rep.id);
                    $("#addEditUserDocumentModal").modal();
                    $('#document_name').val(rep.document_name);
                    $('#document_id').val(rep.document_id).trigger('change');
                    $('#issue_date').val(rep.issue_date);
                    $('#expiry_date').val(rep.expiry_date);
                    $('#document_type').val(rep.document_type);
                    $('#document_description').val(rep.description);
                    $('#set_user_document').html(rep.user_document);
                    $('#is_active').val(rep.is_active);
                }else{
                    swal_error('Something went wrong');
                    return false;
                }
            }
        });  
    }

    /*Drag-and-drop functionality*/
    function save_user_document_data(save_file_name=''){
        $('#addEditUserDocumentSubmit').html('<i class="fa fa-spinner fa-spin"></i> Loading...');
        $('#addEditUserDocumentSubmit').attr('disabled',true);

        var formData = new FormData($("#addEditUserDocumentFormId")[0]);
        formData.append('user_document',save_file_name);
        $.ajax({
            type: "POST",
            url: "{{url('master/add_user_document')}}",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function(resp) {
                $('#addEditUserDocumentSubmit').html('<i class="fa fa-send"></i> Submit');
                $('#addEditUserDocumentSubmit').attr('disabled',false);
                if(resp.status == 'success'){
                    swal_success(resp.s_msg);
                    $("#addEditUserDocumentModal").modal('hide');
                    user_change_tab('document');
                    //user_document_data_table_list();
                    //location.reload();
                }else{
                    swal_error(resp.s_msg);
                }
            }
        });
    }


    function user_search(){
        user_data_table_list();
    }
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
</script>