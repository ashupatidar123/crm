@extends('layouts.header')

@section('title') Register @endsection

@section('content')
<div class="container">
    <h2>Register</h2>
    <h3 id="show_message"></h3>
    <form method="POST" id="formId">
        @csrf
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" placeholder="Enter name" name="name">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" placeholder="Enter password" name="password">
        </div>
        <button type="button" class="btn btn-primary" id="submitButton">Submit</button>
    </form>
</div>
<script>
    $(document).ready(function () {
        $("#submitButton").click(function (event) {
            event.preventDefault();
            
            $('#show_message').html('');
            $('#submitButton').html('Loading...');

            var form = $("#formId");
            $.ajax({
                type: "POST",
                url: "{{url('register')}}",
                data: form.serialize(),
                success: function (resp) {
                    $('#submitButton').html('Submit');
                    $('#show_message').html(resp.message);
                    if(resp.status == 'success'){
                        $('#formId')[0].reset();
                        window.setTimeout(function(){
                            window.location.href = "{{url('login')}}";
                        },3000);
                    }
                }
            });
        });
    });
</script>
@endsection