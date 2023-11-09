<?php

namespace App\Http\Controllers\Admin;

use App\Models\country;
use App\Models\shipping;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class shippingController extends Controller
{
    public function create(){
      $countries = country::get();
      $shippingCharges = shipping::select('countries.name','shipping_charges.*')
      ->leftJoin('countries','countries.id','shipping_charges.country_id')->orderBy('shipping_charges.id','asc')->get();
      $data['shippingCharges'] = $shippingCharges;
      $data['countries'] = $countries;
      return view('admin.shipping.create',$data);
    }
    public function store(Request $req){


      $validator =  Validator::make($req->all(),[
            'country' => 'required',
            'amount' => 'required|numeric'
        ]);
        if($validator->passes()){

            $count = shipping::where('country_id',$req->country)->count();
            if($count > 0){
                Session()->flash('error','Shipping already Added .');
                return response()->json([
                    'status' => true,
                    'message' => 'Shipping already Added'
                ]);
            }

            $shipping = new shipping;
            $shipping->country_id = $req->country;
            $shipping->amount = $req->amount;
            $shipping->save();
            Session()->flash('success','Shipping Added Successfully');
            return response()->json([
                'status' => true,
                'message' => 'Shipping Added Successfully'
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function edit($id){

        $shippingCharge = shipping::find($id);
        if($shippingCharge == null){
            Session()->flash('error','Shipping not found');

            return redirect()->route('shipping.create');

        }
        $countries = country::get();
        $data['shippingCharge'] = $shippingCharge;
        $data['countries'] = $countries;
        return view('admin.shipping.edit',$data);
    }
    public function update(Request $req , $id){
        $validator =  Validator::make($req->all(),[
            'country' => 'required',
            'amount' => 'required|numeric'
        ]);
        if($validator->passes()){
            $shipping = shipping::find($id);

            $shipping->country_id = $req->country;
            $shipping->amount = $req->amount;
            $shipping->save();
            Session()->flash('success','Shipping updated Successfully');
            return response()->json([
                'status' => true,
                'message' => 'Shipping updated Successfully'
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($id){
        $shipping =  shipping::find($id);
        if($shipping == null){
            Session()->flash('error','Shipping not found');
            return response()->json([
                'status' => true,
                'message' => 'Shipping not found'
            ]);
        }
        $shipping->delete();
        Session()->flash('success','Shipping deleted Successfully');
            return response()->json([
                'status' => true,
                'message' => 'Shipping deleted Successfully'
            ]);
    }
}
