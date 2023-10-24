<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class settingController extends Controller
{
    public function changePasswordFrom(){
        return view('admin.changePassword');
    }
    public function updatePassword(Request $req){
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
