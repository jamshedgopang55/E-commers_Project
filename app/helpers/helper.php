<?php
use App\Models\page;
use App\Models\order;
use App\Models\product;
use App\Mail\orderEmail;
use App\Models\category;
use App\Models\productImage;
use Illuminate\Support\Facades\Mail;

function getCategories(){
    return category::orderBy('name','ASC')->with('subCategory')->where('status',1)->where('showOnHome','Yes')->get();
}
function  featuredProduct(){
    return product::where('is_featured','YES')->where('status',1)->get();
}
function image($productId){
   return productImage::where('product_id',$productId)->first();
}
function staticPage(){
    $pages =page::orderBy('name','ASC')->get();
    return $pages;
}
function productSlug($id){
    $productInfo = product::select('slug')->where('id',$id)->first();
    return $productInfo->slug;

}
function orderEmail($orderId,$user = 'customer'){
    $order = order::where('id',$orderId)->with('items')->first();
    if($user == 'customer'){
        $subject ='Thanks For Your Order';
        $email = $order->email;
    }else{
        $subject ='You have received an Order';
        $email =  $email = Auth::user()->email;
    }
    $mailData = [
        'subject' => $subject,
        'order' => $order,
        'userType' =>$user
    ];
    Mail::to($email)->send(new orderEmail($mailData));
}


?>
