<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;

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

        $gCaptch = $this->g_recaptcha_verify($postData['g-recaptcha-response']);
        if($gCaptch == 'failed'){
            //session()->flash('error', 'reCAPTCHA verification failed. Please try again....');
            //return redirect()->back();
        }
        

        if (Auth::attempt(['login_id' => $request->username, 'password' => $request->password, 'is_active' => 1])) {
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

    
}
