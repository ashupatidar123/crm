<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'WELCOME TO CRM')</title>
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
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <style type="text/css">
            body {
              background-image: url("{{url('public/images/img/login-backgrond.jpg')}}");
            }
            .logo-img{
                height: 100px;
                margin-left: 35px;
            }
        </style>
    </head>
    <body class="hold-transition login-page">
        <div class="login-box">
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
                    <a href="{{url('/')}}" class="h1"><b>WELCOME TO CRM</a>
                </div>
                <div class="card-body">
                    <img class="logo-img" src="{{url('public/images/img/sts-marine.png')}}">
                    <p class="login-box-msg">STS Marine Management Pvt. Ltd.</p>
                    
                    <form action="{{url('/login')}}" method="post">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="email" id="email" class="form-control" placeholder="Email or username*" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                        <p id="usernameError" class="text-danger"></p>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password*" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <p id="passwordError" class="text-danger"></p>
                        <div class="input-group mb-3">
                            <div class="g-recaptcha" data-sitekey="6LeTj4sqAAAAAI8296xKQZveu3-qttAbuUv3DVjc"></div>
                            <p id="googleError" class="text-danger"></p>
                        </div>
                        <div class="row">
                            <div class="col-8">
                                <div class="icheck-primary">
                                    <input type="checkbox" id="remember">
                                    <label for="remember">Remember Me</label>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-4">
                                <button type="submit" class="btn btn-primary btn-block" onclick="return user_login();">Sign In</button>
                            </div>
                            <!-- /.col -->
                        </div>
                    </form>
                    <p class="mb-1">
                        <a href="{{url('/forgot my password')}}">I forgot my password</a>
                    </p>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </body>
</html>
<script type="text/javascript">
    function user_login(){
        $('#emailError, #passwordError').html('');
        var check = 0;
        
        if($('#email').val() == ''){
            var check = 1;
            $('#usernameError').html('Email or username is required');
        }
        if($('#password').val() == ''){
            var check = 1;
            $('#passwordError').html('Password is required');
        }
        if(check == 1){
            return false;
        }
        return true;
    }
</script>