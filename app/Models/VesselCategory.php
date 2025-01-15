<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VesselCategory extends Model{
    use HasFactory, SoftDeletes;

    function single_category(){
        return $this->hasOne(VesselCategory::class,'id','parent_category_id')->select('id','category_name');
    }
}
