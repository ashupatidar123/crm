<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model{
    use HasFactory, SoftDeletes;

    function single_country(){
        return $this->belongsTo(Country::class,'country_id','id')->select('id','name');
    }
}
