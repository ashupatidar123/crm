<script type="text/javascript">
    function department_permission(){
        
        var all_menu_ids = [];
        var all_menu_names = [];
        var menu_add_access = [];
        var menu_edit_access = [];
        var menu_delete_access = [];

        var department_id = $('#department_id').val();
        var department_name = $('#department_name').val();
        $('.menu_ids:checked').each(function() {
            all_menu_ids.push($(this).val());
            all_menu_names.push($(this).data('name'));

            if($(this).attr('data-add_access') == undefined || $(this).attr('data-add_access') == ''){
                menu_add_access.push('no');
            }else{
                menu_add_access.push($(this).attr('data-add_access')); 
            }

            if($(this).attr('data-edit_access') == undefined || $(this).attr('data-edit_access') == ''){
                menu_edit_access.push('no');
            }else{
                menu_edit_access.push($(this).attr('data-edit_access')); 
            }

            if($(this).attr('data-delete_access') == undefined || $(this).attr('data-delete_access') == ''){
                menu_delete_access.push('no');
            }else{
                menu_delete_access.push($(this).attr('data-delete_access')); 
            } 
        });

        var top_menu_ids = [];
        $('.top_menu_ids:checked').each(function() {
            top_menu_ids.push($(this).val());
        });
        
        //console.log(top_menu_ids); return false;
        

        if(top_menu_ids == ''){
            swal_error('Please select atleast one main permission menu');
            return false;
        }
        else if(all_menu_ids == ''){
            swal_error('Please select atleast one permission menu');
            return false;
        }
        else if(department_id < 1){
            swal_error('Opps! Something went wrong');
            return false;
        }
        
        Swal.fire({
            title: 'Are you sure to apply permission for '+department_name+'?',
            text: "You will be able to revert this permission!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, ok it!"
        }).then((result) => {
            if(result.isConfirmed) {
                $('#applyPermissionLoader').html('Permission loading...');
                $('#applyPermissionLoader').attr('disabled',true);

                $.ajax({
                    type: "POST",
                    url: "{{ route('menu_department_permission_store') }}",
                    data:{department_id,top_menu_ids,all_menu_ids,menu_add_access,
                        menu_edit_access,menu_delete_access
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrf_token
                    },
                    dataType:'JSON',
                    success: function (resp) {
                        $('#applyPermissionLoader').html('<i class="fas fa-user-shield"></i> Apply Permission');
                        $('#applyPermissionLoader').attr('disabled',false);
                        if(resp.status == 'success'){
                            swal_success(resp.message);
                        }else{
                            swal_error(resp.message);
                        }
                    }
                });
            }
        });        
    }

    function user_permission(){
        
        var all_menu_ids = [];
        var all_menu_names = [];
        var menu_add_access = [];
        var menu_edit_access = [];
        var menu_delete_access = [];

        var user_id = $('#user_id').val();
        var first_name = $('#first_name').val();
        $('.menu_ids:checked').each(function() {
            all_menu_ids.push($(this).val());
            all_menu_names.push($(this).data('name'));

            if($(this).attr('data-add_access') == undefined || $(this).attr('data-add_access') == ''){
                menu_add_access.push('no');
            }else{
                menu_add_access.push($(this).attr('data-add_access')); 
            }

            if($(this).attr('data-edit_access') == undefined || $(this).attr('data-edit_access') == ''){
                menu_edit_access.push('no');
            }else{
                menu_edit_access.push($(this).attr('data-edit_access')); 
            }

            if($(this).attr('data-delete_access') == undefined || $(this).attr('data-delete_access') == ''){
                menu_delete_access.push('no');
            }else{
                menu_delete_access.push($(this).attr('data-delete_access')); 
            } 
        });

        var top_menu_ids = [];
        $('.top_menu_ids:checked').each(function() {
            top_menu_ids.push($(this).val());
        });
        
        //console.log(top_menu_ids); return false;
        

        if(top_menu_ids == ''){
            swal_error('Please select atleast one main permission menu');
            return false;
        }
        else if(all_menu_ids == ''){
            swal_error('Please select atleast one permission menu');
            return false;
        }
        else if(user_id < 1){
            swal_error('Opps! Something went wrong');
            return false;
        }
        
        Swal.fire({
            title: 'Are you sure to apply permission for '+first_name+'?',
            text: "You will be able to revert this permission!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, ok it!"
        }).then((result) => {
            if(result.isConfirmed) {
                $('#applyPermissionLoader').html('Permission loading...');
                $('#applyPermissionLoader').attr('disabled',true);

                $.ajax({
                    type: "POST",
                    url: "{{ route('menu_user_permission_store') }}",
                    data:{user_id,top_menu_ids,all_menu_ids,menu_add_access,
                        menu_edit_access,menu_delete_access
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrf_token
                    },
                    dataType:'JSON',
                    success: function (resp) {
                        $('#applyPermissionLoader').html('<i class="fas fa-user-shield"></i> Apply Permission');
                        $('#applyPermissionLoader').attr('disabled',false);
                        if(resp.status == 'success'){
                            swal_success(resp.message);
                        }else{
                            swal_error(resp.message);
                        }
                    }
                });
            }
        });        
    }

    function add_edit_delete_access(check,id='',type=''){
        var isChecked = $(check).prop('checked');
        
        if(type == 'add'){
            if(isChecked == true){
                $('.add_edit_delete_access_check_'+id).attr('data-add_access','yes');
            }else{
                $('.add_edit_delete_access_check_'+id).attr('data-add_access','no');
            }
        }
        else if(type == 'edit'){
            if(isChecked == true){
                $('.add_edit_delete_access_check_'+id).attr('data-edit_access','yes');
            }else{
                $('.add_edit_delete_access_check_'+id).attr('data-edit_access','no');
            }
        }
        else if(type == 'delete'){
            if(isChecked == true){
                $('.add_edit_delete_access_check_'+id).attr('data-delete_access','yes');
            }else{
                $('.add_edit_delete_access_check_'+id).attr('data-delete_access','no');
            }
        }
    }

    /* table list record start */
    function menu_department_permissiondata_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;

        var search_menu_id = $('#search_menu_id').val();
        var search_department_id = $('#search_department_id').val();
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route("menu_permission_department_list")}}',
                type: 'POST',
                data:{start_limit,end_limit,search_menu_id,search_department_id},
                headers: {
                    'X-CSRF-TOKEN': csrf_token
                },
            },
            columns: [
                { data: 'sno' },
                { data: 'action' },
                { data: 'menu_name' },
                { data: 'department_name' },
                { data: 'created_at', type: 'date' }
            ],
            "order": [[4, 'DESC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "lengthChange": false,
            "searching": false,
            "responsive": true,
            "columnDefs": [{"targets": [0,1,2,3],"orderable": false}]
        });
    }

    function search_reset_form(){
        $('#search_menu_id, #search_department_id').val('');
        $('#advanceSearch').trigger("reset");
        menu_department_permissiondata_table_list();
    }
    /* table list record end*/
</script>