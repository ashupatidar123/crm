<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Port;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class VesselPortController extends Controller{

    public function index(){
        return view('vessel.port.index');
    }

    public function port_list_filter_count($search){
        if(!empty($search)) {
            $filter_count = Port::where('port_name', 'LIKE', '%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%')->orWhere('email', 'LIKE', '%'.$search.'%')->count();
        }else{
            $filter_count = Port::count();
        }
        return $filter_count;
    }

    public function port_list(Request $request){
        
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','is_active','port_name','phone','email','country','state','','zip_code','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = Port::select('id','port_name','phone','email','country','state','address','zip_code','created_at','is_active');
        if(!empty($search)) {
            $query->where('port_name', 'LIKE', '%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%')->orWhere('email', 'LIKE', '%'.$search.'%');
        }
        $query->orderBy($orderColumnIndex, $orderDirection);
        $users = $query->offset($start_limit)->limit($end_limit)->get(); 
        //printr($users);
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($users)){
            $recordsTotal = Port::count();
            $sno = 1+$start_limit;
            foreach($users as $record){
                $edit = '<button class="btn btn-default btn-sm addEditLoader_'.$record->id.'" onclick="return add_edit_port('.$record->id.',\'edit\');" title="Edit"><i class="fa fa-edit"></i></button>';
                $delete = '<button class="btn btn-default btn-sm deleteLoader_'.$record->id.'" onclick="return ajax_delete('.$record->id.',\'port\');" title="Delete"><i class="fa fa-trash"></i></button>';

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',1,\'port\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',2,\'port\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $parent_vessel_category = !empty(@$record->single_category->category_name)?$record->single_category->category_name:'No Parent';
                $all_data[] = [
                    'sno'=> $sno++,
                    'port_name'=> $record->port_name,
                    'phone'=> $record->phone,
                    'email'=> $record->email,
                    'country'=> $record->country,
                    'state'=> $record->state,
                    'address'=> $record->address,
                    'zip_code'=> $record->zip_code,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at)),
                    'action'=>$edit.' '.$status.' '.$delete
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->port_list_filter_count($search),
            'data' => $all_data,
        ]);
    }

    public function store(Request $request){
        $p_id = ($request->p_id > 0)?$request->p_id:'';
        
        $check = Port::where('port_name',$request->port_name)->where('id','!=',$p_id)->count();
        if($check > 0){
            return response()->json(['status' =>'error','message' => 'Port name already exist...'],200);
        }
        
        $data = [
            'port_name' => $request->port_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'country' => $request->country,
            'state' => $request->state,
            'address' => $request->address,
            'zip_code' => $request->zip_code,
            'description' => $request->description,
            'created_by'=>Auth::user()->id
        ];
        
        $message = 'Opps! Something went wrong...';
        if($p_id > 0){
            $lastId = Port::where('id',$p_id)->update($data);
            $message = 'Port management updated successfully...';
        }else{
            $lastId = Port::insertGetId($data);
            $message = 'Port management added successfully...';
        }
        if($lastId > 0){
            return response()->json(['status' =>'success','message' => $message],200);
        }else{
            return response()->json(['status' =>'error','message' => 'Opps! Something went wrong...'],200);
        }
    }

    public function port_edit(Request $request){
        $data = Port::where('id',$request->p_id)->first();
        echo json_encode(['data'=>$data]);
    }
}

