<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'SEA Transport Register')</title>
        <!-- Google Font: Source Sans Pro -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ url('public/assets/plugins/fontawesome-free/css/all.min.css') }}">
        <!-- icheck bootstrap -->
        <link rel="stylesheet" href="{{ url('public/assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ url('public/assets/dist/css/adminlte.min.css') }}">
        
        <script src="{{ url('public/assets/plugins/jquery/jquery.min.js') }}"></script>
        <script src="{{ url('public/assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    </head>
    <body class="hold-transition register-page">
        <div class="register-box">
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @elseif(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <div class="card card-outline card-primary">
                <div class="card-header text-center">
                    <a href="{{url('/')}}" class="h1"><b>SEA</b>TRANSPORT</a>
                </div>
                
                <div class="card-body">
                    <p class="login-box-msg">Register a new membership</p>
                    <p class="login-box-msg" id="show_message"></p>
                    <form method="POST" id="formId">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="text" id="name" class="form-control" placeholder="Full name" name="name">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="email" id="email" class="form-control" placeholder="Email" name="email">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" id="password" class="form-control" placeholder="Password" name="password">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" id="cpassword" class="form-control" placeholder="Retype password" name="cpassword">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8">
                                <div class="icheck-primary">
                                    <input type="checkbox" id="agreeTerms" name="terms" value="agree">
                                    <label for="agreeTerms">
                                    I agree to the <a href="#">terms</a>
                                    </label>
                                </div>
                            </div>
                            <div class="col-4">
                                <button type="submit" class="btn btn-primary btn-block" id="submitButton">Register</button>
                            </div>
                        </div>
                    </form>
                    <div class="social-auth-links text-center">
                        <a href="#" class="btn btn-block btn-primary">
                        <i class="fab fa-facebook mr-2"></i>
                        Sign up using Facebook
                        </a>
                        <a href="#" class="btn btn-block btn-danger">
                        <i class="fab fa-google-plus mr-2"></i>
                        Sign up using Google+
                        </a>
                    </div>
                    <a href="login.html" class="text-center">I already have a membership</a>
                </div>
                <!-- /.form-box -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.register-box -->
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
    </body>
</html>