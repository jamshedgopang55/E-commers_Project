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
        $products =  product::where('status',1)->get();

        // Apply Filter
        if(!empty($categorySlug)){
         $category = category::where('slug',$categorySlug)->first();
         $products =  product::where('category_id',$category->id)->get();
         $categorySelected =$category->id;
        }
        if(!empty($subCategorySlug)){
            $subCategory = subCategory::where('slug',$subCategorySlug)->first();
            $products =  product::where('sub_category_id',$subCategory->id)->get();
            $SubCategorySelected = $subCategory->id;
           }
           if ($req->get('brand')) {
            $brandArray = explode(',', $req->get('brand'));
            $products =  product::whereIn('brand_id',$brandArray)->get();
        }

        if($req->get('price_min')!= '' && $req->get('price_max')!= ''){
            $products =  product::whereBetween('price',[intval($req->get('price_min')),intval($req->get('price_max'))])->get();
        }
        // die();
        $data['categorySelected'] = $categorySelected;
        $data['SubCategorySelected'] = $SubCategorySelected;
        $data['brandArray'] = $brandArray;
        $data['products'] = $products;
        $data['brands'] = $brands;
        return view('front.shop',$data);
    }
}
