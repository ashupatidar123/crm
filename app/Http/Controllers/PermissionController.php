<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Document;
use App\Models\UserDocument;
use App\Models\UserDocumentAccess;
use App\Models\DepartmentDesignation;
use App\Models\UserAddress;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Vessel;
use App\Models\VesselCheckInOut;
use App\Models\Apprisal;
use App\Models\Menu;
use App\Models\Permission;

use App\Traits\FileUploadTrait;

class PermissionController extends Controller{   
    use FileUploadTrait;
    public function __construct(){

    }

    public function menu_department_permission_check($menu_id,$department_id){
        $count = Permission::where('menu_id',$menu_id)->where('department_id',$department_id)->where('permission_type','department')->count();
        if($count > 0){
            return 'yes';
        }else{
            return 'no';
        }
    }
    public function menu_department_action_permission_check($menu_id,$department_id,$type='add_access'){
        $count = Permission::where('menu_id',$menu_id)->where('department_id',$department_id)->where($type,'yes')->where('permission_type','department')->count();
        if($count > 0){
            return 'yes';
        }else{
            return 'no';
        }
    }

    public function department_sub_menu($menu_id,$department_id=''){
        $menu = Menu::select('id','menu_name','menu_code','menu_link')->where('parent_menu_id',$menu_id)->where('is_active',1)->orderBy('menu_sequence','ASC')->limit(20)->get();
        $menu_one_array = [];
        if(count($menu) > 0){
            foreach($menu as $record){
                $menu_one_array[] = [
                    'id'=>$record->id,
                    'menu_name'=>$record->menu_name,
                    'menu_link'=>$record->menu_link,
                    'permission_check'=>$this->menu_department_permission_check($record->id,$department_id),
                    'add_access'=>$this->menu_department_action_permission_check($record->id,$department_id,'add_access'),
                    'edit_access'=>$this->menu_department_action_permission_check($record->id,$department_id,'edit_access'),
                    'delete_access'=>$this->menu_department_action_permission_check($record->id,$department_id,'delete_access'),
                    'sub_menus'=>empty($record->menu_link)?$this->department_sub_menu($record->id,$department_id):[],
                ];
            }
            return $menu_one_array;
        }else{
            return $menu_one_array;
        }
    }

    public function menu_department_permission($department_id='0'){
        
        //$department = Department::select('id','department_name','department_type')->where('id',$department_id)->where('is_active',1)->first();
        //return view('permission.department1',compact('department_id','department'));

        $count = Department::where('id',$department_id)->where('is_active',1)->count();
        if($count < 1){
            return redirect(url('master/department'),301); 
        }
        $department = Department::select('id','department_name','department_type')->where('id',$department_id)->where('is_active',1)->first();

        $main_menu = Menu::select('id','menu_name','menu_code','menu_link','menu_icon')->where('parent_menu_id',0)->where('is_active',1)->orderBy('parent_menu_id','ASC')->limit(50)->get();
        
        $all_menu = [];
        if(count($main_menu) > 0){
            foreach($main_menu as $record){
                $all_menu[] = [
                    'id'=>$record->id,
                    'menu_name'=>$record->menu_name,
                    'menu_link'=>$record->menu_link,
                    'permission_check'=>'no',
                    'add_access'=>'no',
                    'edit_access'=>'no',
                    'delete_access'=>'no',
                    'sub_menu_one'=>empty($record->menu_link)?$this->department_sub_menu($record->id,$department_id):[],
                ];
            }
        }

        //printr($all_menu,'p');
        return view('permission.department',compact('all_menu','department_id','department'));
    }

    public function menu_department_permission_store(Request $request){
        $all_menu_ids = !empty($request->all_menu_ids)?$request->all_menu_ids:'';
        $department_id = !empty($request->department_id)?$request->department_id:'';    
        
        if(empty($all_menu_ids) || empty($department_id)){
            return response()->json(['status' =>'failed','s_msg'=>'All fields are required...'],200);
        }
        //printr($request->all_menu_ids,'p');
        
        Permission::where(['department_id'=>$department_id,'permission_type'=>'department'])->forceDelete();
        
        $menu_add_access = $menu_edit_access = $menu_delete_access = 'no';
        foreach($all_menu_ids as $key=>$menu_id){
            if(!empty($request->menu_add_access[$key])){
                $menu_add_access = $request->menu_add_access[$key];
            }
            if(!empty($request->menu_edit_access[$key])){
                $menu_edit_access = $request->menu_edit_access[$key];
            }
            if(!empty($request->menu_delete_access[$key])){
                $menu_delete_access = $request->menu_delete_access[$key];
            }

            $save_data = [
                'menu_id' => $menu_id,
                'department_id' => $department_id,
                'permission_type'=> 'department',
                'add_access'=> !empty($menu_add_access)?$menu_add_access:'no',
                'edit_access'=> !empty($menu_edit_access)?$menu_edit_access:'no',
                'delete_access'=> !empty($menu_delete_access)?$menu_delete_access:'no',
                'created_by'=>Auth::user()->id
            ];
            //printr($save_data,'p');
            $lastId = Permission::insertGetId($save_data);
        }
        return response()->json(['status' =>'success','message' =>'Permission applied successfully...'],200);
    }

