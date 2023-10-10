<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\product;
use App\Models\productImage;
use App\Models\category;
use App\Models\tmp_image;
use App\Models\brand;
use App\Models\subCategory;

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
            $products = $products->where('category_id', $category->id);
            $categorySelected = $category->id;
        }

        if (!empty($subCategorySlug)) {
            $subCategory = subCategory::where('slug', $subCategorySlug)->first();
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
            $related_products = product::whereIn('id',$productArray)->with('product_images')->get();
        };
        $data['related_products'] = $related_products;

        $data['product'] = $product;
        return view('front.product',$data);

    }
}
