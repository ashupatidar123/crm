<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model{
    use HasFactory, SoftDeletes;
    protected $table = 'menus';

    public function parent_menu(){
        return $this->hasOne(Menu::class,'id','parent_menu_id')->select('id','menu_name');
    }

    public function all_child_menu(){
        return $this->hasMany(Menu::class,'parent_menu_id','id')->select('id','menu_name','menu_code','menu_sequence','menu_link','menu_icon','parent_menu_id','description','is_active','created_at')->orderBy('menu_sequence','ASC');
    }
}
