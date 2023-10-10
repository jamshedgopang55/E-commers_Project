<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\product;
use App\Models\productImage;
use App\Models\category;
use App\Models\tmp_image;
use App\Models\brand;
use App\Models\subCategory;

class FrontController extends Controller
{
    public function index(){
        $featured =  product::where('is_featured','YES')->where('status',1)->take(8)->get();
        $latestProducts =  product::orderBy('id','ASC')->where('status',1)->take(8)->get();
        $data['featuredProducts'] = $featured;
        $data['latestProducts'] = $latestProducts;
       return view('front.home',$data);
    }
}
