<script type="text/javascript">
    function department_permission(){
        var all_menu_ids = [];
        var all_menu_names = [];
        var department_id = $('#department_id').val();
        var department_name = $('#department_name').val();
        $('.menu_ids:checked').each(function() {
            all_menu_ids.push($(this).val());
            all_menu_names.push($(this).data('name'));
        });
        if(all_menu_ids == ''){
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
                    url: "{{ route('department-permission.store') }}",
                    data: {department_id,all_menu_ids},
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
</script>