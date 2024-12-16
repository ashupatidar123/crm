<script type="text/javascript">
    function get_ajax_country(p_id='',html_id=''){
        $('#city').find('option').not(':first').remove();
        var type = 'ajax_list';
        $.ajax({
            type: "POST",
            url: "{{url('get_ajax_country')}}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                $('#'+html_id).html(resp);  
            }
        });
    }

    function get_ajax_state(country_id='',html_id=''){
        var type = 'ajax_list';
        $.ajax({
            type: "POST",
            url: "{{url('get_ajax_state')}}",
            data: {country_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                $('#'+html_id).html(resp);
                $('#city').empty();
                $('#city').html('<option value="" hidden="">Select city</option>');
            }
        });
    }

    function get_ajax_city(state_id='',html_id=''){
        var type = 'ajax_list';
        $.ajax({
            type: "POST",
            url: "{{url('get_ajax_city')}}",
            data: {state_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                $('#'+html_id).html(resp);
            }
        });
    }

    /* country section */
    function country_data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit   = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{url("master/region/country_list")}}',
                type: 'GET',
                data:{start_limit,end_limit},
                headers: {
                    'X-CSRF-TOKEN': csrf_token
                },
            },
            columns: [
                { data: 'sno' },
                { data: 'id' },
                { data: 'name' },
                { data: 'iso3' },
                { data: 'numeric_code' },
                { data: 'capital' },
                { data: 'currency' },
                { data: 'created_at', type: 'date' },
                { data: 'action' }
            ],
            "order": [[2, 'ASC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "dom": 'Bfrtip',
            "buttons": ['copy', 'csv', 'excel', 'pdf', 'print',"colvis"],
            "columnDefs": [{"targets": [0,8],"orderable": false}]
        });
    }

    function country_edit(p_id=''){
        var type = 'ajax_single';
        $.ajax({
            type: "POST",
            url: "{{url('get_ajax_country')}}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                if(resp.data != ''){
                    var rep = resp.data;
                    $('#p_id').val(p_id);
                    $('#name').val(rep.name);
                    $('#iso3').val(rep.iso3);
                    $('#numeric_code').val(rep.numeric_code);
                    $('#iso2').val(rep.iso2);
                    $('#phonecode').val(rep.phonecode);
                    $('#capital').val(rep.capital);
                    $('#currency').val(rep.currency);
                    $('#currency_name').val(rep.currency_name);
                    $("#countryModal").modal();
                }else{
                    swal_error('Something went wrong');
                    return false;
                }
            }
        });   
    }

    function country_delete(p_id){
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
                    url: "{{url('master/region/country_delete')}}",
                    data: {p_id},
                    headers: {
                        'X-CSRF-TOKEN': csrf_token
                    },
                    dataType:"JSON",
                    success: function (resp) {
                        if(resp.status == 'success'){
                            country_data_table_list();
                            swal_success(resp.message,1800);
                        }else{
                            swal_error(resp.message,1800); 
                        }
                    }
                });
            }
        });        
    }

    $("#update_country").on("click",function (event) {
        event.preventDefault();
        $('#nameError, #mobileError, #emailError, #passwordError').html('');
        var check = 0;
        if($('#name').val() == ''){
            var check = 1;
            $('#nameError').html('Name is required');
        }
        if($('#mobile').val() == ''){
            var check = 1;
            $('#mobileError').html('Mobile is required');
        }
        if($('#email').val() == ''){
            var check = 1;
            $('#emailError').html('Email is required');
        }
        if($('#password').val() == ''){
            var check = 1;
            $('#passwordError').html('Password is required');
        }
        if(check == 1){
            return false;
        }

        $('.show_message').html('');
        $('#update_country').html('Loading...');

        var form = $("#countryFormId");
        $.ajax({
            type: "POST",
            url: "{{url('master/region/country_update')}}",
            data: form.serialize(),
            success: function (resp) {
                $('#update_country').html('Submit');
                if(resp.status == 'success'){
                    country_data_table_list();
                    $('#countryModal').modal('hide');
                    swal_success(resp.message);
                }else{
                    swal_error(resp.message);
                }
            }
        });
    });
    
    /* state section */
    function state_data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit   = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{url("master/region/state_list")}}',
                type: 'GET',
                data:{start_limit,end_limit},
                headers: {
                    'X-CSRF-TOKEN': csrf_token
                },
            },
            columns: [
                { data: 'sno' },
                { data: 'id' },
                { data: 'name' },
                { data: 'country_name' },
                { data: 'iso2' },
                { data: 'country_code' },
                { data: 'created_at', type: 'date' },
                { data: 'action' }
            ],
            "order": [[2, 'ASC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "dom": 'Bfrtip',
            "buttons": ['copy', 'csv', 'excel', 'pdf', 'print',"colvis"],
            "columnDefs": [{"targets": [0,3,7],"orderable": false}]
        });
    }

    function state_edit(p_id=''){
        var type = 'ajax_single';
        $.ajax({
            type: "POST",
            url: "{{url('get_ajax_state')}}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                if(resp.data != ''){
                    var rep = resp.data;
                    $('#p_id').val(p_id);
                    $('#name').val(rep.name);
                    $('#country_id').val(rep.country_id);
                    $('#iso2').val(rep.iso2);
                    $('#country_code').val(rep.country_code);
                    $("#stateModal").modal();
                }else{
                    swal_error('Something went wrong');
                    return false;
                }
            }
        });   
    }

    $("#update_state").on("click",function (event) {
        event.preventDefault();
        $('#nameError, #country_idError, #country_codeError, #iso2Error').html('');
        var check = 0;
        if($('#name').val() == ''){
            var check = 1;
            $('#nameError').html('State name is required');
        }
        if($('#country_id').val() == ''){
            var check = 1;
            $('#country_idError').html('Country name is required');
        }
        if($('#country_code').val() == ''){
            var check = 1;
            $('#country_codeError').html('Country code is required');
        }
        if($('#iso2').val() == ''){
            var check = 1;
            $('#iso2Error').html('ISO 2 is required');
        }
        if(check == 1){
            return false;
        }

        $('.show_message').html('');
        $('#update_state').html('Loading...');

        var form = $("#stateFormId");
        $.ajax({
            type: "POST",
            url: "{{url('master/region/state_update')}}",
            data: form.serialize(),
            success: function (resp) {
                $('#update_state').html('Submit');
                if(resp.status == 'success'){
                    state_data_table_list();
                    $('#stateModal').modal('hide');
                    swal_success(resp.message);
                }else{
                    swal_error(resp.message);
                }
            }
        });
    });

    function state_delete(p_id){
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
                    url: "{{url('master/region/state_delete')}}",
                    data: {p_id},
                    headers: {
                        'X-CSRF-TOKEN': csrf_token
                    },
                    dataType:"JSON",
                    success: function (resp) {
                        if(resp.status == 'success'){
                            state_data_table_list();
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