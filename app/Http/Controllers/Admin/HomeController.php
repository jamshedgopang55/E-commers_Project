<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    function index(){
        return view('admin.dashboard');
        return 'wellcome'.'<a href="'.route('admin.logout').'">Logout</a>';
    }
    function logout(){
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
