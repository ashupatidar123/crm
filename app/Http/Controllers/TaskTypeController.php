<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Menu;
use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\TaskType;
use App\Models\Permission;

use App\Traits\FileUploadTrait;

class TaskTypeController extends Controller{   
    use FileUploadTrait;
    public function __construct(){

    }

    public function index(){
        check_authorize('list','task_type','non_ajax');
        return view('common.task_type.index');
    }

    public function task_type_list_filter_count($search){
        if(!empty($search)) {
            $filter_count = TaskType::where('task_type', 'LIKE', '%'.$search.'%')->orWhere('task_code', 'LIKE', '%'.$search.'%')->count();
        }else{
            $filter_count = TaskType::count();
        }
        return $filter_count;
    }

    public function task_type_list(Request $request){
        
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','is_active','task_type','task_code','description','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = TaskType::select('id','task_type','task_code','is_active','created_at','description');
        if(!empty($search)) {
            $query->where('task_type', 'LIKE', '%'.$search.'%')->orWhere('task_code', 'LIKE', '%'.$search.'%');
        }
        $query->orderBy($orderColumnIndex, $orderDirection);
        $all_records = $query->offset($start_limit)->limit($end_limit)->get(); 
        //printr($all_records);
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($all_records)){
            $recordsTotal = TaskType::count();
            $sno = 1+$start_limit;
            
            $edit = $delete = '';
            $action_permission = check_user_action_permission('task_type');

            foreach($all_records as $record){
                if(@$action_permission->edit_access == 'yes'){
                    $edit = '<button class="btn btn-default btn-sm addEditLoader_'.$record->id.'" onclick="return add_edit_task_type('.$record->id.',\'edit\');" title="Edit"><i class="fa fa-edit"></i></button>';
                }
                if(@$action_permission->delete_access == 'yes'){
                    $delete = '<button class="btn btn-default btn-sm deleteLoader_'.$record->id.'" onclick="return ajax_delete('.$record->id.',\'task_type\');" title="Delete"><i class="fa fa-trash"></i></button>';
                }

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',1,\'task_type\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',2,\'task_type\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $all_data[] = [
                    'sno'=> $sno++,
                    'task_type'=> $record->task_type,
                    'task_code'=> $record->task_code,
                    'description'=> $record->description,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at)),
                    'action'=>$edit.' '.$status.' '.$delete
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->task_type_list_filter_count($search),
            'data' => $all_data,
        ]);
    }

    public function store(Request $request){
        $p_id = ($request->p_id > 0)?$request->p_id:'';
        if($p_id > 0){
            check_authorize('edit','task_type');
        }else{
            check_authorize('add','task_type');
        }
        
        $check = TaskType::where('task_type',$request->task_type)->where('id','!=',$p_id)->count();
        if($check > 0){
            return response()->json(['status' =>'error','message' => 'Task type already exist...'],200);
        }
        
        $data = [
            'task_type'=> $request->task_type,
            'task_code' => $request->task_code,
            'description' => !empty($request->description)?$request->description:'',
            'is_active' => 1,
            'created_by'=>Auth::user()->id
        ];

        $message = 'Opps! Something went wrong...';
        if($p_id > 0){
            $lastId = TaskType::where('id',$p_id)->update($data);
            $message = 'Task type updated successfully...';
        }else{
            $lastId = TaskType::insertGetId($data);
            $message = 'Task type added successfully...';
        }
        if($lastId > 0){
            return response()->json(['status' =>'success','message' => $message],200);
        }else{
            return response()->json(['status' =>'error','message' => 'Opps! Something went wrong...'],200);
        }
    }

    public function task_type_list_edit(Request $request){
        check_authorize('edit','task_type');
        $data = TaskType::where('id',$request->p_id)->first();
        echo json_encode(['data'=>$data]);
    }

    
}