    /* permission table start*/
    public function menu_permission_department_list_filter_count($search,$postData){
        $filter_count = Permission::where('id','>',0);
        if(!empty($postData['search_menu_id'])){
            $filter_count->where('menu_id',$postData['search_menu_id']); 
        }
        if(!empty($postData['search_department_id'])){
            $filter_count->where('department_id',$postData['search_department_id']); 
        }
        return $filter_count->count();
    }

    public function menu_permission_department_list(Request $request){
        $postData = $request->input();
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','is_active','','','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = Permission::with('get_menu_name','get_department_name')->select('id','menu_id','user_id','department_id','permission_type','created_at');
        
        $query->orderBy($orderColumnIndex, $orderDirection);
        if(!empty($request->input('search_menu_id'))){
            $query->where('menu_id',$request->input('search_menu_id')); 
        }
        if(!empty($request->input('search_department_id'))){
            $query->where('department_id',$request->input('search_department_id')); 
        }

        $all_records = $query->offset($start_limit)->limit($end_limit)->get(); 
        //printr($all_records);
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($all_records)){
            $recordsTotal = Permission::count();
            $sno = 1+$start_limit;
            foreach($all_records as $record){
                $delete = '<button class="btn btn-default btn-sm deleteLoader_'.$record->id.'" onclick="return ajax_delete('.$record->id.',\'menu_permission_department\');" title="Delete"><i class="fa fa-trash"></i></button>';

                $menu_name = !empty(@$record->get_menu_name->menu_name)?$record->get_menu_name->menu_name:'No Menu';
                $department_name = !empty(@$record->get_department_name->department_name)?$record->get_department_name->department_name:'No Menu';
                $all_data[] = [
                    'sno'=> $sno++,
                    'menu_name'=> $menu_name,
                    'department_name'=> $department_name,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at)),
                    'action'=>$delete
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->menu_permission_department_list_filter_count($search,$postData),
            'data' => $all_data,
        ]);
    }

    /* user menu permission start*/
    public function menu_user_permission_check($menu_id,$user_id){
        $count = Permission::where('menu_id',$menu_id)->where('user_id',$user_id)->where('permission_type','user')->count();
        if($count > 0){
            return 'yes';
        }else{
            return 'no';
        }
    }

    public function user_sub_menu($menu_id,$user_id=''){
        $menu = Menu::select('id','menu_name','menu_code','menu_link')->where('parent_menu_id',$menu_id)->where('is_active',1)->orderBy('menu_sequence','ASC')->limit(20)->get();
        $menu_one_array = [];
        if(count($menu) > 0){
            foreach($menu as $record){
                $menu_one_array[] = [
                    'id'=>$record->id,
                    'menu_name'=>$record->menu_name,
                    'menu_link'=>$record->menu_link,
                    'permission_check'=>$this->menu_user_permission_check($record->id,$user_id),
                    'sub_menus'=>empty($record->menu_link)?$this->user_sub_menu($record->id,$user_id):[],
                ];
            }
            return $menu_one_array;
        }else{
            return $menu_one_array;
        }
    }

    public function menu_user_permission($user_id='0'){
        
        $count = User::where('id',$user_id)->where('is_active',1)->count();
        
        if($count < 1){
            return redirect(url('user/user'),301); 
        }
        $user = User::select('id','first_name','email')->where('id',$user_id)->where('is_active',1)->first();

        $main_menu = Menu::select('id','menu_name','menu_code','menu_link','menu_icon')->where('parent_menu_id',0)->where('is_active',1)->orderBy('parent_menu_id','ASC')->limit(50)->get();
        
        $all_menu = [];
        if(count($main_menu) > 0){
            foreach($main_menu as $record){
                $all_menu[] = [
                    'id'=>$record->id,
                    'menu_name'=>$record->menu_name,
                    'menu_link'=>$record->menu_link,
                    'permission_check'=>'no',
                    'sub_menu_one'=>empty($record->menu_link)?$this->user_sub_menu($record->id,$user_id):[],
                ];
            }
        }

        //printr($all_menu,'p');
        return view('permission.user',compact('all_menu','user_id','user'));
    }

    public function menu_user_permission_store(Request $request){
        $all_menu_ids = !empty($request->all_menu_ids)?$request->all_menu_ids:'';
        $user_id = !empty($request->user_id)?$request->user_id:'';    
        
        if(empty($all_menu_ids) || empty($user_id)){
            return response()->json(['status' =>'failed','s_msg'=>'All fields are required...'],200);
        }
        //printr($all_menu_ids,'p');
        
        Permission::where(['user_id'=>$user_id,'permission_type'=>'user'])->forceDelete();
        foreach($all_menu_ids as $menu_id){
            $save_data = [
                'menu_id' => $menu_id,
                'user_id' => $user_id,
                'permission_type'=> 'user',
                'created_by'=>Auth::user()->id
            ];
            $lastId = Permission::insertGetId($save_data);
        }
    
        return response()->json(['status' =>'success','message' =>'Permission applied successfully...'],200);
    }
}
