<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller{

    public function __construct(){
        $this->middleware('guest');
    }

    public function showRegistrationForm(){
        return view('auth.register');
    }

    public function register(Request $request){
        if(empty($request->email) || empty($request->name) || empty($request->password)){
            echo '<p class="text-danger">All fields are required...</p>'; exit;
        }


        $checkEmail = User::where(['email' => $request->email])->count();
        if($checkEmail > 0){
            echo '<p class="text-danger">Email already exist...</p>'; exit;
        }
        $id = User::insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        
        if($id > 0){
            $msg = '<p class="text-success">User registration success...</p>';
        }else{
            $msg = '<p class="text-danger">Opps! Something went wrong...</p>';
        }
        echo $msg; exit;
    }
}
