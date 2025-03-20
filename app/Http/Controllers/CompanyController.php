<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Menu;
use App\Models\Company;
use App\Models\Permission;

use App\Traits\FileUploadTrait;

class CompanyController extends Controller{   
    use FileUploadTrait;
    public function __construct(){

    }

    public function index(){
        check_authorize('list','company_profile','non_ajax');
        return view('common.company.index');
    }

    public function company_list_filter_count($search){
        if(!empty($search)) {
            $filter_count = Company::where('company_name', 'LIKE', '%'.$search.'%')->orWhere('currency', 'LIKE', '%'.$search.'%')->orWhere('zip_code', 'LIKE', '%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%')->orWhere('email', 'LIKE', '%'.$search.'%')->orWhere('website_url', 'LIKE', '%'.$search.'%')->count();
        }else{
            $filter_count = Company::count();
        }
        return $filter_count;
    }

    public function company_list(Request $request){
        
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','is_active','company_name','currency','phone','email','website_url','gst_no','address','zip_code','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = Company::select('id','company_name','currency','zip_code','phone','email','website_url','gst_no','company_logo','is_active','created_at','description','fax','address');
        if(!empty($search)) {
            $query->where('company_name', 'LIKE', '%'.$search.'%')->orWhere('currency', 'LIKE', '%'.$search.'%')->orWhere('zip_code', 'LIKE', '%'.$search.'%')->orWhere('email', 'LIKE', '%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%')->orWhere('website_url', 'LIKE', '%'.$search.'%');
        }
        $query->orderBy($orderColumnIndex, $orderDirection);
        $all_records = $query->offset($start_limit)->limit($end_limit)->get(); 
        //printr($all_records);
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($all_records)){
            $recordsTotal = Company::count();
            $sno = 1+$start_limit;

            $edit = $delete = '';
            $action_permission = check_user_action_permission('company_profile');
            foreach($all_records as $record){
                if(@$action_permission->edit_access == 'yes'){
                    $edit = '<button class="btn btn-default btn-sm addEditLoader_'.$record->id.'" onclick="return add_edit_company('.$record->id.',\'edit\');" title="Edit"><i class="fa fa-edit"></i></button>';
                }
                if(@$action_permission->delete_access == 'yes'){
                    $delete = '<button class="btn btn-default btn-sm deleteLoader_'.$record->id.'" onclick="return ajax_delete('.$record->id.',\'company\');" title="Delete"><i class="fa fa-trash"></i></button>';
                }

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',1,\'company\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',2,\'company\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $company_logo = '';
                if(!empty($record->company_logo)){
                    $company_logo = asset('storage/app/public/uploads/image/logo').'/'.$record->company_logo;
                    $company_logo = '<img target="_blank" width="130" height="110" class="btn btn-default" src="'.$company_logo.'">';
                }

                $all_data[] = [
                    'sno'=> $sno++,
                    'company_name'=> $record->company_name,
                    'currency'=> $record->currency,
                    'phone'=> $record->phone,
                    'email'=> $record->email,
                    'website_url'=> $record->website_url,
                    'gst_no'=> $record->gst_no,
                    'address'=> $record->address,
                    'zip_code'=> $record->zip_code,
                    'description'=> $record->description,
                    'company_logo'=> $company_logo,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at)),
                    'action'=>$edit.' '.$status.' '.$delete
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->company_list_filter_count($search),
            'data' => $all_data,
        ]);
    }

    public function store(Request $request){
        $p_id = ($request->p_id > 0)?$request->p_id:'';
        if($p_id > 0){
            check_authorize('edit','company_profile');
        }else{
            check_authorize('add','company_profile');
        }
        
        $check = Company::where('company_name',$request->company_name)->where('id','!=',$p_id)->count();
        if($check > 0){
            return response()->json(['status' =>'error','message' => 'Company name already exist...'],200);
        }
        
        $data = [
            'company_name' => $request->company_name,
            'currency'=> $request->currency,
            'address' => $request->address,
            'zip_code' => $request->zip_code,
            'phone' => $request->phone,
            'fax' => $request->fax,
            'email' => $request->email,
            'website_url' => $request->website_url,
            'gst_no' => $request->gst_no,
            'description' => !empty($request->description)?$request->description:'',
            'is_active' => 1,
            'created_by'=>Auth::user()->id
        ];

        if(!empty($request->file('company_logo'))){
            $data['company_logo'] = $this->uploadFile($request, 'company_logo', 'uploads/image/logo');
        }
        
        $message = 'Opps! Something went wrong...';
        if($p_id > 0){
            $lastId = Company::where('id',$p_id)->update($data);
            $message = 'Company updated successfully...';
        }else{
            $lastId = Company::insertGetId($data);
            $message = 'Company added successfully...';
        }
        if($lastId > 0){
            return response()->json(['status' =>'success','message' => $message],200);
        }else{
            return response()->json(['status' =>'error','message' => 'Opps! Something went wrong...'],200);
        }
    }

    public function company_list_edit(Request $request){
        check_authorize('edit','company_profile');
        $data = Company::where('id',$request->p_id)->first();
        $company_logo = '';
        if(!empty($data->company_logo)){
            $company_logo = asset('storage/app/public/uploads/image/logo').'/'.$data->company_logo;
            $data['company_logo'] = '<img target="_blank" width="140" height="90" class="btn btn-default" src="'.$company_logo.'">';
        }
        echo json_encode(['data'=>$data]);
    }

    
}
