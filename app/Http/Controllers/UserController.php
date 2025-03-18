<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Document;
use App\Models\UserDocument;
use App\Models\UserDocumentAccess;
use App\Models\DepartmentDesignation;
use App\Models\UserAddress;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Vessel;
use App\Models\VesselCheckInOut;
use App\Models\Apprisal;
use App\Models\Menu;
use App\Models\Permission;

use App\Traits\FileUploadTrait;

class UserController extends Controller{   
    use FileUploadTrait;
    public function __construct(){

    }

    public function dashboard(){
        return view('dashboard');
    }

    public function user(){
        check_authorize('list','user');
        $action_permission = check_user_action_permission('user');
        return view('user.user.user_list',compact('action_permission'));
    }

    public function user_list_filter_count($search,$postData){
        $search_department_name = @$postData['search_department_name'];
        $search_designation_name = @$postData['search_designation_name'];
        $search_start_date = !empty($postData['search_start_date'])?$postData['search_start_date']:'';
        $search_end_date = !empty($postData['search_end_date'])?$postData['search_end_date']:'';

        $filter_count = User::with('single_department','single_designation')->where('id','>',0);
        if(!empty($search)) {
            $filter_count = User::with('single_department','single_designation')->where('first_name', 'LIKE', '%'.$search.'%')->orWhere('email', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%')->orWhere('login_id', 'LIKE', '%'.$search.'%');
        }
        if(!empty($postData['search_name'])) {
            $filter_count->where('first_name', 'LIKE', '%'.$postData['search_name'].'%');
        }
        if(!empty($postData['search_email'])) {
            $filter_count->where('email', 'LIKE', '%'.$postData['search_email'].'%');
        }
        if(!empty($search_start_date) && !empty($search_end_date)){
            $search_start_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_start_date'])));
            $search_end_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_end_date']. ' +1 day')));

            $filter_count->where('created_at', '>=', date($search_start_date));
            $filter_count->where('created_at', '<=', date($search_end_date)); 
        }
        if(!empty($postData['search_department_type'])){
            $filter_count->where('department_type',$postData['search_department_type']);
        }
        if(!empty($search_department_name)){
            $filter_count->where('department_id',$search_department_name);
        }
        if(!empty($search_designation_name)){
            $filter_count->where('department_designation_id',$search_designation_name);
        }
        return $filter_count->count();
    }

    public function user_list(Request $request){
        $postData = $request->input();

        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }

        $search_name = !empty($request->input('search_name'))?$request->input('search_name'):'';
        $search_email = !empty($request->input('search_email'))?$request->input('search_email'):'';
        $search_department_type = !empty($request->input('search_department_type'))?$request->input('search_department_type'):'';
        $search_department_name = !empty($request->input('search_department_name'))?$request->input('search_department_name'):'';
        $search_designation_name = !empty($request->input('search_designation_name'))?$request->input('search_designation_name'):'';

        $search_start_date = !empty($request->input('search_start_date'))?$request->input('search_start_date'):'';
        $search_end_date = !empty($request->input('search_end_date'))?$request->input('search_end_date'):'';
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','','first_name','login_id','email','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = User::with('single_department','single_designation')->select('id','department_id','department_designation_id','name_title','first_name','middle_name','last_name','login_id','email','date_birth','created_at','is_active');
        
        if(!empty($search)) {
            $query->where('first_name', 'LIKE', '%'.$search.'%')->orWhere('login_id', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%')->orWhere('email', 'LIKE', '%'.$search.'%');
        }
        if(!empty($search_name)){
            $query->where('first_name', 'LIKE', '%'.$search_name.'%'); 
        }
        if(!empty($search_email)){
            $query->where('email', 'LIKE', '%'.$search_email.'%'); 
        }
        if(!empty($search_start_date) && !empty($search_end_date)){
            $search_start_date = date('Y-m-d',strtotime(str_replace('/','-',$request->input('search_start_date'))));
            $search_end_date = date('Y-m-d',strtotime(str_replace('/','-',$request->input('search_end_date'). ' +1 day')));

            $query->where('created_at', '>=', date($search_start_date));
            $query->where('created_at', '<=', date($search_end_date)); 
        }
        if(!empty($search_department_type)){
            $query->where('department_type',$search_department_type);
        }
        if(!empty($search_department_name)){
            $query->where('department_id',$search_department_name);
        }
        if(!empty($search_designation_name)){
            $query->where('department_designation_id',$search_designation_name);
        }
        $query->orderBy($orderColumnIndex, $orderDirection);
        $users = $query->offset($start_limit)->limit($end_limit)->get(); 
        //printr($users);
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($users)){
            $action_permission = check_user_action_permission('user');

            $recordsTotal = User::count();
            $sno = 1+$start_limit;
            foreach($users as $record){
                $edit = $view = $delete = $details = '';
                if(@$action_permission->edit_access == 'yes'){
                    $edit = '<a href="'.url('user/edit-user').'/'.$record->id.'" class="btn btn-default btn-sm" title="Edit"><i class="fa fa-edit"></i></a>';
                }
                
                if(@$action_permission->view_access == 'yes'){
                    $view = '<button class="btn btn-default btn-sm" onclick="return ajax_view('.$record->id.',\'user\');" title="View"><i class="fa fa-eye"></i></button>';
                }

                if(@$action_permission->delete_access == 'yes'){
                    $delete = '<button class="btn btn-default btn-sm deleteLoader_'.$record->id.'" onclick="return ajax_delete('.$record->id.',\'user\');" title="Delete"><i class="fa fa-trash"></i></button>';
                }

                $details = '<a target="_blank" href="'.route('user-details',['id'=>$record->id]).'" class="btn btn-default btn-sm" title="User details"><i class="fa fa-eye"></i></a>';

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',1,\'user\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',2,\'user\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $permission = '<a href="'.route('menu_user_permission', ['id'=>$record->id]).'" class="btn btn-default btn-sm" title="Menu permission"><i class="fa fa-key"></i></a>';

                $department_type = @$record->single_department->department_type;
                $department_name = @$record->single_department->department_name;
                $designation_name = @$record->single_designation->designation_name;

                $date_birth = !empty($record->date_birth)?date('d/M/Y',strtotime($record->date_birth)):'';

                $all_data[] = [
                    'sno'=> $sno++,
                    'action'=>$edit.' '.$status.' '.$details.' '.$delete.' '.$permission,
                    'first_name'=> @$record->name_title.' '.@$record->first_name.' '.@$record->middle_name.' '.@$record->last_name,
                    'login_id'=> @$record->login_id,
                    'email'=> @$record->email,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at)),
                    'department_name'=>!empty($department_type)?$department_name.' ('.$department_type.')':'empty',
                    'designation_name'=>!empty($designation_name)?$designation_name:'empty'
                ];
            }
        }
        //printr($all_data,'p');
        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->user_list_filter_count($search,$postData),
            'data' => $all_data,
        ]);
    }

    public function ajax_user_check_record(Request $request){
        $check_type = !empty($request->check_type)?$request->check_type:'';
        $where_value_id = !empty($request->where_value)?$request->where_value:0;
        $count = 0;
        if($check_type == 'username_login_id'){
            $count = User::where('login_id',$where_value_id)->count();
        }
        else if($check_type == 'email'){
            $count = User::where('email',$where_value_id)->count();
        }
        return $count;
    }

    public function showAddUser(){
        check_authorize('add','user');

        $user = User::where('id',Auth::user()->id)->first();
        $role = Role::select('id','role_name','rank')->where('is_active',1)->get();
        $department = Department::select('id','department_name')->where('is_active',1)->get();
        return view('user.user.add_user',compact('user','role','department'));
    }

    public function add_user(Request $request){
        check_authorize('add','user');

        if(empty($request->first_name) || empty($request->login_id) || empty($request->department_type) || empty($request->department_designation_id) || empty($request->department_id) || empty($request->password) || empty($request->email)){
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">All fields are required...</p>','s_msg'=>'All fields are required...'],200);
        }

        $checkEmail = User::where(['email' => $request->email])->count();
        if($checkEmail > 0){
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">Email already exist...</p>','s_msg'=>'Email already exist...'],200);
        }
        $checkLoginId = User::where(['login_id' => $request->login_id])->count();
        if($checkLoginId > 0){
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">Login id already exist...</p>','s_msg'=>'Login id already exist...'],200);
        }
        
        $user_image = '';
        if(!empty($request->file('user_image'))){
            $user_image = $this->uploadFile($request, 'user_image', 'uploads/image/users');
        }

        $id = User::insertGetId([
            'user_id'=>Auth::user()->id,
            'name_title' => $request->name_title,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'user_image' => $user_image,
            'phone' => $request->phone1,
            'date_birth' => date('Y-m-d',strtotime($request->date_birth)),
            'is_active' => ($request->is_active==1)?1:2,
            'login_id' => $request->login_id,
            'department_type' => $request->department_type,
            'department_id' => $request->department_id,
            'department_designation_id' => $request->department_designation_id,
            'password' => Hash::make($request->password),
            'created_by'=>Auth::user()->id
        ]);
        
        if($id > 0){
            $address = UserAddress::insertGetId([
                'user_id'=>$id,
                'country_id' => !empty($request->country_id)?$request->country_id:'',
                'state_id' => !empty($request->state_id)?$request->state_id:'',
                'city_id' => !empty($request->city_id)?$request->city_id:'',
                'address_type' => !empty($request->address_type)?$request->address_type:'office_address',
                'zip_code' => $request->zip_code,
                'phone1' => $request->phone1,
                'phone2' => $request->phone2,
                'address1' => $request->address1,
                'address2' => $request->address2,
                'address3' => $request->address3,
                'is_active' => ($request->is_active==1)?1:2,
                'created_by'=>Auth::user()->id
            ]);

            $get_menu_permission = Permission::where('department_id',$request->department_id)->where('permission_type','department')->get();
            if(count($get_menu_permission) > 0){
                foreach($get_menu_permission as $record){
                    $save_data = [
                        'menu_id' => $record->menu_id,
                        'user_id' => $id,
                        'permission_type'=> 'user',
                        'add_access'=> $record->add_access,
                        'edit_access'=> $record->edit_access,
                        'view_access'=> $record->view_access,
                        'delete_access'=> $record->delete_access,
                        'created_by'=>Auth::user()->id
                    ];
                    Permission::insertGetId($save_data);
                }
            }

            return response()->json(['status' =>'success','message' => '<p class="alert alert-success">User registration success...</p>','s_msg'=>'User registration success...'],200);
        }else{
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">Opps! Something went wrong...</p>','s_msg'=>'Opps! Something went wrong...'],200);
        }
    }

    public function showEditUser(Request $request){
        check_authorize('edit','user');

        $data = User::where('id',$request->id)->first();
        if(empty($data)){
            return redirect(url('user/user'),302);
        }
        $address = UserAddress::where('user_id',$request->id)->first();
        return view('user.user.edit_user',compact('data','address'));
    }

    public function update_user(Request $request){
        check_authorize('edit','user');
        if(empty($request->first_name) || empty($request->department_type) || empty($request->department_id) || empty($request->department_designation_id) || empty($request->user_id) || empty($request->email)){
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">All fields are required...</p>','s_msg'=>'All fields are required...'],200);
        }

        $checkEmail = User::where('id', '!=',$request->user_id)->where('email',$request->email)->count();
        if($checkEmail > 0){
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">Email already exist...</p>','s_msg'=>'Email already exist...'],200);
        }
        
        $update_data = [
            'name_title' => $request->name_title,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone1,
            'date_birth' => date('Y-m-d',strtotime($request->date_birth)),
            'is_active' => ($request->is_active==1)?1:2,
            'department_type' => $request->department_type,
            'department_id' => $request->department_id,
            'department_designation_id' => $request->department_designation_id,
            'created_by'=>Auth::user()->id
        ];

        if(!empty($request->file('user_image'))){
            $update_data['user_image'] = $this->uploadFile($request, 'user_image', 'uploads/image/users');
        }
        
        $id = User::where('id',$request->user_id)->update($update_data);
        if($id > 0){
            return response()->json(['status' =>'success','message' => '<p class="alert alert-success">User updated successfully...</p>','s_msg'=>'User updated successfully...'],200);
        }else{
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">Opps! Something went wrong...</p>','s_msg'=>'Opps! Something went wrong...'],200);
        }
    }

    /* user address section*/
    public function user_address_list(Request $request){
        $postData = $request->input();

        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $orderColumnIndex = 'created_at';
        $orderDirection   = 'DESC';
        
        $query = UserAddress::with('single_country','single_state','single_city')->select('id','user_id','country_id','state_id','city_id','address_type','zip_code','address1','address2','address3','created_at','is_active')->where('user_id',$postData['user_id']);
        
        if(!empty($search)) {
            $query->where('user_id', 'LIKE', '%'.$search.'%')->orWhere('country_id', 'LIKE', '%'.$search.'%');
        }
        
        $query->orderBy($orderColumnIndex, $orderDirection);
        $users = $query->offset($start_limit)->limit($end_limit)->get(); 
        //printr($users);
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($users)){
            $recordsTotal = UserAddress::where('user_id',$postData['user_id'])->count();
            $sno = 1+$start_limit;
            foreach($users as $record){
                $edit = '<button class="btn btn-default btn-sm addEditLoader_'.$record->id.'" onclick="return add_edit_address('.$record->id.',\'edit\');" title="Edit"><i class="fa fa-edit"></i></button>';

                $all_data[] = [
                    'sno'=> $sno++,
                    'action'=>$edit,
                    'country_id'=> @$record->single_country->name,
                    'state_id'=> @$record->single_state->name,
                    'city_id'=> @$record->single_city->name,
                    'address_type'=> @$record->address_type,
                    'zip_code'=> @$record->zip_code,
                    'address1'=> @$record->address1,
                    'address2'=> @$record->address2,
                    'address3'=> @$record->address3,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at)),
                ];
            }
        }
        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $all_data,
        ]);
    }

    public function address_edit(Request $request){
        $data = UserAddress::where('id',$request->p_id)->first();
        echo json_encode(['data'=>$data]);
    }

    public function add_edit_address_save(Request $request){
        if(empty($request->user_id) || empty($request->country_id) || empty($request->state_id) || empty($request->city_id) || empty($request->address_type) || empty($request->zip_code) || empty($request->address1) || empty($request->address2)){
            return response()->json(['status' =>'failed','message'=>'All fields are required...'],200);
        }
        $p_id = !empty($request->p_id)?$request->p_id:'';

        $check_address_type = UserAddress::where('id','!=',$p_id)->where('address_type',$request->address_type)->where('user_id',$request->user_id)->count();
        if($check_address_type > 0){
            return response()->json(['status' =>'failed','message'=>'Address type already exist'],200);
        }
        
        $save_data = [
            'user_id'=>$request->user_id,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'address_type' => $request->address_type,
            'zip_code' => $request->zip_code,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'address3' => $request->address3,
            'created_by'=>Auth::user()->id
        ];
        //printr($save_data,'p');
        if($p_id > 0){
            $id = UserAddress::where('id',$p_id)->update($save_data);
        }else{
            $id = UserAddress::insertGetId($save_data);
        }
        
        if($id > 0){
            return response()->json(['status' =>'success','message'=>'Address saved successfully'],200);
        }else{
            return response()->json(['status' =>'failed','message'=>'Opps! Something went wrong...'],200);
        }
    }

    public function get_role_reporting(Request $request){
        $id = !empty($request->p_id)?$request->p_id:'';
        $show_type = !empty($request->type)?$request->type:'all';
        $rank = !empty($request->rank)?$request->rank:0;
        
        if($show_type == 'ajax_list'){
            $data = Role::select('id','role_name','rank')->where('rank','<=',$rank)->where('is_active',1)->orderBy('role_name','ASC')->limit(500)->get();
            $html = '<option value="" hidden="">Select reporting</option>';
            if(!empty($data)){
                foreach($data as $record){
                    $selected = '';
                    if($id == $record->id){
                        $selected = 'selected';
                    }
                    $html .= '<option value="'.$record->id.'" '.$selected.' data-name="'.$record->role_name.'">'.ucwords($record->role_name).'</option>';
                }
            }else{
                $html .= '<option value="" hidden>Not found</option>';
            }
            echo $html;
        }
    }

    public function get_department_record(Request $request){
        $id = !empty($request->p_id)?$request->p_id:'';
        $show_type = !empty($request->type)?$request->type:'all';
        $department_type = !empty($request->department_type)?trim($request->department_type):'';
        
        if($show_type == 'ajax_list'){
            if(empty($department_type)){
                $data = Department::select('id','department_name','department_type')->where('department_type','!=','')->where('is_active',1)->orderBy('department_name','ASC')->limit(500)->get();
            }else{
                $data = Department::select('id','department_name','department_type')->where('department_type',$department_type)->where('is_active',1)->orderBy('department_name','ASC')->limit(500)->get();
            }
            
            $html = '<option value="" hidden="">Select department</option>';
            if(!empty($data)){
                foreach($data as $record){
                    $selected = '';
                    if($id == $record->id){
                        $selected = 'selected';
                    }
                    $html .= '<option value="'.$record->id.'" '.$selected.' data-name="'.$record->department_name.'">'.ucwords($record->department_name).'</option>';
                }
            }else{
                $html .= '<option value="" hidden>Not found</option>';
            }
            echo $html;
        }
    }

    public function get_designation_record(Request $request){
        $id = !empty($request->p_id)?$request->p_id:'';
        $show_type = !empty($request->type)?$request->type:'all';
        $department_id = !empty($request->department_id)?trim($request->department_id):'';
        if($show_type == 'ajax_list'){
            $data = DepartmentDesignation::select('id','designation_name')->where('department_id',$department_id)->where('is_active',1)->orderBy('rank','ASC')->limit(500)->get();
            $html = '<option value="" hidden="">Select designation</option>';
            if(!empty($data)){
                foreach($data as $record){
                    $selected = '';
                    if($id == $record->id){
                        $selected = 'selected';
                    }
                    $html .= '<option value="'.$record->id.'" '.$selected.' data-name="'.$record->designation_name.'">'.ucwords($record->designation_name).'</option>';
                }
            }else{
                $html .= '<option value="" hidden>Not found</option>';
            }
            echo $html;
        }
    }

    public function showUserDetails(Request $request){
        
        $data = User::where('id',$request->id)->first();
        if(empty($data)){
            return redirect(route('user'),302);
        }
        $address = UserAddress::where('user_id',$request->id)->first();
        return view('user.user.user_details',compact('data','address'));
    }

    public function showProfile(){
        $data = User::with('single_department','single_designation')->where('id',Auth::user()->id)->first();
        //printr($data);
        if(empty($data)){
            return redirect(url('user/user'),302);
        }
        $address = UserAddress::where('user_id',Auth::user()->id)->first();
        return view('admin.profile.profile',compact('data','address'));
    }

    public function updateProfile(Request $request){
        if(empty($request->first_name) || empty($request->email)){
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">All fields are required...</p>','s_msg'=>'All fields are required...'],200);
        }
        $login_user_id = Auth::user()->id;

        $checkEmail = User::where('id', '!=',$login_user_id)->where('email',$request->email)->count();
        if($checkEmail > 0){
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">Email already exist...</p>','s_msg'=>'Email already exist...'],200);
        }
        
        $update_data = [
            'name_title' => $request->name_title,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone1,
            'date_birth' => date('Y-m-d',strtotime($request->date_birth)),
            'is_active' => ($request->is_active==1)?1:2
        ];

        if(!empty($request->file('user_image'))){
            $update_data['user_image'] = $this->uploadFile($request, 'user_image', 'uploads/image/users');
        }
        
        $id = User::where('id',$login_user_id)->update($update_data);
        if($id > 0){
            $addressData = [
                'country_id' => !empty($request->country_id)?$request->country_id:'',
                'state_id' => !empty($request->state_id)?$request->state_id:'',
                'city_id' => !empty($request->city_id)?$request->city_id:'',
                'zip_code' => $request->zip_code,
                'phone1' => $request->phone1,
                'phone2' => $request->phone2,
                'address1' => $request->address1,
                'address2' => $request->address2,
                'address3' => $request->address3,
                'is_active' => ($request->is_active==1)?1:2
            ];

            if($request->address_id > 0){
                UserAddress::where('id',$request->address_id)->update($addressData);
            }else{
                $addressData['user_id'] = $login_user_id;
                UserAddress::insertGetId($addressData);
            }
        }

        return response()->json(['status' =>'success','message' => '<p class="alert alert-success">Profile updated...</p>','s_msg'=>'Profile updated...'],200);
    }

    public function showChangePassword(){
        return view('admin.profile.change_password');
    }

    public function changePassword(Request $request){
        $validated = $request->validate([
            'opass' => 'required',
            'npass' => 'required',
            'cpass' => 'required',
        ]);

        if(empty($request->opass) || empty($request->npass) || empty($request->cpass)){
            session()->flash('error', 'All fields are required...');
            return redirect()->back();
        }
        else if($request->npass != $request->cpass){
            session()->flash('error', 'Confirm password is not match...');
            return redirect()->back();
        }

        if(!Hash::check($request->opass, Auth::user()->password)) {
            session()->flash('error', 'The current password is incorrect...');
            return redirect()->back();

        } 

        Auth::user()->update([
            'password' => Hash::make($request->cpass),
        ]);

        session()->flash('success', 'Password updated...');
        return redirect()->back();
    }

    public function user_delete(Request $request){
        $check = Test::find($request->id);
        if($check){
            $check->delete();
        }
    }

    /* user details section tabs*/
    public function user_tab_detail(Request $request){
        $id = !empty($request->id)?$request->id:0;
        $page_type = !empty($request->page_type)?$request->page_type:'profile';
        $data = User::where('id',$id)->first();
        
        $department_type = @$data->department_type;
        if($page_type == 'profile'){
            $address = UserAddress::where('user_id',$id)->first();
            $apprisal_rate = Apprisal::selectRaw('SUM(rating) as total_rating, COUNT(*) as count_rating, ROUND(AVG(rating),1) as average_rating')->where('user_id',$id)->where('is_active',1)->first();
            return view('user.user.tab.edit_user',compact('data','address','apprisal_rate'));
        }
        else if($page_type == 'document'){
            $data_document = Document::where(['document_type'=>$department_type,'is_active'=>1])->limit(50)->get();
            $data_user = User::where(['is_active'=>1])->orderBy('id','DESC')->limit(50)->get();
            return view('user.user.tab.user_document_list',compact('data','data_document','data_user'));
        }
        else if($page_type == 'other_document'){
            $data_document = Document::where(['is_active'=>1])->limit(50)->get();
            return view('user.user.tab.other_document_list',compact('data','data_document'));
        }
        else if($page_type == 'vessel_check_in_out'){
            $vessel = Vessel::select('id','vessel_name','vessel_email')->where('is_active',1)->orderBy('vessel_name','ASC')->limit(50)->get();
            return view('user.user.tab.vessel_check_in_out',compact('data','vessel'));
        }
        else if($page_type == 'vessel_apprisal'){
            $vessel = Vessel::select('id','vessel_name','vessel_email')->where('is_active',1)->orderBy('vessel_name','ASC')->limit(50)->get();
            return view('user.user.tab.vessel_apprisal',compact('data','vessel'));
        }
    }

    public function user_document_list_tab_filter_count($search,$postData){
        $user_id = !empty($postData['user_id'])?$postData['user_id']:0;

        if(!empty($postData['search_user_name']) && $postData['search_user_name'] == 'all'){
            $filter_count = UserDocument::with('single_document')->where('user_id','>',0);
        }
        else if(!empty($postData['search_user_name'])){
            $user_id = $postData['search_user_name'];
            $filter_count = UserDocument::with('single_document')->where('user_id',$user_id);
        }else{
            $filter_count = UserDocument::with('single_document')->where('user_id',$user_id);  
        }

        if(!empty($search)) {
            $filter_count = UserDocument::with('single_document')->where('document_name', 'LIKE', '%'.$search.'%')->orWhere('document_type', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%')->orWhere('user_document', 'LIKE', '%'.$search.'%');
        }
        if(!empty($postData['search_start_date']) && !empty($postData['search_end_date'])){
            $search_start_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_start_date'])));
            $search_end_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_end_date']. ' +1 day')));

            $filter_count->where('created_at', '>=', date($search_start_date));
            $filter_count->where('created_at', '<=', date($search_end_date)); 
        }
        if(!empty($postData['search_document_name'])){
            $filter_count->where('document_name','LIKE','%'.$postData['search_document_name'].'%');
        }
        if(!empty($postData['search_document_category'])){
            $filter_count->where('document_id',$postData['search_document_category']);
        }

        if(!empty($postData['search_issue_date'])){
            $search_issue_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_issue_date'])));
            $filter_count->where('issue_date',date($search_issue_date));
        }
        if(!empty($postData['search_expiry_date'])){
            $search_expiry_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_expiry_date'])));
            $filter_count->where('expiry_date',date($search_expiry_date));
        }
        return $filter_count->count();
    }

    public function user_document_list_tab(Request $request){
        $postData = $request->input();
        $user_id = !empty($request->input('user_id'))?$request->input('user_id'):0;
        if(!empty($postData['search_user_name'])){
            $user_id = $postData['search_user_name'];
        }

        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','is_active','','document_name','','document_type','issue_date','expiry_date','user_document','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = UserDocument::with('single_document','single_user')->select('id','document_id','document_name','document_type','issue_date','expiry_date','user_document','created_at','is_active','user_id');
        
        if(!empty($postData['search_user_name']) && $postData['search_user_name'] == 'all'){
            $query->where('user_id','>',0);
        }
        else{
            $query->where('user_id',$user_id);
        }

        if(!empty($search)) {
            $query->where('document_name', 'LIKE', '%'.$search.'%')->orWhere('document_type', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%')->orWhere('user_document', 'LIKE', '%'.$search.'%');
        }
        
        /*custom search*/
        if(!empty($postData['search_start_date']) && !empty($postData['search_end_date'])){
            $search_start_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_start_date'])));
            $search_end_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_end_date']. ' +1 day')));

            $query->where('created_at', '>=', date($search_start_date));
            $query->where('created_at', '<=', date($search_end_date)); 
        }
        if(!empty($postData['search_document_name'])){
            $query->where('document_name','LIKE','%'.$postData['search_document_name'].'%');
        }
        
        if(!empty($postData['search_document_category'])){
            $query->where('document_id',$postData['search_document_category']);
        }

        if(!empty($postData['search_issue_date'])){
            $search_issue_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_issue_date'])));
            $query->where('issue_date',date($search_issue_date));
        }
        if(!empty($postData['search_expiry_date'])){
            $search_expiry_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_expiry_date'])));
            $query->where('expiry_date',date($search_expiry_date));
        }
        
        $query->orderBy($orderColumnIndex, $orderDirection);
        $users = $query->offset($start_limit)->limit($end_limit)->get(); 
        //printr($users);
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($users)){
            
            if(!empty($postData['search_user_name']) && $postData['search_user_name'] == 'all'){
                $recordsTotal = UserDocument::count();
            }
            else{
                $recordsTotal = UserDocument::where('user_id',$user_id)->count();
            }

            $sno = 1+$start_limit;
            foreach($users as $record){
                $edit = '<button class="btn btn-default btn-sm addEditLoader_'.$record->id.'" onclick="return add_edit_user_document('.$record->id.',\'edit\');" title="Edit"><i class="fa fa-edit"></i></button>';

                $access = '<button class="btn btn-default btn-sm accessLoader_'.$record->id.'" onclick="return access_rights_user_document('.$record->id.',\'access_user_document\');" title="Access rights"><i class="fas fa-key"></i></button>';

                $view = '<button class="btn btn-default btn-sm" onclick="return ajax_view('.$record->id.',\'user_document\');" title="View"><i class="fa fa-eye"></i></button>';

                $delete = '<button class="btn btn-default btn-sm deleteLoader_'.$record->id.'" onclick="return ajax_delete('.$record->id.',\'user_document\');" title="Delete"><i class="fa fa-trash"></i></button>';

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',1,\'user_document\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',2,\'user_document\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $issue_date = !empty($record->issue_date)?date('d/M/Y',strtotime($record->issue_date)):'';
                $expiry_date = !empty($record->expiry_date)?date('d/M/Y',strtotime($record->expiry_date)):'';
                
                
                $expiry_date_text = '';
                if(!empty($record->expiry_date)){
                    if(date($record->expiry_date) < date('Y-m-d')){
                        $expiry_date_text = '<span class="text-danger expiry_date_text">(Expired)</span>';
                    }else{
                        $expiry_date_text = '<span class="text-success">(Active)</span>';
                    }
                }

                if(!empty($record->user_document)){
                    $file_type = check_file_type($record->user_document);
                    
                    $doc_url = '<button class="btn btn-default" onclick="return view_document(\''.$record->user_document.'\',\''.$file_type.'\',\'user_document\');"><i class="fa fa-eye"> '.$file_type.'</i></button>';
                }else{
                    $doc_url = '<button class="btn btn-default"><i class="fa fa-close"> no</i></button>';
                }

                $category_name = @$record->single_document->category_name;
                $document_user_name = @$record->single_user->first_name;

                $all_data[] = [
                    'sno'=> $sno++,
                    'action'=>$edit.' '.$status.' '.$access.' '.$delete,
                    'document_name'=> @$record->document_name,
                    'document_user_name'=> @$document_user_name,
                    'category_name'=> @$category_name,
                    'document_type'=> @$record->document_type,
                    'issue_date'=> $issue_date,
                    'expiry_date'=> $expiry_date.' '.$expiry_date_text,
                    'user_document'=> @$doc_url,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at))
                ];
            }
        }
        //printr($all_data,'p');
        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->user_document_list_tab_filter_count($search,$postData),
            'data' => $all_data,
        ]);
    }

    public function add_user_document(Request $request){
        //printr($request->file('user_document'),'pp');
        if(empty($request->document_name) || empty($request->document_id) || empty($request->user_id)){
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">All fields are required...</p>','s_msg'=>'All fields are required...'],200);
        }
        $p_id = !empty(@$request->p_id)?@$request->p_id:'';
        if(empty($request->user_document) && $p_id < 1){
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">Document is required...</p>','s_msg'=>'Document is required...'],200);
        }
        
        $postData = [
            'user_id'=>@$request->user_id,
            'document_id' => $request->document_id,
            'document_type' => $request->document_type,
            'document_name' => $request->document_name,
            'issue_date' => !empty($request->issue_date)?date('Y-m-d',strtotime(str_replace('/','-',$request->issue_date))):null,
            'expiry_date' => !empty($request->expiry_date)?date('Y-m-d',strtotime(str_replace('/','-',$request->expiry_date))):null,
            'is_active' => ($request->is_active==1)?1:2,
            'description' => @$request->document_description,
            'created_by'=>Auth::user()->id
        ];
        if(!empty($request->user_document)){
            $postData['user_document'] = $request->user_document;
        }

        if($p_id <= 0){
            $id = UserDocument::insertGetId($postData);
        }else{
            UserDocument::where('id',$request->p_id)->update($postData);
            $id = $p_id;
        }
        if($id > 0){
            return response()->json(['status' =>'success','message' => '<p class="alert alert-success">User document uploaded...</p>','s_msg'=>'User document uploaded...'],200);
        }else{
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">Opps! Something went wrong...</p>','s_msg'=>'Opps! Something went wrong...'],200);
        }
    }

    public function user_document_edit(Request $request){
        $data = UserDocument::where('id',$request->p_id)->first();
        if(!empty($data->user_document)){
            $doc_url = asset('storage/app/public/uploads/document/users').'/'.$data->user_document;
            $doc_url = '<a target="_blank" class="btn btn-default" href="'.$doc_url.'">'.$data->user_document.'</a>';
        }else{
            $doc_url = '';
        }
        $data['user_document'] = @$doc_url;
        if(!empty($data->issue_date)){
            $data['issue_date'] = date('d/m/Y',strtotime($data->issue_date));
        }
        if(!empty($data->expiry_date)){
            $data['expiry_date'] = date('d/m/Y',strtotime($data->expiry_date));
        }
        echo json_encode(['data'=>$data]);
    }

    /* access rights code*/
    public function access_rights_user_document_list_tab_filter_count($search,$postData){
        
        if(!empty($search)) {
            $filter_count = User::where('is_active',1)->where('first_name', 'LIKE', '%'.$search.'%')->orWhere('middle_name', 'LIKE', '%'.$search.'%')->orWhere('last_name', 'LIKE', '%'.$search.'%')->orWhere('email', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%');
        }else{
            $filter_count = User::where('is_active',1);
        }
        return $filter_count->count();
    }

    public function check_access_user_document($user_id='',$document_id=''){
        $count = UserDocumentAccess::where(['user_id'=>$user_id,'document_id'=>$document_id])->count();
        return $count;
    }

    public function access_rights_user_document_list_tab(Request $request){
        $postData = $request->input();
        $document_id = !empty($request->input('p_id'))?$request->input('p_id'):0;
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','','','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = User::select('id','name_title','first_name','middle_name','last_name','email','created_at','is_active')->where('is_active',1);

        if(!empty($search)) {
            $query->where('first_name', 'LIKE', '%'.$search.'%')->orWhere('middle_name', 'LIKE', '%'.$search.'%')->orWhere('last_name', 'LIKE', '%'.$search.'%')->orWhere('email', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%');
        }
        
        $query->orderBy($orderColumnIndex, $orderDirection);
        $users = $query->offset($start_limit)->limit($end_limit)->get(); 
        //printr($users);
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($users)){
            
            $recordsTotal = User::count();

            $sno = 1+$start_limit;
            foreach($users as $record){
                if($record->is_active == 1){
                    $check_access = $this->check_access_user_document($record->id,$document_id);
                    $check_access = ($check_access > 0)?'checked':'';

                    $checkBox = '
                        <div class="form-check text-center">
                            <input type="checkbox" class="form-check-input access_rights_user" id="access_rights_user_'.$record->id.'" value="'.$record->id.'" onclick="return access_rights_user_document_add_ids('.$record->id.','.$document_id.',\'access_rights_user_document\');" '.$check_access.'>
                            <span id="access_rights_user_loader_'.$record->id.'"></span>
                        </div>';
                }else{
                    $checkBox = '
                        <div class="form-check text-center">
                            <input type="checkbox" class="form-check-input access_rights_user" disabled>
                        </div>';
                }

                $all_data[] = [
                    'sno'=> $sno++,
                    'action'=>$checkBox,
                    'user_name'=> $record->name_title.' '.$record->first_name.' '.$record->middle_name.' '.$record->last_name,
                    'email'=> !empty($record->email)?$record->email:'No email',
                    'created_at'=> date('d/M/Y',strtotime($record->created_at))
                ];
            }
        }
        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->access_rights_user_document_list_tab_filter_count($search,$postData),
            'data' => $all_data,
        ]);
    }

    public function user_document_access_save(Request $request){
        if(empty($request->user_id) || empty($request->document_id)){
            return response()->json(['status' =>'failed','s_msg'=>'All fields are required...'],200);
        }
        $checkExist = UserDocumentAccess::where(['user_id'=>$request->user_id,'document_id'=>$request->document_id])->count();
        if($checkExist > 0){
            UserDocumentAccess::where(['user_id'=>$request->user_id,'document_id'=>$request->document_id])->forceDelete();
            return response()->json(['status' =>'success','s_msg'=>'Permission revoke'],200);
        }
        
        $postData = [
            'user_id'=>@$request->user_id,
            'document_id' => $request->document_id,
            'created_by'=>Auth::user()->id
        ];

        $id = UserDocumentAccess::insertGetId($postData);
        if($id > 0){
            return response()->json(['status' =>'success','s_msg'=>'Permission applied'],200);
        }else{
            return response()->json(['status' =>'failed','s_msg'=>'Opps! Something went wrong...'],200);
        }
    }

    /* other document */
    public function user_other_document_list_tab_filter_count($search,$postData){
        $user_id = !empty($postData['user_id'])?$postData['user_id']:0;

        $filter_count = UserDocument::with('single_document')->where('user_id',$user_id);

        if(!empty($search)) {
            $filter_count = UserDocument::with('single_document')->where('document_name', 'LIKE', '%'.$search.'%');
        }
        if(!empty($postData['search_start_date']) && !empty($postData['search_end_date'])){
            $search_start_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_start_date'])));
            $search_end_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_end_date']. ' +1 day')));

            $filter_count->where('created_at', '>=', date($search_start_date));
            $filter_count->where('created_at', '<=', date($search_end_date)); 
        }
        if(!empty($postData['search_document_name'])){
            $filter_count->where('document_name','LIKE','%'.$postData['search_document_name'].'%');
        }
        if(!empty($postData['search_document_category'])){
            $filter_count->where('document_id',$postData['search_document_category']);
        }

        if(!empty($postData['search_issue_date'])){
            $search_issue_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_issue_date'])));
            $filter_count->where('issue_date',date($search_issue_date));
        }
        if(!empty($postData['search_expiry_date'])){
            $search_expiry_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_expiry_date'])));
            $filter_count->where('expiry_date',date($search_expiry_date));
        }
        return $filter_count->count();
    }

    public function user_other_document_list_tab(Request $request){
        $postData = $request->input();
        $user_id = !empty($request->input('user_id'))?$request->input('user_id'):0;
        
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }

        $all_dc_id = [];
        $document_id = UserDocumentAccess::select('id','document_id')->where('user_id',$user_id)->offset($start_limit)->limit($end_limit)->get();
        
        if(count($document_id) > 0){
            foreach($document_id as $d_id){
                $all_dc_id[] = $d_id->document_id;
            }
        }

        if(empty($all_dc_id) || $user_id < 1){
            return response()->json([
                'draw' => 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }
        //printr($all_dc_id,'p');
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','','document_name','','document_type','issue_date','expiry_date','user_document','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = UserDocument::with('single_document','single_user')->select('id','document_id','document_name','document_type','issue_date','expiry_date','user_document','created_at','is_active','user_id')->whereIn('id',$all_dc_id);

        if(!empty($search)) {
            $query->where('document_name', 'LIKE', '%'.$search.'%');
        }
        
        /*custom search*/
        if(!empty($postData['search_start_date']) && !empty($postData['search_end_date'])){
            $search_start_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_start_date'])));
            $search_end_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_end_date']. ' +1 day')));

            $query->where('created_at', '>=', date($search_start_date));
            $query->where('created_at', '<=', date($search_end_date)); 

        }
        //printr($postData['search_start_date'],'p');
        if(!empty($postData['search_document_name'])){
            $query->where('document_name','LIKE','%'.$postData['search_document_name'].'%');
        }
        
        if(!empty($postData['search_document_category'])){
            $query->where('document_id',$postData['search_document_category']);
        }

        if(!empty($postData['search_issue_date'])){
            $search_issue_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_issue_date'])));
            $query->where('issue_date',date($search_issue_date));
        }
        if(!empty($postData['search_expiry_date'])){
            $search_expiry_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_expiry_date'])));
            $query->where('expiry_date',date($search_expiry_date));
        }
        
        $query->orderBy($orderColumnIndex, $orderDirection);
        $users = $query->offset($start_limit)->limit($end_limit)->get(); 
        //printr($users);
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($users)){
            
            $recordsTotal = UserDocumentAccess::count();

            $sno = 1+$start_limit;
            foreach($users as $record){
                $edit = '<button class="btn btn-default btn-sm addEditLoader_'.$record->id.'" onclick="return add_edit_user_document('.$record->id.',\'edit\');" title="Edit"><i class="fa fa-edit"></i></button>';

                $access = '<button class="btn btn-default btn-sm accessLoader_'.$record->id.'" onclick="return access_rights_user_document('.$record->id.',\'access_user_document\');" title="Access rights"><i class="fas fa-key"></i></button>';

                $view = '<button class="btn btn-default btn-sm" onclick="return ajax_view('.$record->id.',\'user_document\');" title="View"><i class="fa fa-eye"></i></button>';

                $delete = '<button class="btn btn-default btn-sm deleteLoader_'.$record->id.'" onclick="return ajax_delete('.$record->id.',\'user_document\');" title="Delete"><i class="fa fa-trash"></i></button>';

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',1,\'user_document\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',2,\'user_document\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $issue_date = !empty($record->issue_date)?date('d/M/Y',strtotime($record->issue_date)):'';
                $expiry_date = !empty($record->expiry_date)?date('d/M/Y',strtotime($record->expiry_date)):'';
                
                if(!empty($record->user_document)){
                    $file_type = check_file_type($record->user_document);
                    
                    $doc_url = '<button class="btn btn-default" onclick="return view_document(\''.$record->user_document.'\',\''.$file_type.'\',\'user_document\');"><i class="fa fa-eye"> '.$file_type.'</i></button>';
                }else{
                    $doc_url = '<button class="btn btn-default"><i class="fa fa-close"> no</i></button>';
                }

                $expiry_date_text = '';
                if(!empty($record->expiry_date)){
                    if(date($record->expiry_date) < date('Y-m-d')){
                        $expiry_date_text = '<span class="text-danger expiry_date_text">(Expired)</span>';
                    }else{
                        $expiry_date_text = '<span class="text-success">(Active)</span>';
                    }
                }

                $category_name = @$record->single_document->category_name;
                $document_user_name = @$record->single_user->first_name;

                $all_data[] = [
                    'sno'=> $sno++,
                    'action'=>$edit.' '.$status.' '.$access.' '.$delete,
                    'document_name'=> @$record->document_name,
                    'document_user_name'=> @$document_user_name,
                    'category_name'=> @$category_name,
                    'document_type'=> @$record->document_type,
                    'issue_date'=> $issue_date,
                    'expiry_date'=> $expiry_date.' '.$expiry_date_text,
                    'user_document'=> @$doc_url,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at))
                ];
            }
        }
        //printr($all_data,'p');
        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->user_other_document_list_tab_filter_count($search,$postData),
            'data' => $all_data,
        ]);
    }

    public function vessel_check_in_out_list_tab_filter_count($search,$postData){
        $filter_count = VesselCheckInOut::where('id','>',0)->where('user_id',$postData['user_id']);

        if(!empty($search)) {
            $filter_count = VesselCheckInOut::where('description', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%')->count();
        }
        if(!empty($postData['search_vessel_id'])) {
            $filter_count->where('vessel_id',$postData['search_vessel_id']);
        }
        if(!empty($postData['search_start_check_in_date']) && !empty($postData['search_end_check_in_date'])) {
            $search_start_check_in_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_start_check_in_date'])));
            $search_end_check_in_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_end_check_in_date'])));
            
            $filter_count->where('check_in_date', '>=', date($search_start_check_in_date));
            $filter_count->where('check_in_date', '<=', date($search_end_check_in_date));
        }
        else if(!empty($postData['search_start_check_in_date'])){
            $search_start_check_in_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_start_check_in_date'])));
            $filter_count->where('check_in_date',date($search_start_check_in_date));
        }

        if(!empty($postData['search_start_check_out_date']) && !empty($postData['search_end_check_out_date'])) {
            $search_start_check_out_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_start_check_out_date'])));
            $search_end_check_out_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_end_check_out_date'])));
            
            $filter_count->where('check_out_date', '>=', date($search_start_check_out_date));
            $filter_count->where('check_out_date', '<=', date($search_end_check_out_date));
        }
        else if(!empty($postData['search_start_check_out_date'])){
            $search_start_check_out_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_start_check_out_date'])));
            $filter_count->where('check_out_date',date($search_start_check_out_date));
        }
        return $filter_count->count();
    }

    public function vessel_check_in_out_list_tab(Request $request){
        $postData = $request->input();
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','is_active','','','check_in_date','check_out_date','description','check_out_date','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = VesselCheckInOut::with('single_user','single_vessel')->select('id','user_id','vessel_id','description','check_out_description','check_in_date','check_out_date','created_at','is_active','check_status')->where('user_id',$request->input('user_id'));
        
        if(!empty($request->input('search_vessel_id'))){
            $query->where('vessel_id',$request->input('search_vessel_id')); 
        }
        
        if(!empty($request->input('search_start_check_in_date')) && !empty($request->input('search_end_check_in_date'))){
            $search_start_check_in_date = date('Y-m-d',strtotime(str_replace('/','-',$request->input('search_start_check_in_date'))));
            $search_end_check_in_date = date('Y-m-d',strtotime(str_replace('/','-',$request->input('search_end_check_in_date'))));
            
            $query->where('check_in_date', '>=', date($search_start_check_in_date));
            $query->where('check_in_date', '<=', date($search_end_check_in_date));
        }
        else if(!empty($request->input('search_start_check_in_date'))){
            $search_start_check_in_date = date('Y-m-d',strtotime(str_replace('/','-',$request->input('search_start_check_in_date'))));
            $query->where('check_in_date',date($search_start_check_in_date));
        }

        if(!empty($request->input('search_start_check_out_date')) && !empty($request->input('search_end_check_out_date'))){
            $search_start_check_out_date = date('Y-m-d',strtotime(str_replace('/','-',$request->input('search_start_check_out_date'))));
            $search_end_check_out_date = date('Y-m-d',strtotime(str_replace('/','-',$request->input('search_end_check_out_date'))));
            
            $query->where('check_out_date', '>=', date($search_start_check_out_date));
            $query->where('check_out_date', '<=', date($search_end_check_out_date));
        }
        else if(!empty($request->input('search_start_check_out_date'))){
            $search_start_check_out_date = date('Y-m-d',strtotime(str_replace('/','-',$request->input('search_start_check_out_date'))));
            $query->where('check_out_date',date($search_start_check_out_date));
        }


        $query->orderBy($orderColumnIndex, $orderDirection);
        $response_data = $query->offset($start_limit)->limit($end_limit)->get(); 
        //printr($response_data);
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($response_data)){
            $recordsTotal = VesselCheckInOut::where('user_id',$request->input('user_id'))->count();
            $sno = 1+$start_limit;
            foreach($response_data as $record){
                $check_in_date = !empty($record->check_in_date)?date('d/M/Y',strtotime($record->check_in_date)):'Empty';

                $user_name = !empty(@$record->single_user->first_name)?$record->single_user->first_name.' ('.$record->single_user->email.')':'';
                $vessel_name = !empty(@$record->single_vessel->vessel_name)?$record->single_vessel->vessel_name.' ('.$record->single_vessel->vessel_email.')':'';

                $all_data[] = [
                    'sno'=> $sno++,
                    'user_name'=> $user_name,
                    'vessel_name'=> $vessel_name,
                    'check_in_date'=> date('d/M/Y',strtotime($record->check_in_date)),
                    'check_out_date'=> !empty($record->check_out_date)?'<span class="text-danger">'.date('d/M/Y',strtotime($record->check_out_date)).'</span>':'',
                    'description'=> $record->description,
                    'check_out_description'=> $record->check_out_description,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at)),
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->vessel_check_in_out_list_tab_filter_count($search,$postData),
            'data' => $all_data,
        ]);
    }

    /* vessel apprisal list tab */
    public function vessel_apprisal_list_tab_filter_count($search,$postData){
        $filter_count = Apprisal::where('id','>',0)->where('user_id',$postData['user_id']);

        if(!empty($search)) {
            $filter_count = Apprisal::where('description', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%')->count();
        }
        else if(!empty($postData['search_start_apprisal_date']) && !empty($postData['search_end_apprisal_date'])){
            $search_start_apprisal_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_start_apprisal_date'])));
            $search_end_apprisal_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_end_apprisal_date'])));
            
            $filter_count->where('apprisal_date', '>=', date($search_start_apprisal_date));
            $filter_count->where('apprisal_date', '<=', date($search_end_apprisal_date));
        }
        else if(!empty($postData['search_start_apprisal_date'])){
            $search_start_apprisal_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_start_apprisal_date'])));
            $filter_count->where('apprisal_date',date($search_start_apprisal_date));
        }
        else if(!empty($postData['search_rating'])){
            $filter_count->where('rating',$postData['search_rating']);
        }
        return $filter_count->count();
    }

    public function vessel_apprisal_list_tab(Request $request){
        $postData = $request->input();
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','is_active','','','apprisal_date','rating','specific_strength','area_of_improvement','','','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = Apprisal::with('assign_user','vessel_user')->select('*')->where('user_id',$request->input('user_id'));
        
        if(!empty($request->input('search_start_apprisal_date')) && !empty($request->input('search_end_apprisal_date'))){
            $search_start_apprisal_date = date('Y-m-d',strtotime(str_replace('/','-',$request->input('search_start_apprisal_date'))));
            $search_end_apprisal_date = date('Y-m-d',strtotime(str_replace('/','-',$request->input('search_end_apprisal_date'))));
            
            $query->where('apprisal_date', '>=', date($search_start_apprisal_date));
            $query->where('apprisal_date', '<=', date($search_end_apprisal_date));
        }
        else if(!empty($request->input('search_start_apprisal_date'))){
            $search_start_apprisal_date = date('Y-m-d',strtotime(str_replace('/','-',$request->input('search_start_apprisal_date'))));
            $query->where('apprisal_date',date($search_start_apprisal_date));
        }
        if(!empty($request->input('search_rating'))){
            $query->where('rating',$request->input('search_rating'));
        }


        $query->orderBy($orderColumnIndex, $orderDirection);
        $response_data = $query->offset($start_limit)->limit($end_limit)->get(); 
        //printr($response_data);
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($response_data)){
            $recordsTotal = Apprisal::where('user_id',$request->input('user_id'))->count();
            $sno = 1+$start_limit;
            foreach($response_data as $record){
                $check_in_date = !empty($record->check_in_date)?date('d/M/Y',strtotime($record->check_in_date)):'Empty';

                $user_name = !empty(@$record->single_user->first_name)?$record->single_user->first_name.' ('.$record->single_user->email.')':'';
                $vessel_name = !empty(@$record->single_vessel->vessel_name)?$record->single_vessel->vessel_name.' ('.$record->single_vessel->vessel_email.')':'';

                $edit = $delete = '';
                $status = '#';
                if($record->login_user_id == Auth::user()->id){

                    $edit = '<button class="btn btn-default btn-sm addEditLoader_'.$record->id.'" onclick="return add_edit_apprisal('.$record->id.',\'edit\');" title="Edit"><i class="fa fa-edit"></i></button>';

                    $delete = '<button class="btn btn-default btn-sm deleteLoader_'.$record->id.'" onclick="return ajax_delete('.$record->id.',\'apprisal\');" title="Delete"><i class="fa fa-trash"></i></button>';

                    if($record->is_active == 1){
                        $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',1,\'apprisal\');" title="Active"><i class="fa fa-check"></i></button>';
                    }else{
                        $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',2,\'apprisal\');" title="In-Active"><i class="fa fa-close"></i></button>';
                    }
                }


                $vessel_user = @$record->vessel_user->name_title.' '.@$record->vessel_user->first_name.' ('.@$record->vessel_user->email.')';
                $assign_user = @$record->assign_user->name_title.' '.@$record->assign_user->first_name.' ('.@$record->assign_user->email.')';;

                $all_data[] = [
                    'sno'=> $sno++,
                    'action'=>$edit.' '.$status.' '.$delete,
                    'vessel_user'=> $vessel_user,
                    'assign_user'=> $assign_user,
                    'apprisal_date'=> date('d/M/Y',strtotime($record->apprisal_date)),
                    'rating'=> $record->rating,
                    'specific_strength'=> '<a href="#" title="'.$record->specific_strength.'">'.substr($record->specific_strength,0,15).'...</a>',
                    'area_of_improvement'=> '<a href="#" title="'.$record->area_of_improvement.'">'.substr($record->area_of_improvement,0,15).'...</a>',
                    'additional_notes'=> '<a href="#" title="'.$record->additional_notes.'">'.substr($record->additional_notes,0,15).'...</a>',
                    'description'=> '<a href="#" title="'.$record->description.'">'.substr($record->description,0,15).'...</a>',
                    'created_at'=> date('d/M/Y',strtotime($record->created_at)),
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->vessel_apprisal_list_tab_filter_count($search,$postData),
            'data' => $all_data,
        ]);
    }

    public function vessel_apprisal_list_edit(Request $request){
        $data = Apprisal::where('id',$request->p_id)->first();
        if(!empty($data->apprisal_date)){
            $data['apprisal_date'] = date('d/m/Y',strtotime($data->apprisal_date));
        }
        echo json_encode(['data'=>$data]);
    }

    public function add_update_vessel_apprisal(Request $request){
        //printr($request->file('user_document'),'pp');
        if(empty($request->rating) || empty($request->apprisal_date) || empty($request->user_id)){
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">All fields are required...</p>','s_msg'=>'All fields are required...'],200);
        }
        $p_id = !empty(@$request->p_id)?@$request->p_id:'';
        
        $postData = [
            'user_id'=>@$request->user_id,
            'rating' => $request->rating,
            'apprisal_date' => !empty($request->apprisal_date)?date('Y-m-d',strtotime(str_replace('/','-',$request->apprisal_date))):null,
            'is_active' => ($request->is_active==1)?1:2,
            'login_user_id' => Auth::user()->id,
            'specific_strength' => @$request->specific_strength,
            'area_of_improvement' => @$request->area_of_improvement,
            'additional_notes' => @$request->additional_notes,
            'description' => @$request->apprisal_description,
            'created_by'=>Auth::user()->id
        ];

        if($p_id < 1){
            $id = Apprisal::insertGetId($postData);
            $s_msg = 'Apprisal added successfully...';
        }else{
            Apprisal::where('id',$request->p_id)->update($postData);
            $s_msg = 'Apprisal updated successfully...';
            $id = $p_id;
        }
        if($id > 0){
            return response()->json(['status' =>'success','s_msg'=>$s_msg],200);
        }else{
            return response()->json(['status' =>'failed','s_msg'=>'Opps! Something went wrong...'],200);
        }
    }
}
