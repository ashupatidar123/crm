<script type="text/javascript">
    
    function referesh_form(){
        $('.referesh_form').html('Loading');
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

    function ajax_active_inactive(p_id='',type='',tbl=''){
        if(type == 1){
           var title =  "Are you sure to In-Active?";
        }else{
           var title =  "Are you sure to Active?"; 
        }
        Swal.fire({
            title: title,
            text: "You will be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, ok it!"
        }).then((result) => {
            if(result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{url('master/ajax_active_inactive')}}",
                    data: {p_id,type,tbl},
                    headers: {
                        'X-CSRF-TOKEN': csrf_token
                    },
                    dataType:"JSON",
                    success: function (resp) {
                        if(resp.status == 'success'){
                            if(tbl == 'user'){
                                user_data_table_list();
                            }
                            else if(tbl == 'role'){
                                role_data_table_list();
                            }
                            else if(tbl == 'department'){
                                department_data_table_list();
                            }
                            swal_success(resp.message,1800);
                        }else{
                            swal_error(resp.message,1800); 
                        }
                    }
                });
            }
        });        
    }

    function ajax_delete(p_id='',tbl=''){
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
                    url: "{{url('master/ajax_delete')}}",
                    data: {p_id,tbl},
                    headers: {
                        'X-CSRF-TOKEN': csrf_token
                    },
                    dataType:"JSON",
                    success: function (resp) {
                        if(resp.status == 'success'){
                            if(tbl == 'user'){
                                user_data_table_list();
                            }
                            else if(tbl == 'role'){
                                role_data_table_list();
                            }
                            else if(tbl == 'department'){
                                department_data_table_list();
                            }
                            swal_success(resp.message,1800);
                        }else{
                            swal_error(resp.message,1800); 
                        }
                    }
                });
            }
        });        
    }

</script>