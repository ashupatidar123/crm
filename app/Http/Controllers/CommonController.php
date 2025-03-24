<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\DepartmentDesignation;
use App\Models\Document;
use App\Models\UserDocument;
use App\Models\Vessel;
use App\Models\VesselCategory;
use App\Models\VesselDocument;
use App\Models\VesselCheckInOut;
use App\Models\Menu;
use App\Models\Apprisal;
use App\Models\Permission;
use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Port;

use App\Models\UserAddress;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Traits\FileUploadTrait;

class CommonController extends Controller{
    use FileUploadTrait;
    public function __construct(){

    }

    public function ajax_active_inactive(Request $request){
        $type = (@$request->type==1)?2:1;
        $tbl = @$request->tbl;
        
        if($request->p_id < 1 || empty($tbl)){
           return response()->json(['status' =>'error','message' => 'Something went wrong'],201); 
        }
        else if($tbl == 'user'){
            User::where('id',$request->p_id)->update(['is_active'=>$type]);
        }
        else if($tbl == 'role'){
            Role::where('id',$request->p_id)->update(['is_active'=>$type]);
        }
        else if($tbl == 'department'){
            Department::where('id',$request->p_id)->update(['is_active'=>$type]);
        }
        else if($tbl == 'designation'){
            DepartmentDesignation::where('id',$request->p_id)->update(['is_active'=>$type]);
        }
        else if($tbl == 'document'){
            Document::where('id',$request->p_id)->update(['is_active'=>$type]);
        }
        else if($tbl == 'user_document'){
            UserDocument::where('id',$request->p_id)->update(['is_active'=>$type]);
        }
        else if($tbl == 'vessel'){
            Vessel::where('id',$request->p_id)->update(['is_active'=>$type]);
        }
        else if($tbl == 'vessel_category'){
            VesselCategory::where('id',$request->p_id)->update(['is_active'=>$type]);
        }
        else if($tbl == 'vessel_document'){
            VesselDocument::where('id',$request->p_id)->update(['is_active'=>$type]);
        }
        else if($tbl == 'vessel_check_out'){
            VesselCheckInOut::where('id',$request->p_id)->update(['is_active'=>$type]);
        }
        else if($tbl == 'menu'){
            Menu::where('id',$request->p_id)->update(['is_active'=>$type]);
        }
        else if($tbl == 'apprisal'){
            Apprisal::where('id',$request->p_id)->update(['is_active'=>$type]);
        }
        else if($tbl == 'menu_permission_department'){
            Permission::where('id',$request->p_id)->update(['is_active'=>$type]);
        }
        else if($tbl == 'company'){
            Company::where('id',$request->p_id)->update(['is_active'=>$type]);
        }
        else if($tbl == 'company_branch'){
            CompanyBranch::where('id',$request->p_id)->update(['is_active'=>$type]);
        }
        else if($tbl == 'port'){
            Port::where('id',$request->p_id)->update(['is_active'=>$type]);
        }
        else{
           return response()->json(['status' =>'error','message' => 'Something went wrong'],200); 
        }

        if($type == 1){
            return response()->json(['status' =>'success','message' => 'Active successfully'],200);
        }
        else if($type == 2){
            return response()->json(['status' =>'success','message' => 'In-Active successfully'],200);
        }
        else{
            return response()->json(['status' =>'error','message' => 'Something went wrong'],201);  
        } 
    }

    public function ajax_delete(Request $request){
        
        $record_dlt = '';
        $tbl = !empty($request->tbl)?$request->tbl:'';
        $p_id = !empty($request->p_id)?$request->p_id:'';
        
        if(empty($tbl) || empty($p_id)){
            return response()->json(['status' =>'error','message' => 'Something went wrong'],201);
        }
        
        if($tbl == 'user'){
            $record_dlt = User::find($request->p_id);
        }
        else if($tbl == 'role'){
            $record_dlt = Role::find($request->p_id);
        }
        else if($tbl == 'department'){
            $record_dlt = Department::find($request->p_id);
        }
        else if($tbl == 'designation'){
            $record_dlt = DepartmentDesignation::find($request->p_id);
        }
        else if($tbl == 'document'){
            $record_dlt = Document::find($request->p_id);
        }
        else if($tbl == 'user_document'){
            $record_dlt = UserDocument::find($request->p_id);
        }
        else if($tbl == 'vessel'){
            $record_dlt = Vessel::find($request->p_id);
        }
        else if($tbl == 'vessel_category'){
            $record_dlt = VesselCategory::find($request->p_id);
        }
        else if($tbl == 'vessel_document'){
            $record_dlt = VesselDocument::find($request->p_id);
        }
        else if($tbl == 'vessel_check_out'){
            $record_dlt = VesselCheckInOut::find($request->p_id);
        }
        else if($tbl == 'menu'){
            $record_dlt = Menu::find($request->p_id);
        }
        else if($tbl == 'apprisal'){
            $record_dlt = Apprisal::find($request->p_id);
        }
        else if($tbl == 'menu_permission_department'){
            $record_dlt = Permission::find($request->p_id);
        }
        else if($tbl == 'company'){
            $record_dlt = Company::find($request->p_id);
        }
        else if($tbl == 'company_branch'){
            $record_dlt = CompanyBranch::find($request->p_id);
        }
        else if($tbl == 'port'){
            $record_dlt = Port::find($request->p_id);
        }
        else{
            return response()->json(['status' =>'error','message' => 'Deletion failed'],201);
        }

        if($record_dlt) {
            $record_dlt->delete();
            return response()->json(['status' =>'success','message' => 'Deleted successfully'],200); 
        }else{
            return response()->json(['status' =>'error','message' => 'Deletion failed'],201);
        }
    }

    public function ajax_view(Request $request){
        $tbl = !empty($request->tbl)?$request->tbl:'';
        $p_id = !empty($request->p_id)?$request->p_id:'';
        if(empty($tbl) || empty($p_id)){
            return response()->json(['status' =>'error','message' => 'Something went wrong'],201);
        }
        $data = '';
        if($tbl == 'user'){
            $data = User::where('id',$request->p_id)->first();
            $data['date_birth'] = date('d/m/Y',strtotime($data->date_birth));
            $data['update_at'] = !empty($data->update_at)?date('d/m/Y',strtotime($data->update_at)):'Not updated';
            echo json_encode(['data'=>$data]);
        }
        
        if(empty($data)){
            echo json_encode(['data'=>'']);
        }
    }

    public function dropzone_file_upload(Request $request){
        $file_name = '';
        if(!empty($request->file('user_document'))){
            $file_name = $this->uploadFile($request, 'user_document', 'uploads/document/users');
        }
        else if(!empty($request->file('user_image'))){
            $file_name = $this->uploadFile($request, 'user_image', 'uploads/image/users');
        }
        else if(!empty($request->file('vessel_image'))){
            $file_name = $this->uploadFile($request, 'vessel_image', 'uploads/image/vessels');
        }
        else if(!empty($request->file('vessel_document'))){
            $file_name = $this->uploadFile($request, 'vessel_document', 'uploads/document/vessels');
        }
        echo $file_name;
    }
}
