<script type="text/javascript">
    function vessel_category_data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route("vessel_category_list")}}',
                type: 'GET',
                data:{start_limit,end_limit},
            },
            columns: [
                { data: 'sno' },
                { data: 'action' },
                { data: 'category_name' },
                { data: 'parent_vessel_category' },
                { data: 'description' },
                { data: 'created_at', type: 'date' }
            ],
            "order": [[5, 'DESC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "columnDefs": [{"targets": [0,3],"orderable": false}]
        });
    }  

    function add_edit_vessel_category(p_id='',type=''){
        $('#addSubmitButton').html('<i class="fa fa-send"></i> Submit');
        $('#addSubmitButton').attr('disabled',false);
        if(type == 'add'){
            $('#p_id, #category_name, #document_type, #description').val('');
            $("#addModal").modal();
            get_parent_vessel_category('');
            return false;
        }
        $('.addEditLoader_'+p_id).html('<i class="fa fa-spinner fa-spin"></i>');
        $('.addEditLoader_'+p_id).attr('disabled',true);

        $.ajax({
            type: "POST",
            url: "{{ route('vessel_category_edit') }}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                
                if(resp.data != ''){
                    var rep = resp.data;
                    $('#p_id').val(rep.id);
                    $('#category_name').val(rep.category_name);
                    $('#document_type').val(rep.document_type);
                    $('#description').val(rep.description);
                    $('#is_active').val(rep.is_active);
                    $("#addModal").modal();
                    get_parent_vessel_category(rep.parent_category_id);
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

        if($('#category_name').val() == ''){
            var check = 1;
            $('#category_nameError').html('This field is required');
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
            url: "{{ route('vessel-category.store') }}",
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
                    vessel_category_data_table_list();
                    $('#addModal').modal('hide');
                    swal_success(resp.message);
                }else{
                    swal_error(resp.message);
                }
            }
        });
    });

    function get_parent_vessel_category(p_id=''){
        var type = 'ajax_list';
        $.ajax({
            type: "POST",
            url: "{{route('get_parent_vessel_category')}}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                $('#parent_category_id').html(resp);
            }
        });
    }
</script>