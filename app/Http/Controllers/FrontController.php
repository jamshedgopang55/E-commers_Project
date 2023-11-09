<?php

namespace App\Http\Controllers;

use App\Models\page;
use App\Models\brand;
use App\Models\product;
use App\Models\category;
use App\Models\wishlist;
use App\Mail\contectMail;
use App\Models\tmp_image;
use App\Models\subCategory;
use App\Models\productImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FrontController extends Controller
{
    public function index()
    {
        $featured = product::where('is_featured', 'YES')->where('status', 1)->take(8)->get();
        $latestProducts = product::orderBy('id', 'DESC')->where('status', 1)->take(8)->get();
        $data['featuredProducts'] = $featured;
        $data['latestProducts'] = $latestProducts;
        return view('front.home', $data);
    }
    public function addToWishList(Request $req)
    {
        if (Auth::check() == false) {
            session(['url.intended' => url()->previous()]);
            return response()->json([
                'status' => false,
            ]);
        }
        $product = product::find($req->id);

        if ($product == null) {
            return response()->json([
                'status' => true,
                'message' => "<div class='alert alert-danger'>Product not found</div>"
            ]);
        }
        $wishlistCount = Wishlist::where('user_id', Auth::User()->id)->where('product_id', $req->id)->count();
        // return $wishlistCount < 0;
        if ($wishlistCount > 0) {
            return response()->json([
                'status' => true,
                'message' => " <div class='alert alert-secondary'>Product Already Added In your WishList</div>"
            ]);
        }




        wishlist::updateOrcreate(
            [
                'user_id' => Auth::User()->id,
                'product_id' => $req->id
            ],

            [
                'user_id' => Auth::User()->id,
                'product_id' => $req->id,
            ]
        );


        return response()->json([
            'status' => true,
            'message' => "<div class='alert alert-success'>" . "'<strong>'$product->tittle'</strong>'" . " added In Your WishList</div>"
        ]);
    }

    public function page($slug)
    {
        $page = page::where("slug", $slug)->first();
        if ($page == null) {
            abort(404);
        }
        return view('front.page', compact('page'));
    }
    public function sendContectEmail(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'msg_subject' => 'required',
            'message' => 'required|min:20'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }else{
            $mailData = [
                'name' =>$req->name,
                'email' => $req->email,
                'subject' => $req->msg_subject,
                'message' => $req->message,
                'mail_subject' => 'You Have a received a contact mail'
            ];
            $email = 'jamshedgopang283@gmail.com';
            Mail::to($email)->send(new contectMail($mailData));

            return response()->json([
                'status' => true,
                'message' => "<div class='alert alert-success'>Thanks for   contacting us , we will get back to you soon.</div>"
            ]);
        }
    }
}
