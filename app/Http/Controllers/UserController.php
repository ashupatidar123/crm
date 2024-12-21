<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
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

    public function user_list_filter_count($search){
        if(!empty($search)) {
            $filter_count = User::where('first_name', 'LIKE', '%'.$search.'%')->orWhere('email', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%')->orWhere('login_id', 'LIKE', '%'.$search.'%')->count();
        }else{
            $filter_count = User::count();
        }
        return $filter_count;
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
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','id','first_name','login_id','email','date_birth','created_at','is_active'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = User::select('id','first_name','last_name','login_id','email','date_birth','created_at','is_active');
        if(!empty($search)) {
            $query->where('first_name', 'LIKE', '%'.$search.'%')->orWhere('login_id', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%')->orWhere('email', 'LIKE', '%'.$search.'%');
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
                $edit = '<a href="'.url('master/edit-user').'/'.$record->id.'" class="btn btn-default btn-sm" title="Edit"><i class="fa fa-edit"></i></a>';
                $delete = '<button class="btn btn-default btn-sm" onclick="return ajax_delete('.$record->id.',\'user\');" title="Delete"><i class="fa fa-trash"></i></button>';

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm" onclick="return ajax_active_inactive('.$record->id.',1,\'user\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm" onclick="return ajax_active_inactive('.$record->id.',2,\'user\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $all_data[] = [
                    'sno'=> $sno++,
                    'id'=> $record->id,
                    'first_name'=> $record->first_name.' '.$record->last_name,
                    'login_id'=> $record->login_id,
                    'email'=> $record->email,
                    'date_birth'=> !empty($record->date_birth)?date('d/M/Y',strtotime($record->date_birth)):'',
                    'created_at'=> date('d/M/Y',strtotime($record->created_at)),
                    'status'=>$status,
                    'action'=>$edit.' '.$delete
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->user_list_filter_count($search),
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
        
        if(empty($request->first_name) || empty($request->login_id) || empty($request->role) || empty($request->reporting_role_id) || empty($request->department_id) || empty($request->password) || empty($request->email)){
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
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'user_image' => $user_image,
            'phone' => $request->phone1,
            'date_birth' => date('Y-m-d',strtotime($request->date_birth)),
            'is_active' => ($request->is_active==1)?1:2,
            'login_id' => $request->login_id,
            'role_id' => $request->role,
            'reporting_role_id' => $request->reporting_role_id,
            'department_id' => $request->department_id,
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
        else if($show_type == 'ajax_single'){
            $data = Role::select('id','role_name','rank')->where('id',$request->p_id)->where('is_active',1)->first();
            echo json_encode(['data'=>$data]);
        }
    }

    public function showEditUser(Request $request){
        $data = User::where('id',$request->id)->first();
        if(empty($data)){
            return redirect(url('master/user'),302);
        }
        $address = UserAddress::where('user_id',$request->id)->first();
        $role = Role::select('id','role_name','rank')->where('is_active',1)->get();
        $department = Department::select('id','department_name')->where('is_active',1)->get();

        return view('master.user.edit_user',compact('data','role','address','department'));
    }

    public function update_user(Request $request){
        if(empty($request->first_name) || empty($request->role) || empty($request->reporting_role_id) || empty($request->department_id) || empty($request->user_id) || empty($request->address_id) || empty($request->email)){
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">All fields are required...</p>','s_msg'=>'All fields are required...'],200);
        }

        $checkEmail = User::where('id', '!=',$request->user_id)->where('email',$request->email)->count();
        if($checkEmail > 0){
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">Email already exist...</p>','s_msg'=>'Email already exist...'],200);
        }
        
        $update_data = [
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone1,
            'date_birth' => date('Y-m-d',strtotime($request->date_birth)),
            'is_active' => ($request->is_active==1)?1:2,
            'role_id' => $request->role,
            'reporting_role_id' => $request->reporting_role_id,
            'department_id' => $request->department_id,
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

    public function showProfile(){
        $data = User::where('id',Auth::user()->id)->first();
        return view('admin.profile.profile',compact('data'));
    }

    public function updateProfile(Request $request){
        User::where('id',Auth::user()->id)->update([
            'first_name' => $request->first_name,
            'phone' => $request->phone,
        ]);
        session()->flash('success', 'Profile updated...');
        return redirect()->back();
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
