<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\product;
use App\Models\productImage;
use App\Models\category;
use App\Models\tmp_image;
use App\Models\brand;
use App\Models\subCategory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Image;

class productController extends Controller
{
    public function index(Request $request){
        $products = product::with('product_images')->paginate(10);
        if($request->get('keyword')){
            $products = product::where('tittle','like','%'.$request->get('keyword').'%')->paginate(10);
        }

        $data['products'] = $products;
        return view('admin.products.list',$data);
    }
    public function create(){
        $categories = category::orderBy('name','ASC')->get();
        $brands = brand::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        return view('admin.products.create',$data);
    }

    public function store(Request  $req){
        // return $req->track_qty;
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products,slug',
            'price' => 'required|numeric',
            'sku' => 'required|numeric',
            'track_qty' => 'required|in:Yes,No',
            'category'=> 'required|numeric',
            'featured'=> 'required|in:Yes,No',
            'status' => 'required',
        ];
        if(!empty($req->track_qty && $req->track_qty == 'Yes')){
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($req->all(),$rules);

        if($validator->passes()){
           $product = new product();
           $product->tittle = $req->title;
           $product->slug = $req->slug;
           $product->description = $req->description;
           $product->price = $req->price;
           $product->compare_price = $req->compare_price;
           $product->sku = $req->sku;
           $product->barcode = $req->barcode;
           $product->track_qty = $req->track_qty;
           $product->qty = $req->qty;
           $product->status = $req->status;
           $product->category_id = $req->category;
           $product->is_featured = $req->featured;
           $product->sub_category_id = $req->sub_category;
           $product->brand_id = $req->brand;
           $product->save();


           if(!empty($req->images_array)){
            foreach ($req->images_array as $temp_img_id) {
                $temp_img = tmp_image::find($temp_img_id);
                $ext = last(explode('.',$temp_img->name));

                $productImage = new productImage();
                $productImage->product_id = $product->id;
                $productImage->image = 'NULL';
                $productImage->save();

                $image_name = $product->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                $productImage->image = $image_name;
                $productImage->save();

                ///Large Thumbnail
                $sPath  = public_path('temp/'.$temp_img->name);
                $dPath  = public_path('uploads/product/large/'.$image_name);
                $image  = image::make($sPath);
                $image->resize(1400,null,function($constraint){
                    $constraint->aspectRatio();
                });
                $image->save($dPath);
                ///Small Thumbnail

                $dPath  = public_path('uploads/product/small/'.$image_name);
                $image  = image::make($sPath);
                $image->resize(300,300,function($constraint){
                    $constraint->aspectRatio();
                });
                $image->save($dPath);
            }

        }
        return response()->json([
            $req->session()->flash('success','Category added successfully'),
            'status' => true,
            'message' =>'product Added Successfully'
        ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }
    public function edit($id){
        $product = product::find($id);
        if(empty($product)){
            session()->flash('error','Recorde not Found');
            return redirect()->route('products.list');
        };
        $productImages = productImage::where('product_id',$product->id)->get();
        $subCategories = subCategory::where('category_id',$product->category_id)->get();
        $data['productImages'] = $productImages;
        $data['product'] = $product;
        $data['subCategories'] = $subCategories;
        $categories = category::orderBy('name','ASC')->get();
        $brands = brand::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;


        return view('admin.products.edit',$data);
    }
    public function update($id,Request $req){

        $product = product::find($id);


        if(empty($product)){
            $req->session()->flash('error','product update Failed');
            return redirect()->route('products.list');
        };
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products,slug,'.$id.',id',
            'price' => 'required|numeric',
            'sku' => 'required|numeric',
            'track_qty' => 'required|in:Yes,No',
            'category'=> 'required|numeric',
            'featured'=> 'required|in:Yes,No',
            'status' => 'required',
        ];
        if(!empty($req->track_qty && $req->track_qty == 'Yes')){
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($req->all(),$rules);

        if($validator->passes()){

           $product->tittle = $req->title;
           $product->slug = $req->slug;
           $product->description = $req->description;
           $product->price = $req->price;
           $product->compare_price = $req->compare_price;
           $product->sku = $req->sku;
           $product->barcode = $req->barcode;
           $product->track_qty = $req->track_qty;
           $product->qty = $req->qty;
           $product->status = $req->status;
           $product->category_id = $req->category;
           $product->is_featured = $req->featured;
           $product->sub_category_id = $req->sub_category;
           $product->brand_id = $req->brand;
           $product->save();

        //    if(!empty($req->old_images)){
        //     foreach ($req->old_images as $productImage) {
        //         File::delete(public_path().'/uploads/product/large/'.$productImage);
        //         File::delete(public_path().'/uploads/product/small/'.$productImage);
        //     }

        //    }
        //    $productImages = productImage::where('product_id',$product->id);
        //    $productImages->delete();


           if(!empty($req->images_array)){
            foreach ($req->images_array as $temp_img_id) {
                $temp_img = tmp_image::find($temp_img_id);
                $ext = last(explode('.',$temp_img->name));

                $productImage = new productImage();
                $productImage->product_id = $product->id;
                $productImage->image = 'NULL';
                $productImage->save();

                $image_name = $product->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                $productImage->image = $image_name;
                $productImage->save();

                ///Large Thumbnail
                $sPath  = public_path('temp/'.$temp_img->name);
                $dPath  = public_path('uploads/product/large/'.$image_name);
                $image  = image::make($sPath);
                $image->resize(1400,null,function($constraint){
                    $constraint->aspectRatio();
                });
                $image->save($dPath);

                ///Small Thumbnail
                $dPath  = public_path('uploads/product/small/'.$image_name);
                $image  = image::make($sPath);
                $image->resize(300,300,function($constraint){
                    $constraint->aspectRatio();
                });
                $image->save($dPath);
            }

        }

        return response()->json([
            $req->session()->flash('success','product Updated successfully'),
            'status' => true,
            'message' =>'product Updated Successfully'
        ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function destroy($id,Request $req){
        $product = product::find($id);
        $productImages = productImage::where('product_id',$id)->get();

        if(empty($product)){
            $req->session()->flash('error','Category product Failed');
            return redirect()->route('products.list');
        };
        if(!empty($productImages))
        foreach ($productImages as $productImage) {
            File::delete(public_path().'/uploads/product/large/'.$productImage->image);
            File::delete(public_path().'/uploads/product/small/'.$productImage->image);

        }
        $product->delete();

        $req->session()->flash('success','product Deleted successfully');
        return response()->json([
            'status' => true,
            'errors' => "product Deleted successfully"
        ]);
    }
    public function imageDelete(Request $req){
        $productImages = productImage::find($req->id);
        if(!empty($productImages)){
            $productImages->delete();
            return response()->json([
                'status' => true,
                'errors' => "Image Deleted successfully"
            ]);
        }else{
            return response()->json([
                'status' => true,
                'errors' => "Image Deleted Failed"
            ]);
        }
    }
}
