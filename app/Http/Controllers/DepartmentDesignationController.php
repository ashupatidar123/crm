<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\DepartmentDesignation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DepartmentDesignationController extends Controller{
    public function index(){
        $departmentData = Department::select('id','department_name','department_type')->where('is_active',1)->orderBy('department_name','ASC')->limit(100)->get();
        //printr($data);
        return view('master.designation.index',compact('departmentData'));
    }

    public function designation_list_filter_count($search){
        if(!empty($search)) {
            $filter_count = DepartmentDesignation::where('designation_name', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%')->count();
        }else{
            $filter_count = DepartmentDesignation::count();
        }
        return $filter_count;
    }

    public function designation_list(Request $request){
        
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','is_active','designation_name','','rank','description','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = DepartmentDesignation::with('single_department')->select('id','department_id','designation_name','rank','description','created_at','is_active');
        if(!empty($search)) {
            $query->where('designation_name', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%');
        }
        $query->orderBy($orderColumnIndex, $orderDirection);
        $listData = $query->offset($start_limit)->limit($end_limit)->get(); 
        
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($listData)){
            $recordsTotal = DepartmentDesignation::count();
            $sno = 1+$start_limit;
            foreach($listData as $record){
                $edit = '<button class="btn btn-default btn-sm" onclick="return add_edit_designation('.$record->id.',\'edit\');" title="Edit"><i class="fa fa-edit"></i></button>';
                $delete = '<button class="btn btn-default btn-sm" onclick="return ajax_delete('.$record->id.',\'designation\');" title="Delete"><i class="fa fa-trash"></i></button>';

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm" onclick="return ajax_active_inactive('.$record->id.',1,\'designation\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm" onclick="return ajax_active_inactive('.$record->id.',2,\'designation\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $all_data[] = [
                    'sno'=> $sno++,
                    'action'=>$edit.' '.$status.' '.$delete,
                    'designation_name'=> $record->designation_name,
                    'department_name'=> $record->single_department->department_name.' ('.$record->single_department->department_type.')',
                    'rank'=> $record->rank,
                    'description'=> $record->description,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at))
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->designation_list_filter_count($search),
            'data' => $all_data,
        ]);
    }

    public function store(Request $request){
        $p_id = ($request->p_id > 0)?$request->p_id:'';
        
        $check = DepartmentDesignation::where('designation_name',$request->designation_name)->where('id','!=',$p_id)->count();
        if($check > 0){
            return response()->json(['status' =>'error','message' => 'Designation already exist...'],200);
        }
        
        $data = [
            'designation_name' => $request->designation_name,
            'department_id' => $request->department_id,
            'rank' => $request->rank,
            'description' => $request->description,
            'is_active' => ($request->is_active==1)?1:2,
            'created_by'=>Auth::user()->id
        ];
        
        $message = 'Opps! Something went wrong...';
        if($p_id > 0){
            $lastId = DepartmentDesignation::where('id',$p_id)->update($data);
            $message = 'Designation updated successfully...';
        }else{
            $lastId = DepartmentDesignation::insertGetId($data);
            $message = 'Designation added successfully...';
        }
        if($lastId > 0){
            return response()->json(['status' =>'success','message' => $message],200);
        }else{
            return response()->json(['status' =>'error','message' => 'Opps! Something went wrong...'],200);
        }
    }

    public function designation_edit(Request $request){
        $data = DepartmentDesignation::with('single_department')->where('id',$request->p_id)->first();
        $data['department_type'] = @$data->single_department->department_type;
        echo json_encode(['data'=>$data]);
    }  
}
