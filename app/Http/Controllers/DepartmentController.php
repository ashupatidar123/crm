<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DepartmentController extends Controller{
    
    public function index(){
        return view('master.department.index');
    }

    public function department_list_filter_count($search){
        if(!empty($search)) {
            $filter_count = Department::where('department_name', 'LIKE', '%'.$search.'%')->orWhere('description', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%')->count();
        }else{
            $filter_count = Department::count();
        }
        return $filter_count;
    }

    public function department_list(Request $request){
        
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','id','department_name','description','created_at','is_active'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = Department::select('id','department_name','description','created_at','is_active');
        if(!empty($search)) {
            $query->where('department_name', 'LIKE', '%'.$search.'%')->orWhere('description', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%');
        }
        $query->orderBy($orderColumnIndex, $orderDirection);
        $users = $query->offset($start_limit)->limit($end_limit)->get(); 
        //printr($users);
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($users)){
            $recordsTotal = Department::count();
            $sno = 1+$start_limit;
            foreach($users as $record){
                $edit = '<button class="btn btn-default btn-sm" onclick="return add_edit_department('.$record->id.',\'edit\');" title="Edit"><i class="fa fa-edit"></i></button>';
                $delete = '<button class="btn btn-default btn-sm" onclick="return ajax_delete('.$record->id.',\'department\');" title="Delete"><i class="fa fa-trash"></i></button>';

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm" onclick="return ajax_active_inactive('.$record->id.',1,\'department\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm" onclick="return ajax_active_inactive('.$record->id.',2,\'department\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $all_data[] = [
                    'sno'=> $sno++,
                    'id'=> $record->id,
                    'department_name'=> $record->department_name,
                    'description'=> $record->description,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at)),
                    'status'=>$status,
                    'action'=>$edit.' '.$delete
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->department_list_filter_count($search),
            'data' => $all_data,
        ]);
    }

    public function store(Request $request){
        $p_id = ($request->p_id > 0)?$request->p_id:'';
        
        $check = Department::where('department_name',$request->department_name)->where('id','!=',$p_id)->count();
        if($check > 0){
            return response()->json(['status' =>'error','message' => 'Department already exist...'],200);
        }
        
        $data = [
            'department_name' => $request->department_name,
            'description' => $request->description,
            'is_active' => ($request->is_active==1)?1:2,
            'created_by'=>Auth::user()->id
        ];

        $message = 'Error';
        if($p_id > 0){
            $lastId = Department::where('id',$p_id)->update($data);
            $message = 'Department updated successfully...';
        }else{
            $lastId = Department::insertGetId($data);
            $message = 'Department added successfully...';
        }
        if($lastId > 0){
            return response()->json(['status' =>'success','message' => $message],200);
        }else{
            return response()->json(['status' =>'error','message' => 'Opps! Something went wrong...'],200);
        }
    }

    public function department_edit(Request $request){
        $data = Department::where('id',$request->p_id)->first();
        echo json_encode(['data'=>$data]);
    }

    public function update(Request $request, string $id){
        $update_data = [
            'department_name' => $request->department_name,
            'description' => $request->description,
            'is_active' => ($request->is_active==1)?1:2,
            'created_by'=>Auth::user()->id
        ];
        
        $lastId = Department::where('id',$id)->update($update_data);
        if($lastId > 0){
            session()->flash('success', '<p class="alert alert-success">Department updated successfully...</p>');
            return redirect()->route('department.edit', $id);
        }else{
            session()->flash('error', '<p class="alert alert-danger">Opps! Something went wrong...</p>');
            return redirect()->route('department.edit', $id);
        }
    }
}