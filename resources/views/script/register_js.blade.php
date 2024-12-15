<script type="text/javascript">
    $(document).ready(function(){
        $('#login_id11').on('keyup',function(){
            var login_id = this.value;
            $('#login_idError').html('');
            $.ajax({
                type: "POST",
                url: "{{url('check_login_id')}}",
                data: {login_id},
                headers: {
                    'X-CSRF-TOKEN': token
                },
                success: function (resp) {
                    if(resp > 0){
                        $('#login_idError').html('Login id is already taken');
                        return false;
                    }else
                }
            });
        });
    });  
</script>