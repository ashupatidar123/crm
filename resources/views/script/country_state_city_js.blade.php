<script type="text/javascript">
    var token = "{{ csrf_token() }}";
    function get_ajax_country(p_id='',html_id=''){
        $('#city').find('option').not(':first').remove();
        $.ajax({
            type: "POST",
            url: "{{url('get_ajax_country')}}",
            data: {p_id},
            headers: {
                'X-CSRF-TOKEN': token
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
                'X-CSRF-TOKEN': token
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
                'X-CSRF-TOKEN': token
            },
            success: function (resp) {
                $('#'+html_id).html(resp);
            }
        });
    }
    
</script>