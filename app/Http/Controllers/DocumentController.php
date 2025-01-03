<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DocumentController extends Controller{

    public function index(){
        return view('master.document.index');
    }

    public function document_list_filter_count($search){
        if(!empty($search)) {
            $filter_count = Document::where('category_name', 'LIKE', '%'.$search.'%')->orWhere('document_type', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%')->count();
        }else{
            $filter_count = Document::count();
        }
        return $filter_count;
    }

    public function document_list(Request $request){
        
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','is_active','category_name','document_type','','description','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = Document::with('single_doc')->select('id','category_name','document_type','parent_category_id','description','created_at','is_active');
        if(!empty($search)) {
            $query->where('category_name', 'LIKE', '%'.$search.'%')->orWhere('document_type', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%');
        }
        $query->orderBy($orderColumnIndex, $orderDirection);
        $users = $query->offset($start_limit)->limit($end_limit)->get(); 
        //printr($users);
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($users)){
            $recordsTotal = Document::count();
            $sno = 1+$start_limit;
            foreach($users as $record){
                $edit = '<button class="btn btn-default btn-sm" onclick="return add_edit_document('.$record->id.',\'edit\');" title="Edit"><i class="fa fa-edit"></i></button>';
                $delete = '<button class="btn btn-default btn-sm" onclick="return ajax_delete('.$record->id.',\'document\');" title="Delete"><i class="fa fa-trash"></i></button>';

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm" onclick="return ajax_active_inactive('.$record->id.',1,\'document\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm" onclick="return ajax_active_inactive('.$record->id.',2,\'document\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $parent_document = !empty(@$record->single_doc->category_name)?$record->single_doc->category_name:'No Parent';
                $all_data[] = [
                    'sno'=> $sno++,
                    'category_name'=> $record->category_name,
                    'document_type'=> $record->document_type,
                    'parent_document'=> $parent_document,
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
            'recordsFiltered' => $this->document_list_filter_count($search),
            'data' => $all_data,
        ]);
    }

    public function store(Request $request){
        $p_id = ($request->p_id > 0)?$request->p_id:'';
        
        $check = Document::where('category_name',$request->category_name)->where('id','!=',$p_id)->count();
        if($check > 0){
            return response()->json(['status' =>'error','message' => 'Document already exist...'],200);
        }
        
        $data = [
            'category_name' => $request->category_name,
            'document_type' => $request->document_type,
            'parent_category_id' => !empty($request->parent_category_id)?$request->parent_category_id:0,
            'description' => $request->description,
            'is_active' => ($request->is_active==1)?1:2,
            'created_by'=>Auth::user()->id
        ];
        
        $message = 'Opps! Something went wrong...';
        if($p_id > 0){
            $lastId = Document::where('id',$p_id)->update($data);
            $message = 'Document updated successfully...';
        }else{
            $lastId = Document::insertGetId($data);
            $message = 'Document added successfully...';
        }
        if($lastId > 0){
            return response()->json(['status' =>'success','message' => $message],200);
        }else{
            return response()->json(['status' =>'error','message' => 'Opps! Something went wrong...'],200);
        }
    }

    public function document_edit(Request $request){
        $data = Document::where('id',$request->p_id)->first();
        echo json_encode(['data'=>$data]);
    }

    public function update(Request $request, string $id){
        $update_data = [
            'category_name' => $request->category_name,
            'document_type' => $request->document_type,
            'description' => $request->description,
            'is_active' => ($request->is_active==1)?1:2,
            'created_by'=>Auth::user()->id
        ];
        
        $lastId = Document::where('id',$id)->update($update_data);
        if($lastId > 0){
            session()->flash('success', '<p class="alert alert-success">Document updated successfully...</p>');
            return redirect()->route('document.edit', $id);
        }else{
            session()->flash('error', '<p class="alert alert-danger">Opps! Something went wrong...</p>');
            return redirect()->route('document.edit', $id);
        }
    }

    public function get_parent_document(Request $request){
        $id = !empty($request->p_id)?$request->p_id:'';
        $show_type = !empty($request->type)?$request->type:'all';
        $document_type = !empty($request->document_type)?trim($request->document_type):'';
        
        if($show_type == 'ajax_list'){
            $data = Document::select('id','parent_category_id','category_name')->where('document_type',$document_type)->where('is_active',1)->orderBy('parent_category_id','ASC')->limit(500)->get();
            $html = '<option value="" hidden="">Select parent category</option>';
            if(count($data->toArray())){
                foreach($data as $record){
                    $parent_category_id = ($record->parent_category_id==0)?'Parent':'Child';
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
}
