@extends('layouts.head')

@section('title') Home @endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <!-- <h1>Change Password</h1> -->
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Change Password</li>
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
                            @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @elseif(session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif
                            <div class="card-header card_header_color">
                                <h3 class="card-title">Change Password</h3>
                            </div>
                            <form method="POST" action="{{url('change-password')}}">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="opass">Old Password</label>
                                        <input type="password" class="form-control" id="opass" placeholder="Old Password" name="opass" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="opass">New Password</label>
                                        <input type="password" class="form-control" id="npass" name="npass" placeholder="New Password" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="opass">Confirm Password</label>
                                        <input type="password" class="form-control" id="cpass" name="cpass" placeholder="Confirm Password" required>
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
@endsection