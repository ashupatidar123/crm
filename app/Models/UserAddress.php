<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model{
    use HasFactory, SoftDeletes;
    protected $table = 'user_addresses';

    public function single_country(){
        return $this->hasOne(Country::class,'id','country_id')->select('id','name');
    }

    public function single_state(){
        return $this->hasOne(State::class,'id','state_id')->select('id','name');
    }

    public function single_city(){
        return $this->hasOne(City::class,'id','city_id')->select('id','name');
    }
}
