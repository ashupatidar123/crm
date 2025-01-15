<script type="text/javascript">
    function get_ajax_country(p_id='',html_id=''){
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

    function get_ajax_state(country_id='',html_id='',state_id=''){
        var type = 'ajax_list';
        $.ajax({
            type: "POST",
            url: "{{url('get_ajax_state')}}",
            data: {country_id,type,state_id},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                //$('#city_id').empty();
                //$('#city_id').html('<option value="" hidden="">Select city</option>');
                $('#'+html_id).html(resp);
            }
        });
    }

    function get_ajax_city(state_id='',html_id='',city_id=''){
        var type = 'ajax_list';
        
        $.ajax({
            type: "POST",
            url: "{{url('get_ajax_city')}}",
            data: {state_id,type,city_id},
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
                { data: 'action' },
                { data: 'name' },
                { data: 'iso3' },
                { data: 'numeric_code' },
                { data: 'capital' },
                { data: 'currency' },
                { data: 'created_at', type: 'date' }
            ],
            "order": [[2, 'ASC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            //"dom": 'Bfrtip',
            "buttons": ['copy', 'csv', 'excel', 'pdf', 'print',"colvis"],
            "columnDefs": [{"targets": [0],"orderable": false}]
        });
    }

    function country_edit(p_id=''){
        $('.addEditLoader_'+p_id).html('<i class="fa fa-spinner fa-spin"></i>');
        $('.addEditLoader_'+p_id).attr('disabled',true);

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
                    $('#p_id').val(rep.id);
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
                $('.addEditLoader_'+p_id).html('<i class="fa fa-edit"></i>');
                $('.addEditLoader_'+p_id).attr('disabled',false);
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
                $('.deleteLoader_'+p_id).html('<i class="fa fa-spinner fa-spin"></i>');
                $('.deleteLoader_'+p_id).attr('disabled',true);

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
                        $('.deleteLoader_'+p_id).html('<i class="fa fa-trash"></i>');
                        $('.deleteLoader_'+p_id).attr('disabled',false);
                    }
                });
            }
        });        
    }

    $("#update_country").on("click",function (event) {
        event.preventDefault();
        $('#nameError, #iso3Error, #numeric_codeError, #phonecodeError').html('');
        var check = 0;

        if($('#name').val() == ''){
            var check = 1;
            $('#nameError').html('This field is required');
        }
        if($('#iso3').val() == ''){
            var check = 1;
            $('#iso3Error').html('This field is required');
        }
        if($('#numeric_code').val() == ''){
            var check = 1;
            $('#numeric_codeError').html('This field is required');
        }
        if($('#phonecode').val() == ''){
            var check = 1;
            $('#phonecodeError').html('This field is required');
        }
        if(check == 1){
            return false;
        }

        $('.show_message').html('');
        $('#update_country').html('Loading...');
        $('#update_country').attr('disabled',true);
        var form = $("#countryFormId");
        $.ajax({
            type: "POST",
            url: "{{url('master/region/country_update')}}",
            data: form.serialize(),
            success: function (resp) {
                $('#update_country').html('<i class="fa fa-send"></i> Submit');
                $('#update_country').attr('disabled',false);
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
                { data: 'action' },
                { data: 'name' },
                { data: 'country_name' },
                { data: 'iso2' },
                { data: 'country_code' },
                { data: 'created_at', type: 'date' }
            ],
            "order": [[2, 'ASC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            //"dom": 'Bfrtip',
            "buttons": ['copy', 'csv', 'excel', 'pdf', 'print',"colvis"],
            "columnDefs": [{"targets": [0,3],"orderable": false}]
        });
    }

    function state_edit(p_id=''){
        $('.addEditLoader_'+p_id).html('<i class="fa fa-spinner fa-spin"></i>');
        $('.addEditLoader_'+p_id).attr('disabled',true);

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
                    $('#p_id').val(rep.id);
                    $('#name').val(rep.name);
                    $('#country_id').val(rep.country_id);
                    $('#iso2').val(rep.iso2);
                    $('#country_code').val(rep.country_code);
                    $("#stateModal").modal();
                    
                    var type = 'ajax_list';
                    var p_id = rep.country_id;
                    $.ajax({
                        type: "POST",
                        url: "{{url('get_ajax_country')}}",
                        data: {p_id,type},
                        headers: {
                            'X-CSRF-TOKEN': csrf_token
                        },
                        success: function (resp) {
                            $('#country_id').html(resp);
                        }
                    });

                }else{
                    swal_error('Something went wrong');
                    return false;
                }
                $('.addEditLoader_'+p_id).html('<i class="fa fa-edit"></i>');
                $('.addEditLoader_'+p_id).attr('disabled',false);
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
        $('#update_state').attr('disabled',true);

        var form = $("#stateFormId");
        $.ajax({
            type: "POST",
            url: "{{url('master/region/state_update')}}",
            data: form.serialize(),
            success: function (resp) {
                $('#update_state').html('<i class="fa fa-send"></i> Submit');
                $('#update_state').attr('disabled',false);
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
                $('.deleteLoader_'+p_id).html('<i class="fa fa-spinner fa-spin"></i>');
                $('.deleteLoader_'+p_id).attr('disabled',true);
                
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
                        $('.deleteLoader_'+p_id).html('<i class="fa fa-trash"></i>');
                        $('.deleteLoader_'+p_id).attr('disabled',false);
                    }
                }); 
            }
        });        
    }

    /* city section */
    function city_data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit   = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{url("master/region/city_list")}}',
                type: 'GET',
                data:{start_limit,end_limit},
                headers: {
                    'X-CSRF-TOKEN': csrf_token
                },
            },
            columns: [
                { data: 'sno' },
                { data: 'action' },
                { data: 'name' },
                { data: 'country_name' },
                { data: 'state_name' },
                { data: 'state_code' },
                { data: 'created_at', type: 'date' }
            ],
            "order": [[2, 'ASC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            //"dom": 'Bfrtip',
            "buttons": ['copy', 'csv', 'excel', 'pdf', 'print',"colvis"],
            "columnDefs": [{"targets": [0,3,4],"orderable": false}]
        });
    }

    function city_edit(p_id=''){
        $('.addEditLoader_'+p_id).html('<i class="fa fa-spinner fa-spin"></i>');
        $('.addEditLoader_'+p_id).attr('disabled',true);

        var type = 'ajax_single';
        $.ajax({
            type: "POST",
            url: "{{url('get_ajax_city')}}",
            data: {p_id,type},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            dataType:'JSON',
            success: function (resp) {
                if(resp.data != ''){
                    var rep = resp.data;
                    $('#p_id').val(rep.id);
                    $('#name').val(rep.name);
                    $('#country_id').val(rep.country_id);
                    $('#state_id').val(rep.state_id);
                    $('#state_code').val(rep.state_code);
                    $("#cityModal").modal();

                    var type = 'ajax_list';
                    var p_id = rep.country_id;
                    $.ajax({
                        type: "POST",
                        url: "{{url('get_ajax_country')}}",
                        data: {p_id,type},
                        headers: {
                            'X-CSRF-TOKEN': csrf_token
                        },
                        success: function (resp) {
                            $('#country_id').html(resp);
                        }
                    });

                    var country_id = rep.country_id;
                    var state_id = rep.state_id
                    $.ajax({
                        type: "POST",
                        url: "{{url('get_ajax_state')}}",
                        data: {country_id,type,state_id},
                        headers: {
                            'X-CSRF-TOKEN': csrf_token
                        },
                        success: function (resp) {
                            $('#state_id').html(resp);
                        }
                    });

                }else{
                    swal_error('Something went wrong');
                    return false;
                }
                $('.addEditLoader_'+p_id).html('<i class="fa fa-edit"></i>');
                $('.addEditLoader_'+p_id).attr('disabled',false);
            }
        });   
    }

    $("#update_city").on("click",function (event) {
        event.preventDefault();
        $('#nameError, #country_idError, #state_idError, #state_codeError').html('');
        var check = 0;
        if($('#name').val() == ''){
            var check = 1;
            $('#nameError').html('State name is required');
        }
        if($('#country_id').val() == ''){
            var check = 1;
            $('#country_idError').html('Country name is required');
        }
        if($('#state_id').val() == ''){
            var check = 1;
            $('#state_idError').html('State name is required');
        }
        if($('#state_code').val() == ''){
            var check = 1;
            $('#state_codeError').html('State code is required');
        }
        if(check == 1){
            return false;
        }

        $('.show_message').html('');
        $('#update_city').html('Loading...');

        var form = $("#cityFormId");
        $.ajax({
            type: "POST",
            url: "{{url('master/region/city_update')}}",
            data: form.serialize(),
            success: function (resp) {
                $('#update_city').html('<i class="fa fa-send"></i> Submit');
                if(resp.status == 'success'){
                    city_data_table_list();
                    $('#cityModal').modal('hide');
                    swal_success(resp.message);
                }else{
                    swal_error(resp.message);
                }
            }
        });
    });

    function city_delete(p_id){
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
                    url: "{{url('master/region/city_delete')}}",
                    data: {p_id},
                    headers: {
                        'X-CSRF-TOKEN': csrf_token
                    },
                    dataType:"JSON",
                    success: function (resp) {
                        if(resp.status == 'success'){
                            city_data_table_list();
                            swal_success(resp.message,1800);
                        }else{
                            swal_error(resp.message,1800); 
                        }
                        $('.deleteLoader_'+p_id).html('<i class="fa fa-trash"></i>');
                        $('.deleteLoader_'+p_id).attr('disabled',false);
                    }
                });
            }
        });        
    }

    function region_active_inactive(p_id='',type='',tbl=''){
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
                    url: "{{url('master/region/region_active_inactive')}}",
                    data: {p_id,type,tbl},
                    headers: {
                        'X-CSRF-TOKEN': csrf_token
                    },
                    dataType:"JSON",
                    success: function (resp) {
                        if(resp.status == 'success'){
                            if(tbl == 'country'){
                                country_data_table_list();
                            }
                            else if(tbl == 'state'){
                                state_data_table_list();
                            }
                            else if(tbl == 'city'){
                                city_data_table_list();
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
</script>