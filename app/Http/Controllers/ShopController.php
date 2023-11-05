<?php

namespace App\Http\Controllers;

use App\Models\brand;
use App\Models\product;
use App\Models\category;
use App\Models\tmp_image;
use App\Models\subCategory;
use App\Models\productImage;
use Illuminate\Http\Request;
use App\Models\productRating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function index(Request $req ,$categorySlug = null,$subCategorySlug = null){
        $categorySelected = '';
        $SubCategorySelected = '';
        $brandArray = [];

        $brands = brand::orderBy('name','ASC')->where('status',1)->get();
        $products =  product::where('status',1);

        // Apply Filter


        if (!empty($categorySlug)) {
            $category = category::where('slug', $categorySlug)->first();
            if($category == null){
                return abort(404);
            }
            $products = $products->where('category_id', $category->id);
            $categorySelected = $category->id;
        }

        if (!empty($subCategorySlug)) {
            $subCategory = subCategory::where('slug', $subCategorySlug)->first();
            if($subCategory == null){
                return abort(404);
            }
            $products = $products->where('sub_category_id', $subCategory->id);
            $SubCategorySelected = $subCategory->id;
        }

        if ($req->get('brand')) {
            $brandArray = explode(',', $req->get('brand'));
            $products = $products->whereIn('brand_id', $brandArray);
        }



        if ($req->get('price_min') != '' && $req->get('price_max') != '') {
            if($req->get('price_max')==5000){
            $products = $products->whereBetween('price', [intval($req->get('price_min')),100000000]);

            }else{
            $products = $products->whereBetween('price', [intval($req->get('price_min')), intval($req->get('price_max'))]);

            }

        }
        if ($req->get('search')) {
            $products = $products->where('tittle','like','%'.$req->get('search').'%');
        }

        if ($req->get('sort')!='') {
            if($req->get('sort')=='latest'){
             $products = $products->orderBy('id','DESC');
            }
           else if($req->get('sort')=='price_asc'){
             $products = $products->orderBy('price','ASC');
            }
            else {
             $products = $products->orderBy('price','DESC');
            }
         }
         else{
             $products = $products->orderBy('id','DESC');
         }
         // Finally, retrieve the filtered products
         $products = $products->paginate(6);

        $data['categorySelected'] = $categorySelected;
        $data['SubCategorySelected'] = $SubCategorySelected;
        $data['brandArray'] = $brandArray;
        $data['products'] = $products;
        $data['brands'] = $brands;
        $data['priceMin'] =  intval($req->get('price_min'));
        $data['priceMax'] =  (intval($req->get('price_max'))==0) ? 5000 : intval($req->get('price_max'));;
        $data['sort'] =  $req->get('sort');

        return view('front.shop',$data);
    }
    public function product($slug){
        $product = product::where('slug',$slug)->with('product_images')->first();


        if($product== NULL ){
            abort(404);
        }

        $related_products = [];
        $productArray = [];
        if($product->related_products != null){
            $productArray = explode(',',$product->related_products);
            $related_products = product::whereIn('id',$productArray)->where('status',1)->with('product_images')->get();
        };
        $data['related_products'] = $related_products;

        $data['product'] = $product;
        return view('front.product',$data);

    }
    ///////Store Rating

    public function storeRating(Request $req , $id) {
        $validator = Validator::make($req->all(), [
            'name' => 'required|min:3',
            'comment' => 'required|min:10',
            'rating' => 'required'

        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }else{
         $count =  productRating::where('product_id' ,$id)->where('user_id' , Auth::user()->id)->count();

         if(Auth::check() ==  false){
            return response()->json([
                'status' => false,
                'errors' => 'plase login'
            ]);
         }

         if($count > 0){
            return response()->json([
                'status'=> true,
                'message'=> " <div class='alert alert-secondary'>you Already Rated This Product..</div>"
                ]);
         }
            $rating  =  new productRating;
            $rating->product_id =  $id;
            $rating->user_id =  Auth::user()->id;
            $rating->name = $req->name;
            $rating->email = Auth::user()->email;
            $rating->comment = $req->comment;
            $rating->rating = $req->rating;
            $rating->status = 0;
            $rating->save();
        }
        return response()->json([
            'status' => true,
            'message' => "<div class='alert alert-success'>Thanks For Your Ratings</div>"
        ]);
    }

    public function showRatigs(Request $req){
        $reviews = productRating::where('product_id' , $req->product_id)->get();
        $count = productRating::where('product_id' , $req->product_id)->count();
        if($count == null){
            $count = 0;
        }
        if($reviews == null){
            return response()->json([
                'status' => false,
                'message' => 'not found',
                'count' => $count
            ]);
        }
        return response()->json([
            'status' => true,
            'reviews' => $reviews,
            'count' => $count
        ]);
    }

}
