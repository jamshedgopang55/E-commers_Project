<?php

namespace App\Http\Controllers\Admin;

use App\Models\category;
use App\Models\tmp_image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class adminCategoryController extends Controller
{
    public function index(Request $request){

        $categories = category::paginate(10);
        if($request->get('keyword')){
            $categories = category::where('name','like','%'.$request->get('keyword').'%')->paginate(10);

        }
        return view('admin.category.list',compact('categories'));
    }
    public function create(){
        return view('admin.category.create');
    }

    public function store(Request $req){

        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
        ]);
        if($validator->passes()){
            $category = new category();
            $category->name =  $req->name;
            $category->slug =  $req->slug;
            $category->status =  $req->status;
            $category->save();

            if(!empty($req->image_id)){
                // $category->image =  $req->image_id;
                $tempImage = tmp_image::find($req->image_id);

                $ext =  last(explode('.',$tempImage->name));
                $newName =  $category->id .".". $ext;

                $sPath  = public_path('temp/'.$tempImage->name);
                $dPath  = public_path('uploads/category/'.$newName);
                File::copy($sPath,$dPath);
                $category->image = $newName;
                $category->save();
            }

            $req->session()->flash('success','Category added successfully');
            return response()->json([
                'status' => true,
                'message' => 'Category added successfully'
            ]);


        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }
    public function edit($id){
        $category = category::find($id);
        if(empty($category)){
            session()->flash('error','Recorde not Found');
            return redirect()->route('category.index');
        };
        return view('admin.category.cateoryEdit',compact('category'));
    }
    public function update($id,Request $req){

        $category = category::find($id);
        if(empty($category)){
            session()->flash('error','Recorde not Found');
            return redirect()->route('category.index');
        };

        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$id.',id'
        ]);
        if($validator->passes()){
            $category->name =  $req->name;
            $category->slug =  $req->slug;
            $category->status =  $req->status;
            $category->save();
            $oldImage = $category->image;

            if(!empty($req->image_id)){
                $tempImage = tmp_image::find($req->image_id);
                $ext =  last(explode('.',$tempImage->name));
                $newName =  $category->id ."-".time().".". $ext;

                $sPath  = public_path('temp/'.$tempImage->name);
                $dPath  = public_path('uploads/category/'.$newName);
                File::copy($sPath,$dPath);
                $category->image = $newName;
                $category->save();
                File::delete(public_path().'/uploads/category/'.$oldImage);
            }

            $req->session()->flash('success','Category added successfully');
            return response()->json([
                'status' => true,
                'message' => 'Category added successfully'
            ]);


        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($id,Request $req){
        $category = category::find($id);
        if(empty($category)){
            $req->session()->flash('error','Category Deleted Failed');
            return redirect()->route('category.index');
        };
        File::delete(public_path().'/uploads/category/'.$category->image);
        $category->delete();

        $req->session()->flash('success','Category Deleted successfully');
        return response()->json([
            'status' => true,
            'errors' => "Category Deleted successfully"
        ]);
    }
}
