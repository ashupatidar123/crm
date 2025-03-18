<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\UserAddress;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Menu;
use App\Models\Permission;

use App\Traits\FileUploadTrait;

class CompanyController extends Controller{   
    use FileUploadTrait;
    public function __construct(){

    }

    public function index(){
        return view('common.company.index');
    }

    public function user(){
        check_authorize('list','user');
        $action_permission = check_user_action_permission('user');
        return view('user.user.user_list',compact('action_permission'));
    }

    
}
