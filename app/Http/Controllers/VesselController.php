<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Document;
use App\Models\Vessel;
use App\Models\UserDocument;
use App\Models\VesselDocument;
use App\Models\VesselCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\FileUploadTrait;

class VesselController extends Controller{
    
    use FileUploadTrait;
    public function __construct(){

    }

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
            $end_limit = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw = $request->input('draw');
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
                $edit = '<button class="btn btn-default btn-sm addEditLoader_'.$record->id.'" onclick="return add_edit_vessel('.$record->id.',\'edit\');" title="Edit"><i class="fa fa-edit"></i></button>';
                $delete = '<button class="btn btn-default btn-sm deleteLoader_'.$record->id.'" onclick="return ajax_delete('.$record->id.',\'vessel\');" title="Delete"><i class="fa fa-trash"></i></button>';

                $details = '<a target="_blank" href="'.route('vessel-details',['id'=>$record->id]).'" class="btn btn-default btn-sm" title="Vessel details"><i class="fa fa-eye"></i></a>';

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',1,\'vessel\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',2,\'vessel\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $all_data[] = [
                    'sno'=> $sno++,
                    'action'=>$edit.' '.$status.' '.$details.' '.$delete,
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
        //printr($request->all(),'p');
        $data = [
            'vessel_name' => $request->vessel_name,
            'technical_manager' => $request->technical_manager,
            'registered_owner' => $request->registered_owner,
            'hull_no' => $request->hull_no,
            'master' => $request->master,
            'vessel_email' => $request->vessel_email,
            'imo_no' => $request->imo_no,
            'category_id' => $request->category_id,
            'parent_category_id' => $request->parent_category_id,
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
            'sid' => @$request->sid,
            'is_active' => ($request->is_active==1)?1:2,
            'description' => $request->description,
            'created_by'=>Auth::user()->id
        ];

        if(!empty($request->vessel_image)){
            $data['vessel_image'] = $request->vessel_image;
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
    
        if(!empty($data->vessel_image)){
            $img_url = asset('storage/app/public/uploads/image/vessel').'/'.$data->vessel_image;
        }else{
            $img_url = url('public/images/img/no_image.png');
        }
        
        if(file_exists($img_url)){
            $img_url = $img_url;
        }
        $data['vessel_image'] = '<img src="'.$img_url.'" width="90px" height="70px">';

        echo json_encode(['data'=>$data]);
    }

    public function vessel_file_upload(Request $request){
        $file_name = '';
        if(!empty($request->file('vessel_image'))){
            $file_name = $this->uploadFile($request, 'vessel_image', 'uploads/image/vessels');
        }
        else if(!empty($request->file('vessel_document'))){
            $file_name = $this->uploadFile($request, 'vessel_document', 'uploads/document/vessels');
        }
        echo $file_name;
    }

    public function get_all_vessel_category(Request $request){
        $id = !empty($request->p_id)?$request->p_id:'';
        $c_id = !empty($request->c_id)?$request->c_id:'';
        $show_type = !empty($request->type)?$request->type:'all';
        
        
        if($show_type == 'ajax_list'){
            if($c_id > 0){
                $data = VesselCategory::select('id','category_name','parent_category_id')->where('is_active',1)->where('parent_category_id',$c_id)->orderBy('category_name','ASC')->limit(50)->get();
            }else{
                $data = VesselCategory::select('id','category_name')->where('is_active',1)->where('parent_category_id',0)->orderBy('category_name','ASC')->limit(50)->get();
            }
            

            $html = '<option value="">Select</option>';
            if(count($data->toArray())){
                foreach($data as $record){
                    $selected = '';
                    if($id == $record->id){
                        $selected = 'selected';
                    }
                    $html .= '<option value="'.$record->id.'" '.$selected.'>'.ucwords($record->category_name).'</option>';
                }
            }else{
                $html = '<option value="" hidden>Not found</option>';
            }
            echo $html;
        }
    }

    public function get_all_parent_vessel_category(Request $request){
        $id = !empty($request->p_id)?$request->p_id:'';
        $show_type = !empty($request->type)?$request->type:'all';
        
        
        if($show_type == 'ajax_list'){
            $data = VesselCategory::select('id','category_name')->where('is_active',1)->where('parent_category_id',0)->orderBy('category_name','ASC')->limit(50)->get();
            $html = '<option value="">Select</option>';
            if(count($data->toArray())){
                foreach($data as $record){
                    $selected = '';
                    if($id == $record->id){
                        $selected = 'selected';
                    }
                    $html .= '<option value="'.$record->id.'" '.$selected.'>'.ucwords($record->category_name).' ('.$parent_category_id.')</option>';
                }
            }else{
                $html = '<option value="" hidden>Not found</option>';
            }
            echo $html;
        }
    }

