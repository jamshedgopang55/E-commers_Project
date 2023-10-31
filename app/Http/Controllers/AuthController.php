<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\order;
use App\Models\wishlist;
use App\Models\order_item;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\resetpasswordEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Notifications\ResetPassword;

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
        $orders = order::where('user_id', $userId)->orderBy('created_at', 'DESC')->where('payment_status' ,'!=' ,'pending')->get();
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
        } else {

            $user = User::find($userId);
            if (Hash::check($req->old_password, $user->password)) {
                User::where('id', $userId)->update([
                    'password' => Hash::make($req->new_password)
                ]);
                return response()->json([
                    'status' => true,
                    'success' => true,
                    'message' => "<div class='alert alert-success'>Your Password Updated Successfully</div>"
                ]);

            } else {

                return response()->json([
                    'status' => true,
                    'success' => false,
                    'message' => "<div class='alert alert-danger'>Your Old Password is incorrect</div>"
                ]);

            }

        }
    }
    public function forgetPassword()
    {
        return view('front.account.forgetPassword');
    }

    public function processForgetPassword(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'email' => 'required|email|exists:users,email',
        ]);
        if ($validator->fails()) {
            // return redirect()->route('front.forgetPassword')->withErrors($validator)->withInput($req->only('email'));
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        } else {
            $token = Str::random(60);
            DB::table('password_reset_tokens')->where('email', $req->email)->delete();
            DB::table('password_reset_tokens')->insert([
                'email' => $req->email,
                'token' => $token,
                'created_at' => now()
            ]);

            $user = User::where('email', $req->email)->first();
            //Send Email
            $formData = [
                'token' => $token,
                'user' => $user,
                'mailSubject' => 'You have requested to reset your Password.'
            ];



            Mail::to($req->email)->send(new resetpasswordEmail($formData));

            session()->flash('success', 'Plesae Check your inbox to reset Your Password');
            return response()->json([
                'status' => true,
                'message' => 'Plesae Check your inbox to reset Your Password'
            ]);

        }
        // $user = User::find($request->user_id);

    }
    public function resetPassword($token)
    {
        $tokenExist = DB::table('password_reset_tokens')->where('token', $token)->first();
        if ($tokenExist ==  null) {
            return redirect()->route('front.forgetPassword')->with('error', 'Invalid Request');
        }
        return view('front.account.resetPassword',[
            'token'=> $token,
        ]);
    }

    public function processResetPassword(Request $req){

        $tokenObj = DB::table('password_reset_tokens')->where('token', $req->token)->first();
        if ($tokenObj ==  null) {
            return redirect()->route('front.forgetPassword')->with('error', 'Invalid Request');
        }
        $user = User::where('email', $tokenObj->email)->first();


        $validator = Validator::make($req->all(), [
            'password' => 'required|min:5',
            'password_confirmation' => 'required|same:password'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }else{
            User::where('id' , $user->id)->update([
                'password' => Hash::make($req->password)
            ]);
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            session()->flash('success', 'You have successfully updated your password');
            return response()->json([
                'status' => true,
                'message'=> 'You have successfully updated your password',
            ]);
        }
    }

}

