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
        $.ajax({
            type: "POST",
            url: "{{url('get_ajax_state')}}",
            data: {country_id},
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
        $.ajax({
            type: "POST",
            url: "{{url('get_ajax_city')}}",
            data: {state_id},
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function (resp) {
                $('#'+html_id).html(resp);
            }
        });
    }

    function data_table_list(){
        $('#tableList').DataTable().clear().destroy();
        var start_limit = ($('#start_limit').val() != '')?$('#start_limit').val():0;
        var end_limit   = $('#end_limit').val();
        var end_limit = (end_limit > 0)?end_limit:10;
        var pageLength = (end_limit > 0)?end_limit:10;
        
        $('#tableList').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{url("region/country_list")}}',
                type: 'GET',
                data:{start_limit,end_limit},
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'iso3' },
                { data: 'numeric_code' },
                { data: 'capital' },
                { data: 'currency' },
                { data: 'created_at', type: 'date' },
                { data: 'action' }
            ],
            "order": [[1, 'ASC']],
            "lengthMenu": [10,25,75,50,100,500,550,1000],
            "pageLength": pageLength,
            "responsive": true,
            "dom": 'Bfrtip',
            "buttons": ['copy', 'csv', 'excel', 'pdf', 'print',"colvis"],
            "columnDefs": [{"targets": [7],"orderable": false}]
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

    $(document).ready(function(){
        $("#update_country").click(function (event) {
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
                //return false;
            }

            $('.show_message').html('');
            $('#update_country').html('Loading...');

            var form = $("#countryFormId");
            $.ajax({
                type: "POST",
                url: "{{url('region/update_country')}}",
                data: form.serialize(),
                success: function (resp) {
                    $('#update_country').html('Submit');
                    if(resp.status == 'success'){
                        data_table_list();
                        $('#countryModal').modal('hide');
                        swal_success(resp.message);
                    }else{
                        swal_error(resp.message);
                    }
                }
            });
        });
    });
    
</script>