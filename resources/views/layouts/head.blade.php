<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title', 'My Website')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link href="{{ url('public/assets/plugins/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ url('public/assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- select2 -->
    <link rel="stylesheet" href="{{ url('public/assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ url('public/assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ url('public/assets/plugins/jqvmap/jqvmap.min.css') }}">
    
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ url('public/assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ url('public/assets/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ url('public/assets/plugins/summernote/summernote-bs4.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ url('public/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ url('public/assets/dist/css/adminlte.min.css') }}">
    <!-- sweetalert2 -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.14.5/sweetalert2.css">
    <!-- datepicker -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- dropzone image -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">
    @include('stylesheet.custom_css')
    
    <script src="{{ url('public/assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ url('public/assets/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script type="text/javascript">const base_url = "{{ url('/') }}" </script>
    <script type="text/javascript">const csrf_token = "{{ csrf_token() }}" </script>
    @if(1==11)
        <!-- Include DataTables CSS -->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    @endif
    <style type="text/css">
        .sidebar-dark-lime .nav-sidebar>.nav-item>.nav-link.active, .sidebar-light-lime .nav-sidebar>.nav-item>.nav-link.active{
            background-color:#64C5B1;
        }
        .card_header_color{
            background-color:#837d7d !important;
        }
        .preloader-bg-color{
            background-color:#000000;
        }
    </style>
</head>
<body class="sidebar-mini layout-fixed layout-navbar-fixed control-sidebar-slide-open text-sm">
    <div class="wrapper">
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center preloader-bg-color">
            <img class="animation__shake1" src="{{ url('public/images/img/loader/loader.gif') }}" alt="loader" height="175" width="180">
        </div>
        <!-- Preloader end height="100" width="180"-->
        
        @include('layouts.topbar')
        @include('layouts.sidebar')
        @yield('content')
    </div>
@include('layouts.foot')

