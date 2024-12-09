<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Address;

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
    
    public function advanced_form(){
        return view('forms.advanced_form');
    }

    public function user_tables(){
        return view('forms.user_tables');
    }

    public function showRegistration(){
        $data = User::where('id',Auth::user()->id)->first();
        return view('admin.profile.register',compact('data'));
    }

    public function register(Request $request){
        if(empty($request->email) || empty($request->name) || empty($request->password)){
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">All fields are required...</p>'],200);
        }

        $checkEmail = User::where(['email' => $request->email])->count();
        if($checkEmail > 0){
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">Email already exist...</p>'],500);
        }
        $id = User::insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        
        if($id > 0){
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
            'name' => $request->name,
            'mobile' => $request->mobile,
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
