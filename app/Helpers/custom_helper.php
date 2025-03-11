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
    else if($menu_name == 'menu'){
        $menu_id = 5;
    }
    else if($menu_name == 'department'){
        $menu_id = 6;
    }
    else if($menu_name == 'designation'){
        $menu_id = 7;
    }
    else if($menu_name == 'document'){
        $menu_id = 8;
    }
    else if($menu_name == 'country'){
        $menu_id = 9;
    }
    else if($menu_name == 'state'){
        $menu_id = 10;
    }
    else if($menu_name == 'city'){
        $menu_id = 11;
    }
    else if($menu_name == 'vessel'){
        $menu_id = 12;
    }
    else if($menu_name == 'category'){
        $menu_id = 13;
    }
    else if($menu_name == 'signing_signout'){
        $menu_id = 14;
    }
    else{
        $menu_id = '';
    }

    $record = DB::table('permissions')->select('id','add_access','edit_access','delete_access','view_access','tab_access')->where('user_id',Auth::user()->id)->where('menu_id',$menu_id)->where('permission_type','user')->first();
    //printr($record,'p');
    return $record;
}

function check_authorize($action='',$menu_name=''){
    if(empty($action) || empty($menu_name)){
        return abort(403, 'Forbidden error');
    }

    $action_permission = check_user_action_permission($menu_name);
    if($action == 'add'){
        if($action_permission->add_access != 'yes'){
            return abort(403, 'Unauthorized action.');
        }
    }
    else if($action == 'edit'){
        if($action_permission->edit_access != 'yes'){
            return abort(403, 'Unauthorized action.');
        }
    }
    else if($action == 'delete'){
        if($action_permission->delete_access != 'yes'){
            return abort(403, 'Unauthorized action.');
        }
    }
    else if($action == 'view'){
        if($action_permission->view_access != 'yes'){
            return abort(403, 'Unauthorized action.');
        }
    }
    else if($action == 'tab'){
        if($action_permission->tab_access != 'yes'){
            return abort(403, 'Unauthorized action.');
        }
    }
} 

?>