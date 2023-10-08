<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\subCategory;
class productSubCategoryController extends Controller
{
    public function index(Request $req) {
        $subCategory = subCategory::where('category_id',$req->category_id)->get();
        if(!empty($subCategory)){
            return response()->json([
                'status' => true,
                'subCategories' => $subCategory
            ]);
        }else{
            return response()->json([
                'status' => false,
                'subCategories' => []
            ]);
        }
    }
}
