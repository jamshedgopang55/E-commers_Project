<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(){
        return view('front.account.login');
    }
    public function register(){
        return view('front.account.register');
    }
    public function processRegister(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5|confirmed',
            'password_confirmation' => 'required'
        ]);
        if($validator->passes()){
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();
            session()->flash('success','You have been registerd successfully.');
            return response()->json([
                'status' => true,
                'message' => 'You have been registerd successfully.'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function authenticate(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if($validator->passes()){
            if(Auth::attempt(['email' =>$request->email , 'password' => $request->password ] ,$request->get('remember') )){
                return redirect()->route('account.profile');
            }else{
                session()->flash('error','Either Email/Password is incorrect.');
            return redirect()->route('account.login')->withInput($request->only('email'));

            }
        }else{
            return redirect()->route('account.login')->withErrors($validator)->withInput($request->only('email'));

        }
    }
    public function profile(){
        return view('front.account.profile');
    }
    public function logout(){
    Auth::logout();
    session()->flash('success','You are Logout SuccessFully');
    return redirect()->route('account.login');
    }
}
