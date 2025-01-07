<script type="text/javascript">
    function vessel_data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route("vessel.list")}}',
                type: 'GET',
                data:{start_limit,end_limit},
            },
            columns: [
                { data: 'sno' },
                { data: 'action' },
                { data: 'vessel_name' },
                { data: 'technical_manager' },
                { data: 'registered_owner' },
                { data: 'vessel_email' },
                { data: 'created_at', type: 'date' }
            ],
            "order": [[6, 'DESC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "columnDefs": [{"targets": [0],"orderable": false}]
        });
    }  

    function add_edit_vessel(p_id='',type=''){
        $('#addEditSubmitButton').html('Submit');
        $('#addEditSubmitButton').attr('disabled',false);
        $('.remove_text').html('');
        if(type == 'add'){
            $('#addFormId')[0].reset();
            $('#p_id, #vessel_name, #technical_manager').val('');
            $("#addEditModal").modal();
            return false;
        }
        $('.addEditLoader_'+p_id).html('<i class="fa fa-spinner fa-spin"></i>');
        $('.addEditLoader_'+p_id).attr('disabled',true);

        $.ajax({
            type: "POST",
            url: "{{ url('vessel/vessel_edit') }}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                
                if(resp.data != ''){
                    var rep = resp.data;
                    $('#p_id').val(rep.id);
                    $('#vessel_name').val(rep.vessel_name);
                    $('#technical_manager').val(rep.technical_manager);
                    $('#registered_owner').val(rep.registered_owner);
                    $('#hull_no').val(rep.hull_no);
                    $('#master').val(rep.master);
                    $('#vessel_email').val(rep.vessel_email);
                    $('#imo_no').val(rep.imo_no);
                    $('#category').val(rep.category);
                    $('#type').val(rep.type);
                    $('#delivery_date').val(rep.delivery_date);
                    $('#dead_weight').val(rep.dead_weight);
                    $('#main_engine').val(rep.main_engine);
                    $('#bhp').val(rep.bhp);
                    $('#flag').val(rep.flag);
                    $('#grt').val(rep.grt);
                    $('#nrt').val(rep.nrt);
                    $('#cy_number').val(rep.cy_number);
                    $('#de_number').val(rep.de_number);
                    $('#sg_number').val(rep.sg_number);
                    $('#yard').val(rep.yard);
                    $('#sid').val(rep.sid);
                    $('#is_active').val(rep.is_active);
                    $('#set_vessel_image').html(rep.vessel_image);
                    $('#description').val(rep.description);
                    $("#addEditModal").modal();
                    $('#addEditLoader_'+p_id).html('<i class="fa fa-edit"></i>');
                }else{
                    swal_error('Something went wrong');
                    return false;
                }
                $('.addEditLoader_'+p_id).html('<i class="fa fa-edit"></i>');
                $('.addEditLoader_'+p_id).attr('disabled',false);
            }
        });  
    }

    var save_image_name = myDropzone = '';
    $(document).ready(function() {
        Dropzone.autoDiscover = false;
        var myDropzone = new Dropzone("#myDropzone", {
            url: "{{url('vessel/vessel_file_upload')}}",
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            autoProcessQueue: false,
            addRemoveLinks: true,
            paramName: "vessel_image",
            dictDefaultMessage: "Drag files here or click to upload",
            maxFiles: 1,
            maxFilesize: 2,
            acceptedFiles: ".jpg,.jpeg,.png,.gif,.pdf",
            init: function() {
                var myDropzone = this;
                this.on("success", function(vessel_image, response) {
                    var save_image_name = response;
                    save_vessel_data(save_image_name);
                });
            }
        });
    
        $("#addEditSubmitButton").on("click",function (event) {
            event.preventDefault();
            $('.remove_text').html('');
            var check = 0;

            if($('#vessel_name').val() == ''){
                var check = 1;
                $('#vessel_nameError').html('This field is required');
            }
            if($('#technical_manager').val() == ''){
                var check = 1;
                $('#technical_managerError').html('This field is required');
            }
            if($('#registered_owner').val() == ''){
                var check = 1;
                $('#registered_ownerError').html('This field is required');
            }
            if($('#hull_no').val() == ''){
                var check = 1;
                $('#hull_noError').html('This field is required');
            }
            if($('#master').val() == ''){
                var check = 1;
                $('#masterError').html('This field is required');
            }
            if($('#vessel_email').val() == ''){
                var check = 1;
                $('#vessel_emailError').html('This field is required');
            }
            if($('#category').val() == ''){
                var check = 1;
                $('#categoryError').html('This field is required');
            }
            if($('#type').val() == ''){
                var check = 1;
                $('#typeError').html('This field is required');
            }
            if($('#flag').val() == ''){
                var check = 1;
                $('#flagError').html('This field is required');
            }
            if($('#sid').val() == ''){
                var check = 1;
                $('#sidError').html('This field is required');
            }
            if($('#is_active').val() == ''){
                var check = 1;
                $('#is_activeError').html('This field is required');
            }
            if(check == 1){
                //return false;
            }

            $('.show_message').html('');
            $('#addEditSubmitButton').html('<i class="fa fa-spinner fa-spin"></i> Loading');
            $('#addEditSubmitButton').attr('disabled',true);
            
            if(myDropzone.files != ''){
                if(myDropzone.files.length > 0) {
                    myDropzone.processQueue(); 
                }
            }
            else{
                alert();
                save_vessel_data('');
            }
            return false;
        });
    });

    function save_vessel_data(save_image_name=''){
        var formData = new FormData($("#addFormId")[0]);
        formData.append('vessel_image',save_image_name);
        
        $.ajax({
            type: "POST",
            url: "{{ route('vessel.store') }}",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                $('#addEditSubmitButton').html('Submit');
                $('#addEditSubmitButton').attr('disabled',false);
                if(resp.status == 'success'){
                    vessel_data_table_list();
                    $('#addEditModal').modal('hide');
                    swal_success(resp.message);
                }else{
                    swal_error(resp.message);
                }
            }
        });
    }

    $('#delivery_date').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
    });
</script>