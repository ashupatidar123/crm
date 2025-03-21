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
use App\Models\Permission;

use App\Traits\FileUploadTrait;

class CompanyBranchController extends Controller{   
    use FileUploadTrait;
    public function __construct(){

    }

    public function index(){
        check_authorize('list','company_branch','non_ajax');
        $company_data = Company::select('id','company_name')->where('is_active',1)->get();
        return view('common.company_branch.index',compact('company_data'));
    }

    public function company_branch_list_filter_count($search){
        if(!empty($search)) {
            $filter_count = CompanyBranch::where('branch_code', 'LIKE', '%'.$search.'%')->orWhere('branch_name', 'LIKE', '%'.$search.'%')->orWhere('country', 'LIKE', '%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%')->orWhere('email', 'LIKE', '%'.$search.'%')->orWhere('website_url', 'LIKE', '%'.$search.'%')->count();
        }else{
            $filter_count = CompanyBranch::count();
        }
        return $filter_count;
    }

    public function company_branch_list(Request $request){
        
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','is_active','','branch_code','branch_name','country','phone','email','address','zip_code','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = CompanyBranch::with('single_company')->select('id','company_id','branch_code','branch_name','country','address','zip_code','phone','email','is_active','created_at','website_url','branch_logo','description');
        if(!empty($search)) {
            $query->where('branch_code', 'LIKE', '%'.$search.'%')->orWhere('branch_name', 'LIKE', '%'.$search.'%')->orWhere('country', 'LIKE', '%'.$search.'%')->orWhere('email', 'LIKE', '%'.$search.'%')->orWhere('phone', 'LIKE', '%'.$search.'%')->orWhere('website_url', 'LIKE', '%'.$search.'%');
        }
        $query->orderBy($orderColumnIndex, $orderDirection);
        $all_records = $query->offset($start_limit)->limit($end_limit)->get(); 
        //printr($all_records);
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($all_records)){
            $recordsTotal = CompanyBranch::count();
            $sno = 1+$start_limit;
            
            $edit = $delete = '';
            $action_permission = check_user_action_permission('company_profile');

            foreach($all_records as $record){
                if(@$action_permission->edit_access == 'yes'){
                    $edit = '<button class="btn btn-default btn-sm addEditLoader_'.$record->id.'" onclick="return add_edit_company_branch('.$record->id.',\'edit\');" title="Edit"><i class="fa fa-edit"></i></button>';
                }
                if(@$action_permission->delete_access == 'yes'){
                    $delete = '<button class="btn btn-default btn-sm deleteLoader_'.$record->id.'" onclick="return ajax_delete('.$record->id.',\'company_branch\');" title="Delete"><i class="fa fa-trash"></i></button>';
                }

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',1,\'company_branch\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',2,\'company_branch\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $branch_logo = '';
                if(!empty($record->branch_logo)){
                    $branch_logo = asset('storage/app/public/uploads/image/logo').'/'.$record->branch_logo;
                    $branch_logo = '<img target="_blank" width="130" height="110" class="btn btn-default" src="'.$branch_logo.'">';
                }

                $all_data[] = [
                    'sno'=> $sno++,
                    'company_name'=> @$record->single_company->company_name,
                    'branch_code'=> $record->branch_code,
                    'branch_name'=> $record->branch_name,
                    'country'=> $record->country,
                    'address'=> $record->address,
                    'zip_code'=> $record->zip_code,
                    'phone'=> $record->phone,
                    'email'=> $record->email,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at)),
                    'action'=>$edit.' '.$status.' '.$delete
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->company_branch_list_filter_count($search),
            'data' => $all_data,
        ]);
    }

    public function store(Request $request){
        $p_id = ($request->p_id > 0)?$request->p_id:'';
        if($p_id > 0){
            check_authorize('edit','company_branch');
        }else{
            check_authorize('add','company_branch');
        }
        
        $check = CompanyBranch::where('branch_name',$request->branch_name)->where('id','!=',$p_id)->count();
        if($check > 0){
            return response()->json(['status' =>'error','message' => 'Company branch already exist...'],200);
        }
        
        $data = [
            'company_id' => $request->company_id,
            'branch_code'=> $request->branch_code,
            'branch_name' => $request->branch_name,
            'country' => $request->country,
            'address' => $request->address,
            'zip_code' => $request->zip_code,
            'phone' => $request->phone,
            'email' => $request->email,
            'website_url' => $request->website_url,
            'description' => !empty($request->description)?$request->description:'',
            'is_active' => 1,
            'created_by'=>Auth::user()->id
        ];

        if(!empty($request->file('branch_logo'))){
            $data['branch_logo'] = $this->uploadFile($request, 'branch_logo', 'uploads/image/logo');
        }
        
        $message = 'Opps! Something went wrong...';
        if($p_id > 0){
            $lastId = CompanyBranch::where('id',$p_id)->update($data);
            $message = 'Company updated successfully...';
        }else{
            $lastId = CompanyBranch::insertGetId($data);
            $message = 'Company added successfully...';
        }
        if($lastId > 0){
            return response()->json(['status' =>'success','message' => $message],200);
        }else{
            return response()->json(['status' =>'error','message' => 'Opps! Something went wrong...'],200);
        }
    }

    public function company_branch_list_edit(Request $request){
        check_authorize('edit','company_branch');
        $data = CompanyBranch::where('id',$request->p_id)->first();
        $branch_logo = '';
        if(!empty($data->branch_logo)){
            $branch_logo = asset('storage/app/public/uploads/image/logo').'/'.$data->branch_logo;
            $data['branch_logo'] = '<img target="_blank" width="140" height="90" class="btn btn-default" src="'.$branch_logo.'">';
        }
        echo json_encode(['data'=>$data]);
    }

    
}
