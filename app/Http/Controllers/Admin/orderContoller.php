<?php

namespace App\Http\Controllers\Admin;

use App\Models\order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class orderContoller extends Controller
{
    public function index(Request $req){
        $orders = order::latest('order.created_at')->select('order.*','users.name','users.email');
        $orders = $orders->leftJoin('users','users.id' , 'order.user_id');


        if($req->get('keyword') !=  ''){
            $orders = $orders->where('users.name','like','%'.$req->keyword.'%');
            $orders = $orders->orWhere('users.email','like','%'.$req->keyword.'%');
            $orders = $orders->orWhere('order.id','like','%'.$req->keyword.'%');
        }
        $orders = $orders->paginate(10);

        // return $orders;

        $data['orders'] = $orders;
        return view('admin.orders.list',$data);
    }
}
