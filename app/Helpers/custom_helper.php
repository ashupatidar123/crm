<?php

if(!function_exists('printr')) {
    function printr($data,$type='multi'){
        if(!empty($data) && $type == 'multi'){
            echo '<pre>';
            print_r($data->toArray());
            echo '<pre>';
        }else{
            echo '<pre>';
            print_r($data);
            echo '<pre>';
        }
        exit;
    }
}

if(!function_exists('user_title')) {
    function user_title($val=''){
        $option = 
        '<option value="Mr.">Mr.</option>
        <option value="Mrs.">Mrs.</option>
        <option value="Miss.">Miss.</option>
        <option value="Dr.">Dr.</option>
        <option value="Prof.">Prof.</option>
        <option value="Engg.">Engg.</option>
        <option value="Caption">Caption</option>';

        return $option;
    }
}

if(!function_exists('check_file_type')) {
    function check_file_type($file=''){
        $fileInfo = pathinfo($file);
        $extension = strtolower($fileInfo['extension']);
        if($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif'){
            return 'image';
        }
        else if($extension == 'pdf'){
            return 'pdf';
        }
        else if($extension == 'csv' || $extension == 'xlsx' || $extension == 'xls' || $extension == 'doc'){
            return 'doc';
        }else{
            return 'other';
        }
    }
}

function sidebar_menu_open($menu_url,$menu_name){
    $menu_open = '';
    if(($menu_url=='user/user') && ($menu_name=='User Management')){
        $menu_open = 'menu-is-opening menu-open';
    }
    else if(($menu_url == 'master/menu' || $menu_url == 'master/department' || $menu_url == 'master/designation' || $menu_url == 'master/document' || $menu_url == 'master/region/country' || $menu_url == 'master/region/state' || $menu_url == 'master/region/city') && ($menu_name == 'Master Management')){
        $menu_open = 'menu-is-opening menu-open';
    }
    else if(($menu_url == 'vessel/vessel' || $menu_url == 'vessel/vessel-category' || $menu_url == 'vessel/check-in-out') && ($menu_name == 'Vessel Management')){
        $menu_open = 'menu-is-opening menu-open';
    }
    return $menu_open;
}

function sidebar_menu_active($menu_uri,$menu_name){
    $menu_active = '';
    if(($menu_uri=='user') && ($menu_name=='User Management')){
        $menu_active = 'active';
    }
    else if(($menu_uri=='menu' || $menu_uri=='department' || $menu_uri=='designation' || $menu_uri=='document' || $menu_uri=='region' || $menu_uri=='department' || $menu_uri=='department' || $menu_uri=='department' || $menu_uri=='department') && ($menu_name=='Master Management')){
        $menu_active = 'active';
    }
    else if(($menu_uri=='vessel' || $menu_uri=='vessel-category' || $menu_uri=='check-in-out') && ($menu_name=='Vessel Management')){
        $menu_active = 'active';
    }
    return $menu_active;
}

function check_user_action_permission($menu_name=''){
    if($menu_name == 'users'){
        $menu_id = 4;
    }
    else if($menu_name == 'users'){
        $menu_id = 4;
    }
    else if($menu_name == 'users'){
        $menu_id = 4;
    }
    else if($menu_name == 'users'){
        $menu_id = 4;
    }
    else if($menu_name == 'users'){
        $menu_id = 4;
    }
    else if($menu_name == 'users'){
        $menu_id = 4;
    }
    else if($menu_name == 'users'){
        $menu_id = 4;
    }
    else if($menu_name == 'users'){
        $menu_id = 4;
    }
    else if($menu_name == 'users'){
        $menu_id = 4;
    }
    else if($menu_name == 'users'){
        $menu_id = 4;
    }
    else if($menu_name == 'users'){
        $menu_id = 4;
    }
    else if($menu_name == 'users'){
        $menu_id = 4;
    }
    else if($menu_name == 'users'){
        $menu_id = 4;
    }
    else{
        $menu_id = '';
    }

    $record = DB::table('permissions')->select('id','add_access','edit_access','delete_access','view_access','tab_access')->where('user_id',Auth::user()->id)->where('menu_id',$menu_id)->where('permission_type','user')->first();
    //printr($record,'p');
    return $record;
} 

?>