<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class UserController extends Controller
{
    public function __construct(){
    }

    public function home(){
        return view('home');
    }

    public function userList_filter_count($search){
        
        if(!empty($search)) {
            $filter_count = User::where('name', 'LIKE', '%'.$search.'%')->orWhere('email', 'LIKE', '%'.$search.'%')->count();
        }else{
            $filter_count = User::count();
        }
        return $filter_count;
    }

    public function userList(Request $request){
        $start_limit = !empty($request->input('start_limit'))?$request->input('start_limit'):'';
        $end_limit = !empty($request->input('end_limit'))?$request->input('end_limit'):0;
        if($start_limit < 1){
            $start_limit = $request->input('start');
            $end_limit   = $request->input('length');
        }
        
        $draw  = $request->input('draw');
        $search = !empty($request->input('search.value'))?$request->input('search.value'):'';

        $columns = ['id','name','email','mobile','amount','created_at'];
        $orderColumnIndex = !empty($request->input('order.0.column'))?$columns[$request->input('order.0.column')]:'id';
        $orderDirection   = !empty($request->input('order.0.dir'))?$request->input('order.0.dir'):'DESC';
        
        $query = User::select('id','name','email','mobile','amount','created_at');
        if(!empty($search)) {
            $query->where('name', 'LIKE', '%'.$search.'%')->orWhere('email', 'LIKE', '%'.$search.'%');
        }
        $query->orderBy($orderColumnIndex, $orderDirection);
        $users = $query->skip($start_limit)->take($end_limit)->get(); 

        $all_data = [];
        $recordsTotal = $recordsFiltered = 0;
        if(!empty($users)){
            $recordsTotal = User::count();
            
            foreach($users as $record){
                $all_data[] = [
                    'id'=> $record->id,
                    'name'=> $record->name,
                    'email'=> $record->email,
                    'mobile'=> $record->mobile,
                    'amount'=> $record->amount,
                    'created_at'=> date('d/M/Y',strtotime($record->created_at))
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $this->userList_filter_count($search),
            'data' => $all_data,
        ]);
    }
    
}
