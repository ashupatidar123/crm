<script type="text/javascript">
    function menu_data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route("menu_list")}}',
                type: 'POST',
                data:{start_limit,end_limit},
                headers: {
                    'X-CSRF-TOKEN': csrf_token
                },
            },
            columns: [
                { data: 'sno' },
                { data: 'action' },
                { data: 'menu_name' },
                { data: 'parent_menu' },
                { data: 'menu_code' },
                { data: 'menu_sequence' },
                { data: 'menu_link' },
                { data: 'menu_icon' },
                { data: 'description' },
                { data: 'created_at', type: 'date' }
            ],
            "order": [[9, 'DESC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "columnDefs": [{"targets": [0,3,6,7],"orderable": false}]
        });
    }  

    function add_edit_menu(p_id='',type=''){
        $('#addSubmitButton').html('<i class="fa fa-send"></i> Submit');
        $('#addSubmitButton').attr('disabled',false);
        if(type == 'add'){
            $("#addFormId")[0].reset();
            $('#p_id, #description').val('');
            $('#menu_slug').prop('readonly', true);
            $("#addModal").modal();
            get_parent_menu('');
            return false;
        }
        $('.addEditLoader_'+p_id).html('<i class="fa fa-spinner fa-spin"></i>');
        $('.addEditLoader_'+p_id).attr('disabled',true);

        $.ajax({
            type: "POST",
            url: "{{ route('menu_list_edit') }}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                
                if(resp.data != ''){
                    var rep = resp.data;
                    $('#menu_slug').prop('readonly', false);
                    $('#p_id').val(rep.id);
                    $('#menu_name').val(rep.menu_name);
                    $('#menu_slug').val(rep.menu_slug);
                    $('#menu_code').val(rep.menu_code);
                    $('#menu_sequence').val(rep.menu_sequence);
                    $('#menu_link').val(rep.menu_link);
                    $('#menu_icon').val(rep.menu_icon);
                    $('#description').val(rep.description);
                    $('#is_active').val(rep.is_active);
                    $("#addModal").modal();
                    get_parent_menu(rep.parent_menu_id);
                }else{
                    swal_error('Something went wrong');
                    return false;
                }
                $('.addEditLoader_'+p_id).html('<i class="fa fa-edit"></i>');
                $('.addEditLoader_'+p_id).attr('disabled',false);
            }
        });  
    }

    $("#addSubmitButton").on("click",function (event) {
        event.preventDefault();
        $('.remove_error').html('');
        var check = 0;

        if($('#menu_name').val() == ''){
            var check = 1;
            $('#menu_nameError').html('This field is required');
        }
        if($('#menu_slug').val() == ''){
            var check = 1;
            $('#menu_slugError').html('This field is required');
        }
        if($('#menu_sequence').val() == ''){
            var check = 1;
            $('#menu_sequenceError').html('This field is required');
        }
        if($('#is_active').val() == ''){
            var check = 1;
            $('#is_activeError').html('This field is required');
        }
        if(check == 1){
            return false;
        }

        $('.show_message').html('');
        $('#addSubmitButton').html('<i class="fa fa-spinner fa-spin"></i> Loading');
        $('#addSubmitButton').attr('disabled',true);
        
        var formData = new FormData($("#addFormId")[0]);
        $.ajax({
            type: "POST",
            url: "{{ route('menu.store') }}",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                $('#addSubmitButton').html('Submit');
                $('#addSubmitButton').attr('disabled',false);
                if(resp.status == 'success'){
                    menu_data_table_list();
                    $('#addModal').modal('hide');
                    swal_success(resp.message);
                }else{
                    swal_error(resp.message);
                }
            }
        });
    });

    function get_parent_menu(p_id=''){
        var type = 'ajax_list';
        $.ajax({
            type: "POST",
            url: "{{route('get_parent_menu')}}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                $('#parent_menu_id').html(resp);
            }
        });
    }

    function create_menu_slug(val=''){
        var slug = val.toLowerCase().trim().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '_').replace(/-+/g, '_');
        if(slug == ''){
            $('#menu_slug').prop('readonly', true);
        }else{
            $('#menu_slug').prop('readonly', false); 
        }
        $('#menu_slug').val(slug);
    }
</script>