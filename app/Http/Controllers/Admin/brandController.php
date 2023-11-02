<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\brand;
use Illuminate\Support\Facades\Validator;

class brandController extends Controller
{
    public function index(Request $req){

        $brands = brand::orderBy('id' , 'desc')->paginate(10);
        if($req->get('keyword')){
            $brands = brand::where('name','like','%'.$req->get('keyword').'%')->paginate(10);

        }
        return view('admin.brands.list',compact('brands'));
    }


    public function create(){
        return view('admin.brands.create');
    }
    public function store(Request $req){
        $validator = Validator::make($req->all(), [

            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'status' => 'required',
        ]);
        if($validator->passes()){
            $brand = new brand();
            $brand->name = $req->name;
            $brand->slug = $req->slug;
            $brand->status = $req->status;
            $brand->save();
            $req->session()->flash('success','Brand added successfully');
            return response()->json([
                'status' => true,
                'message' => 'Brand added successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function edit($id){
        $brand = brand::find($id);
        if(empty($brand)){
            session()->flash('error','Recorde not Found');
            return redirect()->route('brand.index');
        };
        return view('admin.brands.edit',compact('brand'));
    }


    public function update($id,Request $req){
        // return $req;

        $brand = brand::find($id);

        if(empty($brand)){
            return redirect()->route('category.index');
            session()->flash('error','Recorde not Found');
        };

         $validator = Validator::make($req->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$id.',id',
            'status' => 'required',
        ]);
        if($validator->passes()){
            $brand->name =  $req->name;
            $brand->slug  =  $req->slug;
            $brand->status =  $req->status;
            $brand->save();

            $req->session()->flash('success','Brand Edit successfully');
            return response()->json([
                'status' => true,
                'message' => 'Brand Edit successfully'
            ]);
         }
         else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
         }
    }
    public function destroy($id,Request $req){
        $brand = brand::find($id);
        if(empty($brand)){
            $req->session()->flash('error','Category Deleted Failed');
            return redirect()->route('category.index');
        };
        $brand->delete();

        $req->session()->flash('success','Category Deleted successfully');
        return response()->json([
            'status' => true,
            'errors' => "Category Deleted successfully"
        ]);
    }
}
