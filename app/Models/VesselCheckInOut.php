<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VesselCheckInOut extends Model{
    use HasFactory, SoftDeletes;
    protected $table = 'vessel_check_in_outs';

    function single_user(){
        return $this->hasOne(User::class,'id','user_id')->select('id','first_name','last_name','email');
    }

    function single_vessel(){
        return $this->hasOne(Vessel::class,'id','vessel_id')->select('id','vessel_name','vessel_email');
    }
}
