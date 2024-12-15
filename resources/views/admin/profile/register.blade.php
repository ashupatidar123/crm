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
                                <strong class="ml-3 mt-4">Contact Information</strong>
                                <div class="card-body row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">First Name<span class="text-danger">*</span></label>
                                            <input type="text" name="first_name" id="first_name" class="form-control"  placeholder="Enter first name">
                                            <p class="text-danger" id="first_nameError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="middle_name">Middle Name<span class="text-danger">*</span></label>
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
                                            <label for="phone2">Phone 2<span class="text-danger">*</span></label>
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
                                            <input type="file" name="user_image" id="user_image" class="form-control">
                                            <p class="text-danger" id="user_imageError"></p>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <strong class="ml-4">Login Credentials</strong>
                                <div class="card-body row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>User Role<span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="role" id="role">
                                                <option value="" hidden="">Select role</option>
                                                @if(!empty($role))
                                                    @foreach($role as $recod)
                                                        <option value="{{$recod->id}}">{{ucwords($recod->role_name)}}</option>
                                                    @endforeach
                                                @endif        
                                            </select>
                                            <p class="text-danger" id="roleError"></p>
                                        </div>
                                    </div>
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
                                            <input type="text" name="password" id="password" class="form-control" placeholder="Enter password" minlength="6">
                                            <p class="text-danger" id="passwordError"></p>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <strong class="text-center1 ml-4">Address Information</strong>
                                <div class="card-body row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Country<span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="country" id="country" onchange="return get_ajax_state(this.value,'state');">
                                                <option value="">Select country</option>
                                            </select>
                                            <p class="text-danger" id="countryError"></p>
                                        </div>

                                        <div class="form-group">
                                            <label>City<span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="city" id="city">
                                                <option value="">Select city</option>
                                            </select>
                                            <p class="text-danger" id="cityError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>State<span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="state" id="state" onchange="return get_ajax_city(this.value,'city');">
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
                                            <label>Address line 1</label>
                                            <input type="text" name="address1" id="address1" class="form-control"  placeholder="Enter street address or building name">
                                            <p class="text-danger" id="address1Error"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Address line 2</label>
                                            <input type="text" name="address2" id="address2" class="form-control"  placeholder="Enter apartment number, suite, or floor">
                                            <p class="text-danger" id="address2Error"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Address line 3</label>
                                            <input type="text" name="address1" id="address1" class="form-control"  placeholder="Enter further address details or landmarks">
                                            <p class="text-danger" id="address3Error"></p>
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
        $(document).ready(function(){
            get_ajax_country('','country');
            $('.select2').select2();
        });

        $(document).ready(function(){
            $("#submitButton").click(function (event) {
                event.preventDefault();
                $('#first_nameError, #last_nameError, #emailError, #date_birthError, #roleError, #login_idError, #passwordError, #countryError, #phone1Error, #stateError, #cityError, #address1Error, #address2Error').html('');
                var check = 0;
                if($('#first_name').val() == ''){
                    var check = 1;
                    $('#first_nameError').html('First name is required');
                }
                if($('#last_nameError').val() == ''){
                    var check = 1;
                    $('#last_nameError').html('Last name is required');
                }
                if($('#email').val() == ''){
                    var check = 1;
                    $('#emailError').html('Email is required');
                }
                if($('#phone1').val() == ''){
                    var check = 1;
                    $('#phone1Error').html('Phone is required');
                }
                if($('#date_birth').val() == ''){
                    var check = 1;
                    $('#date_birthError').html('Date of birth is required');
                }
                if($('#role').val() == ''){
                    var check = 1;
                    $('#roleError').html('Role is required');
                }
                if($('#login_id').val() == ''){
                    var check = 1;
                    $('#login_idError').html('Login id is required');
                }
                if($('#password').val() == ''){
                    var check = 1;
                    $('#passwordError').html('Password id is required');
                }
                if($('#country').val() == ''){
                    var check = 1;
                    $('#countryError').html('Country is required');
                }
                if($('#state').val() == ''){
                    var check = 1;
                    $('#stateError').html('State is required');
                }
                if($('#city').val() == ''){
                    var check = 1;
                    $('#cityError').html('City is required');
                }
                if($('#city').val() == ''){
                    var check = 1;
                    $('#cityError').html('City is required');
                }
                if($('#zip_code').val() == ''){
                    var check = 1;
                    $('#zip_codeError').html('ZIP code is required');
                }
                if($('#address1').val() == ''){
                    var check = 1;
                    $('#address1Error').html('Address line 1 is required');
                }
                if($('#address2').val() == ''){
                    var check = 1;
                    $('#address2Error').html('Address line 2 is required');
                }
                if(check == 1){
                    //return false;
                }

                $('.show_message').html('');
                $('#submitButton').html('Loading...');

                var form = $("#formId");
                $.ajax({
                    type: "POST",
                    url: "{{url('register')}}",
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': csrf_token
                    },
                    success: function (resp) {
                        $('#submitButton').html('Submit');
                        $('.show_message').html(resp.message);
                        if(resp.status == 'success'){
                            swal_success(resp.s_msg);
                            window.setTimeout(function(){
                                window.location.href = "{{url('register')}}";
                            },5000);
                        }else{
                            swal_error(resp.s_msg);
                        }
                    }
                });
            });
        });
    </script>
    @include('script.comman_js')
    @include('script.country_state_city_js')
    @include('script.check_record_js')
@endsection