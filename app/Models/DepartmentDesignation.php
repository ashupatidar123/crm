<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentDesignation extends Model{
    use HasFactory, SoftDeletes;

    function single_department(){
        return $this->belongsTo(Department::class,'department_id','id')->select('id','department_name','department_type');
    }
}
