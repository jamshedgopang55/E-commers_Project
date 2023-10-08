<?php

namespace App\Http\Controllers\Admin;

use App\Models\tmp_image;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Image;

class TempController extends Controller
{
    public function create(Request $request){
        //Get Img
        $image = $request->image;
        //Get Img Extension
        $ext  =  $image->getClientOriginalExtension();
        if(!empty($image)){
        //Creating New image Name
        $newName = time() . '.' . $ext;
        //Store Temp Image in TempTable
        $temImage  = new tmp_image();
        $temImage->name = $newName;
        $temImage->save();
            // echo public_path('temp');

       $image->move(public_path('temp'),$newName);

        $sPath = public_path() . '/temp/' . $newName;
        $dPath = public_path() . '/temp/thumb/'.$newName;

        $image  = image::make($sPath);
        $image->fit(300,275);
        $image->save($dPath);

        return response()->json([
            'status' => true,
            'image_id' => $temImage->id,
            'image_path' => asset('temp/thumb/'.$newName),
            'message' => 'Image uploaded successfully'
        ]);

        }
    }
}

