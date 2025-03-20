<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MenuController extends Controller{

    public function index(){
        check_authorize('list','menu','non_ajax');
        return view('master.menu.index');
    }

    public function menu_list_filter_count($search){
        if(!empty($search)) {
            $filter_count = Menu::where('menu_name', 'LIKE', '%'.$search.'%')->orWhere('menu_code', 'LIKE', '%'.$search.'%')->orWhere('menu_link', 'LIKE', '%'.$search.'%')->orWhere('description', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%')->count();
        }else{
            $filter_count = Menu::count();
        }
        return $filter_count;
    }

    public function menu_list(Request $request){
        
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','is_active','menu_name','','menu_code','menu_sequence','','','description','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = Menu::with('parent_menu')->select('id','menu_name','menu_slug','menu_code','menu_sequence','menu_link','menu_icon','parent_menu_id','description','is_active','created_at');
        if(!empty($search)) {
            $query->where('menu_name', 'LIKE', '%'.$search.'%')->orWhere('menu_code', 'LIKE', '%'.$search.'%')->orWhere('menu_link', 'LIKE', '%'.$search.'%')->orWhere('description', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%');
        }
        $query->orderBy($orderColumnIndex, $orderDirection);
        $all_records = $query->offset($start_limit)->limit($end_limit)->get(); 
        //printr($all_records);
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($all_records)){
            $recordsTotal = Menu::count();
            $sno = 1+$start_limit;
            
            $edit = $view = $delete = '';
            $action_permission = check_user_action_permission('menu');

            foreach($all_records as $record){
                if(@$action_permission->edit_access == 'yes'){
                    $edit = '<button class="btn btn-default btn-sm addEditLoader_'.$record->id.'" onclick="return add_edit_menu('.$record->id.',\'edit\');" title="Edit"><i class="fa fa-edit"></i></button>';
                }
                if(@$action_permission->delete_access == 'yes'){
                    $delete = '<button class="btn btn-default btn-sm deleteLoader_'.$record->id.'" onclick="return ajax_delete('.$record->id.',\'menu\');" title="Delete"><i class="fa fa-trash"></i></button>';
                }

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',1,\'menu\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return ajax_active_inactive('.$record->id.',2,\'menu\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $parent_menu_name = !empty(@$record->parent_menu->menu_name)?$record->parent_menu->menu_name:'No Parent';
                $all_data[] = [
                    'sno'=> $sno++,
                    'menu_name'=> $record->menu_name,
                    'menu_slug'=> $record->menu_slug,
                    'parent_menu'=> $parent_menu_name,
                    'menu_code'=> $record->menu_code,
                    'menu_sequence'=> $record->menu_sequence,
                    'menu_link'=> !empty($record->menu_link)?$record->menu_link:'',
                    'menu_icon'=> $record->menu_icon,
                    'description'=> $record->description,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at)),
                    'action'=>$edit.' '.$status.' '.$delete
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->menu_list_filter_count($search),
            'data' => $all_data,
        ]);
    }

    public function store(Request $request){
        
        $p_id = ($request->p_id > 0)?$request->p_id:'';
        if($p_id > 0){
            check_authorize('edit','menu');
        }else{
            check_authorize('add','menu');
        }
        
        $check = Menu::where('menu_name',$request->menu_name)->where('id','!=',$p_id)->count();
        if($check > 0){
            return response()->json(['status' =>'error','message' => 'Menu already exist...'],200);
        }
        $check = Menu::where('menu_link',$request->menu_link)->where('menu_link','!=','')->where('id','!=',$p_id)->count();
        if($check > 0){
            return response()->json(['status' =>'error','message' => 'Menu link already exist...'],200);
        }
        
        $data = [
            'menu_name' => $request->menu_name,
            'menu_slug'=> $request->menu_slug,
            'parent_menu_id' => !empty($request->parent_menu_id)?$request->parent_menu_id:0,
            'menu_code' => $request->menu_code,
            'menu_sequence' => $request->menu_sequence,
            'menu_link' => $request->menu_link,
            'menu_icon' => $request->menu_icon,
            'description' => $request->description,
            'is_active' => ($request->is_active==1)?1:2,
            'created_by'=>Auth::user()->id
        ];
        
        $message = 'Opps! Something went wrong...';
        if($p_id > 0){
            $lastId = Menu::where('id',$p_id)->update($data);
            $message = 'Menu updated successfully...';
        }else{
            $lastId = Menu::insertGetId($data);
            $message = 'Menu added successfully...';
        }
        if($lastId > 0){
            return response()->json(['status' =>'success','message' => $message],200);
        }else{
            return response()->json(['status' =>'error','message' => 'Opps! Something went wrong...'],200);
        }
    }

    public function menu_list_edit(Request $request){
        check_authorize('edit','menu');
        $data = Menu::where('id',$request->p_id)->first();
        echo json_encode(['data'=>$data]);
    }

    public function get_parent_menu(Request $request){
        $id = !empty($request->p_id)?$request->p_id:'';
        $show_type = !empty($request->type)?$request->type:'all';
        
        
        if($show_type == 'ajax_list'){
            $data = Menu::select('id','parent_menu_id','menu_name')->where('is_active',1)->orderBy('menu_name','ASC')->limit(50)->get();
            $html = '<option value="0">Select</option>';
            if(count($data->toArray())){
                foreach($data as $record){
                    $parent_menu_id = ($record->parent_menu_id==0)?'Parent':'Child';
                    $selected = '';
                    if($id == $record->id){
                        $selected = 'selected';
                    }
                    $html .= '<option value="'.$record->id.'" '.$selected.'>'.ucwords($record->menu_name).' ('.$parent_menu_id.')</option>';
                }
            }else{
                $html = '<option value="" hidden>Not found</option>';
            }
            echo $html;
        }
    }
}

