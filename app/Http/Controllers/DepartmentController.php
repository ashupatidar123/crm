<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DepartmentController extends Controller{
    
    public function curl_api(){
        $url = 'https://tractorgyan.com/api/whatsapp_popup_enquiry';
        
        $url = 'https://staging.tractorgyan.com/api/whatsapp_popup_enquiry';
        
        $data = array(
            'name' => str_shuffle('surbhi jain'),
            'mobile' =>rand(7777777777,9999999999),//rand(1111111111,4444444444)
            'brand' => str_shuffle('mahindra me'),
            'model' => rand(111,999),
            'state' => str_shuffle('Delhi'),
            'district' => str_shuffle('Delhi allowed'),
            'tehsil' => str_shuffle('delhipup'),
            'page_source' => 'https://tractorgyan.com',
            'type_id' => rand(11,88),
            'verified_flag' => 'Verified'
        );

        // Encode data to JSON
        $json_data = json_encode($data);

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);  // Set the URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Return the response as a string
        curl_setopt($ch, CURLOPT_POST, true);  // Set the method to POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);  // Attach the JSON data to the request
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',  // Set content type to JSON
            'Authorization: Bearer YOUR_API_KEY'  // Optional: Add authorization header if needed
        ));

        $response = curl_exec($ch);

        if(curl_errno($ch)) {
            return 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);
        return $response;
    }

    public function curl_api_verify($primary_id,$otp){
        $url = 'https://tractorgyan.com/api/enquiry_otp_verify';

        $data = array(
            'primary_id' => $primary_id,
            'otp' =>$otp
        );

        // Encode data to JSON
        $json_data = json_encode($data);

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);  // Set the URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Return the response as a string
        curl_setopt($ch, CURLOPT_POST, true);  // Set the method to POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);  // Attach the JSON data to the request
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',  // Set content type to JSON
            'Authorization: Bearer YOUR_API_KEY'  // Optional: Add authorization header if needed
        ));

        $response = curl_exec($ch);

        if(curl_errno($ch)) {
            return 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);
        return $response;
    }

    public function url_run(){
        //$url = 'https://staging.tractorgyan.com/';
        //$url = 'https://staging.tractorgyan.com/api/home_popular_tractor';
        $url = 'https://staging.tractorgyan.com';
        $ch = curl_init();
        //printr($url,'p');
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // Return response as a string
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Set timeout (seconds)
        
        // Execute the cURL session and capture the response
        $response = curl_exec($ch);
        return true;
        printr($response,'p');
    }

    public function index(){
        
        return view('master.department.index'); exit;
        for($i=1;$i<=5;$i++){
            $data = $this->curl_api();
            printr($data,'p');
            $data = json_decode($data);
            if(!empty($data->data->otp)){
                $primary_id = $data->data->primary_id;
                $otp = $data->data->otp;
                $vdata = $this->curl_api_verify($primary_id,$otp);
            }
        }
        return view('master.department.index');
    }

    public function department_list_filter_count($search){
        if(!empty($search)) {
            $filter_count = Department::where('department_name', 'LIKE', '%'.$search.'%')->orWhere('department_type', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%')->count();
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

        $columns = ['','is_active','department_name','department_type','description','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = Department::select('id','department_name','department_type','description','created_at','is_active');
        if(!empty($search)) {
            $query->where('department_name', 'LIKE', '%'.$search.'%')->orWhere('department_type', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%');
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
                $edit = '<button class="btn btn-default btn-sm addEditLoader_'.$record->id.'" onclick="return add_edit_department('.$record->id.',\'edit\');" title="Edit"><i class="fa fa-edit"></i></button>';
                $delete = '<button class="btn btn-default btn-sm deleteLoader_'.$record->id.'" onclick="return ajax_delete('.$record->id.',\'department\');" title="Delete"><i class="fa fa-trash"></i></button>';

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',1,\'department\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',2,\'department\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $all_data[] = [
                    'sno'=> $sno++,
                    'department_name'=> $record->department_name,
                    'department_type'=> $record->department_type,
                    'description'=> $record->description,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at)),
                    'status'=>$status,
                    'action'=>$edit.' '.$delete.' '.$status
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
            'department_type' => $request->department_type,
            'description' => $request->description,
            'is_active' => ($request->is_active==1)?1:2,
            'created_by'=>Auth::user()->id
        ];
        
        $message = 'Opps! Something went wrong...';
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
            'department_type' => $request->department_type,
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
