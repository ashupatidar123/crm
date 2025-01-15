<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\VesselCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class VesselCategoryController extends Controller{

    public function index(){
        $parent_child_category = VesselCategory::select('id','parent_category_id','category_name')->where('is_active',1)->orderBy('category_name','ASC')->limit(5)->get();
        return view('vessel.category.index');
    }

    public function vessel_category_list_filter_count($search){
        if(!empty($search)) {
            $filter_count = VesselCategory::where('category_name', 'LIKE', '%'.$search.'%')->orWhere('description', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%')->count();
        }else{
            $filter_count = VesselCategory::count();
        }
        return $filter_count;
    }

    public function vessel_category_list(Request $request){
        
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','is_active','category_name','','description','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = VesselCategory::with('single_category')->select('id','category_name','parent_category_id','description','created_at','is_active');
        if(!empty($search)) {
            $query->where('category_name', 'LIKE', '%'.$search.'%')->orWhere('description', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%');
        }
        $query->orderBy($orderColumnIndex, $orderDirection);
        $users = $query->offset($start_limit)->limit($end_limit)->get(); 
        //printr($users);
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($users)){
            $recordsTotal = VesselCategory::count();
            $sno = 1+$start_limit;
            foreach($users as $record){
                $edit = '<button class="btn btn-default btn-sm addEditLoader_'.$record->id.'" onclick="return add_edit_vessel_category('.$record->id.',\'edit\');" title="Edit"><i class="fa fa-edit"></i></button>';
                $delete = '<button class="btn btn-default btn-sm deleteLoader_'.$record->id.'" onclick="return ajax_delete('.$record->id.',\'vessel_category\');" title="Delete"><i class="fa fa-trash"></i></button>';

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',1,\'vessel_category\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',2,\'vessel_category\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $parent_vessel_category = !empty(@$record->single_category->category_name)?$record->single_category->category_name:'No Parent';
                $all_data[] = [
                    'sno'=> $sno++,
                    'category_name'=> $record->category_name,
                    'parent_vessel_category'=> $parent_vessel_category,
                    'description'=> $record->description,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at)),
                    'action'=>$edit.' '.$status.' '.$delete
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->vessel_category_list_filter_count($search),
            'data' => $all_data,
        ]);
    }

    public function store(Request $request){
        $p_id = ($request->p_id > 0)?$request->p_id:'';
        
        $check = VesselCategory::where('category_name',$request->category_name)->where('id','!=',$p_id)->count();
        if($check > 0){
            return response()->json(['status' =>'error','message' => 'Vessel category already exist...'],200);
        }
        
        $data = [
            'category_name' => $request->category_name,
            'parent_category_id' => !empty($request->parent_category_id)?$request->parent_category_id:0,
            'description' => $request->description,
            'is_active' => ($request->is_active==1)?1:2,
            'created_by'=>Auth::user()->id
        ];
        
        $message = 'Opps! Something went wrong...';
        if($p_id > 0){
            $lastId = VesselCategory::where('id',$p_id)->update($data);
            $message = 'Vessel category updated successfully...';
        }else{
            $lastId = VesselCategory::insertGetId($data);
            $message = 'Vessel category added successfully...';
        }
        if($lastId > 0){
            return response()->json(['status' =>'success','message' => $message],200);
        }else{
            return response()->json(['status' =>'error','message' => 'Opps! Something went wrong...'],200);
        }
    }

    public function vessel_category_edit(Request $request){
        $data = VesselCategory::where('id',$request->p_id)->first();
        echo json_encode(['data'=>$data]);
    }

    public function get_parent_vessel_category(Request $request){
        $id = !empty($request->p_id)?$request->p_id:'';
        $show_type = !empty($request->type)?$request->type:'all';
        
        
        if($show_type == 'ajax_list'){
            $data = VesselCategory::select('id','parent_category_id','category_name')->where('is_active',1)->orderBy('category_name','ASC')->limit(50)->get();
            $html = '<option value="0">Select</option>';
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

