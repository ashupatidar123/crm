<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Carbon\Carbon;

class RegionController extends Controller{
    
    public function __construct(){

    }
    public function get_ajax_country(Request $request){
        $id = !empty($request->p_id)?$request->p_id:'';
        $show_type = !empty($request->type)?$request->type:'all';
        
        if($show_type == 'ajax_list'){
            $data = Country::select('id','name')->where('is_active',1)->orderBy('name','ASC')->limit(500)->get();
            $html = '<option value="" hidden="">Select country</option>';
            if(!empty($data)){
                foreach($data as $record){
                    $selected = '';
                    if($id == $record->id){
                        $selected = 'selected';
                    }
                    $html .= '<option value="'.$record->id.'" '.$selected.' data-name="'.$record->name.'">'.$record->name.'</option>';
                }
            }else{
                $html .= '<option value="" hidden>Not found</option>';
            }
            echo $html;
        }
        else if($show_type == 'ajax_single'){
            $data = Country::select('id','name','iso3','numeric_code','iso2','phonecode','capital','currency','currency_name')->where('id',$request->p_id)->where('is_active',1)->first();
            echo json_encode(['data'=>$data]);
        }
    }

    public function get_ajax_state(Request $request){
        $show_type = !empty($request->type)?$request->type:'all';
        $country_id = ($request['country_id'] > 0)?$request['country_id']:0;
        $state_id = ($request['state_id'] > 0)?$request['state_id']:0;

        if($show_type == 'ajax_list'){
            $data = State::select('id','name')->where('country_id',$country_id)->orderBy('name','ASC')->where('is_active',1)->limit(500)->get();
            $html = '<option value="" hidden="">Select state</option>';
            if(!empty($data)){
                foreach($data as $record){
                    $selected = '';
                    if($state_id == $record->id){
                        $selected = 'selected';
                    }

                    $html .= '<option value="'.$record->id.'" '.$selected.' data-name="'.$record->name.'">'.$record->name.'</option>';
                }
            }else{
                $html .= '<option value="" hidden>Not found</option>';
            }
            echo $html;
        }
        else if($show_type == 'ajax_single'){
            $data = State::select('id','name','iso2','country_id','country_code')->where('id',$request->p_id)->where('is_active',1)->first();
            echo json_encode(['data'=>$data]);
        }
    }

    public function get_ajax_city(Request $request){
        $show_type = !empty($request->type)?$request->type:'all';
        $state_id = ($request['state_id'] > 0)?$request['state_id']:0;
        $city_id = ($request['city_id'] > 0)?$request['city_id']:0;

        if($show_type == 'ajax_list'){
            $data = City::select('id','name')->where('state_id',$state_id)->where('is_active',1)->orderBy('name','ASC')->limit(500)->get();
            $html = '<option value="" hidden="">Select city</option>';
            if(!empty($data)){
                foreach($data as $record){
                    $selected = '';
                    if($city_id == $record->id){
                        $selected = 'selected';
                    }
                    $html .= '<option value="'.$record->id.'" '.$selected.' data-name="'.$record->name.'">'.$record->name.'</option>';
                }
            }else{
                $html .= '<option value="" hidden="">Not found</option>';
            }
            echo $html;
        }
        else if($show_type == 'ajax_single'){
            $data = City::select('id','name','country_id','state_id','state_code')->where('id',$request->p_id)->where('is_active',1)->first();
            echo json_encode(['data'=>$data]);
        }
    }

    /* Country section */
    public function country(){
        return view('master.region.country.index');
    }

    public function country_list_filter_count($search){
        
        if(!empty($search)) {
            $filter_count = Country::where('name', 'LIKE', '%'.$search.'%')->orWhere('iso3', 'LIKE', '%'.$search.'%')->count();
        }else{
            $filter_count = Country::count();
        }
        return $filter_count;
    }

