@extends('layouts.head')

@section('title') Users @endsection

@section('content')

<!-- Add CKEditor via CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/38.0.0/classic/ckeditor.js"></script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    @if(@$action_permission->add_access == 'yes')
                        <a href="{{url('user/add-user')}}" class="btn btn-sm btn-default" title="Add new user"><i class="fa fa-plus"></i> Add</a>
                    @endif
                    <button type="button" class="btn btn-sm btn-default" onclick="return referesh_form();"><i class="fa fa-refresh" aria-hidden="true"></i> Refresh</button>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <input type="hidden" value="{{@$user_department_type}}" id="user_department_type">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- /.card -->
                    <div class="card card-primary">
                        <div class="card-header card_header_color">
                            <h3 class="card-title">All {{$user_department_type}} Users</h3>
                        </div>
                        <div class="modal-body">
                            <form method="POST" id="userSearch">
                                @csrf    
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-primary">
                                            <div class="show_message"></div>
                                            <div class="card-body row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="text" name="search_name" id="search_name" class="form-control" placeholder="Search name">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="text" name="search_email" id="search_email" class="form-control" placeholder="Search email">
                                                    </div>
                                                </div>
                                                
                                                @if(1==2)
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select class="form-control" name="search_department_type" id="search_department_type" onchange="return get_department_record('','search_department_name');">
                                                            <option value="" hidden="">Department type</option>
                                                            <option value="office">Office</option>
                                                            <option value="vessel">Vessel</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                @endif

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select class="form-control select2" name="search_department_name" id="search_department_name" onchange="return get_designation_record('','search_designation_name','search_department_name');">
                                                            <option value="" hidden="">Select department</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select class="form-control select2" name="search_designation_name" id="search_designation_name">
                                                            <option value="" hidden="">Select designation</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <div class="input-group date">
                                                            <input type="text" name="search_start_date" id="search_start_date" class="form-control" placeholder="Search start date" readonly>
                                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <div class="input-group date">
                                                            <input type="text" name="search_end_date" id="search_end_date" class="form-control" placeholder="Search end date" readonly>
                                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if(1==11)
                                                <div class="col-md-12">
                                                    <textarea name="editor" id="summernote"></textarea>
                                                </div>
                                                <script>
                                                    $(document).ready(function () {
                                                        $('#summernote').summernote({
                                                            placeholder: 'Enter your text here...',
                                                            height: 200
                                                        });
                                                    });
                                                </script>
                                                @endif
                                                <!-- <div class="col-md-12">
                                                    <div class="form-group"><input type="text" name="search_m_user" id="search_m_user" class="form-control" placeholder="Search maintion user">
                                                    </div>
                                                </div> -->
                                                @if(1==11)
                                                <div class="col-md-12">
                                                    <div class="mention-container">
                                                        <input type="text" id="mentionInput" class="mention-input form-control" placeholder="Type '@' to mention a user..." oninput="showMentionDropdown()">
                                                        <div id="mentionDropdown" class="mention-dropdown form-control"></div>
                                                    </div>
                                                </div>    
                                                @endif
                                                    
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <button type="button" class="btn btn-primary" onclick="return user_data_table_list();">Search</button>
                                                        <button type="button" class="btn btn-danger" onclick="return search_reset_form();">Reset</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- /.card-header -->
                        <div class="card-body responsive">
                            <table id="tableList" class="table responsive table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sno</th>
                                        <th class="set_action_width4">Action</th>
                                        <th>Name</th>
                                        <th>Login ID</th>
                                        <th>Email</th>
                                        <th>Created AT</th>
                                        <th>Department Name</th>
                                        <th>Designation name</th>
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
    <!-- /.content -->

    <!-- Modal -->
    <section class="content">
        <div class="modal fade" id="userViewModal" role="dialog">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">View user</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="p_id" id="p_id">
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>First name</th>
                                                    <td class="view_tbl_first_name"></td>
                                                </tr>
                                                <tr>
                                                    <th>Middle name</th>
                                                    <td class="view_tbl_middle_name"></td>
                                                </tr>
                                                <tr>
                                                    <th>Last name</th>
                                                    <td class="view_tbl_last_name"></td>
                                                </tr>
                                                <tr>
                                                    <th>Phone</th>
                                                    <td class="view_tbl_phone"></td>
                                                </tr>
                                                <tr>
                                                    <th>Date Of Birth</th>
                                                    <td class="view_tbl_date_birth"></td>
                                                </tr>
                                                <tr>
                                                    <th>Updated date</th>
                                                    <td class="view_tbl_update_at"></td>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<script type="text/javascript">
    $(document).ready(function() {
        user_data_table_list();
        get_department_record('','search_department_name');
    });
</script>

<script>
  // Example list of users
  const users = ["John", "Jane", "Alex", "Alice", "Bob", "Charlie", "David", "Diana", "Eva"];

  const mentionInput = document.getElementById('mentionInput');
  const mentionDropdown = document.getElementById('mentionDropdown');

  let isMentioning = false;

  // Function to display the dropdown
  function showMentionDropdown() {
    const inputText = mentionInput.value;
    const lastChar = inputText.slice(-1);

    // Check if the last character is '@'
    if (lastChar === '@') {
      isMentioning = true;
      mentionDropdown.style.display = 'block';
      showSuggestions('');
    } else if (isMentioning && lastChar === ' ') {
      // End mentioning if space is typed
      isMentioning = false;
      mentionDropdown.style.display = 'none';
    }

    if (isMentioning) {
      // Extract query after '@' to filter users
      const searchQuery = inputText.slice(inputText.lastIndexOf('@') + 1).toLowerCase();
      showSuggestions(searchQuery);
    }
  }

  // Function to show suggestions based on search query
  function showSuggestions(query) {
    mentionDropdown.innerHTML = '';  // Clear previous suggestions

    // Filter users based on query
    const filteredUsers = users.filter(user => user.toLowerCase().includes(query));

    if (filteredUsers.length === 0) {
      mentionDropdown.style.display = 'none';
      return;
    }

    // Show filtered suggestions
    filteredUsers.forEach(user => {
      const div = document.createElement("div");
      div.textContent = user;
      div.onclick = function() {
        insertMention(user);
      };
      mentionDropdown.appendChild(div);
    });
  }

  // Function to insert selected mention back into the input field
  function insertMention(user) {
    const inputText = mentionInput.value;
    const atIndex = inputText.lastIndexOf('@');
    const beforeText = inputText.slice(0, atIndex);
    const afterText = inputText.slice(inputText.indexOf(' ', atIndex));
    mentionInput.value = `${beforeText}@${user}${afterText}`;
    mentionDropdown.style.display = 'none';  // Hide the dropdown after selection
  }
</script>
@include('script.user_js')
@include('script.comman_js')
@endsection