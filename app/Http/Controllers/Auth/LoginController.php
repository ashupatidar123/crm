<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;

use App\Models\Department;
use App\Models\Menu;
use App\Models\Permission;

class LoginController extends Controller{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm(){
        return view('auth.login');
    }

    public function g_recaptcha_verify($recaptcha) {
        $secret = '6LeTj4sqAAAAAMFOkql5qnMU7PlZQlqbtxe_a490';
        $response = $recaptcha;
        $remote_ip = $_SERVER['REMOTE_ADDR'];
        $verify_url = "https://www.google.com/recaptcha/api/siteverify";
        
        $data = [
            'secret' => $secret,
            'response' => $response,
            'remoteip' => $remote_ip
        ];
        
        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($data)
            ]
        ];
        
        $context  = stream_context_create($options);
        $verify_response = file_get_contents($verify_url, false, $context);
        $response_data = json_decode($verify_response);
        if($response_data->success) {
            return 'success';
        }else {
            return 'failed';
        }
    }

    public function login(Request $request){
        $postData = $request->all();
        
        if(empty($request->username) || empty($request->password)){
            session()->flash('error', 'All fields are required...');
            return redirect()->back();
        }
        else if(empty($postData['g-recaptcha-response'])){
            //session()->flash('error', 'reCAPTCHA verification is required....');
            //return redirect()->back();
        }

        $gCaptch = 1;//$this->g_recaptcha_verify($postData['g-recaptcha-response']);
        if($gCaptch == 'failed'){
            //session()->flash('error', 'reCAPTCHA verification failed. Please try again....');
            //return redirect()->back();
        }

        if(Auth::attempt(['login_id' => $request->username, 'password' => $request->password, 'is_active' => 1])) {
            $this->menu_access_permission(Auth::user()->id);
            return redirect()->intended('/dashboard');
        }else{
           session()->flash('error', 'Invalid login details...');
           return redirect()->back();
        }
    }


    public function logout(){
        Session::flush();
        Auth::logout();
        return redirect('/login',301);
    }

    public function menu_permission_check($menu_id,$user_id){
        $count = Permission::where('menu_id',$menu_id)->where('user_id',$user_id)->where('permission_type','user')->count();
        if($count > 0){
            return 'yes';
        }else{
            return 'no';
        }
    }

    public function sub_menu($menu_id,$user_id=''){
        $menu = Menu::select('id','menu_name','menu_slug','menu_code','menu_link')->where('parent_menu_id',$menu_id)->where('is_active',1)->orderBy('menu_sequence','ASC')->limit(20)->get();
        $menu_one_array = [];
        if(count($menu) > 0){
            foreach($menu as $record){
                $permission_check = $this->menu_permission_check($record->id,$user_id);
                if($permission_check == 'yes'){    
                    $menu_one_array[] = [
                        'id'=>$record->id,
                        'menu_name'=>$record->menu_name,
                        'menu_slug'=>$record->menu_slug,
                        'menu_link'=>$record->menu_link,
                        'permission_check'=>$permission_check,
                        'sub_menus'=>empty($record->menu_link)?$this->sub_menu($record->id,$user_id):[],
                    ];
                }
            }
            return $menu_one_array;
        }else{
            return $menu_one_array;
        }
    }

    public function menu_access_permission($user_id='0'){
        
        $main_menu = Menu::select('id','menu_name','menu_slug','menu_code','menu_link','menu_icon')->where('parent_menu_id',0)->where('is_active',1)->orderBy('parent_menu_id','ASC')->limit(50)->get();
        
        $all_menu = [];
        if(count($main_menu) > 0){
            foreach($main_menu as $record){
                $permission_check = $this->menu_permission_check($record->id,$user_id);
                if($permission_check == 'yes'){
                    $all_menu[] = [
                        'id'=>$record->id,
                        'menu_name'=>$record->menu_name,
                        'menu_slug'=>$record->menu_slug,
                        'menu_link'=>$record->menu_link,
                        'permission_check'=>$permission_check,
                        'sub_menu_one'=>empty($record->menu_link)?$this->sub_menu($record->id,$user_id):[],
                    ];
                }
            }
        }
        //printr($all_menu,'p');
        Session::put('permission_menu',$all_menu);
        return true;
    }
}
