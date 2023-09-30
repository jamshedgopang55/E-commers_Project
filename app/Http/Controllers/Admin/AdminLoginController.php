<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminLoginController extends Controller
{
    function index(){
        return view('admin.login');
    }
    function authenticate(Request $request){
        $request->validate([
            'email' => 'required |email',
            'password' => 'required'
        ]);
        // return $request->password ;
        if(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password],$request->get('remeber'))){
                $admin = Auth::guard('admin')->user();
                if($admin->role == 2){
                    return  redirect()->route('admin.dashboard');
                }
                else{
                    return redirect()->route('admin.login')->with('error' ,'Invalid Email Or Password');
                }
            }
            else{
                return redirect()->route('admin.login')->with('error' ,'Invalid Email Or Password');
            }
        }

    }

