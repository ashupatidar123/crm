@extends('layouts.header')

@section('title') Register @endsection

@section('content')
<div class="container">
    <h2>Register</h2>
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
            var form = $("#formId");
            $.ajax({
                type: "POST",
                url: "{{url('register')}}",
                data: form.serialize(),
                success: function (data) {
                    alert(data);
                    //alert("Form Submitted Successfully");
                }
            });
        });
    });
</script>
@endsection