    public function country_list(Request $request){
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):'';
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):0;
        if($start_limit < 1){
            $start_limit = $request->input('start');
            $end_limit   = $request->input('length');
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','is_active','name','iso3','numeric_code','capital','currency','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'name';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'ASC';
        
        $query = Country::select('id','name','iso3','numeric_code','capital','currency','is_active','created_at');
        if(!empty($search)) {
            $query->where('name', 'LIKE', '%'.$search.'%')->orWhere('iso3', 'LIKE', '%'.$search.'%');
        }
        $query->orderBy($orderColumnIndex, $orderDirection);
        $listData = $query->skip($start_limit)->take($end_limit)->get(); 

        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($listData)){
            $recordsTotal = Country::count();
            $sno = 1+$start_limit;
            foreach($listData as $record){
                $edit = '<button class="btn btn-default btn-sm addEditLoader_'.$record->id.'" onclick="return country_edit('.$record->id.');" title="Edit"><i class="fa fa-edit"></i></button>';
                $delete = '<button class="btn btn-default btn-sm deleteLoader_'.$record->id.'" onclick="return country_delete('.$record->id.');" title="Delete"><i class="fa fa-trash"></i></button>';

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return region_active_inactive('.$record->id.',1,\'country\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return region_active_inactive('.$record->id.',2,\'country\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $all_data[] = [
                    'sno'=> $sno++,
                    'name'=> $record->name,
                    'iso3'=> $record->iso3,
                    'numeric_code'=> $record->numeric_code,
                    'capital'=> $record->capital,
                    'currency'=> $record->currency,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at)),
                    'action'=>$edit.' '.$status.' '.$delete
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->country_list_filter_count($search),
            'data' => $all_data,
        ]);
    }

    public function country_update(Request $request){
        
        $check = Country::find($request->p_id);
        if($check) {
            $data = [];
            if(!empty($request->name)){
                $data['name'] = $request->name;
            }
            if(!empty($request->iso3)){
                $data['iso3'] = $request->iso3;
            }
            if(!empty($request->numeric_code)){
                $data['numeric_code'] = $request->numeric_code;
            }
            if(!empty($request->iso2)){
                $data['iso2'] = $request->iso2;
            }
            if(!empty($request->phonecode)){
                $data['phonecode'] = $request->phonecode;
            }
            if(!empty($request->capital)){
                $data['capital'] = $request->capital;
            }
            if(!empty($request->currency)){
                $data['currency'] = $request->currency;
            }
            if(!empty($request->currency_name)){
                $data['currency_name'] = $request->currency_name;
            }
            Country::where('id',$request->p_id)->update($data);
            return response()->json(['status' =>'success','message' => 'Country updated successfully'],200); 
        }else{
            return response()->json(['status' =>'error','message' => 'Something went wrong'],201);
        }
    }

    public function country_delete(Request $request){
        $record_dlt = Country::find($request->p_id);
        if($record_dlt) {
            $record_dlt->delete();
            return response()->json(['status' =>'success','message' => 'Country deleted successfully'],200); 
        }else{
            return response()->json(['status' =>'error','message' => 'Country deletion failed'],201);
        }
    }

    /* State section */
    public function state(){
        return view('master.region.state.index');
    }

    public function state_list_filter_count($search){
        
        if(!empty($search)) {
            $filter_count = State::where('name', 'LIKE', '%'.$search.'%')->orWhere('iso2', 'LIKE', '%'.$search.'%')->count();
        }else{
            $filter_count = State::count();
        }
        return $filter_count;
    }

    public function state_list(Request $request){
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):'';
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','is_active','name','iso2','country_code','country_id','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'name';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'ASC';
        
        $query = State::with('single_country')->select('id','name','iso2','country_code','country_id','is_active','created_at');
        if(!empty($search)) {
            $query->where('name', 'LIKE', '%'.$search.'%')->orWhere('iso2', 'LIKE', '%'.$search.'%');
        }

        $query->orderBy($orderColumnIndex, $orderDirection);
        $listData = $query->skip($start_limit)->take($end_limit)->get(); 
        
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($listData)){
            $recordsTotal = State::count();
            $sno = 1+$start_limit;
            foreach($listData as $record){
                $edit = '<button class="btn btn-default btn-sm addEditLoader_'.$record->id.'" onclick="return state_edit('.$record->id.');" title="Edit"><i class="fa fa-edit"></i></button>';
                $delete = '<button class="btn btn-default btn-sm deleteLoader_'.$record->id.'" onclick="return state_delete('.$record->id.');" title="Delete"><i class="fa fa-trash"></i></button>';

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return region_active_inactive('.$record->id.',1,\'state\');" title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return region_active_inactive('.$record->id.',2,\'state\');" title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $all_data[] = [
                    'sno'=> $sno++,
                    'name'=> $record->name,
                    'country_name'=> $record->single_country->name,
                    'iso2'=> $record->iso2,
                    'country_code'=> $record->country_code,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at)),
                    'action'=>$edit.' '.$status.' '.$delete
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->state_list_filter_count($search),
            'data' => $all_data,
        ]);
    }

    public function state_update(Request $request){
        $check = State::find($request->p_id);
        
        if($check) {
            $data = [];
            if(!empty($request->name)){
                $data['name'] = $request->name;
            }
            if(!empty($request->country_id)){
                $data['country_id'] = $request->country_id;
            }
            if(!empty($request->country_code)){
                $data['country_code'] = $request->country_code;
            }
            if(!empty($request->iso2)){
                $data['iso2'] = $request->iso2;
            }
            State::where('id',$request->p_id)->update($data);
            return response()->json(['status' =>'success','message' => 'State updated successfully'],200); 
        }else{
            return response()->json(['status' =>'error','message' => 'Something went wrong'],201);
        }
    }

    public function state_delete(Request $request){
        $record_dlt = State::find($request->p_id);
        if($record_dlt) {
            $record_dlt->delete();
            return response()->json(['status' =>'success','message' => 'State deleted successfully'],200); 
        }else{
            return response()->json(['status' =>'error','message' => 'State deletion failed'],201);
        }
    }

    /* State section */
    public function city(){
        return view('master.region.city.index');
    }

    public function city_list_filter_count($search){
        
        if(!empty($search)) {
            $filter_count = City::where('name', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%')->orWhere('state_code', 'LIKE', '%'.$search.'%')->count();
        }else{
            $filter_count = City::count();
        }
        return $filter_count;
    }

    public function city_list(Request $request){
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):0;
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):10;
        if($start_limit < 1){
            $start_limit = !empty($request->input('start'))?$request->input('start'):0;
            $end_limit   = !empty($request->input('length'))?$request->input('length'):10;
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['','is_active','name','country_id','state_id','state_code','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'name';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'ASC';
        
        $query = City::with('single_country','single_state')->select('id','name','country_id','state_id','state_code','is_active','created_at');
        if(!empty($search)) {
            $query->where('name', 'LIKE', '%'.$search.'%')->orWhere('id', 'LIKE', '%'.$search.'%')->orWhere('state_code', 'LIKE', '%'.$search.'%');
        }
        $query->orderBy($orderColumnIndex, $orderDirection);
        $listData = $query->skip($start_limit)->take($end_limit)->get(); 
        
        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($listData)){
            $recordsTotal = City::count();
            $sno = 1+$start_limit;
            foreach($listData as $record){
                $edit = '<button class="btn btn-default btn-sm addEditLoader_'.$record->id.'" onclick="return city_edit('.$record->id.');" title="Edit"><i class="fa fa-edit"></i></button>';
                $delete = '<button class="btn btn-default btn-sm deleteLoader_'.$record->id.'" onclick="return city_delete('.$record->id.');" title="Delete"><i class="fa fa-trash"></i></button>';

                if($record->is_active == 1){
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return region_active_inactive('.$record->id.',1,\'city\');"title="Active"><i class="fa fa-check"></i></button>';
                }else{
                    $status = '<button class="btn btn-default btn-sm activeInactiveLoader_'.$record->id.'" onclick="return region_active_inactive('.$record->id.',2,\'city\');"title="In-Active"><i class="fa fa-close"></i></button>';
                }

                $all_data[] = [
                    'sno'=> $sno++,
                    'name'=> $record->name,
                    'country_name'=> $record->single_country->name,
                    'state_name'=> $record->single_state->name,
                    'state_code'=> $record->state_code,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at)),
                    'action'=>$edit.' '.$status.' '.$delete
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->city_list_filter_count($search),
            'data' => $all_data,
        ]);
    }

    public function city_update(Request $request){
        $check = City::find($request->p_id);
        
        if($check) {
            $data = [];
            if(!empty($request->name)){
                $data['name'] = $request->name;
            }
            if(!empty($request->country_id)){
                $data['country_id'] = $request->country_id;
            }
            if(!empty($request->state_id)){
                $data['state_id'] = $request->state_id;
            }
            if(!empty($request->country_code)){
                $data['state_code'] = $request->state_code;
            }
            
            City::where('id',$request->p_id)->update($data);
            return response()->json(['status' =>'success','message' => 'City updated successfully'],200); 
        }else{
            return response()->json(['status' =>'error','message' => 'Something went wrong'],201);
        }
    }

    public function city_delete(Request $request){
        $record_dlt = City::find($request->p_id);
        if($record_dlt) {
            $record_dlt->delete();
            return response()->json(['status' =>'success','message' => 'City deleted successfully'],200); 
        }else{
            return response()->json(['status' =>'error','message' => 'City deletion failed'],201);
        }
    }

    public function region_active_inactive(Request $request){
        $type = ($request->type==1)?2:1;
        $tbl = $request->tbl;
        
        if($request->p_id < 1){
           return response()->json(['status' =>'error','message' => 'Something went wrong'],201); 
        }
        else if($tbl == 'country'){
            Country::where('id',$request->p_id)->update(['is_active'=>$type]);
            if($type == 1){
                return response()->json(['status' =>'success','message' => 'Active successfully'],200);
            }else{
                return response()->json(['status' =>'success','message' => 'In-Active successfully'],200);
            }
        }
        else if($tbl == 'state'){
            State::where('id',$request->p_id)->update(['is_active'=>$type]);
            if($type == 1){
                return response()->json(['status' =>'success','message' => 'Active successfully'],200);
            }else{
                return response()->json(['status' =>'success','message' => 'In-Active successfully'],200);
            }
        }
        else if($tbl == 'city'){
            City::where('id',$request->p_id)->update(['is_active'=>$type]);
            if($type == 1){
                return response()->json(['status' =>'success','message' => 'Active successfully'],200);
            }else{
                return response()->json(['status' =>'success','message' => 'In-Active successfully'],200);
            }
        }

        return response()->json(['status' =>'error','message' => 'Something went wrong'],201);
    }
}
