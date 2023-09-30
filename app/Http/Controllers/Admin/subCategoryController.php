<?php

namespace App\Http\Controllers\Admin;
use App\Models\category;
use App\Models\subCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class subCategoryController extends Controller
{
    public function index(Request $req){
        $sub_categories = subCategory::select('sub_categories.*','categories.name as categoryName')->latest('id')->
        leftJoin('categories','sub_categories.category_id','categories.id')
        ->paginate(10);
        if($req->get('keyword')){
            $sub_categories = subCategory::where('name','like','%'.$req->get('keyword').'%')->paginate(10);

        }
        return view('admin.subCategory.list',compact('sub_categories'));

    }
    public function create(){
        $categories = category::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        return view('admin.subCategory.subCategory',$data);
    }
    public function store(Request $req){

        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'status' => 'required',
            'category' => 'required'
        ]);
        if($validator->passes()){
            $subCategory = new subCategory();
            $subCategory->category_id =  $req->category;
            $subCategory->status =  $req->status;
            $subCategory->name  =  $req->name;
            $subCategory->slug  =  $req->slug;
            $subCategory->save();


            $req->session()->flash('success','Sub Category added successfully');
            return response()->json([
                'status' => true,
                'message' => 'Category added successfully'
            ]);
         }
         else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
         }
    }
    public function edit($id){
        $subCategory = subCategory::find($id);
        $categories = category::orderBy('name','ASC')->get();
        if(empty($subCategory)){
        session()->flash('error','Recorde not Found');
            return redirect()->route('sub-Category.index');
        };
        $data['subCategory'] = $subCategory;

        $data['categories'] = $categories;
        return view('admin.subCategory.edit',$data);

    }
    public function update($id,Request $req){
        $subCategory = subCategory::find($id);

        if(empty($subCategory)){
            return redirect()->route('category.index');
            session()->flash('error','Recorde not Found');
        };

         $validator = Validator::make($req->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$id.',id',
            'status' => 'required',
            'category' => 'required'
        ]);
        if($validator->passes()){

            $subCategory->category_id =  $req->category;
            $subCategory->status =  $req->status;
            $subCategory->name  =  $req->name;
            $subCategory->slug  =  $req->slug;
            $subCategory->save();

            $req->session()->flash('success','Sub Category Edit successfully');
            return response()->json([
                'status' => true,
                'message' => 'Sub Category Edit successfully'
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
        $subCategory = subCategory::find($id);
        if(empty($subCategory)){
            $req->session()->flash('error','Category Deleted Failed');
            return redirect()->route('category.index');
        };
        $subCategory->delete();

        $req->session()->flash('success','Category Deleted successfully');
        return response()->json([
            'status' => true,
            'errors' => "Category Deleted successfully"
        ]);
    }
}
