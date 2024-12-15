<script type="text/javascript">
    
    function referesh_form(){
        location.reload();
    }

    function swal_success(message='Success',timer=1800,return_msg=''){
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: message,
            showConfirmButton: false,
            timer: timer
        });
        if(return_msg == 'yes'){
            return true;
        }else{
            return false;
        }
        
    }

    function swal_error(message='Error',timer=1800){
        Swal.fire({
            position: "top-end",
            icon: "error",
            title: message,
            showConfirmButton: false,
            timer: timer
        });
        return false;
    }

    function reset_filter(type=''){
        $('#start_limit').val('');
        $('#end_limit').val('');
        data_table_list();
    }
</script>