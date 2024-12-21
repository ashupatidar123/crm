@extends('layouts.head')

@section('title') Add user @endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <a href="{{url('master/user')}}" class="btn btn-sm btn-default" title="All users"><i class="fa fa-list"></i> List</a>
                        <button type="button" class="btn btn-sm btn-default" onclick="return referesh_form();"><i class="fa fa-refresh" aria-hidden="true"></i> Refresh</button>
                        <!-- <div class="show_message"></div> -->
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
                            <li class="breadcrumb-item active">Add User</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <form method="POST" id="registerFormId" enctype="multipart/form-data">
                    @csrf    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <div class="card-header card_header_color">
                                    <h3 class="card-title">Add User</h3>
                                </div>
                                <strong class="ml-3 mt-4">Contact Information</strong>
                                <div class="card-body row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">First Name<span class="text-danger">*</span></label>
                                            <input type="text" name="first_name" id="first_name" class="form-control"  placeholder="Enter first name" required>
                                            <p class="text-danger" id="first_nameError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="middle_name">Middle Name</label>
                                            <input type="text" name="middle_name" id="middle_name" class="form-control"  placeholder="Enter middle name">
                                            <p class="text-danger" id="middle_nameError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name<span class="text-danger">*</span></label>
                                            <input type="text" name="last_name" id="last_name" class="form-control"  placeholder="Enter last name">
                                            <p class="text-danger" id="last_nameError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email<span class="text-danger">*</span></label>
                                            <input type="text" name="email" id="email" class="form-control" placeholder="Enter email" onkeyup="return check_user_record(this.value,'email');" autocomplete="off">
                                            <p class="text-danger" id="emailError"></p>
                                        </div> 
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone1">Phone 1<span class="text-danger">*</span></label>
                                            <input type="text" name="phone1" id="phone1" class="form-control"  placeholder="Enter phone 1 number">
                                            <p class="text-danger" id="phone1Error"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone2">Phone 2</label>
                                            <input type="text" name="phone2" id="phone2" class="form-control"  placeholder="Enter phone 2 number">
                                            <p class="text-danger" id="phone2Error"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Date Of Birth</label>
                                            <input type="date" name="date_birth" id="date_birth" class="form-control">
                                            <p class="text-danger" id="date_birthError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Is Active<span class="text-danger">*</span></label>
                                            <select class="form-control" name="is_active" id="is_active">
                                                <option value="1">Active</option>
                                                <option value="2">In-Active</option>
                                            </select>
                                            <p class="text-danger" id="is_activeError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">    
                                        <div class="form-group">
                                            <label>User Image</label>
                                            <input type="file" name="user_image" id="user_image" class="form-control" accept="image/*">
                                            <p class="text-danger" id="user_imageError"></p>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <strong class="ml-3">Login Credentials</strong>
                                <p class="ml-3 text-danger">*Password should be at least 6 characters including a number (123), special characters (@) and a lowercase letter.</p>
                                <div class="card-body row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="login_id">Login ID (Username)<span class="text-danger">*</span></label>
                                            <input type="text" name="login_id" id="login_id" class="form-control" placeholder="Enter login id" onkeyup="return check_user_record(this.value,'username_login_id');">
                                            <p class="text-danger" id="login_idError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Password<span class="text-danger">*</span></label>
                                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" minlength="6">
                                            <p class="text-danger" id="passwordError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Confirm Password<span class="text-danger">*</span></label>
                                            <input type="password" id="confirm_password" class="form-control" placeholder="Enter confirm password" minlength="6">
                                            <p class="text-danger" id="confirm_passwordError"></p>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <strong class="ml-3">Role Permission</strong>
                                <div class="card-body row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Role<span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="role" id="role" onchange="return get_role_reporting('','reporting_role_id');">
                                                <option value="" hidden="">Select role</option>
                                                @if(!empty($role))
                                                    @foreach($role as $recod)
                                                        <option value="{{$recod->id}}" data-rank="{{$recod->rank}}">{{ucwords($recod->role_name)}}</option>
                                                    @endforeach
                                                @endif        
                                            </select>
                                            <p class="text-danger" id="roleError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Reporting<span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="reporting_role_id" id="reporting_role_id">
                                                <option value="" hidden="">Select reporting</option>
                                            </select>
                                            <p class="text-danger" id="reporting_role_idError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Department<span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="department_id" id="department_id">
                                                <option value="" hidden="">Select department</option>
                                                @if(!empty($department))
                                                    @foreach($department as $recod)
                                                        <option value="{{$recod->id}}" data-name="{{$recod->department_name}}">{{ucwords($recod->department_name)}}</option>
                                                    @endforeach
                                                @endif        
                                            </select>
                                            <p class="text-danger" id="department_idError"></p>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <strong class="text-center1 ml-3">Address Information</strong>
                                <div class="card-body row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Country<span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="country_id" id="country_id" onchange="return get_ajax_state(this.value,'state_id');">
                                                <option value="">Select country</option>
                                            </select>
                                            <p class="text-danger" id="countryError"></p>
                                        </div>

                                        <div class="form-group">
                                            <label>City<span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="city_id" id="city_id">
                                                <option value="">Select city</option>
                                            </select>
                                            <p class="text-danger" id="cityError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>State<span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="state_id" id="state_id" onchange="return get_ajax_city(this.value,'city_id');">
                                                <option value="">Select state</option>
                                            </select>
                                            <p class="text-danger" id="stateError"></p>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>ZIP Code / Postal code<span class="text-danger">*</span></label>
                                            <input type="number" name="zip_code" id="zip_code" class="form-control"  placeholder="Enter zip/postal code">
                                            <p class="text-danger" id="zip_codeError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Address line 1<span class="text-danger">*</span></label>
                                            <input type="text" name="address1" id="address1" class="form-control"  placeholder="Enter street address or building name">
                                            <p class="text-danger" id="address1Error"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Address line 2<span class="text-danger">*</span></label>
                                            <input type="text" name="address2" id="address2" class="form-control"  placeholder="Enter apartment number, suite, or floor">
                                            <p class="text-danger" id="address2Error"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Address line 3</label>
                                            <input type="text" name="address3" id="address3" class="form-control"  placeholder="Enter further address details or landmarks">
                                            <p class="text-danger" id="address3Error"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <button id="submitRegister" type="button" class="btn btn-primary">Submit</button>
                                            <button type="button" class="btn btn-danger referesh_form" onclick="return referesh_form();">Refresh</button>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="show_message"></div>
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
        $(document).ready(function(){
            get_ajax_country('','country_id');
            $('.select2').select2();
        });
    </script>
    @include('script.comman_js')
    @include('script.country_state_city_js')
    @include('script.user_js')
@endsection