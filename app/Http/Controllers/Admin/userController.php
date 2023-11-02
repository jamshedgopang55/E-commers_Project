<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class userController extends Controller
{
    public function index(Request $request)
    {
        $users = User::orderBy('id' , 'desc')->where('role',1)->paginate(10);
        if($request->get('keyword')){
            $users = User::where('name','like','%'.$request->get('keyword').'%')->where('role',1)->paginate(10);
        }
        return view("admin.users.list", compact("users"));
    }

    public function destroy($id,Request $req){

        $user = User::find($id);
        if(empty($user)){
            $req->session()->flash('error','User Deleted Failed');
            return redirect()->route('users.index');
        };
        $user->delete();

       return response()->json([
                'status' => true,
                'message' =>"<div class='alert alert-success'>User Deleted Successfully</div>"
            ]);
    }

}
