<!-- Modal -->
<section class="content">
    <div class="modal fade" id="addEditApprisalModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Vessle Apprisal</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="addEditApprisalFormId">
                        @csrf    
                        <div class="row">
                            <input type="hidden" name="p_id" id="p_id">
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header card_header_color"><h3 class="card-title">Add/Edit Apprisal</h3>
                                    </div>
                                    <div class="show_message"></div>
                                    <div class="card-body row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Rating<span class="text-danger">*</span></label>
                                                <select class="form-control" name="rating" id="rating" required>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                </select>
                                                <p class="text-danger remove_error" id="ratingError"></p>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Apprisal Date<span class="text-danger">*</span></label>
                                                <input type="text" name="apprisal_date" id="apprisal_date" class="form-control apprisal_date" placeholder="dd/mm/yyyy" readonly>
                                                <p class="remove_error text-danger" id="apprisal_dateError"></p>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Is Active<span class="text-danger">*</span></label>
                                                <select class="form-control" name="is_active" id="is_active" required>
                                                    <option value="1">Active</option>
                                                    <option value="2">In-Active</option>
                                                </select>
                                                <p class="text-danger remove_error" id="is_activeError"></p>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Specific Strength</label>
                                                <input type="text" name="specific_strength" id="specific_strength" class="form-control" placeholder="Enter Specific Strength">
                                                <p class="text-danger remove_error" id="specific_strengthError"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Area of Improvemnt</label>
                                                <input type="text" name="area_of_improvement" id="area_of_improvement" class="form-control" placeholder="Enter Area of Improvemnt">
                                                <p class="text-danger remove_error" id="area_of_improvementError"></p>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Additional Notes</label>
                                                <textarea name="additional_notes" id="additional_notes" class="form-control" placeholder="Enter additional notes" required></textarea>
                                                <p class="text-danger remove_error" id="additional_notesError"></p>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Description (Remark)</label>
                                                <textarea name="apprisal_description" id="apprisal_description" class="form-control" placeholder="Enter description" required></textarea>
                                                <p class="text-danger remove_error" id="apprisal_descriptionError"></p>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <button id="addEditApprisalSubmitButton" type="submit" class="btn btn-primary"><i class="fa fa-send-o"></i> Submit</button>
                                                <button type="button" class="close1 btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    function add_edit_apprisal(p_id='',type=''){
        $('#addEditApprisalSubmitButton').html('<i class="fa fa-send"></i> Submit');
        $('#addEditApprisalSubmitButton').attr('disabled',false);
        if(type == 'add'){
            $("#addEditApprisalFormId")[0].reset();
            $('#p_id, #description').val('');
            $("#addEditApprisalModal").modal();
            return false;
        }
        $('.addEditLoader_'+p_id).html('<i class="fa fa-spinner fa-spin"></i>');
        $('.addEditLoader_'+p_id).attr('disabled',true);
        
        $.ajax({
            type: "POST",
            url: "{{ route('vessel_apprisal_list_edit') }}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                if(resp.data != ''){
                    var rep = resp.data;
                    $('#p_id').val(rep.id);
                    $('#apprisal_date').val(rep.apprisal_date);
                    $('#rating').val(rep.rating).trigger('change');
                    $('#specific_strength').val(rep.specific_strength);
                    $('#area_of_improvement').val(rep.area_of_improvement);
                    $('#additional_notes').val(rep.additional_notes);
                    $('#apprisal_description').val(rep.description);
                    $('#is_active').val(rep.is_active);
                    $("#addEditApprisalModal").modal();
                }else{
                    swal_error('Something went wrong');
                    return false;
                }
                $('.addEditLoader_'+p_id).html('<i class="fa fa-edit"></i>');
                $('.addEditLoader_'+p_id).attr('disabled',false);
            }
        });  
    }

    $("#addEditApprisalSubmitButton").on("click",function (event) {
            event.preventDefault();
        $('.remove_text').html('');
        var user_id = $('#user_id').val();
        var check = 0;
        
        if($('#rating').val() == ''){
            var check = 1;
            $('#ratingError').html('This field is required');
        }
        if($('#apprisal_date').val() == ''){
            var check = 1;
            $('#apprisal_dateError').html('This field is required');
        }
        if($('#user_id').val() < 1){
            var check = 1;
            swal_error('Invalid user');
            return false;
        }
        
        if(check == 1){
            swal_error('Some fields are required');
            return false;
        }

        $('#addEditApprisalSubmitButton').html('<i class="fa fa-spinner fa-spin"></i> Loading...');
        $('#addEditApprisalSubmitButton').attr('disabled',true);

        var formData = new FormData($("#addEditApprisalFormId")[0]);
        formData.append('user_id',user_id);
        $.ajax({
            type: "POST",
            url: "{{route('add_update_vessel_apprisal')}}",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function(resp) {
                $('#addEditApprisalSubmitButton').html('<i class="fa fa-send"></i> Submit');
                $('#addEditApprisalSubmitButton').attr('disabled',false);
                if(resp.status == 'success'){
                    swal_success(resp.s_msg);
                    $("#addEditApprisalModal").modal('hide');
                    user_change_tab('vessel_apprisal');
                }else{
                    swal_error(resp.s_msg);
                }
            }
        });

    });

    $('.apprisal_date').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
    });
</script>

