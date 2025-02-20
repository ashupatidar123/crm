<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model{
    use HasFactory, SoftDeletes;
    
    public function get_menu_name(){
        return $this->hasOne(Menu::class,'id','menu_id')->select('id','menu_name');
    }

    public function get_department_name(){
        return $this->hasOne(Department::class,'id','department_id')->select('id','department_name');
    }
}
