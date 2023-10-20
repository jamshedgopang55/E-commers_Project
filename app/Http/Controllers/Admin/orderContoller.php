<?php

namespace App\Http\Controllers\Admin;

use App\Models\order;
use App\Models\order_item;
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
        $data['orders'] = $orders;
        return view('admin.orders.list',$data);
    }
    public function detail($orderId){
        $order = order::select('order.*','countries.name as country_name')->where('order.id',$orderId)->leftJoin('countries','countries.id','order.country_id')
        ->first();
        if($order == null){
            return redirect()->back();
        }
        $items =  order_item::where('order_id', $order->id)->get();
        return view('admin.orders.detail',[
            'order' => $order,
            'items' => $items
        ]);
    }

    public function changeOrderStatus(Request $req,$id){
        $order =  order::find($id);
        if( $order == null ){
            session()->flash('error', 'Record Not Found');
            return response()->json([
                'status' => true,
                'message'=> 'Record Not Found'
            ]);
        }
        $order->status = $req->status;
        $order->shipped_date = $req->shipped_date;
        $order->save();
        $message = 'Order Updated Successfully';
        session()->flash('success', $message);

        return response()->json([
            'status' => true,
            'message'=> $message
        ]);
    }
}
