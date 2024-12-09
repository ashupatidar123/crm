@extends('layouts.head')

@section('title') Home @endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
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
                            <li class="breadcrumb-item active">Edit Profile</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary">
                            <div class="card-header card_header_color">
                                <h3 class="card-title">Edit Profile</h3>
                            </div>
                            <form method="POST" action="{{url('profile')}}">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" class="form-control" id="name" placeholder="Enter name" value="{{$data->name}}">
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="text" class="form-control" placeholder="Enter email" value="{{$data->email}}" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="mobile">Mobile</label>
                                        <input type="number" name="mobile" class="form-control" id="mobile" placeholder="Enter mobile" value="{{$data->mobile}}">
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>    
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    @if(1==2)
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
    @endif
@endsection