@extends('layouts.head')

@section('title') Registration @endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <div class="show_message"></div>
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @elseif(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Registration</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <form method="POST" id="formId">
                    @csrf    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <div class="card-header card_header_color">
                                    <h3 class="card-title">Registration</h3>
                                </div>
                                <div class="card-body row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Name<span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name" class="form-control"  placeholder="Enter name">
                                            <p class="text-danger" id="nameError"></p>
                                        </div>
                                        <div class="form-group">
                                            <label>Email<span class="text-danger">*</span></label>
                                            <input type="email" name="email" id="email" class="form-control" placeholder="Enter email">
                                            <p class="text-danger" id="emailError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mobile">Mobile<span class="text-danger">*</span></label>
                                            <input type="number" name="mobile" id="mobile" class="form-control" placeholder="Enter mobile">
                                            <p class="text-danger" id="mobileError"></p>
                                        </div>
                                        <div class="form-group">
                                            <label>Password<span class="text-danger">*</span></label>
                                            <input type="text" name="password" id="password" class="form-control" placeholder="Enter password" minlength="6">
                                            <p class="text-danger" id="passwordError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <button id="submitButton" type="submit" class="btn btn-primary">Submit</button>
                                            <button type="button" class="btn btn-danger" onclick="return referesh_form();">Refresh</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
    <script>
        $(document).ready(function () {
            $("#submitButton").click(function (event) {
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
                $('#submitButton').html('Loading...');

                var form = $("#formId");
                $.ajax({
                    type: "POST",
                    url: "{{url('register')}}",
                    data: form.serialize(),
                    success: function (resp) {
                        $('#submitButton').html('Submit');
                        $('.show_message').html(resp.message);
                        if(resp.status == 'success'){
                            $('#formId')[0].reset();
                            window.setTimeout(function(){
                                //window.location.href = "{{url('dashboard')}}";
                            },3000);
                        }
                    }
                });
            });
        });
    </script>

    @include('script.comman_js')
@endsection