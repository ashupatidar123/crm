<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\DepartmentDesignation;
use App\Models\UserAddress;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Traits\FileUploadTrait;

class UserController extends Controller
{   
    use FileUploadTrait;
    public function __construct(){

    }

    public function dashboard(){
        return view('dashboard');
    }

    public function user(){
        return view('master.user.user_list');
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
        
        if(!empty($search_department_name)) {
            $filter_count->whereHas('single_department', function ($query) use ($search_department_name) {
                $query->where('department_name', 'LIKE', '%'.$search_department_name.'%');
            });
        }
        if(!empty($search_designation_name)) {
            $filter_count->whereHas('single_designation', function ($query) use ($search_designation_name) {
                $query->where('designation_name', 'LIKE', '%'.$search_designation_name.'%');
            });
        }
        return $filter_count->count();
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
        if(!empty($search_department_name)){
            $query->whereHas('single_department', function ($query) use ($search_department_name) {
                $query->where('department_name', 'LIKE', '%'.$search_department_name.'%');
            });
        }
        if(!empty($search_designation_name)){
            $query->whereHas('single_designation', function ($query) use ($search_designation_name) {
                $query->where('designation_name', 'LIKE', '%'.$search_designation_name.'%');
            });
        }
        $query->orderBy($orderColumnIndex, $orderDirection);
        $users = $query->offset($start_limit)->limit($end_limit)->get(); 
        
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($users)){
            $recordsTotal = User::count();
            $sno = 1+$start_limit;
            foreach($users as $record){
                $edit = '<a href="'.url('master/edit-user').'/'.$record->id.'" class="btn btn-default btn-sm" title="Edit"><i class="fa fa-edit"></i></a>';
                $view = '<button class="btn btn-default btn-sm" onclick="return ajax_view('.$record->id.',\'user\');" title="View"><i class="fa fa-eye"></i></button>';

                $delete = '<button class="btn btn-default btn-sm" onclick="return ajax_delete('.$record->id.',\'user\');" title="Delete"><i class="fa fa-trash"></i></button>';

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm" onclick="return ajax_active_inactive('.$record->id.',1,\'user\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm" onclick="return ajax_active_inactive('.$record->id.',2,\'user\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $department_type = @$record->single_department->department_type;
                $department_name = @$record->single_department->department_name;
                $designation_name = @$record->single_designation->designation_name;

                $date_birth = !empty($record->date_birth)?date('d/M/Y',strtotime($record->date_birth)):'';

                $all_data[] = [
                    'sno'=> $sno++,
                    'action'=>$edit.' '.$status.' '.$view.' '.$delete,
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

    public function showAddUser(){
        $user = User::where('id',Auth::user()->id)->first();
        $role = Role::select('id','role_name','rank')->where('is_active',1)->get();
        $department = Department::select('id','department_name')->where('is_active',1)->get();
        return view('master.user.add_user',compact('user','role','department'));
    }

    public function add_user(Request $request){
        
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
                'zip_code' => $request->zip_code,
                'phone1' => $request->phone1,
                'phone2' => $request->phone2,
                'address1' => $request->address1,
                'address2' => $request->address2,
                'address3' => $request->address3,
                'is_active' => ($request->is_active==1)?1:2,
                'created_by'=>Auth::user()->id
            ]);

            return response()->json(['status' =>'success','message' => '<p class="alert alert-success">User registration success...</p>','s_msg'=>'User registration success...'],200);
        }else{
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">Opps! Something went wrong...</p>','s_msg'=>'Opps! Something went wrong...'],200);
        }
    }

    public function showEditUser(Request $request){
        $data = User::where('id',$request->id)->first();
        if(empty($data)){
            return redirect(url('master/user'),302);
        }
        $address = UserAddress::where('user_id',$request->id)->first();
        return view('master.user.edit_user',compact('data','address'));
    }

    public function update_user(Request $request){
        if(empty($request->first_name) || empty($request->department_type) || empty($request->department_id) || empty($request->department_designation_id) || empty($request->user_id) || empty($request->address_id) || empty($request->email)){
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
            $address = UserAddress::where('id',$request->address_id)->update([
                'country_id' => !empty($request->country_id)?$request->country_id:'',
                'state_id' => !empty($request->state_id)?$request->state_id:'',
                'city_id' => !empty($request->city_id)?$request->city_id:'',
                'zip_code' => $request->zip_code,
                'phone1' => $request->phone1,
                'phone2' => $request->phone2,
                'address1' => $request->address1,
                'address2' => $request->address2,
                'address3' => $request->address3,
                'is_active' => ($request->is_active==1)?1:2,
                'created_by'=>Auth::user()->id
            ]);

            return response()->json(['status' =>'success','message' => '<p class="alert alert-success">User updated successfully...</p>','s_msg'=>'User updated successfully...'],200);
        }else{
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">Opps! Something went wrong...</p>','s_msg'=>'Opps! Something went wrong...'],200);
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
            $data = Department::select('id','department_name','department_type')->where('department_type',$department_type)->where('is_active',1)->orderBy('department_name','ASC')->limit(500)->get();
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

    public function showProfile(){
        $data = User::with('single_department','single_designation')->where('id',Auth::user()->id)->first();
        //printr($data);
        if(empty($data)){
            return redirect(url('master/user'),302);
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
}
