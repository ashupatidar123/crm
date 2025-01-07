<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Vessel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class VesselController extends Controller{
    
    public function index(){
        return view('vessel.vessel.index');
    }

    public function vessel_list_filter_count($search){
        if(!empty($search)) {
            $filter_count = Vessel::where('vessel_name', 'LIKE', '%'.$search.'%')->orWhere('technical_manager', 'LIKE', '%'.$search.'%')->orWhere('vessel_email', 'LIKE', '%'.$search.'%')->count();
        }else{
            $filter_count = Vessel::count();
        }
        return $filter_count;
    }

    public function vessel_list(Request $request){
        
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','is_active','vessel_name','technical_manager','registered_owner','vessel_email','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = Vessel::select('*');
        if(!empty($search)) {
            $query->where('vessel_name', 'LIKE', '%'.$search.'%')->orWhere('technical_manager', 'LIKE', '%'.$search.'%')->orWhere('vessel_email', 'LIKE', '%'.$search.'%');
        }
        $query->orderBy($orderColumnIndex, $orderDirection);
        $users = $query->offset($start_limit)->limit($end_limit)->get(); 
        //printr($users);
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($users)){
            $recordsTotal = Vessel::count();
            $sno = 1+$start_limit;
            foreach($users as $record){
                $edit = '<button class="btn btn-default btn-sm" onclick="return add_edit_vessel('.$record->id.',\'edit\');" title="Edit"><i class="fa fa-edit"></i></button>';
                $delete = '<button class="btn btn-default btn-sm" onclick="return ajax_delete('.$record->id.',\'vessel\');" title="Delete"><i class="fa fa-trash"></i></button>';

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm" onclick="return ajax_active_inactive('.$record->id.',1,\'vessel\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm" onclick="return ajax_active_inactive('.$record->id.',2,\'vessel\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $all_data[] = [
                    'sno'=> $sno++,
                    'action'=>$edit.' '.$delete.' '.$status,
                    'vessel_name'=> $record->vessel_name,
                    'technical_manager'=> $record->technical_manager,
                    'registered_owner'=> $record->registered_owner,
                    'vessel_email'=> $record->vessel_email,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at))
                    
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->vessel_list_filter_count($search),
            'data' => $all_data,
        ]);
    }

    public function store(Request $request){
        $p_id = ($request->p_id > 0)?$request->p_id:'';
        printr($request->all());
        $data = [
            'vessel_name' => $request->vessel_name,
            'technical_manager' => $request->technical_manager,
            'registered_owner' => $request->registered_owner,
            'hull_no' => $request->hull_no,
            'master' => $request->master,
            'vessel_email' => $request->vessel_email,
            'imo_no' => $request->imo_no,
            'category' => $request->category,
            'type' => $request->type,
            'delivery_date' => $request->delivery_date,
            'dead_weight' => $request->dead_weight,
            'main_engine' => $request->main_engine,
            'bhp' => $request->bhp,
            'flag' => $request->flag,
            'grt' => $request->grt,
            'nrt' => $request->nrt,
            'cy_number' => $request->cy_number,
            'de_number' => $request->de_number,
            'sg_number' => $request->sg_number,
            'yard' => $request->yard,
            'sid' => $request->sid,
            'is_active' => ($request->is_active==1)?1:2,
            'created_by'=>Auth::user()->id
        ];

        if(!empty($request->file('vessel_image'))){
            $data['vessel_image'] = $this->uploadFile($request, 'vessel_image', 'uploads/image/vessel');
        } 
        
        $message = 'Opps! Something went wrong...';
        if($p_id > 0){
            Vessel::where('id',$p_id)->update($data);
            $lastId = $p_id;
            $message = 'Vessel updated successfully...';
        }else{
            $lastId = Vessel::insertGetId($data);
            $message = 'Vessel added successfully...';
        }
        if($lastId > 0){
            return response()->json(['status' =>'success','message' => $message],200);
        }else{
            return response()->json(['status' =>'error','message' => 'Opps! Something went wrong...'],200);
        }
    }

    public function vessel_edit(Request $request){
        $data = Vessel::where('id',$request->p_id)->first();
        echo json_encode(['data'=>$data]);
    }
}

