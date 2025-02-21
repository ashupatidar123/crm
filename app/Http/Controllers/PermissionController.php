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

    public function menu_permission_check($menu_id,$department_id){
        $count = Permission::where('menu_id',$menu_id)->where('department_id',$department_id)->where('permission_type','department')->count();
        if($count > 0){
            return 'yes';
        }else{
            return 'no';
        }
    }

    public function sub_menu_one($menu_id,$department_id=''){
        $menu = Menu::select('id','menu_name','menu_code','menu_link')->where('parent_menu_id',$menu_id)->where('is_active',1)->orderBy('menu_sequence','ASC')->limit(20)->get();
        $menu_one_array = [];
        if(count($menu) > 0){
            foreach($menu as $record){
                $menu_one_array[] = [
                    'id'=>$record->id,
                    'menu_name'=>$record->menu_name,
                    'menu_link'=>$record->menu_link,
                    'permission_check'=>$this->menu_permission_check($record->id,$department_id),
                    'sub_menus'=>empty($record->menu_link)?$this->sub_menu_one($record->id,$department_id):[],
                ];
            }
            return $menu_one_array;
        }else{
            return $menu_one_array;
        }
    }

    public function menu_department_permission($department_id='0'){
        
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
                    'sub_menu_one'=>empty($record->menu_link)?$this->sub_menu_one($record->id,$department_id):[],
                ];
            }
        }

        //printr($all_menu,'p');
        return view('master.permission.index',compact('all_menu','department_id','department'));
    }

    public function get_permission_department_record(Request $request){
        $id = !empty($request->p_id)?$request->p_id:'';
        $show_type = !empty($request->type)?$request->type:'all';
        
        if($show_type == 'ajax_list'){
            $data = Department::select('id','department_name','department_type')->where('department_type','!=','')->where('is_active',1)->orderBy('department_name','ASC')->limit(500)->get();
            
            $html = '<option value="" hidden="">Select department</option>';
            if(!empty($data)){
                foreach($data as $record){
                    $selected = '';
                    if($id == $record->id){
                        $selected = 'selected';
                    }
                    $html .= '<option value="'.$record->id.'" '.$selected.' data-name="'.strtolower($record->department_name).'">'.trim(ucfirst($record->department_name)).'</option>';
                }
            }else{
                $html .= '<option value="" hidden>Not found</option>';
            }
            echo $html;
        }
    }

    public function store(Request $request){
        $all_menu_ids = !empty($request->all_menu_ids)?$request->all_menu_ids:'';
        $department_id = !empty($request->department_id)?$request->department_id:'';    
        
        if(empty($all_menu_ids) || empty($department_id)){
            return response()->json(['status' =>'failed','s_msg'=>'All fields are required...'],200);
        }
        //printr($all_menu_ids,'p');
        $lastId = 0;
        foreach($all_menu_ids as $menu_id){
            
            Permission::where(['menu_id'=>$menu_id,'department_id'=>$department_id,'permission_type'=>'department'])->forceDelete();
            
            $save_data = [
                'menu_id' => $menu_id,
                'department_id' => $department_id,
                'permission_type'=> 'department',
                'created_by'=>Auth::user()->id
            ];
            $lastId = Permission::insertGetId($save_data);
        }
    
        return response()->json(['status' =>'success','message' =>'Permission applied successfully...'],200);
    }

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
}