    /* vesse details section tabs*/
    public function showVesselDetails(Request $request){
        
        $data = Vessel::where('id',$request->id)->first();
        if(empty($data)){
            return redirect(route('vessel.index'),302);
        }
        $address = [];
        return view('vessel.vessel.details',compact('data','address'));
    }

    public function vessel_tab_detail(Request $request){
        $id = !empty($request->id)?$request->id:0;
        $page_type = !empty($request->page_type)?$request->page_type:'profile';
        $data = User::where('id',$id)->first();
        $department_type = @$data->department_type;
        if($page_type == 'profile'){
            //return view('vessel.vessel.tab.edit_user',compact('data'));
        }
        else if($page_type == 'document'){
            $data_document = Document::where(['document_type'=>'vessel','is_active'=>1])->limit(50)->get();
            $data_vessel = Vessel::where(['is_active'=>1])->limit(50)->get();
            return view('vessel.vessel.tab.vessel_document_list',compact('data','data_document','data_vessel'));
        }
    }

    public function vessel_document_list_tab_filter_count($search,$postData){
        $vessel_id = !empty($postData['vessel_id'])?$postData['vessel_id']:0;
        
        if(!empty($postData['search_vessel_name']) && $postData['search_vessel_name'] == 'all'){
            $filter_count = VesselDocument::with('single_document')->where('vessel_id','>',0);
        }
        else if(!empty($postData['search_vessel_name'])){
            $vessel_id = $postData['search_vessel_name'];
            $filter_count = VesselDocument::with('single_document')->where('vessel_id',$vessel_id);
        }else{
            $filter_count = VesselDocument::with('single_document')->where('vessel_id',$vessel_id);  
        }
        
        if(!empty($search)) {
            $filter_count = VesselDocument::with('single_document')->where('document_name', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%')->orWhere('vessel_document', 'LIKE', '%'.$search.'%');
        }

        if(!empty($postData['search_start_date']) && !empty($postData['search_end_date'])){
            $search_start_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_start_date'])));
            $search_end_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_end_date']. ' +1 day')));

            $filter_count->where('created_at', '>=', date($search_start_date));
            $filter_count->where('created_at', '<=', date($search_end_date)); 
        }
        if(!empty($postData['search_document_name'])){
            $filter_count->where('document_name','LIKE','%'.$postData['search_document_name'].'%');
        }
        if(!empty($postData['search_document_category'])){
            $filter_count->where('document_id',$postData['search_document_category']);
        }

        if(!empty($postData['search_issue_date'])){
            $search_issue_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_issue_date'])));
            $filter_count->where('issue_date',date($search_issue_date));
        }
        if(!empty($postData['search_expiry_date'])){
            $search_expiry_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_expiry_date'])));
            $filter_count->where('expiry_date',date($search_expiry_date));
        }
        return $filter_count->count();
    }

    public function vessel_document_list_tab(Request $request){
        $postData = $request->input();
        $vessel_id = !empty($request->input('vessel_id'))?$request->input('vessel_id'):0;
        if(!empty($postData['search_vessel_name'])){
            $vessel_id = $postData['search_vessel_name'];
        }

        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','is_active','','document_name','','issue_date','expiry_date','vessel_document','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = VesselDocument::with('single_document','single_vessel')->select('id','document_id','document_name','issue_date','expiry_date','vessel_document','created_at','is_active','vessel_id');
        
        if(!empty($postData['search_vessel_name']) && $postData['search_vessel_name'] == 'all'){
            $query->where('vessel_id','>',0);
        }
        else{
            $query->where('vessel_id',$vessel_id);
        }

        if(!empty($search)) {
            $query->where('document_name', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%')->orWhere('vessel_document', 'LIKE', '%'.$search.'%');
        }
        
        /*custom search*/
        if(!empty($postData['search_start_date']) && !empty($postData['search_end_date'])){
            $search_start_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_start_date'])));
            $search_end_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_end_date']. ' +1 day')));

            $query->where('created_at', '>=', date($search_start_date));
            $query->where('created_at', '<=', date($search_end_date)); 
        }
        if(!empty($postData['search_document_name'])){
            $query->where('document_name','LIKE','%'.$postData['search_document_name'].'%');
        }
        
        if(!empty($postData['search_document_category'])){
            $query->where('document_id',$postData['search_document_category']);
        }

        if(!empty($postData['search_issue_date'])){
            $search_issue_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_issue_date'])));
            $query->where('issue_date',date($search_issue_date));
        }
        if(!empty($postData['search_expiry_date'])){
            $search_expiry_date = date('Y-m-d',strtotime(str_replace('/','-',$postData['search_expiry_date'])));
            $query->where('expiry_date',date($search_expiry_date));
        }
        
        $query->orderBy($orderColumnIndex, $orderDirection);
        $users = $query->offset($start_limit)->limit($end_limit)->get(); 
        //printr($users);
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($users)){
            if(!empty($postData['search_vessel_name']) && $postData['search_vessel_name'] == 'all'){
                $recordsTotal = VesselDocument::count();
            }
            else{
                $recordsTotal = VesselDocument::where('vessel_id',$vessel_id)->count();
            }

            $sno = 1+$start_limit;
            foreach($users as $record){
                $edit = '<button class="btn btn-default btn-sm addEditLoader_'.$record->id.'" onclick="return add_edit_vessel_document('.$record->id.',\'edit\');" title="Edit"><i class="fa fa-edit"></i></button>';

                $view = '<button class="btn btn-default btn-sm" onclick="return ajax_view('.$record->id.',\'vessel_document\');" title="View"><i class="fa fa-eye"></i></button>';

                $delete = '<button class="btn btn-default btn-sm deleteLoader_'.$record->id.'" onclick="return ajax_delete('.$record->id.',\'vessel_document\');" title="Delete"><i class="fa fa-trash"></i></button>';

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',1,\'vessel_document\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',2,\'vessel_document\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $issue_date = !empty($record->issue_date)?date('d/M/Y',strtotime($record->issue_date)):'';
                $expiry_date = !empty($record->expiry_date)?date('d/M/Y',strtotime($record->expiry_date)):'';

                if(!empty($record->vessel_document)){
                    $file_type = check_file_type($record->vessel_document);
                    
                    $doc_url = '<button class="btn btn-default" onclick="return view_document(\''.$record->vessel_document.'\',\''.$file_type.'\',\'vessel_document\');"><i class="fa fa-eye"> '.$file_type.'</i></button>';
                }else{
                    $doc_url = '<button class="btn btn-default"><i class="fa fa-close"> no</i></button>';
                }

                $category_name = @$record->single_document->category_name;
                $vessel_name = @$record->single_vessel->vessel_name;

                $all_data[] = [
                    'sno'=> $sno++,
                    'action'=>$edit.' '.$status.' '.$delete,
                    'vessel_name'=> @$vessel_name,
                    'document_name'=> @$record->document_name,
                    'category_name'=> @$category_name,
                    'issue_date'=> $issue_date,
                    'expiry_date'=> $expiry_date,
                    'vessel_document'=> @$doc_url,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at))
                ];
            }
        }
        //printr($all_data,'p');
        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->vessel_document_list_tab_filter_count($search,$postData),
            'data' => $all_data,
        ]);
    }

    public function add_vessel_document(Request $request){
        //printr($request->file('user_document'),'pp');
        if(empty($request->document_name) || empty($request->document_id) || empty($request->vessel_id)){
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">All fields are required...</p>','s_msg'=>'All fields are required...'],200);
        }
        $p_id = !empty(@$request->p_id)?@$request->p_id:'';
        if(empty($request->vessel_document) && $p_id < 1){
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">Document is required...</p>','s_msg'=>'Document is required...'],200);
        }
        
        $postData = [
            'vessel_id'=>@$request->vessel_id,
            'document_id' => $request->document_id,
            'document_name' => $request->document_name,
            'issue_date' => !empty($request->issue_date)?date('Y-m-d',strtotime(str_replace('/','-',$request->issue_date))):null,
            'expiry_date' => !empty($request->expiry_date)?date('Y-m-d',strtotime(str_replace('/','-',$request->expiry_date))):null,
            'is_active' => ($request->is_active==1)?1:2,
            'description' => @$request->document_description,
            'created_by'=>Auth::user()->id
        ];
        if(!empty($request->vessel_document)){
            $postData['vessel_document'] = $request->vessel_document;
        }

        if($p_id <= 0){
            $id = VesselDocument::insertGetId($postData);
        }else{
            VesselDocument::where('id',$request->p_id)->update($postData);
            $id = $p_id;
        }
        if($id > 0){
            return response()->json(['status' =>'success','message' => '<p class="alert alert-success">Vessel document uploaded...</p>','s_msg'=>'Vessel document uploaded...'],200);
        }else{
            return response()->json(['status' =>'failed','message' => '<p class="alert alert-danger">Opps! Something went wrong...</p>','s_msg'=>'Opps! Something went wrong...'],200);
        }
    }

    public function vessel_document_edit(Request $request){
        $data = VesselDocument::where('id',$request->p_id)->first();
        if(!empty($data->vessel_document)){
            $doc_url = asset('storage/app/public/uploads/document/vessels').'/'.$data->vessel_document;
            $doc_url = '<a target="_blank" class="btn btn-default" href="'.$doc_url.'">'.$data->vessel_document.'</a>';
        }else{
            $doc_url = '';
        }
        $data['vessel_document'] = @$doc_url;
        if(!empty($data->issue_date)){
            $data['issue_date'] = date('d/m/Y',strtotime($data->issue_date));
        }
        if(!empty($data->expiry_date)){
            $data['expiry_date'] = date('d/m/Y',strtotime($data->expiry_date));
        }
        echo json_encode(['data'=>$data]);
    }
}

