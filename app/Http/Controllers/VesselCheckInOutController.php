<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vessel;
use App\Models\Port;
use App\Models\VesselCheckInOut;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class VesselCheckInOutController extends Controller{

    public function index(){
        $vessel = Vessel::select('id','vessel_name','vessel_email')->where('is_active',1)->orderBy('vessel_name','ASC')->limit(50)->get();
        $port = Port::select('id','port_name')->where('is_active',1)->orderBy('port_name','ASC')->limit(50)->get();
        $vessel_user = User::select('id','name_title','first_name','middle_name','last_name','email')->where('department_type','vessel')->where('is_active',1)->orderBy('first_name','ASC')->limit(150)->get();
        return view('vessel.check.index',compact('vessel','vessel_user','port'));
    }

    public function check_in_out_list_filter_count($search,$postData){
        $filter_count = VesselCheckInOut::where('id','>',0);

        if(!empty($search)) {
            $filter_count = VesselCheckInOut::where('description', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%')->count();
        }
        if(!empty($postData['search_vessel_id'])) {
            $filter_count->where('vessel_id',$postData['search_vessel_id']);
        }
        if(!empty($postData['search_user_id'])) {
            $filter_count->where('user_id',$postData['search_user_id']);
        }
        if(!empty($postData['search_start_check_in_date']) && !empty($postData['search_end_check_in_date'])) {
            $search_start_check_in_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_start_check_in_date'])));
            $search_end_check_in_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_end_check_in_date'])));
            
            $filter_count->where('check_in_date', '>=', date($search_start_check_in_date));
            $filter_count->where('check_in_date', '<=', date($search_end_check_in_date));
        }
        else if(!empty($postData['search_start_check_in_date'])){
            $search_start_check_in_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_start_check_in_date'])));
            $filter_count->where('check_in_date',date($search_start_check_in_date));
        }

        if(!empty($postData['search_start_check_out_date']) && !empty($postData['search_end_check_out_date'])) {
            $search_start_check_out_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_start_check_out_date'])));
            $search_end_check_out_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_end_check_out_date'])));
            
            $filter_count->where('check_out_date', '>=', date($search_start_check_out_date));
            $filter_count->where('check_out_date', '<=', date($search_end_check_out_date));
        }
        else if(!empty($postData['search_start_check_out_date'])){
            $search_start_check_out_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_start_check_out_date'])));
            $filter_count->where('check_out_date',date($search_start_check_out_date));
        }
        return $filter_count->count();
    }

    public function check_in_out_list(Request $request){
        $postData = $request->input();
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','is_active','','','check_in_date','check_out_date','description','check_out_date','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = VesselCheckInOut::with('single_user','single_vessel')->select('id','user_id','vessel_id','description','check_out_description','check_in_date','check_out_date','created_at','is_active','check_status');
        if(!empty($search)) {
            $query->where('check_in_date', 'LIKE', '%'.$search.'%')->orWhere('check_out_date', 'LIKE', '%'.$search.'%');
        }
        if(!empty($request->input('search_vessel_id'))){
            $query->where('vessel_id',$request->input('search_vessel_id')); 
        }
        if(!empty($request->input('search_user_id'))){
            $query->where('user_id',$request->input('search_user_id')); 
        }
        
        if(!empty($request->input('search_start_check_in_date')) && !empty($request->input('search_end_check_in_date'))){
            $search_start_check_in_date = date('Y-m-d',strtotime(str_replace('/','-',$request->input('search_start_check_in_date'))));
            $search_end_check_in_date = date('Y-m-d',strtotime(str_replace('/','-',$request->input('search_end_check_in_date'))));
            
            $query->where('check_in_date', '>=', date($search_start_check_in_date));
            $query->where('check_in_date', '<=', date($search_end_check_in_date));
        }
        else if(!empty($request->input('search_start_check_in_date'))){
            $search_start_check_in_date = date('Y-m-d',strtotime(str_replace('/','-',$request->input('search_start_check_in_date'))));
            $query->where('check_in_date',date($search_start_check_in_date));
        }

        if(!empty($request->input('search_start_check_out_date')) && !empty($request->input('search_end_check_out_date'))){
            $search_start_check_out_date = date('Y-m-d',strtotime(str_replace('/','-',$request->input('search_start_check_out_date'))));
            $search_end_check_out_date = date('Y-m-d',strtotime(str_replace('/','-',$request->input('search_end_check_out_date'))));
            
            $query->where('check_out_date', '>=', date($search_start_check_out_date));
            $query->where('check_out_date', '<=', date($search_end_check_out_date));
        }
        else if(!empty($request->input('search_start_check_out_date'))){
            $search_start_check_out_date = date('Y-m-d',strtotime(str_replace('/','-',$request->input('search_start_check_out_date'))));
            $query->where('check_out_date',date($search_start_check_out_date));
        }


        $query->orderBy($orderColumnIndex, $orderDirection);
        $response_data = $query->offset($start_limit)->limit($end_limit)->get(); 
        //printr($response_data);
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($response_data)){
            $recordsTotal = VesselCheckInOut::count();
            $sno = 1+$start_limit;
            foreach($response_data as $record){
                $check_in_date = !empty($record->check_in_date)?date('d/M/Y',strtotime($record->check_in_date)):'Empty';

                $edit = $sign_out = $delete = $status = '';
                $edit = 'SignOut';
                if($record->check_status == 1){
                    $edit = '<button class="btn btn-default btn-sm addEditLoader_'.$record->id.'" onclick="return add_edit_vessel_check_in_out('.$record->id.',\'edit\');" title="Edit"><i class="fa fa-edit"></i></button>';

                    $sign_out = '<button class="btn btn-default btn-sm checkOutLoader_'.$record->id.'" onclick="return vessel_check_out_popup('.$record->id.',\'edit \',\''.$check_in_date.'\');" title="SignOut"><i class="fas fa-sign-out-alt"></i></button>';

                    $delete = '<button class="btn btn-default btn-sm deleteLoader_'.$record->id.'" onclick="return ajax_delete('.$record->id.',\'vessel_check_out\');" title="Delete"><i class="fa fa-trash"></i></button>';

                    if($record->is_active == 1){
                        $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',1,\'vessel_check_out\');" title="Active"><i class="fa fa-check"></i></button>';
                    }else{
                        $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',2,\'vessel_check_out\');" title="In-Active"><i class="fa fa-close"></i></button>';
                    }
                }

                $user_name = !empty(@$record->single_user->first_name)?$record->single_user->first_name.' ('.$record->single_user->email.')':'';
                $vessel_name = !empty(@$record->single_vessel->vessel_name)?$record->single_vessel->vessel_name.' ('.$record->single_vessel->vessel_email.')':'';

                $all_data[] = [
                    'sno'=> $sno++,
                    'user_name'=> $user_name,
                    'vessel_name'=> $vessel_name,
                    'check_in_date'=> date('d/M/Y',strtotime($record->check_in_date)),
                    'check_out_date'=> !empty($record->check_out_date)?'<span class="text-danger">'.date('d/M/Y',strtotime($record->check_out_date)).'</span>':'',
                    'description'=> $record->description,
                    'check_out_description'=> $record->check_out_description,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at)),
                    'action'=>$edit.' '.$status.' '.$delete.' '.$sign_out
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->check_in_out_list_filter_count($search,$postData),
            'data' => $all_data,
        ]);
    }

    public function store(Request $request){
        $p_id = ($request->p_id > 0)?$request->p_id:'';
        
        $check = VesselCheckInOut::where(['user_id'=>$request->user_id,'vessel_id'=>$request->vessel_id,'check_status'=>1])->where('id','!=',$p_id)->count();
        if($check > 0){
            return response()->json(['status' =>'error','message' => 'Vessel already signing...'],200);
        }
        
        $data = [
            'port_id' => $request->port_id,
            'vessel_id' => $request->vessel_id,
            'user_id' => $request->user_id,
            'check_in_date' => !empty($request->check_in_date)?date('Y-m-d',strtotime(str_replace('/','-',$request->check_in_date))):null,
            'description' => $request->description,
            'is_active' => ($request->is_active==1)?1:2,
            'created_by'=>Auth::user()->id
        ];
        
        $message = 'Opps! Something went wrong...';
        if($p_id > 0){
            $lastId = VesselCheckInOut::where('id',$p_id)->update($data);
            $message = 'Vessel signing updated successfully...';
        }else{
            $lastId = VesselCheckInOut::insertGetId($data);
            $message = 'Vessel signing added successfully...';
        }
        if($lastId > 0){
            return response()->json(['status' =>'success','message' => $message],200);
        }else{
            return response()->json(['status' =>'error','message' => 'Opps! Something went wrong...'],200);
        }
    }

    public function check_in_out_edit(Request $request){
        $data = VesselCheckInOut::where('id',$request->p_id)->first();
        $data['check_in_date'] = date('d/m/Y',strtotime($data->check_in_date));
        echo json_encode(['data'=>$data]);
    }

    public function check_out(Request $request){
        $p_id = ($request->check_out_p_id > 0)?$request->check_out_p_id:'';
        
        $data = [
            'port_id' => $request->check_out_port_id,
            'check_out_date' => !empty($request->check_out_date)?date('Y-m-d',strtotime(str_replace('/','-',$request->check_out_date))):null,
            'check_out_description' => $request->check_out_description,
            'check_status' => 2,
            'check_out_by_user'=>Auth::user()->id,
            'check_out_created_at'=>date('Y-m-d H:i:s')
        ];
        
        if($p_id > 0){
            $lastId = VesselCheckInOut::where('id',$p_id)->update($data);
            return response()->json(['status' =>'success','message' => 'Vessel signout updated successfully...'],200);
        }
        else{
            return response()->json(['status' =>'error','message' => 'Opps! Something went wrong...'],200);
        }
    }
}

