<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Document;
use App\Models\UserDocument;
use App\Models\UserDocumentAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserDocumentAccessController extends Controller{
    
    public function __construct(){

    }
}
