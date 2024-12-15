<script type="text/javascript">
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
</script>