<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Apprisal extends Model{
    use HasFactory, SoftDeletes;

    function assign_user(){
        return $this->hasOne(User::class,'id','login_user_id')->select('id','name_title','first_name','last_name','email');
    }

    function vessel_user(){
        return $this->hasOne(User::class,'id','user_id')->select('id','name_title','first_name','last_name','email');
    }
}
