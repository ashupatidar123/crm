<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
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

    public function login(Request $request){
        if(empty($request->email) || empty($request->password)){
            return response()->json(['status' =>'failed','message' => '<p class="text-danger">All fields are required...</p>'],200);
        }

        if(Auth::attempt($request->only('email', 'password'))) {
            return redirect()->intended('/home');
        }else{
           return response()->json(['status' =>'failed','message' => '<p class="text-danger">Invalid login details...</p>'],500); 
        }
    }


    public function logout(){
        Session::flush();
        Auth::logout();
        //Session::destroy();
        return redirect('/login',301);
    }

    
}
