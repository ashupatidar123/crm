<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\UserAddress;

class UserController extends Controller
{
    public function __construct(){

    }

    public function home(){
        return view('dashboard');
    }

    public function dashboard(){
        return view('dashboard');
    }

    public function userList_filter_count($search){
        if(!empty($search)) {
            $filter_count = User::where('name', 'LIKE', '%'.$search.'%')->orWhere('email', 'LIKE', '%'.$search.'%')->count();
        }else{
            $filter_count = User::count();
        }
        return $filter_count;
    }

    public function userList(Request $request){
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):'';
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):0;
        if($start_limit < 1){
            $start_limit = $request->input('start');
            $end_limit   = $request->input('length');
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['id','name','email','mobile','amount','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = User::select('id','name','email','mobile','amount','created_at');
        if(!empty($search)) {
            $query->where('name', 'LIKE', '%'.$search.'%')->orWhere('email', 'LIKE', '%'.$search.'%');
        }
        $query->orderBy($orderColumnIndex, $orderDirection);
        $users = $query->skip($start_limit)->take($end_limit)->get(); 

        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($users)){
            $recordsTotal = User::count();
            
            foreach($users as $record){
                $all_data[] = [
                    'id'=> $record->id,
                    'name'=> $record->name,
                    'email'=> $record->email,
                    'mobile'=> $record->mobile,
                    'amount'=> $record->amount,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at))
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->userList_filter_count($search),
            'data' => $all_data,
        ]);
    }

    public function showRegistration(){
        $user = User::where('id',Auth::user()->id)->first();
        $role = Role::select('id','role_name')->where('is_active',1)->get();
        return view('admin.profile.register',compact('user','role'));
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

    public function register(Request $request){
        if(empty($request->first_name) || empty($request->role) || empty($request->login_id) || empty($request->password) || empty($request->email)){
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">All fields are required...</p>'],200);
        }

        $checkEmail = User::where(['email' => $request->email])->count();
        if($checkEmail > 0){
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">Email already exist...</p>'],500);
        }
        $checkLoginId = User::where(['login_id' => $request->login_id])->count();
        if($checkLoginId > 0){
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">Login id already exist...</p>'],500);
        }
        
        $id = User::insertGetId([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'date_birth' => date('Y-m-d',strtotime($request->date_birth)),
            'is_active' => $request->is_active,
            'role_id' => $request->role,
            'login_id' => $request->login_id,
            'password' => Hash::make($request->password),
            'created_by'=>User::Auth()->id
        ]);
        
        if($id > 0){
            $address = UserAddress::insertGetId([
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
                'zip_code' => $request->zip_code,
                'phone1' => $request->phone1,
                'phone2' => $request->phone2,
                'address1' => $request->address1,
                'address2' => $request->address2
            ]);

            return response()->json(['status' =>'success','message' => '<p class="alert alert-success">User registration success...</p>'],200);
        }else{
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">Opps! Something went wrong...</p>'],500);
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
}
