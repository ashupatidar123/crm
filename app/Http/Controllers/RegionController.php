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
        $show_type = !empty($request->type)?$request->type:'all';
        
        if($show_type == 'ajax_list'){
            $data = Country::select('id','name')->where('flag',1)->orderBy('name','ASC')->limit(500)->get();
            $html = '<option value="" hidden="">Select country</option>';
            if(!empty($data)){
                foreach($data as $record){
                    $html .= '<option value="'.$record->id.'">'.$record->name.'</option>';
                }
            }else{
                $html .= '<option value="" hidden>Not found</option>';
            }
            echo $html;
        }
        else if($show_type == 'ajax_single'){
            $data = Country::select('id','name','iso3','numeric_code','iso2','phonecode','capital','currency','currency_name')->where('id',$request->p_id)->where('flag',1)->first();
            echo json_encode(['data'=>$data]);
        }
    }

    public function get_ajax_state(Request $request){
        $country_id = ($request['country_id'] > 0)?$request['country_id']:0;
        $data = State::select('id','name')->where('country_id',$country_id)->orderBy('name','ASC')->where('flag',1)->limit(500)->get();
        $html = '<option value="" hidden="">Select state</option>';
        if(!empty($data)){
            foreach($data as $record){
                $html .= '<option value="'.$record->id.'">'.$record->name.'</option>';
            }
        }else{
            $html .= '<option value="" hidden>Not found</option>';
        }
        echo $html;
    }

    public function get_ajax_city(Request $request){
        $state_id = ($request['state_id'] > 0)?$request['state_id']:0;
        $data = City::select('id','name')->where('state_id',$state_id)->where('flag',1)->orderBy('name','ASC')->limit(500)->get();
        $html = '<option value="" hidden="">Select city</option>';
        if(!empty($data)){
            foreach($data as $record){
                $html .= '<option value="'.$record->id.'">'.$record->name.'</option>';
            }
        }else{
            $html .= '<option value="" hidden>Not found</option>';
        }
        echo $html;
    }

    public function country(){
        return view('admin.region.country');
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

        $columns = ['id','name','iso3','numeric_code','capital','currency','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'name';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'ASC';
        
        $query = Country::select('id','name','iso3','numeric_code','capital','currency','created_at');
        if(!empty($search)) {
            $query->where('name', 'LIKE', '%'.$search.'%')->orWhere('iso3', 'LIKE', '%'.$search.'%');
        }
        $query->orderBy($orderColumnIndex, $orderDirection);
        $listData = $query->skip($start_limit)->take($end_limit)->get(); 

        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($listData)){
            $recordsTotal = Country::count();
            
            foreach($listData as $record){
                $edit = '<button class="btn btn-info btn-sm" onclick="return country_edit('.$record->id.');"><i class="fa fa-edit"></i></button>';
                $delete = '<button class="btn btn-danger btn-sm" onclick="return country_delete('.$record->id.');"><i class="fa fa-trash"></i></button>';

                $all_data[] = [
                    'id'=> $record->id,
                    'name'=> $record->name,
                    'iso3'=> $record->iso3,
                    'numeric_code'=> $record->numeric_code,
                    'capital'=> $record->capital,
                    'currency'=> $record->currency,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at)),
                    'action'=>$edit.' '.$delete
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

    public function country_delete(Request $request){
        $record_dlt = Country::find($request->p_id);
        if($record_dlt) {
            $record_dlt->delete();
            return response()->json(['status' =>'success','message' => 'Country deleted successfully'],200); 
        }else{
            return response()->json(['status' =>'error','message' => 'Country deletion failed'],201);
        }
    }

    public function update_country(Request $request){
        //printr($request->all());

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
            return response()->json(['status' =>'success','message' => 'Updation successfully'],200); 
        }else{
            return response()->json(['status' =>'error','message' => 'Something went wrong'],201);
        }
    }
}
