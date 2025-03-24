@extends('layouts.head')

@section('title') Update Profile @endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <!-- <a href="{{url('master/user')}}" class="btn btn-sm btn-default" title="All users"><i class="fa fa-list"></i> List</a> -->
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
                            <li class="breadcrumb-item active">Update user</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <form method="POST" id="profileFormId" enctype="multipart/form-data">
                    @csrf   
                    <input type="hidden" name="user_id" value="{{$data->id}}">
                    <input type="hidden" name="address_id" value="{{@$address->id}}"> 
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <div class="card-header card_header_color">
                                    <h3 class="card-title">Update user</h3>
                                </div>
                                <strong class="ml-3 mt-4">Contact Information</strong>
                                <div class="card-body row">
                                    <div class="col-md-6">    
                                        <div class="form-group">
                                            <?php
                                            if(!empty($data->user_image)){
                                                $img_url = asset('storage/app/public/uploads/image/users').'/'.$data->user_image;
                                            }else{
                                                $img_url = url('public/images/img/no_image.png');
                                            }
                                            ?>

                                            <label>User Image</label>
                                            <input type="file" name="user_image" id="user_image" class="form-control">
                                            <p class="text-danger" id="user_imageError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <img class="rounded-circle" src="{{$img_url}}" width="189" height="155" id="show_user_image">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="name_title">Title<span class="text-danger">*</span></label>
                                            <select type="text" name="name_title" id="name_title" class="form-control"required>
                                                @if(!empty($data->name_title))
                                                    <option value="{{$data->name_title}}" selected hidden>{{$data->name_title}}</option>
                                                @endif
                                                <?php echo user_title(); ?>
                                            </select>
                                            <p class="text-danger" id="name_titleError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="first_name">First Name<span class="text-danger">*</span></label>
                                            <input type="text" name="first_name" id="first_name" class="form-control"  placeholder="Enter first name" required value="{{$data->first_name}}">
                                            <p class="text-danger" id="first_nameError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="middle_name">Middle Name</label>
                                            <input type="text" name="middle_name" id="middle_name" class="form-control"  placeholder="Enter middle name" value="{{$data->middle_name}}">
                                            <p class="text-danger" id="middle_nameError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name<span class="text-danger">*</span></label>
                                            <input type="text" name="last_name" id="last_name" class="form-control"  placeholder="Enter last name" value="{{$data->last_name}}">
                                            <p class="text-danger" id="last_nameError"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email<span class="text-danger">*</span></label>
                                            <input type="text" name="email" id="email" class="form-control" placeholder="Enter email" onkeyup="return check_user_record(this.value,'email','{{$data->id}}');" autocomplete="off" value="{{$data->email}}">
                                            <p class="text-danger" id="emailError"></p>
                                        </div> 
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone1">Phone 1<span class="text-danger">*</span></label>
                                            <input type="text" name="phone1" id="phone1" class="form-control"  placeholder="Enter phone 1 number" value="{{$data->phone}}">
                                            <p class="text-danger" id="phone1Error"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone2">Phone 2</label>
                                            <input type="text" name="phone2" id="phone2" class="form-control"  placeholder="Enter phone 2 number" value="{{@$address->phone2}}">
                                            <p class="text-danger" id="phone2Error"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Date Of Birth</label>
                                            <input type="date" name="date_birth" id="date_birth" class="form-control" value="{{$data->date_birth}}">
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
                                </div>
                                <hr>
                                <strong class="ml-3">Department Permission</strong>
                                <div class="card-body row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Department Type</label>
                                            <input type="text" class="form-control" value="{{@$data->single_department->department_type}}" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Department Name</label>
                                            <input type="text" class="form-control" value="{{@$data->single_department->department_name}}" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Department Name</label>
                                            <input type="text" class="form-control" value="{{@$data->single_designation->designation_name}}" disabled>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <button id="profileSubmitButton" type="button" class="btn btn-primary">Submit</button>
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

        <!-- address section -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- /.card -->
                        <div class="card card-primary">
                            <div class="card-header card_header_color">
                                <h3 class="card-title">All Address</h3>
                            </div>

                            <!-- /.card-header -->
                            <div class="card-body responsive">
                                <button type="button" class="btn btn-sm btn-default mb-3" onclick="return add_edit_address('','add');"><i class="fa fa-plus" aria-hidden="true"></i> Add New Address</button>
                                <table id="tableList" class="table responsive table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sno</th>
                                            <th class="set_action_width4">Action</th>
                                            <th>Address type</th>
                                            <th>Address 1</th>
                                            <th>Address 2</th>
                                            <th>Address 3</th>
                                            <th>ZIP code</th>
                                            <th>Country</th>
                                            <th>State</th>
                                            <th>City</th>
                                            <th>Created AT</th>
                                         </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>

        <!-- Modal -->
        <section class="content">
            <div class="modal fade" id="addAddressModal" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add/Edit Address</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" id="addAddressFormId">
                                @csrf    
                                <div class="row">
                                    <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                                    <input type="hidden" name="p_id" id="p_id">
                                    <div class="col-md-12">
                                        <div class="card card-primary">
                                            <div class="card-header card_header_color"><h3 class="card-title">Add/Edit Address</h3>
                                            </div>
                                            <div class="show_message"></div>
                                            <div class="card-body row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Address Type<span class="text-danger">*</span></label>
                                                        <select class="form-control select2" name="address_type" id="address_type">
                                                            <option value="">Select address type</option>
                                                            <option value="permanent_address">Permanent address</option>
                                                            <option value="current_address">Current address</option>
                                                            <option value="office_address">Office address</option>
                                                            <option value="other_address">Other address</option>
                                                        </select>
                                                        <p class="text-danger" id="address_typeError"></p>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Country<span class="text-danger">*</span></label>
                                                        <select class="form-control select2" name="country_id" id="country_id" onchange="return get_ajax_state(this.value,'state_id');">
                                                            <option value="">Select country</option>
                                                        </select>
                                                        <p class="text-danger" id="countryError"></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>State<span class="text-danger">*</span></label>
                                                        <select class="form-control select2" name="state_id" id="state_id" onchange="return get_ajax_city(this.value,'city_id');">
                                                            <option value="">Select state</option>
                                                        </select>
                                                        <p class="text-danger" id="stateError"></p>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
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
                                                        <button id="addAddressSubmitButton" type="submit" class="btn btn-primary"><i class="fa fa-send-o"></i> Submit</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script>
        var user_id = '{{Auth::user()->id}}';
        $(document).ready(function(){
            user_address_data_table_list();

            //get_ajax_country('{{@$address->country_id}}','country_id');
            //get_ajax_state('{{@$address->country_id}}','state_id','{{@$address->state_id}}');
            //get_ajax_city('{{@$address->state_id}}','city_id','{{@$address->city_id}}');
        });
    </script>
    @include('script.comman_js')
    @include('script.country_state_city_js')
    @include('script.user_js')
@endsection