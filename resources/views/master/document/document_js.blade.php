<script type="text/javascript">
    function document_data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route("document.list")}}',
                type: 'GET',
                data:{start_limit,end_limit},
            },
            columns: [
                { data: 'sno' },
                { data: 'action' },
                { data: 'category_name' },
                { data: 'document_type' },
                { data: 'parent_document' },
                { data: 'description' },
                { data: 'created_at', type: 'date' }
            ],
            "order": [[6, 'DESC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "columnDefs": [{"targets": [0,4],"orderable": false}]
        });
    }  

    function add_edit_document(p_id='',type=''){
        $('#addSubmitButton').html('<i class="fa fa-send"></i> Submit');
        $('#addSubmitButton').attr('disabled',false);
        if(type == 'add'){
            $('#p_id, #category_name, #document_type, #description').val('');
            //$('#parent_category_id').val('').trigger('change');
            $("#addModal").modal();
            return false;
        }
        $.ajax({
            type: "POST",
            url: "{{ route('document.edit') }}",
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
                    get_parent_document(rep.parent_category_id);
                }else{
                    swal_error('Something went wrong');
                    return false;
                }
            }
        });  
    }

    $("#addSubmitButton").on("click",function (event) {
        event.preventDefault();
        $('#category_nameError, #document_typeError, #descriptionError').html('');
        var check = 0;

        if($('#category_name').val() == ''){
            var check = 1;
            $('#category_nameError').html('This field is required');
        }
        if($('#document_type').val() == ''){
            var check = 1;
            $('#document_typeError').html('This field is required');
        }
        if($('#is_active').val() == ''){
            var check = 1;
            $('#is_activeError').html('This field is required');
        }
        if(check == 1){
            return false;
        }

        $('.show_message').html('');
        $('#addSubmitButton').html('Loading...');
        $('#addSubmitButton').attr('disabled',true);
        
        var formData = new FormData($("#addFormId")[0]);
        $.ajax({
            type: "POST",
            url: "{{ route('document.store') }}",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                $('#addSubmitButton').html('<i class="fa fa-send"></i> Submit');
                $('#addSubmitButton').attr('disabled',false);
                if(resp.status == 'success'){
                    document_data_table_list();
                    $('#addModal').modal('hide');
                    swal_success(resp.message);
                }else{
                    swal_error(resp.message);
                }
            }
        });
    });

    function get_parent_document(p_id=''){
        var selectedOption = $('#document_type').find('option:selected');
        var document_type = selectedOption.val();
        
        var type = 'ajax_list';
        $.ajax({
            type: "POST",
            url: "{{route('get_parent_document')}}",
            data: {p_id,type,document_type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                $('#parent_category_id').html(resp);
            }
        });
    }
</script>