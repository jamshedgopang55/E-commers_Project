<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\order;
use App\Models\wishlist;
use App\Models\order_item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        return view('front.account.login');
    }
    public function register()
    {
        return view('front.account.register');
    }
    public function processRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5|confirmed',
            'password_confirmation' => 'required'
        ]);
        if ($validator->passes()) {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();
            session()->flash('success', 'You have been registerd successfully.');
            return response()->json([
                'status' => true,
                'message' => 'You have been registerd successfully.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->passes()) {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {

                if (session()->has('url.intended')) {
                    return redirect(session()->get('url.intended'));
                }
                return redirect()->route('account.profile');
            } else {
                session()->flash('error', 'Either Email/Password is incorrect.');
                return redirect()->route('account.login')->withInput($request->only('email'));

            }
        } else {
            return redirect()->route('account.login')->withErrors($validator)->withInput($request->only('email'));

        }
    }
    public function profile()
    {
        $user = User::find(Auth::User()->id);

        return view('front.account.profile', [
            'user' => $user,
        ]);
    }
    public function logout()
    {
        Auth::logout();
        session()->flash('success', 'You are Logout SuccessFully');
        return redirect()->route('account.login');
    }
    public function orders()
    {
        $userId = Auth()->User()->id;
        $orders = order::where('user_id', $userId)->orderBy('created_at', 'DESC')->get();
        $data['orders'] = $orders;
        return view('front.account.order', $data);
    }
    public function orderDetail($id)
    {
        $userId = Auth()->User()->id;

        $order = order::where('user_id', $userId)->where('id', $id)->first();
        if ($order == null) {
            session()->flash('error', 'record not found ');
            return redirect()->route('account.orders');
        }
        $items = order_item::where('order_id', $order->id)->get();
        $count = order_item::where('order_id', $order->id)->count();
        $data['order'] = $order;
        $data['items'] = $items;
        $data['count'] = $count;
        return view('front.account.orderDetail', $data);
    }
    public function wishlist()
    {
        $wishlist = wishlist::where('user_id', Auth()->user()->id)->with('product')->orderBy('created_at', 'DESC')->get();
        $data['wishlist'] = $wishlist;
        return view('front.account.wishlist', $data);
    }
    public function removeProductFromWishlist(Request $req)
    {
        $wishlist = Wishlist::where('user_id', Auth()->user()->id)->where('product_id', $req->id)->first();
        if ($wishlist == null) {
            session()->flash('error', 'Product not Found');
            return response()->json([
                'status' => true,
            ]);
        } else {
            $wishlist->delete();
            return response()->json([
                'status' => true,
                'message' => "<div class='alert alert-success'>Product remove Successfully</div>"
            ]);

        }
    }
    public function updateProfile(Request $req)
    {
        $userId = Auth()->user()->id;
        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $userId . ',id',
            'phone' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        } else {
            $user = User::find($userId);
            $user->name = $req->name;
            $user->email = $req->email;
            $user->phone = $req->phone;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => "<div class='alert alert-success'>Your Profile Updated Successfully</div>"
            ]);
        }
    }
    public function changePassword()
    {

        return view('front.account.changePassword');
    }
    public function updatePassword(Request $req)
    {
        $userId = Auth()->user()->id;
        $validator = Validator::make($req->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:5',
            'confirm_password' => 'required|same:new_password'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }else{

            $user = User::find($userId);
            if(Hash::check($req->old_password,$user->password)){
              User::where('id',$userId)->update([
                'password'=> Hash::make($req->new_password)
              ]);
              return response()->json([
                'status' => true,
                'success' => true,
                'message' => "<div class='alert alert-success'>Your Password Updated Successfully</div>"
            ]);

            }else{

                return response()->json([
                'status' => true,
                'success' => false,
                'message' => "<div class='alert alert-danger'>Your Old Password is incorrect</div>"
            ]);

            }

        }
    }

}

