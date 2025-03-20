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

function sidebar_menu_open($menu_url,$menu_slug){
    $menu_open = $menu_url;
    if(($menu_url=='user/user' || $menu_url=='user/add-user' || $menu_url=='user/edit-user' || $menu_url=='user/menu-user-permission') && ($menu_slug=='user_management')){
        $menu_open = 'menu-is-opening menu-open';
    }
    else if(($menu_url == 'master/menu' || $menu_url == 'master/department' || $menu_url == 'master/designation' || $menu_url == 'master/document' || $menu_url == 'master/region' ||$menu_url == 'master/region/country' || $menu_url == 'master/region/state' || $menu_url == 'master/region/city' || $menu_url=='master/menu-department-permission') && ($menu_slug == 'master_management')){
        $menu_open = 'menu-is-opening menu-open';
    }
    else if(($menu_url == 'vessel/vessel' || $menu_url == 'vessel/vessel-category' || $menu_url == 'vessel/check-in-out') && ($menu_slug == 'vessel_management')){
        $menu_open = 'menu-is-opening menu-open';
    }
    else if(($menu_url == 'company/company-profile' || $menu_url == 'company/company-branch') && ($menu_slug == 'company_config')){
        $menu_open = 'menu-is-opening menu-open';
    }
    return $menu_open;
}

function sidebar_menu_active($menu_uri,$menu_slug){
    $menu_active = '';
    if(($menu_uri=='user' || $menu_uri=='add-user' || $menu_uri=='edit-user' || $menu_uri=='menu-user-permission') && ($menu_slug=='user_management')){
        $menu_active = 'active';
    }
    else if(($menu_uri=='menu' || $menu_uri=='department' || $menu_uri=='designation' || $menu_uri=='document' || $menu_uri=='region' || $menu_uri=='menu-department-permission') && ($menu_slug=='master_management')){
        $menu_active = 'active';
    }
    else if(($menu_uri=='vessel' || $menu_uri=='vessel-category' || $menu_uri=='check-in-out') && ($menu_slug=='vessel_management')){
        $menu_active = 'active';
    }
    else if(($menu_uri=='company-profile' || $menu_uri=='company-branch') && ($menu_slug=='company_config')){
        $menu_active = 'active';
    }
    return $menu_active;
}

function check_user_action_permission($menu_slug='',$menu_name=''){
    $menu_record = DB::table('menus')->select('id')->where('menu_slug',$menu_slug)->where('is_active',1)->where('deleted_at',null)->first();
   
    if(!empty($menu_record)){
        $menu_id = $menu_record->id;
        $record = DB::table('permissions')->select('id','add_access','edit_access','delete_access','view_access','tab_access')->where('user_id',Auth::user()->id)->where('menu_id',$menu_id)->where('permission_type','user')->first();
        $record->list_access = "yes"; //extra added
    }else{
        $record = [
            'id' => '',
            "list_access" => "no", //extra added
            "add_access" => "no",
            "edit_access" => "no",
            "delete_access" => "no",
            "view_access" => "no",
            "tab_access" => "no",
        ];
        $record = (object) $record;
    }
    //printr($record,'p');
    return $record;
}

function check_authorize($action='',$menu_slug='',$type='ajax'){
    if(empty($action) || empty($menu_slug)){
        return abort(403, 'Forbidden error');
    }

    $action_permission = check_user_action_permission($menu_slug);
    //printr($action_permission,'p');

    if(empty($action_permission)){
        if($type == 'ajax'){
            echo 'Opps! Unauthorized action.'; exit;
        }else{
            return abort(403,'Unauthorized action.'); 
        }
    }
    if($action == 'list'){
        if(@$action_permission->list_access != 'yes'){
            if($type == 'ajax'){
                echo 'Opps! Unauthorized action.'; exit;
            }else{
                return abort(403,'Unauthorized action.'); 
            }
        }
    }
    else if($action == 'add'){
        if($action_permission->add_access != 'yes'){
            if($type == 'ajax'){
                echo 'Opps! Unauthorized action.'; exit;
            }else{
                return abort(403,'Unauthorized action.'); 
            }
        }
    }
    else if($action == 'edit'){
        if($action_permission->edit_access != 'yes'){
            if($type == 'ajax'){
                echo 'Opps! Unauthorized action.'; exit;
            }else{
                return abort(403,'Unauthorized action.'); 
            }
        }
    }
    else if($action == 'delete'){
        if($action_permission->delete_access != 'yes'){
            if($type == 'ajax'){
                echo 'Opps! Unauthorized action.'; exit;
            }else{
                return abort(403,'Unauthorized action.'); 
            }
        }
    }
    else if($action == 'view'){
        if($action_permission->view_access != 'yes'){
            if($type == 'ajax'){
                echo 'Opps! Unauthorized action.'; exit;
            }else{
                return abort(403, 'Unauthorized action.'); 
            }
        }
    }
    else if($action == 'tab'){
        if($action_permission->tab_access != 'yes'){
            if($type == 'ajax'){
                echo 'Opps! Unauthorized action.'; exit;
            }else{
                return abort(403, 'Unauthorized action.'); 
            }
        }
    }
} 

?>