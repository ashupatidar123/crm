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
                $('.activeInactiveLoader_'+p_id).html('<i class="fa fa-spinner fa-spin"></i>');
                $('.activeInactiveLoader_'+p_id).attr('disabled',true);

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
                            else if(tbl == 'designation'){
                                designation_data_table_list();
                            }
                            else if(tbl == 'document'){
                                document_data_table_list();
                            }
                            else if(tbl == 'user_document'){
                                user_document_data_table_list();
                            }
                            else if(tbl == 'vessel'){
                                vessel_data_table_list();
                            }
                            else if(tbl == 'vessel_category'){
                                vessel_category_data_table_list();
                            }
                            else if(tbl == 'vessel_document'){
                                vessel_document_data_table_list();
                            }
                            else if(tbl == 'vessel_check_out'){
                                vessel_check_in_out_data_table_list();
                            }
                            else if(tbl == 'menu'){
                                menu_data_table_list();
                            }
                            else if(tbl == 'apprisal'){
                                vessel_apprisal_data_table_list_tab();
                            }
                            else if(tbl == 'menu_permission_department'){
                                menu_department_permissiondata_table_list();
                            }
                            else if(tbl == 'company'){
                                company_data_table_list();
                            }
                            else if(tbl == 'company_branch'){
                                company_branch_data_table_list();
                            }
                            else{
                                swal_error(resp.message,1800);
                                location.reload();
                                return false;
                            }
                            swal_success(resp.message,1800);
                        }else{
                            if(type == 1){
                                $('.activeInactiveLoader_'+p_id).html('<i class="fa fa-check"></i>');   
                            }else{
                               $('.activeInactiveLoader_'+p_id).html('<i class="fa fa-close"></i>'); 
                            }
                            $('.activeInactiveLoader_'+p_id).attr('disabled',false);
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
                $('.deleteLoader_'+p_id).html('<i class="fa fa-spinner fa-spin"></i>');
                $('.deleteLoader_'+p_id).attr('disabled',true);

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
                            else if(tbl == 'designation'){
                                designation_data_table_list();
                            }
                            else if(tbl == 'document'){
                                document_data_table_list();
                            }
                            else if(tbl == 'user_document'){
                                user_document_data_table_list();
                            }
                            else if(tbl == 'vessel'){
                                vessel_data_table_list();
                            }
                            else if(tbl == 'vessel_category'){
                                vessel_category_data_table_list();
                            }
                            else if(tbl == 'vessel_document'){
                                vessel_document_data_table_list();
                            }
                            else if(tbl == 'vessel_check_out'){
                                vessel_check_in_out_data_table_list();
                            }
                            else if(tbl == 'menu'){
                                menu_data_table_list();
                            }
                            else if(tbl == 'apprisal'){
                                vessel_apprisal_data_table_list_tab();
                            }
                            else if(tbl == 'menu_permission_department'){
                                menu_department_permissiondata_table_list();
                            }
                            else if(tbl == 'company'){
                                company_data_table_list();
                            }
                            else if(tbl == 'company_branch'){
                                company_branch_data_table_list();
                            }
                            else{
                                location.reload();
                            }
                            swal_success(resp.message,1800);
                        }else{
                            $('.deleteLoader_'+p_id).html('<i class="fa fa-trash"></i>');
                            $('.deleteLoader_'+p_id).attr('disabled',false);
                            swal_error(resp.message,1800); 
                        }
                    }
                });
            }
        });        
    }

    function ajax_view(p_id='',tbl=''){
        
        $.ajax({
            type: "POST",
            url: "{{route('ajax_view')}}",
            data: {p_id,tbl},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                if(resp.data != ''){
                    var rep = resp.data;
                    if(tbl == 'user'){
                        show_user_view(rep);
                        $("#userViewModal").modal();
                    }
                }else{
                    swal_error('Something went wrong');
                    return false;
                }
            }
        });   
    }

</script>