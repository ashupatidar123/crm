<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model{
    use HasFactory, SoftDeletes;

    function parent_menu(){
        return $this->hasOne(Menu::class,'id','parent_menu_id')->select('id','menu_name');
    }
}
